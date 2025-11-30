<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\TimeHelper;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\PHPMailerService;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['client', 'service', 'staff']);

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

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('appointment_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhere('walkin_customer_name', 'like', "%{$search}%")
                  ->orWhere('walkin_customer_email', 'like', "%{$search}%")
                  ->orWhere('walkin_customer_phone', 'like', "%{$search}%")
                  ->orWhereHas('service', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $appointments = $query->orderBy('updated_at', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(15);

        // Get statistics for dashboard cards
        $stats = [
            'total' => Appointment::count(),
            'today' => Appointment::whereDate('appointment_date', TimeHelper::todayString())->count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
        ];

        return view('admin.appointments.index', compact('appointments', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = User::where('role', 'client')->orderBy('first_name')->get();
        $services = Service::active()->orderBy('name')->get();
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->orderBy('first_name')->get();

        return view('admin.appointments.create', compact('clients', 'services', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|exists:users,id',
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
            'notes' => 'nullable|string|max:1000',
            'is_walkin' => 'nullable|boolean',
            'walkin_customer_name' => 'required_if:is_walkin,1|nullable|string|max:255',
            'walkin_customer_email' => 'nullable|email|max:255',
            'walkin_customer_phone' => 'required_if:is_walkin,1|nullable|string|max:20',
        ]);

        // Validate that either client_id or walk-in info is provided
        if (!$request->client_id && !$request->is_walkin) {
            return back()->withErrors(['client_id' => 'Please select a client or enter walk-in customer information.'])->withInput();
        }

        // For walk-in appointments, ensure date is today only
        if ($request->is_walkin) {
            $today = TimeHelper::todayString();
            if ($request->appointment_date !== $today) {
                return back()->withErrors(['appointment_date' => 'Walk-in appointments must be scheduled for today only.'])->withInput();
            }
        }

        $service = Service::findOrFail($request->service_id);

        $appointment = Appointment::create([
            'client_id' => $request->client_id,
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'total_amount' => $service->price,
            'notes' => $request->notes,
            'status' => 'confirmed', // Admin created appointments are auto-confirmed
            'walkin_customer_name' => $request->walkin_customer_name,
            'walkin_customer_email' => $request->walkin_customer_email,
            'walkin_customer_phone' => $request->walkin_customer_phone,
        ]);

        // Attach service to the appointment (for backward compatibility with multiple services support)
        $appointment->services()->attach($service->id, ['price' => $service->price]);

        // Create chat conversation if appointment is auto-confirmed
        if ($appointment->status === 'confirmed') {
            try {
                \App\Models\ChatConversation::getOrCreateForAppointment($appointment->id);
            } catch (\Exception $e) {
                // Log error but don't fail the appointment creation
                \Log::warning('Failed to create chat conversation: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.appointments.index')
                        ->with('success', 'Appointment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['client', 'service', 'staff']);
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $clients = User::where('role', 'client')->orderBy('first_name')->get();
        $services = Service::active()->orderBy('name')->get();
        
        // Get day of week for the appointment date
        $appointmentDate = $appointment->appointment_date instanceof Carbon 
            ? $appointment->appointment_date 
            : Carbon::parse($appointment->appointment_date);
        $dayOfWeek = $appointmentDate->format('l'); // Returns "Monday", "Tuesday", etc.
        $dayOfWeekLower = strtolower($dayOfWeek);
        
        // Filter staff to only show those who have a schedule for this day
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])
            ->where('is_active', true)
            ->whereHas('staffSchedules', function($query) use ($dayOfWeek, $dayOfWeekLower) {
                // Match exact day format (capitalized) or lowercase
                $query->where(function($q) use ($dayOfWeek, $dayOfWeekLower) {
                    $q->where('day_of_week', $dayOfWeek)
                      ->orWhere('day_of_week', $dayOfWeekLower)
                      ->orWhereRaw('LOWER(day_of_week) = ?', [$dayOfWeekLower]);
                })
                ->where('is_available', true)
                ->whereNotNull('start_time')
                ->whereNotNull('end_time');
            })
            ->orderBy('first_name')
            ->get();

        return view('admin.appointments.edit', compact('appointment', 'clients', 'services', 'staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'client_id' => 'nullable|exists:users,id',
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
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'notes' => 'nullable|string|max:1000',
            'staff_notes' => 'nullable|string|max:1000',
            'is_walkin' => 'nullable|boolean',
            'walkin_customer_name' => 'required_if:is_walkin,1|nullable|string|max:255',
            'walkin_customer_email' => 'nullable|email|max:255',
            'walkin_customer_phone' => 'required_if:is_walkin,1|nullable|string|max:20',
        ]);

        // Determine if this is a walk-in appointment (preserve original type)
        $isWalkin = $request->has('is_walkin') && ($request->is_walkin == '1' || $request->is_walkin === 1 || $request->is_walkin === true);
        
        // Preserve the original appointment type - cannot change from registered to walk-in or vice versa
        $originalIsWalkin = $appointment->isWalkIn();
        
        if ($isWalkin !== $originalIsWalkin) {
            return back()->withErrors(['is_walkin' => 'Cannot change customer type. Please create a new appointment instead.'])->withInput();
        }

        $service = Service::findOrFail($request->service_id);

        // Check if staff is being changed and if original staff has approved leave
        $originalStaffId = $appointment->staff_id;
        $newStaffId = $request->staff_id;
        $staffChanged = $originalStaffId != $newStaffId && $originalStaffId && $newStaffId;
        
        $originalStaff = null;
        $newStaff = null;
        $hasApprovedLeave = false;
        
        if ($staffChanged) {
            $originalStaff = User::find($originalStaffId);
            $newStaff = User::find($newStaffId);
            
            // Check if original staff has an approved leave for this appointment date/time
            if ($originalStaff) {
                $hasApprovedLeave = \App\Models\StaffUnavailability::where('staff_id', $originalStaffId)
                    ->where('unavailable_date', $request->appointment_date)
                    ->where('approval_status', 'approved')
                    ->where(function($query) use ($request) {
                        // Check if leave is all-day or overlaps with appointment time
                        $query->where(function($q) {
                            $q->whereNull('start_time')
                              ->whereNull('end_time');
                        })->orWhere(function($q) use ($request) {
                            $q->whereNotNull('start_time')
                              ->whereNotNull('end_time')
                              ->where('start_time', '<=', $request->appointment_time)
                              ->where('end_time', '>=', $request->appointment_time);
                        });
                    })
                    ->exists();
            }
        }

        // Prepare update data - preserve client/walk-in info from original appointment
        $updateData = [
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => $request->status,
            'total_amount' => $service->price,
            'notes' => $request->notes,
            'staff_notes' => $request->staff_notes,
        ];

        // Preserve original client/walk-in information (from hidden fields)
        if ($isWalkin) {
            $updateData['client_id'] = null;
            $updateData['walkin_customer_name'] = $request->walkin_customer_name;
            $updateData['walkin_customer_email'] = $request->walkin_customer_email;
            $updateData['walkin_customer_phone'] = $request->walkin_customer_phone;
        } else {
            $updateData['client_id'] = $request->client_id;
            $updateData['walkin_customer_name'] = null;
            $updateData['walkin_customer_email'] = null;
            $updateData['walkin_customer_phone'] = null;
        }

        // Update appointment services (sync with new service)
        $appointment->services()->sync([$service->id => ['price' => $service->price]]);

        // Set timestamps based on status changes
        if ($request->status === 'confirmed' && $appointment->status !== 'confirmed') {
            $updateData['confirmed_at'] = TimeHelper::now();
            
            // Create chat conversation when appointment is confirmed
            try {
                \App\Models\ChatConversation::getOrCreateForAppointment($appointment->id);
            } catch (\Exception $e) {
                // Log error but don't fail the appointment update
                \Log::warning('Failed to create chat conversation: ' . $e->getMessage());
            }
        }

        if ($request->status === 'completed' && $appointment->status !== 'completed') {
            $updateData['completed_at'] = TimeHelper::now();
        }

        if ($request->status === 'cancelled' && $appointment->status !== 'cancelled') {
            $updateData['cancelled_at'] = TimeHelper::now();
            $updateData['cancellation_reason'] = $request->cancellation_reason;
        }

        $appointment->update($updateData);

        // If staff was changed and original staff has approved leave, send email to client
        if ($staffChanged && $hasApprovedLeave && $originalStaff && $newStaff) {
            try {
                $this->sendStaffReplacementEmail($appointment, $originalStaff, $newStaff);
            } catch (\Exception $e) {
                Log::error('Failed to send staff replacement email', [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the update if email fails
            }
        }

        return redirect()->route('admin.appointments.index')
                        ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Send email to client about staff replacement due to leave.
     */
    private function sendStaffReplacementEmail(Appointment $appointment, User $originalStaff, User $newStaff): void
    {
        try {
            // Get client email (registered client or walk-in)
            $clientEmail = $appointment->customer_email;
            $clientName = $appointment->customer_name;
            
            if (!$clientEmail) {
                Log::warning('Cannot send staff replacement email: no email found', [
                    'appointment_id' => $appointment->id,
                    'client_id' => $appointment->client_id,
                    'is_walkin' => $appointment->isWalkIn(),
                ]);
                return;
            }

            $phpMailerService = new PHPMailerService();

            $originalStaffName = $originalStaff->first_name ? 
                ($originalStaff->first_name . ' ' . $originalStaff->last_name) : 
                $originalStaff->name;

            $newStaffName = $newStaff->first_name ? 
                ($newStaff->first_name . ' ' . $newStaff->last_name) : 
                $newStaff->name;

            $appointmentDate = $appointment->appointment_date instanceof Carbon 
                ? $appointment->appointment_date->format('F d, Y')
                : Carbon::parse($appointment->appointment_date)->format('F d, Y');

            $appointmentTime = $appointment->appointment_time;
            $timeCarbon = TimeHelper::parseTime($appointmentTime);
            $formattedTime = $timeCarbon ? $timeCarbon->format('g:i A') : $appointmentTime;

            $serviceName = $appointment->service ? $appointment->service->name : 'Service';

            // Generate unique token for this appointment action
            $token = bin2hex(random_bytes(32));
            
            // Store token in appointment notes
            $appointment->update([
                'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                          "Staff replacement token: {$token}",
            ]);

            $acceptUrl = route('appointments.emergency-staff.accept', [
                'appointment' => $appointment->id,
                'token' => $token,
            ]);
            
            $cancelUrl = route('appointments.emergency-staff.cancel', [
                'appointment' => $appointment->id,
                'token' => $token,
            ]);

            $isWalkIn = $appointment->isWalkIn();

            $emailSent = $phpMailerService->sendEmergencyStaffReplacementEmail(
                $clientEmail,
                $clientName,
                $appointment->appointment_number ?? 'N/A',
                $serviceName,
                $appointmentDate,
                $formattedTime,
                $originalStaffName,
                $newStaffName,
                $acceptUrl,
                $cancelUrl,
                $isWalkIn
            );

            if ($emailSent) {
                Log::info('Staff replacement email sent successfully', [
                    'appointment_id' => $appointment->id,
                    'client_email' => $clientEmail,
                    'is_walkin' => $isWalkIn,
                    'original_staff_id' => $originalStaff->id,
                    'new_staff_id' => $newStaff->id,
                ]);
            } else {
                Log::warning('Failed to send staff replacement email', [
                    'appointment_id' => $appointment->id,
                    'client_email' => $clientEmail,
                    'is_walkin' => $isWalkIn,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Error sending staff replacement email', [
                'appointment_id' => $appointment->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')
                        ->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Store or update admin reply to client feedback.
     */
    public function replyToFeedback(Request $request, Appointment $appointment)
    {
        $request->validate([
            'admin_feedback_reply' => 'nullable|string|max:2000',
        ]);

        $appointment->update([
            'admin_feedback_reply' => $request->admin_feedback_reply,
            'admin_feedback_replied_at' => $request->admin_feedback_reply ? TimeHelper::now() : null,
        ]);

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', 'Your reply to the client feedback has been saved.');
    }

    /**
     * Update appointment status.
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show',
            'cancellation_reason' => 'required_if:status,cancelled|string|max:255',
        ]);

        $updateData = ['status' => $request->status];

        // Set appropriate timestamps
        switch ($request->status) {
            case 'confirmed':
                $updateData['confirmed_at'] = TimeHelper::now();
                
                // Create chat conversation when appointment is confirmed
                try {
                    \App\Models\ChatConversation::getOrCreateForAppointment($appointment->id);
                } catch (\Exception $e) {
                    // Log error but don't fail the status update
                    \Log::warning('Failed to create chat conversation: ' . $e->getMessage());
                }
                break;
            case 'completed':
                $updateData['completed_at'] = TimeHelper::now();
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = TimeHelper::now();
                $updateData['cancellation_reason'] = $request->cancellation_reason;
                break;
        }

        $appointment->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated successfully.',
        ]);
    }

    /**
     * Get appointment calendar data.
     */
    public function calendar(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $appointments = Appointment::with(['client', 'service', 'staff'])
            ->whereBetween('appointment_date', [$start, $end])
            ->get();

        $events = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'title' => $appointment->service->name . ' - ' . $appointment->customer_name,
                'start' => $appointment->appointment_date->format('Y-m-d') . 'T' . $appointment->appointment_time,
                'backgroundColor' => $this->getStatusColor($appointment->status),
                'borderColor' => $this->getStatusColor($appointment->status),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'appointment_number' => $appointment->appointment_number,
                    'client' => $appointment->customer_name,
                    'service' => $appointment->service->name,
                    'staff' => $appointment->staff ? $appointment->staff->name : 'Unassigned',
                    'status' => $appointment->status,
                    'total_amount' => $appointment->formatted_total,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Get status color for calendar events.
     */
    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => '#f59e0b',
            'confirmed' => '#3b82f6',
            'in_progress' => '#8b5cf6',
            'completed' => '#10b981',
            'cancelled' => '#ef4444',
            'no_show' => '#6b7280',
            default => '#6b7280',
        };
    }
}
