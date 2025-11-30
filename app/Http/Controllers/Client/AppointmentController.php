<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentBookedMail;
use App\Helpers\TimeHelper;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of client's appointments.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->clientAppointments()->with(['service', 'staff']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
                             ->orderBy('appointment_time', 'desc')
                             ->paginate(10);

        // Get upcoming appointments
        $upcomingAppointments = Auth::user()->clientAppointments()
            ->with(['service', 'staff'])
            ->upcoming()
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(3)
            ->get();

        return view('appointments.index', compact('appointments', 'upcomingAppointments'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create(Request $request)
    {
        $services = Service::active()->orderBy('name')->get();
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->orderBy('first_name')->get();

        // Pre-select service if provided
        $selectedService = null;
        if ($request->has('service')) {
            $selectedService = Service::active()->find($request->service);
        }

        return view('appointments.create', compact('services', 'staff', 'selectedService'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
            'appointment_date' => ['required', 'date', function ($attribute, $value, $fail) {
                $date = TimeHelper::parseDate($value);
                $today = TimeHelper::today();
                $maxDate = $today->copy()->addDays(30);
                
                if (!$date) {
                    $fail('Invalid date format.');
                } elseif ($date->lt($today)) {
                    $fail('The appointment date must be today or a future date.');
                } elseif ($date->gt($maxDate)) {
                    $fail('You can only schedule an appointment within 30 days from today.');
                }
            }],
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if the selected time slot is available
        $existingAppointment = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->when($request->staff_id, function($query, $staffId) {
                return $query->where('staff_id', $staffId);
            })
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->first();

        if ($existingAppointment) {
            return back()->withErrors([
                'appointment_time' => 'This time slot is already booked. Please choose a different time.'
            ])->withInput();
        }

        $service = Service::findOrFail($request->service_id);

        \Log::info('Appointment booking validation started', [
            'service_id' => $service->id,
            'date' => $request->appointment_date,
            'time' => $request->appointment_time,
            'staff_id' => $request->staff_id,
            'client_id' => Auth::id()
        ]);

        // If a specific staff is selected, check if that staff is available
        // Otherwise, check if any staff is available for this service
        if ($request->staff_id) {
            $selectedStaff = User::find($request->staff_id);
            if (!$selectedStaff || !$selectedStaff->is_active) {
                \Log::warning('Selected staff not found or inactive', [
                    'staff_id' => $request->staff_id
                ]);
                return back()->withErrors([
                    'staff_id' => 'The selected staff member is not available.'
                ])->withInput();
            }
            
            // Check if selected staff is assigned to this service
            if (!$selectedStaff->isAssignedToService($service->id)) {
                \Log::warning('Selected staff not assigned to service', [
                    'staff_id' => $request->staff_id,
                    'service_id' => $service->id
                ]);
                return back()->withErrors([
                    'staff_id' => 'The selected staff member is not assigned to this service.'
                ])->withInput();
            }
            
            // Check if selected staff is available at this time
            $availableStaff = $this->getAvailableStaffForSlot($service, $request->appointment_date, $request->appointment_time);
            \Log::info('Available staff check for selected staff', [
                'selected_staff_id' => $request->staff_id,
                'available_staff_ids' => $availableStaff->pluck('id')->toArray(),
                'available_count' => $availableStaff->count(),
                'is_selected_staff_available' => $availableStaff->contains('id', $request->staff_id)
            ]);
            
            // If the selected staff is not in the available list, do a simpler check
            if (!$availableStaff->contains('id', $request->staff_id)) {
                // Fallback: Check if staff has a basic schedule for this day (less strict check)
                $dateCarbon = \Carbon\Carbon::parse($request->appointment_date);
                $dayOfWeek = $dateCarbon->format('l');
                $dayOfWeekLower = strtolower($dayOfWeek);
                
                $hasBasicSchedule = $selectedStaff->staffSchedules()
                    ->where(function($q) use ($dayOfWeek, $dayOfWeekLower) {
                        $q->where('day_of_week', $dayOfWeek)
                          ->orWhere('day_of_week', $dayOfWeekLower)
                          ->orWhereRaw('LOWER(day_of_week) = ?', [$dayOfWeekLower]);
                    })
                    ->where('is_available', true)
                    ->whereNotNull('start_time')
                    ->whereNotNull('end_time')
                    ->exists();
                
                // Check if staff has approved leave for this date/time
                $normalizedDate = $dateCarbon->format('Y-m-d');
                $normalizedTime = preg_replace('/:\d{2}$/', '', $request->appointment_time);
                
                $hasApprovedLeave = $selectedStaff->staffUnavailabilities()
                    ->whereDate('unavailable_date', $normalizedDate)
                    ->where('approval_status', 'approved')
                    ->get()
                    ->filter(function($leave) use ($normalizedTime, $service) {
                        // All-day leave
                        if (empty($leave->start_time) || empty($leave->end_time)) {
                            return true;
                        }
                        // Check time overlap
                        try {
                            $appointmentStart = \Carbon\Carbon::createFromFormat('H:i', $normalizedTime);
                            $appointmentEnd = $appointmentStart->copy()->addMinutes($service->duration ?? 60);
                            $leaveStart = \Carbon\Carbon::createFromFormat('H:i', preg_replace('/:\d{2}$/', '', $leave->start_time));
                            $leaveEnd = \Carbon\Carbon::createFromFormat('H:i', preg_replace('/:\d{2}$/', '', $leave->end_time));
                            return $appointmentStart->lt($leaveEnd) && $appointmentEnd->gt($leaveStart);
                        } catch (\Exception $e) {
                            return false;
                        }
                    })
                    ->isNotEmpty();
                
                // If staff has basic schedule and no approved leave, allow the booking
                if ($hasBasicSchedule && !$hasApprovedLeave) {
                    \Log::info('Selected staff passed fallback check (basic schedule, no approved leave)', [
                        'staff_id' => $request->staff_id,
                        'date' => $request->appointment_date,
                        'time' => $request->appointment_time
                    ]);
                    // Allow booking to proceed
                } else {
                    \Log::warning('Selected staff not available at time (failed both strict and fallback checks)', [
                        'staff_id' => $request->staff_id,
                        'available_staff_ids' => $availableStaff->pluck('id')->toArray(),
                        'has_basic_schedule' => $hasBasicSchedule,
                        'has_approved_leave' => $hasApprovedLeave,
                        'date' => $request->appointment_date,
                        'time' => $request->appointment_time,
                        'service_id' => $service->id
                    ]);
                    return back()->withErrors([
                        'appointment_time' => 'The selected staff member is not available at this time. Please choose a different time or date.'
                    ])->withInput();
                }
            }
        } else {
            // No specific staff selected - check if any staff is available
            $hasAvailable = $this->hasAvailableStaffForSlot($service, $request->appointment_date, $request->appointment_time);
            \Log::info('Available staff check (no specific staff selected)', [
                'has_available' => $hasAvailable,
                'service_id' => $service->id,
                'date' => $request->appointment_date,
                'time' => $request->appointment_time
            ]);
            
            if (!$hasAvailable) {
                \Log::warning('No staff available for service', [
                    'service_id' => $service->id,
                    'date' => $request->appointment_date,
                    'time' => $request->appointment_time
                ]);
                return back()->withErrors([
                    'appointment_time' => 'No staff members are available for this service at the selected time. Please choose a different time or date.'
                ])->withInput();
            }
        }

        $appointment = Appointment::create([
            'client_id' => Auth::id(),
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'total_amount' => $service->price,
            'client_notes' => $request->notes,
            'status' => 'pending', // Client bookings start as pending
        ]);

        // Send booking confirmation email to the client (non-blocking)
        $client = Auth::user();
        if ($client && $client->email) {
            try {
                Mail::to($client->email)->send(new AppointmentBookedMail($appointment));
                \Log::info("Appointment booking email sent successfully to {$client->email} for appointment {$appointment->id}");
            } catch (\Exception $e) {
                // Log email error but don't prevent appointment booking
                \Log::error("Failed to send appointment booking email to {$client->email} for appointment {$appointment->id}: " . $e->getMessage(), [
                    'exception' => $e,
                    'appointment_id' => $appointment->id,
                    'client_email' => $client->email
                ]);
            }
        }

        return redirect()->route('appointments.show', $appointment)
                        ->with('success', 'Appointment booked successfully! We will confirm your appointment shortly.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        // Ensure the appointment belongs to the authenticated client
        if ($appointment->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to appointment.');
        }

        $appointment->load(['service', 'staff']);
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        // Ensure the appointment belongs to the authenticated client
        if ($appointment->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to appointment.');
        }

        // Only allow editing of pending or confirmed appointments that are in the future
        if (!$appointment->canBeRescheduled()) {
            return redirect()->route('appointments.show', $appointment)
                           ->with('error', 'This appointment cannot be rescheduled.');
        }

        $services = Service::active()->orderBy('name')->get();
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->orderBy('first_name')->get();

        return view('appointments.edit', compact('appointment', 'services', 'staff'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Ensure the appointment belongs to the authenticated client
        if ($appointment->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to appointment.');
        }

        // Only allow editing of pending or confirmed appointments that are in the future
        if (!$appointment->canBeRescheduled()) {
            return redirect()->route('appointments.show', $appointment)
                           ->with('error', 'This appointment cannot be rescheduled.');
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
            'appointment_date' => ['required', 'date', function ($attribute, $value, $fail) {
                $date = TimeHelper::parseDate($value);
                $today = TimeHelper::today();
                $maxDate = $today->copy()->addDays(30);
                
                if (!$date) {
                    $fail('Invalid date format.');
                } elseif ($date->lt($today)) {
                    $fail('The appointment date must be today or a future date.');
                } elseif ($date->gt($maxDate)) {
                    $fail('You can only schedule an appointment within 30 days from today.');
                }
            }],
            'appointment_time' => 'required|date_format:H:i',
            'client_notes' => 'nullable|string|max:500',
        ]);

        // Check if the selected time slot is available (excluding current appointment)
        $existingAppointment = Appointment::where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id)
            ->when($request->staff_id, function($query, $staffId) {
                return $query->where('staff_id', $staffId);
            })
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->first();

        if ($existingAppointment) {
            return back()->withErrors([
                'appointment_time' => 'This time slot is already booked. Please choose a different time.'
            ])->withInput();
        }

        $service = Service::findOrFail($request->service_id);

        if (!$this->hasAvailableStaffForSlot($service, $request->appointment_date, $request->appointment_time)) {
            return back()->withErrors([
                'appointment_time' => 'No staff members are available for this service at the selected time. Please choose a different time or date.'
            ])->withInput();
        }

        $appointment->update([
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'total_amount' => $service->price,
            'client_notes' => $request->client_notes,
            'status' => 'pending', // Reset to pending when rescheduled
            'confirmed_at' => null,
        ]);

        return redirect()->route('appointments.show', $appointment)
                        ->with('success', 'Appointment rescheduled successfully! We will confirm your new appointment time.');
    }

    /**
     * Cancel the specified appointment.
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        // Ensure the appointment belongs to the authenticated client
        if ($appointment->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to appointment.');
        }

        // Only allow cancellation of appointments that can be cancelled
        if (!$appointment->canBeCancelled()) {
            return redirect()->route('appointments.show', $appointment)
                           ->with('error', 'This appointment cannot be cancelled.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_at' => TimeHelper::now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return redirect()->route('appointments.index')
                        ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Store client feedback for a completed appointment.
     */
    public function storeFeedback(Request $request, Appointment $appointment)
    {
        if ($appointment->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access to appointment.');
        }

        if ($appointment->status !== 'completed') {
            return back()->with('error', 'You can only leave feedback for completed appointments.');
        }

        $request->validate([
            'client_rating' => 'required|integer|min:1|max:5',
            'client_feedback' => 'nullable|string|max:1000',
        ]);

        $appointment->update([
            'client_rating' => $request->client_rating,
            'client_feedback' => $request->client_feedback,
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Get available time slots for a specific date and service.
     */
    public function getAvailableTimeSlots(Request $request)
    {
        try {
            $request->validate([
                'date' => ['required', 'date', function ($attribute, $value, $fail) {
                    $date = TimeHelper::parseDate($value);
                    if (!$date || $date->lt(TimeHelper::today())) {
                        $fail('The date must be today or a future date.');
                    }
                }],
                'service_id' => 'required|exists:services,id',
                'staff_id' => 'nullable|exists:users,id',
            ]);

            $date = $request->date;
            $serviceId = $request->service_id;
            $staffId = $request->staff_id;

            // Get service to determine required specialization
            $service = Service::findOrFail($serviceId);
            $duration = $service->duration ?? 60; // Default to 60 minutes if not set
            $requiredSpecialization = $this->resolveRequiredSpecialization($service);

            // Debug: Log service and specialization info
            \Log::info('getAvailableTimeSlots - Service info:', [
                'service_id' => $serviceId,
                'service_name' => $service->name,
                'service_category' => $service->category,
                'required_specialization' => $requiredSpecialization,
                'date' => $date,
                'duration' => $duration
            ]);

            // Get the day of week for schedule checking
            // Admin uses capitalized format (Monday, Tuesday, etc.)
            $dateCarbon = TimeHelper::parseDate($date);
            $dayOfWeek = $dateCarbon ? $dateCarbon->format('l') : date('l', strtotime($date)); // Returns "Monday", "Tuesday", etc.
            $dayOfWeekLower = strtolower($dayOfWeek);
            $dayOfWeekLabel = $dayOfWeek;
            
            // Get all staff schedules for this day for staff who are assigned to this service
            // This ensures we only consider working hours of staff who can actually perform the service
            try {
                $staffSchedules = \App\Models\StaffSchedule::query()
                    ->where(function($q) use ($dayOfWeek, $dayOfWeekLower) {
                        $q->where('day_of_week', $dayOfWeek)
                          ->orWhere('day_of_week', $dayOfWeekLower)
                          ->orWhereRaw('LOWER(day_of_week) = ?', [$dayOfWeekLower]);
                    })
                    ->available()
                    ->whereHas('staff', function($query) use ($serviceId) {
                        $query->whereIn('role', ['nurse', 'aesthetician'])
                              ->where('is_active', true)
                              ->whereHas('assignedServices', function($q) use ($serviceId) {
                                  $q->where('services.id', $serviceId);
                              });
                    })
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error fetching staff schedules: ' . $e->getMessage());
                $staffSchedules = collect([]);
            }
                
            // Debug: Log staff schedules for the day
            \Log::info('Staff schedules for day:', [
                'day_of_week' => $dayOfWeekLabel,
                'schedules_count' => $staffSchedules->count(),
                'schedules' => $staffSchedules->map(function($schedule) {
                    return [
                        'staff_id' => $schedule->staff_id,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time
                    ];
                })
            ]);

            // If no staff schedules found, use default business hours
            if ($staffSchedules->isEmpty()) {
                \Log::warning('No staff schedules found for day for service-assigned staff, using default hours', [
                    'day' => $dayOfWeek,
                    'service_id' => $serviceId
                ]);
                $businessStart = TimeHelper::parseTime('09:00');
                $businessEnd = TimeHelper::parseTime('18:00');
                
                // Fallback to Carbon::createFromTime if parseTime fails
                if (!$businessStart) {
                    $businessStart = \Carbon\Carbon::today(TimeHelper::getTimezone())->setTime(9, 0, 0);
                }
                if (!$businessEnd) {
                    $businessEnd = \Carbon\Carbon::today(TimeHelper::getTimezone())->setTime(18, 0, 0);
                }
            } else {
                // Use the earliest start time and latest end time from all staff schedules
                $earliestStart = $staffSchedules->min('start_time');
                $latestEnd = $staffSchedules->max('end_time');
                
                if (!$earliestStart || !$latestEnd) {
                    \Log::warning('Invalid schedule times, using default hours', [
                        'earliest_start' => $earliestStart,
                        'latest_end' => $latestEnd
                    ]);
                    $businessStart = TimeHelper::parseTime('09:00');
                    $businessEnd = TimeHelper::parseTime('18:00');
                    
                    // Fallback to Carbon::createFromTime if parseTime fails
                    if (!$businessStart) {
                        $businessStart = \Carbon\Carbon::today(TimeHelper::getTimezone())->setTime(9, 0, 0);
                    }
                    if (!$businessEnd) {
                        $businessEnd = \Carbon\Carbon::today(TimeHelper::getTimezone())->setTime(18, 0, 0);
                    }
                } else {
                    // Use TimeHelper for consistent timezone handling
                    try {
                        $businessStart = TimeHelper::parseTime($earliestStart);
                        $businessEnd = TimeHelper::parseTime($latestEnd);
                        
                        if (!$businessStart || !$businessEnd) {
                            throw new \Exception('Failed to parse schedule times');
                        }
                        
                        // CRITICAL: Ensure minimum business hours (9:00 AM - 6:00 PM)
                        // If calculated end time is too early (before 6:00 PM), use default
                        $minimumEndTime = TimeHelper::parseTime('18:00');
                        if (!$minimumEndTime) {
                            $minimumEndTime = \Carbon\Carbon::today(TimeHelper::getTimezone())->setTime(18, 0, 0);
                        }
                        
                        // If calculated business end is earlier than minimum, use minimum
                        if ($businessEnd->lt($minimumEndTime)) {
                            \Log::warning('Calculated business end time is too early, using minimum (6:00 PM)', [
                                'calculated_end' => $businessEnd->format('H:i'),
                                'minimum_end' => $minimumEndTime->format('H:i'),
                                'earliest_start' => $earliestStart,
                                'latest_end' => $latestEnd
                            ]);
                            $businessEnd = $minimumEndTime;
                        }
                        
                        // Also ensure minimum start time (9:00 AM)
                        $minimumStartTime = TimeHelper::parseTime('09:00');
                        if (!$minimumStartTime) {
                            $minimumStartTime = \Carbon\Carbon::today(TimeHelper::getTimezone())->setTime(9, 0, 0);
                        }
                        
                        // If calculated business start is later than minimum, use minimum
                        if ($businessStart->gt($minimumStartTime)) {
                            \Log::info('Using minimum start time (9:00 AM)', [
                                'calculated_start' => $businessStart->format('H:i'),
                                'minimum_start' => $minimumStartTime->format('H:i')
                            ]);
                            $businessStart = $minimumStartTime;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Error parsing schedule times: ' . $e->getMessage(), [
                            'earliest_start' => $earliestStart,
                            'latest_end' => $latestEnd
                        ]);
                        $businessStart = TimeHelper::parseTime('09:00');
                        $businessEnd = TimeHelper::parseTime('18:00');
                        
                        // Fallback to Carbon::createFromTime if parseTime fails
                        if (!$businessStart) {
                            $businessStart = \Carbon\Carbon::today(TimeHelper::getTimezone())->setTime(9, 0, 0);
                        }
                        if (!$businessEnd) {
                            $businessEnd = \Carbon\Carbon::today(TimeHelper::getTimezone())->setTime(18, 0, 0);
                        }
                    }
                    
                    // Ensure we have valid Carbon instances before logging
                    if ($businessStart && $businessEnd) {
                    \Log::info('Calculated business hours from staff schedules:', [
                        'earliest_start' => $earliestStart,
                        'latest_end' => $latestEnd,
                        'business_start' => $businessStart->format('H:i'),
                        'business_end' => $businessEnd->format('H:i'),
                        'staff_schedules_count' => $staffSchedules->count(),
                        'all_end_times' => $staffSchedules->pluck('end_time')->toArray()
                    ]);
                    }
                }
            }

            // Generate all possible time slots
            $timeSlots = [];
            try {
                $current = $businessStart->copy();
                
                // Ensure duration is valid
                if ($duration <= 0) {
                    $duration = 60; // Default to 60 minutes
                }

                \Log::info('Generating time slots:', [
                    'business_start' => $businessStart->format('H:i'),
                    'business_end' => $businessEnd->format('H:i'),
                    'service_duration' => $duration
                ]);

                // Generate time slots in 30-minute intervals
                // Check if the appointment can fit before the business end time
                // IMPORTANT: We need to ensure the appointment ENDS before or at business end time
                // So we check: current time + duration <= business end time
                while ($current->copy()->addMinutes($duration)->lte($businessEnd)) {
                    $timeSlots[] = $current->format('H:i');
                    $current->addMinutes(30); // 30-minute intervals
                    
                    // Safety check to prevent infinite loops
                    if (count($timeSlots) > 100) {
                        \Log::warning('Too many time slots generated, breaking loop', [
                            'count' => count($timeSlots),
                            'business_start' => $businessStart->format('H:i'),
                            'business_end' => $businessEnd->format('H:i'),
                            'last_slot' => end($timeSlots)
                        ]);
                        break;
                    }
                }
                
                \Log::info('Time slots generated:', [
                    'total_slots' => count($timeSlots),
                    'first_slot' => !empty($timeSlots) ? $timeSlots[0] : 'none',
                    'last_slot' => !empty($timeSlots) ? end($timeSlots) : 'none',
                    'business_start' => $businessStart->format('H:i'),
                    'business_end' => $businessEnd->format('H:i')
                ]);
            } catch (\Exception $e) {
                \Log::error('Error generating time slots: ' . $e->getMessage(), [
                    'business_start' => $businessStart->format('H:i'),
                    'business_end' => $businessEnd->format('H:i'),
                    'duration' => $duration
                ]);
                $timeSlots = [];
            }
            
            // If no time slots generated, log warning
            if (empty($timeSlots)) {
                \Log::warning('No time slots generated', [
                    'business_start' => $businessStart->format('H:i'),
                    'business_end' => $businessEnd->format('H:i'),
                    'service_duration' => $duration
                ]);
            }

            // Get booked appointments for the date with their durations
            // Only consider appointments for staff assigned to this service
            try {
                $bookedAppointments = Appointment::where('appointment_date', $date)
                    ->when($staffId, function($query, $staffId) {
                        return $query->where('staff_id', $staffId);
                    })
                    ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                    ->whereHas('staff', function($query) use ($serviceId) {
                        // Only appointments for staff assigned to this service
                        $query->whereHas('assignedServices', function($q) use ($serviceId) {
                            $q->where('services.id', $serviceId);
                        });
                    })
                    ->with(['service', 'services'])
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error fetching booked appointments: ' . $e->getMessage());
                $bookedAppointments = collect([]);
            }

            // Generate all blocked time slots considering service duration
            $blockedSlots = [];
        foreach ($bookedAppointments as $appointment) {
            try {
                // Handle both H:i and H:i:s formats for appointment_time
                if (!$appointment->appointment_time) {
                    continue; // Skip if no time set
                }
                
                $appointmentTime = $appointment->appointment_time;
                $startTime = TimeHelper::parseTime($appointmentTime);
                if (!$startTime) {
                    continue; // Skip if time parsing fails
                }

                // Get service duration - check both single service and multiple services
                $serviceDuration = 60; // Default to 60 minutes
                if ($appointment->service) {
                    $serviceDuration = $appointment->service->duration ?? 60;
                } elseif ($appointment->services && $appointment->services->isNotEmpty()) {
                    // If multiple services, use the longest duration
                    $serviceDuration = $appointment->services->max('duration') ?? 60;
                }
                
                $endTime = $startTime->copy()->addMinutes($serviceDuration);

                // Block all 30-minute intervals that overlap with this appointment
                $current = $startTime->copy();
                while ($current->lt($endTime)) {
                    $blockedSlots[] = $current->format('H:i');
                    $current->addMinutes(30);
                }
            } catch (\Exception $e) {
                // Log the error but continue processing other appointments
                \Log::warning('Error processing appointment time: ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'appointment_time' => $appointment->appointment_time ?? 'null'
                ]);
                continue;
            }
        }

            $blockedSlots = array_unique($blockedSlots);

            // If specific staff requested, check their availability
            if ($staffId) {
            $staff = User::find($staffId);
            
            // Verify staff is assigned to this service and has schedule
            if (!$staff || !$staff->is_active || !$staff->isAssignedToService($serviceId)) {
                \Log::warning('Requested staff is not assigned to service or inactive', [
                    'staff_id' => $staffId,
                    'service_id' => $serviceId,
                    'is_active' => $staff ? $staff->is_active : false,
                    'is_assigned' => $staff ? $staff->isAssignedToService($serviceId) : false
                ]);
                return response()->json([
                    'available_slots' => [],
                    'total_slots' => count($timeSlots),
                    'blocked_slots' => count($blockedSlots),
                    'service_duration' => $duration,
                    'message' => 'Selected staff is not assigned to this service or is inactive',
                ]);
            }
            
            // Check if staff has schedule for this day
            // Use capitalized format to match database enum (Monday, Tuesday, etc.)
            $dayOfWeek = date('l', strtotime($date)); // Returns "Monday", "Tuesday", etc.
            $staffSchedule = $staff->staffSchedules()
                ->where(function($q) use ($dayOfWeek) {
                    $q->where('day_of_week', $dayOfWeek)
                      ->orWhereRaw('LOWER(day_of_week) = ?', [strtolower($dayOfWeek)]);
                })
                ->where('is_available', true)
                ->first();
                
            if (!$staffSchedule) {
                \Log::warning('Requested staff has no schedule for this day', [
                    'staff_id' => $staffId,
                    'day_of_week' => $dayOfWeek
                ]);
                return response()->json([
                    'available_slots' => [],
                    'total_slots' => count($timeSlots),
                    'blocked_slots' => count($blockedSlots),
                    'service_duration' => $duration,
                    'message' => 'Selected staff has no schedule for this day',
                ]);
            }
            
            $availableSlots = array_filter($timeSlots, function($time) use ($staff, $date, $blockedSlots, $service, $duration) {
                // Check if time is blocked
                if (in_array($time, $blockedSlots)) {
                    return false;
                }
                
                // Check if staff is available at this time (considering appointment duration)
                // Calculate appointment end time
                $appointmentStart = TimeHelper::parseTime($time);
                if (!$appointmentStart) {
                    return false;
                }
                $appointmentEnd = $appointmentStart->copy()->addMinutes($duration ?? 60);
                
                // Check for approved leave requests that overlap with appointment time
                // IMPORTANT: Only approved leaves exclude staff
                // Pending/rejected leaves are NOT checked here
                $hasOverlappingLeave = $staff->staffUnavailabilities()
                    ->where('unavailable_date', $date)
                    ->where('approval_status', 'approved') // Only check approved leaves
                    ->get()
                    ->filter(function($leave) use ($appointmentStart, $appointmentEnd) {
                        // All-day leave
                        if (empty($leave->start_time) || empty($leave->end_time)) {
                            return true;
                        }
                        
                        // Parse leave time range
                        $leaveStart = TimeHelper::parseTime($leave->start_time);
                        $leaveEnd = TimeHelper::parseTime($leave->end_time);
                        
                        if (!$leaveStart || !$leaveEnd) {
                            return false;
                        }
                        
                        // Check if appointment time overlaps with leave time range
                        return $appointmentStart->lt($leaveEnd) && $appointmentEnd->gt($leaveStart);
                    })
                    ->isNotEmpty();
                
                if ($hasOverlappingLeave) {
                    return false;
                }
                
                // Also check basic availability (schedule)
                if (!$staff->isAvailableAt($date, $time)) {
                    return false;
                }
                
                // Check for conflicting appointments using the same logic as getAvailableStaffForSlot
                $conflictingAppointment = Appointment::where('staff_id', $staff->id)
                    ->where('appointment_date', $date)
                    ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                    ->get()
                    ->filter(function($appointment) use ($time, $duration) {
                        try {
                            $appointmentTime = $appointment->appointment_time;
                            if (!$appointmentTime) return false;
                            
                            $startTime = TimeHelper::parseTime($appointmentTime);
                            if (!$startTime) return false;
                            
                            $serviceDuration = $appointment->service ? ($appointment->service->duration ?? 60) : 60;
                            $endTime = $startTime->copy()->addMinutes($serviceDuration);
                            
                            $checkTime = TimeHelper::parseTime($time);
                            if (!$checkTime) return false;
                            $checkEndTime = $checkTime->copy()->addMinutes($duration);
                            
                            // Check if time ranges overlap
                            return $checkTime->lt($endTime) && $checkEndTime->gt($startTime);
                        } catch (\Exception $e) {
                            return false;
                        }
                    })
                    ->isNotEmpty();
                
                return !$conflictingAppointment;
            });
            } else {
                // IMPORTANT: For Preferred Time dropdown, show ALL possible time slots
                // Staff filtering will happen when client selects a Preferred Time
                // Only exclude time slots that are blocked by existing appointments
                // This allows clients to see all available times, then staff will be filtered based on their selection
                $availableSlots = [];
                foreach ($timeSlots as $time) {
                    // Only skip if this time slot is blocked by existing appointments
                    // Don't filter based on staff availability here - that happens when staff list is loaded
                    if (in_array($time, $blockedSlots)) {
                        \Log::debug("Time slot {$time} is blocked by existing appointment");
                        continue;
                    }
                    
                    // Include all other time slots (staff filtering happens in getAvailableStaff)
                    $availableSlots[] = $time;
                }
                
                \Log::info('Time slots for Preferred Time dropdown (not filtered by staff availability):', [
                    'total_slots' => count($timeSlots),
                    'blocked_slots' => count($blockedSlots),
                    'available_slots' => count($availableSlots),
                    'first_slot' => !empty($availableSlots) ? $availableSlots[0] : 'none',
                    'last_slot' => !empty($availableSlots) ? end($availableSlots) : 'none'
                ]);
            }

            // Debug: Log final results
            \Log::info('getAvailableTimeSlots - Final results:', [
                'total_possible_slots' => count($timeSlots),
                'blocked_slots' => count($blockedSlots),
                'available_slots' => count($availableSlots),
                'available_times' => $availableSlots,
                'business_hours' => $businessStart->format('H:i') . ' - ' . $businessEnd->format('H:i')
            ]);

            return response()->json([
                'available_slots' => array_values($availableSlots),
                'total_slots' => count($timeSlots),
                'blocked_slots' => count($blockedSlots),
                'service_duration' => $duration,
                'required_specialization' => $requiredSpecialization,
                'day_of_week' => $dayOfWeekLabel,
                'business_hours' => [
                    'start' => $businessStart->format('H:i'),
                    'end' => $businessEnd->format('H:i'),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in getAvailableTimeSlots: ' . $e->getMessage());
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->getMessage(),
                'available_slots' => []
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in getAvailableTimeSlots: ' . $e->getMessage(), [
                'date' => $request->date ?? null,
                'service_id' => $request->service_id ?? null,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Return empty slots instead of error to prevent frontend issues
            return response()->json([
                'available_slots' => [],
                'total_slots' => 0,
                'blocked_slots' => 0,
                'service_duration' => 60,
                    'day_of_week' => TimeHelper::parseDate($request->date ?? TimeHelper::todayString())?->format('l') ?? date('l', strtotime($request->date ?? 'today')),
                'business_hours' => [
                    'start' => '09:00',
                    'end' => '18:00',
                ],
                'error' => 'Failed to load available time slots',
                'message' => 'Please try again or contact support if the issue persists.'
            ], 200); // Return 200 instead of 500 to prevent frontend error
        }
    }

    /**
     * Get available staff for a specific date.
     */
    public function getAvailableStaff(Request $request)
    {
        try {
            $request->validate([
                'date' => ['required', 'date', function ($attribute, $value, $fail) {
                    $date = TimeHelper::parseDate($value);
                    if (!$date) {
                        $fail('Invalid date format.');
                    } elseif ($date->lt(TimeHelper::today())) {
                        $fail('The date must be today or a future date.');
                    }
                }],
                'service_id' => 'required|exists:services,id',
                'time' => 'nullable|date_format:H:i',
            ]);

            $date = $request->date;
            $serviceId = $request->service_id;
            $time = $request->time;

            // Get service to determine required specialization
            $service = Service::findOrFail($serviceId);
            $requiredSpecialization = $this->resolveRequiredSpecialization($service);

            // Get day of week for schedule checking
            // Database uses capitalized format (Monday, Tuesday, etc.)
            $dateCarbon = TimeHelper::parseDate($date);
            $dayOfWeek = $dateCarbon ? $dateCarbon->format('l') : date('l', strtotime($date)); // Returns "Monday", "Tuesday", etc.

            \Log::info('Getting available staff', [
                'date' => $date,
                'day_of_week' => $dayOfWeek,
                'service_id' => $serviceId,
                'time' => $time
            ]);

            // First, let's get all active staff with schedules for this day
            // We'll filter by service assignment and availability in the map function
            $allStaffWithSchedule = User::whereIn('role', ['nurse', 'aesthetician'])
                ->where('is_active', true)
                ->whereHas('staffSchedules', function($query) use ($dayOfWeek) {
                    // Match exact day format (capitalized)
                    $query->where('day_of_week', $dayOfWeek)
                          ->where('is_available', true)
                          ->whereNotNull('start_time')
                          ->whereNotNull('end_time');
                })
                ->with([
                    'staffSchedules' => function($query) use ($dayOfWeek) {
                        $query->where('day_of_week', $dayOfWeek)
                              ->where('is_available', true);
                    },
                    'staffSpecializations',
                    'assignedServices', // Load all assigned services, not filtered
                    'staffUnavailabilities' => function($query) use ($date) {
                        $query->where('unavailable_date', $date)
                              ->where('approval_status', 'approved'); // Only load approved leaves
                    }
                ])
                ->orderBy('first_name')
                ->get();

            \Log::info("Initial staff query results", [
                'total_staff_found' => $allStaffWithSchedule->count(),
                'staff_ids' => $allStaffWithSchedule->pluck('id')->toArray(),
                'service_id' => $serviceId,
                'date' => $date,
                'day_of_week' => $dayOfWeek
            ]);

            // Debug: Log each staff's details
            foreach ($allStaffWithSchedule as $staff) {
                $schedule = $staff->staffSchedules->firstWhere('day_of_week', $dayOfWeek);
                $assignedServiceIds = $staff->assignedServices->pluck('id')->toArray();
                $isAssigned = in_array($serviceId, $assignedServiceIds);
                
                \Log::info("Staff details", [
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->name,
                    'is_active' => $staff->is_active,
                    'has_schedule' => $schedule ? true : false,
                    'schedule_available' => $schedule ? $schedule->is_available : false,
                    'schedule_start' => $schedule ? $schedule->start_time : null,
                    'schedule_end' => $schedule ? $schedule->end_time : null,
                    'is_assigned_to_service' => $isAssigned,
                    'assigned_services_count' => $staff->assignedServices->count(),
                    'assigned_service_ids' => $assignedServiceIds,
                    'required_service_id' => $serviceId,
                    'has_unavailability' => $staff->staffUnavailabilities->isNotEmpty()
                ]);
            }

            // Now filter by service assignment and availability
            $availableStaff = $allStaffWithSchedule
                ->map(function($staff) use ($service, $date, $time, $dayOfWeek) {
                    // Check if staff account is active
                    if (!$staff->is_active) {
                        \Log::info("Staff {$staff->id} ({$staff->name}) is not active - FILTERED OUT");
                        return null;
                    }
                    
                    // Get schedule for this specific day
                    $schedule = $staff->staffSchedules->firstWhere('day_of_week', $dayOfWeek);
                    
                    // Check if staff has a valid schedule for this day
                    if (!$schedule || !$schedule->is_available || !$schedule->start_time || !$schedule->end_time) {
                        \Log::info("Staff {$staff->id} ({$staff->name}) has no valid schedule for {$dayOfWeek} - FILTERED OUT", [
                            'schedule_exists' => $schedule ? true : false,
                            'is_available' => $schedule ? $schedule->is_available : null,
                            'start_time' => $schedule ? $schedule->start_time : null,
                            'end_time' => $schedule ? $schedule->end_time : null
                        ]);
                        return null;
                    }
                    
                    // Check if staff is assigned to this service
                    // Use pluck to get IDs and check if service ID is in the list
                    $assignedServiceIds = $staff->assignedServices->pluck('id')->toArray();
                    if (!in_array($service->id, $assignedServiceIds)) {
                        \Log::info("Staff {$staff->id} ({$staff->name}) is not assigned to service {$service->id} ({$service->name}) - FILTERED OUT", [
                            'assigned_service_ids' => $assignedServiceIds,
                            'required_service_id' => $service->id,
                            'assigned_services_count' => $staff->assignedServices->count()
                        ]);
                        return null;
                    }
                    
                    // If time is provided, check availability at that specific time
                    // Otherwise, check if staff is generally available on that date
                    if ($time) {
                        // Check if time is within schedule hours
                        if (!$schedule->isAvailableAtTime($time)) {
                            \Log::info("Staff {$staff->id} ({$staff->name}) time {$time} is not within schedule hours ({$schedule->start_time} - {$schedule->end_time}) - FILTERED OUT");
                            return null;
                        }
                        
                        // Check for approved leave requests at this specific time
                        // IMPORTANT: Only approved leaves exclude staff from availability
                        // Staff WITHOUT approved leaves should ALWAYS appear in the list
                        // The relationship is already filtered to approved only (line 847-850), so this collection
                        // should only contain approved leaves for this date. If collection is empty, staff has no approved leaves.
                        
                        // Check if staff has any approved leaves loaded
                        // If collection is empty, staff has NO approved leaves and should appear
                        // The relationship is already filtered to only load approved leaves (line 847-850)
                        $hasTimeUnavailability = false;
                        
                        if ($staff->staffUnavailabilities->isNotEmpty()) {
                            // Staff has approved leaves - check if any overlap with selected time
                            $hasTimeUnavailability = $staff->staffUnavailabilities
                                ->filter(function($unavailability) use ($date, $time, $service, $staff) {
                                    // CRITICAL: Only approved leaves exclude staff
                                    // Since relationship is already filtered, all items should be approved
                                    // But we double-check for safety
                                    if ($unavailability->approval_status !== 'approved') {
                                        \Log::warning("Staff {$staff->id} has non-approved leave in filtered collection - IGNORING", [
                                            'approval_status' => $unavailability->approval_status,
                                            'leave_id' => $unavailability->id
                                        ]);
                                        return false; // Ignore pending/rejected leaves
                                    }
                                    
                                    // Must match the date (compare as strings to avoid timezone issues)
                                    $leaveDate = $unavailability->unavailable_date instanceof \Carbon\Carbon 
                                        ? $unavailability->unavailable_date->format('Y-m-d')
                                        : $unavailability->unavailable_date;
                                    $appointmentDate = $date instanceof \Carbon\Carbon 
                                        ? $date->format('Y-m-d')
                                        : $date;
                                    
                                    if ($leaveDate != $appointmentDate) {
                                        return false; // Different date, not relevant
                                    }
                                    
                                    // All-day unavailability (null or empty string) - exclude for entire day
                                    if (empty($unavailability->start_time) || empty($unavailability->end_time)) {
                                        return true;
                                    }
                                    
                                    // Calculate appointment end time (considering service duration)
                                    // Validate and normalize time format (remove seconds if present)
                                    if (empty($time) || !preg_match('/^\d{1,2}:\d{2}/', $time)) {
                                        \Log::warning("Invalid appointment time format: {$time}");
                                        return false;
                                    }
                                    $normalizedTime = preg_replace('/:\d{2}$/', '', $time); // Remove seconds if present
                                    try {
                                        $appointmentStart = \Carbon\Carbon::createFromFormat('H:i', $normalizedTime);
                                        if (!$appointmentStart) {
                                            \Log::warning("Failed to parse appointment time: {$time}");
                                            return false;
                                        }
                                    } catch (\Exception $e) {
                                        \Log::warning("Exception parsing appointment time: {$time} - " . $e->getMessage());
                                        return false;
                                    }
                                    $appointmentEnd = $appointmentStart->copy()->addMinutes($service->duration ?? 60);
                                    
                                    // Parse leave time range - normalize time format
                                    if (empty($unavailability->start_time) || empty($unavailability->end_time)) {
                                        return false; // Skip if times are empty (already handled as all-day above)
                                    }
                                    
                                    $normalizedLeaveStart = preg_replace('/:\d{2}$/', '', $unavailability->start_time);
                                    $normalizedLeaveEnd = preg_replace('/:\d{2}$/', '', $unavailability->end_time);
                                    
                                    // Validate leave time formats
                                    if (!preg_match('/^\d{1,2}:\d{2}/', $normalizedLeaveStart) || !preg_match('/^\d{1,2}:\d{2}/', $normalizedLeaveEnd)) {
                                        \Log::warning("Invalid leave time format", [
                                            'start_time' => $unavailability->start_time,
                                            'end_time' => $unavailability->end_time
                                        ]);
                                        return false;
                                    }
                                    
                                    try {
                                        $leaveStart = \Carbon\Carbon::createFromFormat('H:i', $normalizedLeaveStart);
                                        $leaveEnd = \Carbon\Carbon::createFromFormat('H:i', $normalizedLeaveEnd);
                                        
                                        if (!$leaveStart || !$leaveEnd) {
                                            \Log::warning("Failed to parse leave times", [
                                                'start_time' => $unavailability->start_time,
                                                'end_time' => $unavailability->end_time
                                            ]);
                                            return false;
                                        }
                                    } catch (\Exception $e) {
                                        \Log::warning("Exception parsing leave times: " . $e->getMessage(), [
                                            'start_time' => $unavailability->start_time,
                                            'end_time' => $unavailability->end_time
                                        ]);
                                        return false;
                                    }
                                    
                                    // Check if appointment time overlaps with leave time range
                                    // CRITICAL: Exclude staff if selected time falls within leave_start to leave_end
                                    // Examples:
                                    // - Staff Leave: 7:00 AM - 12:00 PM, Client selects 8:00 AM → 8:00 falls in [7:00-12:00] → Overlap → Staff NOT appear
                                    // - Staff Leave: 7:00 AM - 12:00 PM, Client selects 3:00 PM → 3:00 PM NOT in [7:00-12:00] → No overlap → Staff appear
                                    // Overlap formula: appointment starts before leave ends AND appointment ends after leave starts
                                    $overlaps = $appointmentStart->lt($leaveEnd) && $appointmentEnd->gt($leaveStart);
                                    
                                    if ($overlaps) {
                                        \Log::info("Staff {$staff->id} has overlapping approved leave", [
                                            'leave_time' => "{$unavailability->start_time} - {$unavailability->end_time}",
                                            'appointment_time' => "{$time} - {$appointmentEnd->format('H:i')}",
                                            'overlaps' => true
                                        ]);
                                    }
                                    
                                    return $overlaps;
                                })
                                ->isNotEmpty();
                        } else {
                            // Staff has NO approved leaves - they should appear
                            \Log::info("Staff {$staff->id} ({$staff->name}) has NO approved leaves for {$date} - WILL APPEAR", [
                                'staff_id' => $staff->id,
                                'staff_name' => $staff->name,
                                'date' => $date,
                                'time' => $time
                            ]);
                        }
                        
                        if ($hasTimeUnavailability) {
                            \Log::info("Staff {$staff->id} ({$staff->name}) is not available at {$date} {$time} due to approved leave - FILTERED OUT", [
                                'staff_id' => $staff->id,
                                'staff_name' => $staff->name,
                                'date' => $date,
                                'time' => $time,
                                'has_approved_leave' => true
                            ]);
                            return null;
                        }
                        
                        // Check for conflicting appointments
                        if ($this->hasConflictingAppointment($staff->id, $date, $time, $service->duration)) {
                            \Log::info("Staff {$staff->id} ({$staff->name}) has conflicting appointment at {$date} {$time} - FILTERED OUT");
                            return null;
                        }
                    } else {
                        // No time specified - staff is available if they have a schedule for this day
                        // and are not marked as unavailable for the entire day
                        // Check if there's an all-day approved leave (use loaded relationship)
                        // IMPORTANT: Only approved leaves exclude staff from availability
                        // Staff WITHOUT approved leaves should ALWAYS appear in the list
                        $hasAllDayUnavailability = $staff->staffUnavailabilities
                            ->filter(function($unavailability) use ($date, $staff) {
                                // CRITICAL: Only approved leaves exclude staff
                                // Pending and rejected leaves are ignored
                                // Since relationship is already filtered to approved, this is a safety check
                                if ($unavailability->approval_status !== 'approved') {
                                    \Log::info("Staff {$staff->id} has non-approved all-day leave - IGNORING", [
                                        'approval_status' => $unavailability->approval_status,
                                        'leave_id' => $unavailability->id
                                    ]);
                                    return false; // Ignore pending/rejected leaves
                                }
                                
                                // Must match the date (compare as strings to avoid timezone issues)
                                $leaveDate = $unavailability->unavailable_date instanceof \Carbon\Carbon 
                                    ? $unavailability->unavailable_date->format('Y-m-d')
                                    : $unavailability->unavailable_date;
                                $appointmentDate = $date instanceof \Carbon\Carbon 
                                    ? $date->format('Y-m-d')
                                    : $date;
                                
                                // Must match the date and be all-day (null or empty string)
                                $isAllDay = empty($unavailability->start_time) || empty($unavailability->end_time);
                                $dateMatches = $leaveDate == $appointmentDate;
                                
                                return $dateMatches && $isAllDay;
                            })
                            ->isNotEmpty();
                        
                        if ($hasAllDayUnavailability) {
                            \Log::info("Staff {$staff->id} ({$staff->name}) has all-day approved leave on {$date} - FILTERED OUT", [
                                'staff_id' => $staff->id,
                                'staff_name' => $staff->name,
                                'date' => $date,
                                'has_approved_all_day_leave' => true
                            ]);
                            return null;
                        } else {
                            // Staff has no approved all-day leave - they should appear
                            \Log::info("Staff {$staff->id} ({$staff->name}) has NO approved all-day leave on {$date} - WILL APPEAR", [
                                'staff_id' => $staff->id,
                                'staff_name' => $staff->name,
                                'date' => $date,
                                'total_unavailabilities_loaded' => $staff->staffUnavailabilities->count(),
                                'all_are_approved' => $staff->staffUnavailabilities->every(function($u) {
                                    return $u->approval_status === 'approved';
                                })
                            ]);
                        }
                    }

                    // Final check: Staff should appear if they passed all filters
                    // Log for debugging
                    \Log::info("Staff {$staff->id} ({$staff->name}) PASSED all filters - WILL APPEAR in list", [
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name,
                        'date' => $date,
                        'time' => $time ?? 'not specified',
                        'has_approved_leaves' => $staff->staffUnavailabilities->where('approval_status', 'approved')->isNotEmpty(),
                        'total_unavailabilities_loaded' => $staff->staffUnavailabilities->count(),
                        'approved_count' => $staff->staffUnavailabilities->where('approval_status', 'approved')->count()
                    ]);
                    
                    return [
                        'id' => $staff->id,
                        'first_name' => $staff->first_name,
                        'last_name' => $staff->last_name,
                        'name' => $staff->name,
                        'specialization' => $staff->getPrimarySpecialization()?->specialization ?? 'General',
                        'schedule' => $schedule ? [
                            'start_time' => $schedule->start_time,
                            'end_time' => $schedule->end_time,
                            'formatted_hours' => $schedule->formatted_hours
                        ] : null,
                        'is_available' => true,
                    ];
                })
                ->filter(function($staff) {
                    return $staff !== null; // Remove null entries
                })
                ->values();

            \Log::info("Available staff after filtering", [
                'available_count' => $availableStaff->count(),
                'staff_names' => $availableStaff->pluck('name')->toArray(),
                'staff_ids' => $availableStaff->pluck('id')->toArray(),
                'service_id' => $serviceId,
                'service_name' => $service->name,
                'date' => $date,
                'day_of_week' => $dayOfWeek,
                'time' => $time,
                'initial_query_count' => $allStaffWithSchedule->count(),
                'staff_details' => $availableStaff->map(function($staff) {
                    return [
                        'id' => $staff['id'],
                        'name' => $staff['name'],
                        'specialization' => $staff['specialization'] ?? 'General'
                    ];
                })->toArray()
            ]);

            // Additional debug: Check if there are ANY staff with schedules for this day
            $allStaffWithAnySchedule = User::whereIn('role', ['nurse', 'aesthetician'])
                ->where('is_active', true)
                ->whereHas('staffSchedules', function($query) use ($dayOfWeek) {
                    $query->where('day_of_week', $dayOfWeek)
                          ->where('is_available', true);
                })
                ->count();

            // Check if there are ANY staff assigned to this service
            $allStaffAssignedToService = User::whereIn('role', ['nurse', 'aesthetician'])
                ->where('is_active', true)
                ->whereHas('assignedServices', function($query) use ($serviceId) {
                    $query->where('services.id', $serviceId);
                })
                ->count();

            \Log::info("Debugging information", [
                'total_staff_with_schedule_for_day' => $allStaffWithAnySchedule,
                'total_staff_assigned_to_service' => $allStaffAssignedToService,
                'service_id' => $serviceId,
                'day_of_week' => $dayOfWeek
            ]);

            // Convert collection to array for JSON response
            $staffArray = $availableStaff->map(function($staff) {
                return [
                    'id' => $staff['id'],
                    'first_name' => $staff['first_name'] ?? '',
                    'last_name' => $staff['last_name'] ?? '',
                    'name' => $staff['name'] ?? ($staff['first_name'] ?? '') . ' ' . ($staff['last_name'] ?? ''),
                    'specialization' => $staff['specialization'] ?? 'General',
                    'schedule' => $staff['schedule'] ?? null,
                    'is_available' => $staff['is_available'] ?? true,
                ];
            })->values()->toArray();

            \Log::info("Final staff array for response", [
                'staff_count' => count($staffArray),
                'staff_array' => $staffArray
            ]);

            // Ensure we return both 'staff' and 'available_staff' keys for compatibility
            return response()->json([
                'staff' => $staffArray,
                'available_staff' => $staffArray,
                'date' => $date,
                'day_of_week' => ucfirst($dayOfWeek),
                'required_specialization' => $requiredSpecialization,
                'service_name' => $service->name,
                'service_id' => $service->id,
                'has_available_staff' => count($staffArray) > 0,
                'total_found' => $allStaffWithSchedule->count(),
                'total_available' => count($staffArray),
            ]);
        } catch (\Exception $e) {
            \Log::error('getAvailableStaff error: ' . $e->getMessage(), [
                'date' => $request->date ?? null,
                'service_id' => $request->service_id ?? null,
                'time' => $request->time ?? null,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'error' => 'Failed to load available staff',
                'message' => $e->getMessage(),
                'staff' => [],
                'available_staff' => [],
                'has_available_staff' => false,
                'total_found' => 0,
                'total_available' => 0,
            ], 500);
        }
    }

    private function hasAvailableStaffForSlot(Service $service, string $date, string $time): bool
    {
        $availableStaff = $this->getAvailableStaffForSlot($service, $date, $time);
        $hasAvailable = $availableStaff->isNotEmpty();
        
        \Log::info('hasAvailableStaffForSlot result', [
            'service_id' => $service->id,
            'date' => $date,
            'time' => $time,
            'has_available' => $hasAvailable,
            'available_count' => $availableStaff->count(),
            'available_staff_ids' => $availableStaff->pluck('id')->toArray()
        ]);
        
        return $hasAvailable;
    }

    private function getAvailableStaffForSlot(Service $service, string $date, string $time)
    {
        // Admin uses capitalized format (Monday, Tuesday, etc.)
        $dateCarbon = TimeHelper::parseDate($date);
        $dayOfWeek = $dateCarbon ? $dateCarbon->format('l') : date('l', strtotime($date)); // Returns "Monday", "Tuesday", etc.
        $dayOfWeekLower = strtolower($dayOfWeek);
        
        // Normalize date to Y-m-d format for database comparison
        $normalizedDate = $dateCarbon ? $dateCarbon->format('Y-m-d') : date('Y-m-d', strtotime($date));
        
        \Log::info('getAvailableStaffForSlot called', [
            'service_id' => $service->id,
            'service_name' => $service->name,
            'date' => $date,
            'normalized_date' => $normalizedDate,
            'time' => $time,
            'day_of_week' => $dayOfWeek
        ]);
        
        // Get ONLY staff who meet ALL criteria:
        // 1. Are active users
        // 2. Are assigned to this specific service
        // 3. Have schedules for this specific day
        $staffMembers = User::whereIn('role', ['nurse', 'aesthetician'])
            ->where('is_active', true) // Only active staff
            ->whereHas('assignedServices', function($query) use ($service) {
                // CRITICAL: Only staff assigned to this specific service
                $query->where('services.id', $service->id);
            })
            ->whereHas('staffSchedules', function($query) use ($dayOfWeek, $dayOfWeekLower) {
                // CRITICAL: Only staff with schedule for this specific day
                // Handle both capitalized and lowercase formats
                $query->where(function($q) use ($dayOfWeek, $dayOfWeekLower) {
                    $q->where('day_of_week', $dayOfWeek)
                      ->orWhere('day_of_week', $dayOfWeekLower)
                      ->orWhereRaw('LOWER(day_of_week) = ?', [$dayOfWeekLower]);
                })
                ->where('is_available', true)
                ->whereNotNull('start_time')
                ->whereNotNull('end_time');
            })
            ->with(['staffSpecializations', 'staffSchedules' => function($query) use ($dayOfWeek, $dayOfWeekLower) {
                $query->where(function($q) use ($dayOfWeek, $dayOfWeekLower) {
                    $q->where('day_of_week', $dayOfWeek)
                      ->orWhere('day_of_week', $dayOfWeekLower)
                      ->orWhereRaw('LOWER(day_of_week) = ?', [$dayOfWeekLower]);
                })
                ->where('is_available', true);
            }, 'assignedServices' => function($query) use ($service) {
                // Only load the specific service assignment
                $query->where('services.id', $service->id);
            }, 'staffUnavailabilities' => function($query) use ($normalizedDate) {
                // Load approved leaves for this date to avoid N+1 queries
                // Use normalized date format for comparison
                $query->whereDate('unavailable_date', $normalizedDate)
                      ->where('approval_status', 'approved');
            }])
            ->get();
        
        \Log::info('Initial staff query results', [
            'total_staff_found' => $staffMembers->count(),
            'staff_ids' => $staffMembers->pluck('id')->toArray(),
            'normalized_date' => $normalizedDate
        ]);

        return $staffMembers->filter(function($staff) use ($service, $date, $time) {
            // Check if staff account is active
            if (!$staff->is_active) {
                \Log::info("Staff {$staff->id} ({$staff->name}) is not active");
                return false;
            }
            
            // Verify staff is assigned to this service (double-check)
            if (!$staff->isAssignedToService($service->id)) {
                \Log::info("Staff {$staff->id} ({$staff->name}) is not assigned to service {$service->id}");
                return false;
            }
            
            // Check if staff has a schedule for this day
            $schedule = $staff->staffSchedules->first();
            if (!$schedule || !$schedule->is_available) {
                \Log::info("Staff {$staff->id} ({$staff->name}) has no available schedule for this day");
                return false;
            }
            
            // Check if time is within schedule hours
            // Normalize time format before checking
            $normalizedTimeForCheck = preg_replace('/:\d{2}$/', '', $time); // Remove seconds if present
            try {
                $isTimeAvailable = $schedule->isAvailableAtTime($normalizedTimeForCheck);
                if (!$isTimeAvailable) {
                    \Log::info("Staff {$staff->id} ({$staff->name}) time {$normalizedTimeForCheck} is not within schedule hours", [
                        'schedule_start' => $schedule->start_time,
                        'schedule_end' => $schedule->end_time,
                        'requested_time' => $normalizedTimeForCheck
                    ]);
                    return false;
                }
            } catch (\Exception $e) {
                \Log::warning("Error checking schedule time availability for staff {$staff->id}: " . $e->getMessage(), [
                    'time' => $normalizedTimeForCheck,
                    'schedule_start' => $schedule->start_time,
                    'schedule_end' => $schedule->end_time
                ]);
                return false;
            }
            
            // Check for approved leave requests (considering appointment duration)
            // IMPORTANT: Only approved leaves exclude staff from availability
            // Pending/rejected leaves are NOT checked - staff will still appear in list
            // Validate and normalize time format (remove seconds if present)
            if (empty($time) || !preg_match('/^\d{1,2}:\d{2}/', $time)) {
                \Log::warning("Invalid appointment time format in getAvailableStaffForSlot: {$time}");
                return false;
            }
            $normalizedTime = preg_replace('/:\d{2}$/', '', $time); // Remove seconds if present
            try {
                $appointmentStart = \Carbon\Carbon::createFromFormat('H:i', $normalizedTime);
                if (!$appointmentStart) {
                    \Log::warning("Failed to parse appointment time in getAvailableStaffForSlot: {$time}");
                    return false;
                }
            } catch (\Exception $e) {
                \Log::warning("Exception parsing appointment time in getAvailableStaffForSlot: {$time} - " . $e->getMessage());
                return false;
            }
            $appointmentEnd = $appointmentStart->copy()->addMinutes($service->duration ?? 60);
            
            // Normalize date for comparison
            $dateCarbon = TimeHelper::parseDate($date);
            $normalizedDate = $dateCarbon ? $dateCarbon->format('Y-m-d') : date('Y-m-d', strtotime($date));
            
            // Use loaded relationship if available, otherwise query directly
            // The relationship is already loaded with approved leaves for this date
            if ($staff->relationLoaded('staffUnavailabilities')) {
                // Use loaded relationship - already filtered by date and approval_status
                // Double-check date match in case of format mismatch
                $staffLeaves = $staff->staffUnavailabilities->filter(function($leave) use ($normalizedDate) {
                    $leaveDate = $leave->unavailable_date instanceof \Carbon\Carbon 
                        ? $leave->unavailable_date->format('Y-m-d')
                        : date('Y-m-d', strtotime($leave->unavailable_date));
                    return $leaveDate === $normalizedDate;
                });
            } else {
                // Fallback: query directly if relationship not loaded
                $staffLeaves = $staff->staffUnavailabilities()
                    ->whereDate('unavailable_date', $normalizedDate)
                    ->where('approval_status', 'approved')
                    ->get();
            }
            
            \Log::info("Checking staff availability", [
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'date' => $date,
                'normalized_date' => $normalizedDate,
                'time' => $time,
                'loaded_leaves_count' => $staffLeaves->count(),
                'has_loaded_relationship' => $staff->relationLoaded('staffUnavailabilities'),
                'leaves_details' => $staffLeaves->map(function($leave) {
                    return [
                        'id' => $leave->id,
                        'date' => $leave->unavailable_date instanceof \Carbon\Carbon 
                            ? $leave->unavailable_date->format('Y-m-d')
                            : $leave->unavailable_date,
                        'start_time' => $leave->start_time,
                        'end_time' => $leave->end_time,
                        'approval_status' => $leave->approval_status
                    ];
                })->toArray()
            ]);
            
            $hasOverlappingLeave = $staffLeaves
                ->filter(function($leave) use ($appointmentStart, $appointmentEnd, $staff, $date, $time) {
                    // All-day leave
                    if (empty($leave->start_time) || empty($leave->end_time)) {
                        \Log::info("Staff {$staff->id} has all-day approved leave on {$date}");
                        return true;
                    }
                    
                    // Parse leave time range - normalize time format
                    // Validate leave time formats before parsing
                    if (!preg_match('/^\d{1,2}:\d{2}/', $leave->start_time) || !preg_match('/^\d{1,2}:\d{2}/', $leave->end_time)) {
                        \Log::warning("Invalid leave time format in getAvailableStaffForSlot", [
                            'start_time' => $leave->start_time,
                            'end_time' => $leave->end_time
                        ]);
                        return false;
                    }
                    
                    $normalizedLeaveStart = preg_replace('/:\d{2}$/', '', $leave->start_time);
                    $normalizedLeaveEnd = preg_replace('/:\d{2}$/', '', $leave->end_time);
                    
                    try {
                        $leaveStart = \Carbon\Carbon::createFromFormat('H:i', $normalizedLeaveStart);
                        $leaveEnd = \Carbon\Carbon::createFromFormat('H:i', $normalizedLeaveEnd);
                        
                        if (!$leaveStart || !$leaveEnd) {
                            \Log::warning("Failed to parse leave times in getAvailableStaffForSlot", [
                                'start_time' => $leave->start_time,
                                'end_time' => $leave->end_time
                            ]);
                            return false;
                        }
                    } catch (\Exception $e) {
                        \Log::warning("Exception parsing leave times in getAvailableStaffForSlot: " . $e->getMessage(), [
                            'start_time' => $leave->start_time,
                            'end_time' => $leave->end_time
                        ]);
                        return false;
                    }
                    
                    // Check if appointment time overlaps with leave time range
                    // CRITICAL: Exclude staff if selected time falls within leave_start to leave_end
                    // Examples:
                    // - Staff Leave: 7:00 AM - 12:00 PM, Client selects 8:00 AM → 8:00 falls in [7:00-12:00] → Overlap → Staff NOT appear
                    // - Staff Leave: 7:00 AM - 12:00 PM, Client selects 3:00 PM → 3:00 PM NOT in [7:00-12:00] → No overlap → Staff appear
                    // Overlap formula: appointment starts before leave ends AND appointment ends after leave starts
                    // This ensures any appointment that intersects with leave time is excluded
                    $overlaps = $appointmentStart->lt($leaveEnd) && $appointmentEnd->gt($leaveStart);
                    
                    if ($overlaps) {
                        \Log::info("Staff {$staff->id} has overlapping approved leave", [
                            'leave_time' => "{$leave->start_time} - {$leave->end_time}",
                            'appointment_time' => "{$time} - {$appointmentEnd->format('H:i')}",
                            'overlaps' => true
                        ]);
                    }
                    
                    return $overlaps;
                })
                ->isNotEmpty();
            
            if ($hasOverlappingLeave) {
                \Log::info("Staff {$staff->id} ({$staff->name}) is not available at {$date} {$time} due to approved leave");
                return false;
            }
            
            // Check for conflicting appointments
            try {
                $hasConflict = $this->hasConflictingAppointment($staff->id, $date, $time, $service->duration);
                if ($hasConflict) {
                    \Log::info("Staff {$staff->id} ({$staff->name}) has conflicting appointment at {$date} {$time}");
                    return false;
                }
            } catch (\Exception $e) {
                \Log::warning("Error checking conflicting appointments for staff {$staff->id}: " . $e->getMessage(), [
                    'date' => $date,
                    'time' => $time,
                    'service_duration' => $service->duration
                ]);
                // Don't fail on error - assume no conflict if we can't check
            }
            
            // All checks passed - staff is available
            \Log::info("Staff {$staff->id} ({$staff->name}) PASSED all availability checks", [
                'staff_id' => $staff->id,
                'date' => $date,
                'time' => $time
            ]);
            return true;
        })->values();
        
        \Log::info('Final available staff count in getAvailableStaffForSlot', [
            'total_available' => $staffMembers->count(),
            'available_staff_ids' => $staffMembers->pluck('id')->toArray(),
            'service_id' => $service->id,
            'date' => $date,
            'time' => $time
        ]);
        
        return $staffMembers;
    }
    
    /**
     * Check if staff has a conflicting appointment at the given date and time.
     */
    private function hasConflictingAppointment(int $staffId, string $date, string $time, int $serviceDuration): bool
    {
        // Parse the requested time using TimeHelper
        $requestedStart = TimeHelper::parseTime($time);
        if (!$requestedStart) {
            return false;
        }
        $requestedEnd = $requestedStart->copy()->addMinutes($serviceDuration);
        
        // Get all appointments for this staff on this date
        $appointments = Appointment::where('staff_id', $staffId)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->with('service')
            ->get();
        
        foreach ($appointments as $appointment) {
            try {
                // Parse appointment time using TimeHelper
                $appointmentTime = $appointment->appointment_time;
                $appointmentStart = TimeHelper::parseTime($appointmentTime);
                if (!$appointmentStart) {
                    continue;
                }
                
                $appointmentDuration = $appointment->service->duration ?? 60;
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointmentDuration);
                
                // Check if time ranges overlap
                if ($requestedStart->lt($appointmentEnd) && $requestedEnd->gt($appointmentStart)) {
                    return true; // Conflict found
                }
            } catch (\Exception $e) {
                \Log::warning('Error checking appointment conflict: ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id
                ]);
                continue;
            }
        }
        
        return false; // No conflict
    }

    private function buildStaffQuery(?string $requiredSpecialization)
    {
        $query = User::whereIn('role', ['nurse', 'aesthetician']);

        if ($requiredSpecialization) {
            $query->whereHas('staffSpecializations', function($query) use ($requiredSpecialization) {
                $query->where('specialization', $requiredSpecialization);
            });
        }

        return $query;
    }

    private function resolveRequiredSpecialization(Service $service): ?string
    {
        $mapping = [
            'facial' => 'facial_treatments',
            'consultation' => 'consultation',
            'laser' => 'laser_therapy',
            'peel' => 'facial_treatments',
            'injection' => 'medical_procedures',
            'medical' => 'medical_procedures',
            'body' => 'body_treatments',
            'anti_aging' => 'anti_aging',
            'acne' => 'acne_treatment',
        ];

        if (!$service->category) {
            return null;
        }

        return $mapping[$service->category] ?? $service->category;
    }
}
