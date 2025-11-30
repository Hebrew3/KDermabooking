<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffSchedule;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StaffScheduleController extends Controller
{
    /**
     * Display staff schedules.
     */
    public function index()
    {
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])
            ->with(['staffSchedules' => function($query) {
                $query->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
            }])
            ->orderBy('first_name')
            ->get();

        // Use capitalized day names to match the enum in the staff_schedules table
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('admin.staff-schedule.index', compact('staff', 'daysOfWeek'));
    }

    /**
     * Show the form for editing staff schedules.
     */
    public function edit(User $staff)
    {
        $staff->load('staffSchedules');
        // Use capitalized day names to match the enum in the staff_schedules table
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        // Ensure all days have a schedule record (idempotent)
        foreach ($daysOfWeek as $day) {
            $existing = $staff->staffSchedules->where('day_of_week', $day)->first();

            StaffSchedule::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'day_of_week' => $day,
                ],
                [
                    'is_available' => $existing ? (bool) $existing->is_available : false,
                ]
            );
        }

        // Reload with complete schedules collection
        $staff->load('staffSchedules');

        return view('admin.staff-schedule.edit', compact('staff', 'daysOfWeek'));
    }

    /**
     * Update staff schedules.
     */
    public function update(Request $request, User $staff)
    {
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        // Build validation rules for each day
        $rules = [
            'schedules' => 'required|array',
        ];
        
        foreach ($daysOfWeek as $day) {
            $rules["schedules.{$day}.day_of_week"] = "required|in:{$day}";
            $rules["schedules.{$day}.is_available"] = 'nullable|boolean';
            $rules["schedules.{$day}.start_time"] = 'nullable|date_format:H:i';
            $rules["schedules.{$day}.end_time"] = 'nullable|date_format:H:i';
            $rules["schedules.{$day}.notes"] = 'nullable|string|max:500';
        }
        
        // Custom validation for end_time after start_time
        $validator = Validator::make($request->all(), $rules);
        
        // Add custom validation for required times when available and end_time after start_time
        $validator->after(function ($validator) use ($request, $daysOfWeek) {
            foreach ($daysOfWeek as $day) {
                $startTime = $request->input("schedules.{$day}.start_time");
                $endTime = $request->input("schedules.{$day}.end_time");
                $isAvailable = $request->input("schedules.{$day}.is_available");
                
                // Validate that times are required when day is available
                if ($isAvailable) {
                    if (empty($startTime) || trim($startTime) === '') {
                        $validator->errors()->add(
                            "schedules.{$day}.start_time",
                            "The start time is required when {$day} is marked as available."
                        );
                    }
                    
                    if (empty($endTime) || trim($endTime) === '') {
                        $validator->errors()->add(
                            "schedules.{$day}.end_time",
                            "The end time is required when {$day} is marked as available."
                        );
                    }
                }
                
                // Validate that end_time is after start_time when both are provided
                if ($isAvailable && $startTime && $endTime) {
                    try {
                        $start = Carbon::createFromFormat('H:i', $startTime);
                        $end = Carbon::createFromFormat('H:i', $endTime);
                        
                        if ($end->lte($start)) {
                            $validator->errors()->add(
                                "schedules.{$day}.end_time",
                                "The end time must be after the start time."
                            );
                        }
                    } catch (\Exception $e) {
                        // If time format is invalid, the date_format rule will catch it
                    }
                }
            }
        });
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Normalize time format before saving
        foreach ($request->schedules as $day => $scheduleData) {
            $isAvailable = !empty($scheduleData['is_available']);
            
            // Normalize time format to H:i (remove seconds if present)
            $startTime = null;
            $endTime = null;
            
            if ($isAvailable && !empty($scheduleData['start_time']) && trim($scheduleData['start_time']) !== '') {
                $startTime = $this->normalizeTimeFormat($scheduleData['start_time']);
            }
            
            if ($isAvailable && !empty($scheduleData['end_time']) && trim($scheduleData['end_time']) !== '') {
                $endTime = $this->normalizeTimeFormat($scheduleData['end_time']);
            }

            StaffSchedule::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'day_of_week' => $scheduleData['day_of_week'] ?? $day,
                ],
                [
                    'is_available' => $isAvailable,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'notes' => $scheduleData['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('admin.staff-schedule.index')
                        ->with('success', "Schedule updated for {$staff->full_name}.");
    }
    
    /**
     * Normalize time format to H:i.
     */
    private function normalizeTimeFormat(string $time): string
    {
        $time = trim($time);
        
        // If time is in H:i:s format, convert to H:i
        if (strlen($time) > 5) {
            return substr($time, 0, 5);
        }
        
        // Ensure it's in H:i format (HH:MM)
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $time, $matches)) {
            return sprintf('%02d:%02d', (int)$matches[1], (int)$matches[2]);
        }
        
        return $time;
    }

    /**
     * Bulk update schedules for weekend coverage.
     */
    public function updateWeekendCoverage(Request $request)
    {
        $request->validate([
            'weekend_start_time' => 'required|date_format:H:i',
            'weekend_end_time' => 'required|date_format:H:i|after:weekend_start_time',
        ]);

        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->get();

        foreach ($staff as $staffMember) {
            // Update Saturday and Sunday schedules (capitalized to match enum)
            foreach (['Saturday', 'Sunday'] as $day) {
                StaffSchedule::updateOrCreate(
                    [
                        'staff_id' => $staffMember->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'is_available' => true,
                        'start_time' => $request->weekend_start_time,
                        'end_time' => $request->weekend_end_time,
                        'notes' => 'Weekend coverage - full team scheduled',
                    ]
                );
            }
        }

        return redirect()->route('admin.staff-schedule.index')
                        ->with('success', 'Weekend coverage updated for all staff members.');
    }

    /**
     * Get staff availability for a specific date.
     */
    public function getAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'specialization' => 'nullable|string',
        ]);

        $date = $request->date;
        $dayOfWeek = date('l', strtotime($date));
        $normalizedDay = strtolower($dayOfWeek);
        $specialization = $request->specialization;

        $query = User::whereIn('role', ['nurse', 'aesthetician'])
            ->whereHas('staffSchedules', function($q) use ($normalizedDay) {
                $q->whereRaw('LOWER(day_of_week) = ?', [$normalizedDay])
                  ->where('is_available', true)
                  ->whereNotNull('start_time')
                  ->whereNotNull('end_time');
            })
            ->whereDoesntHave('staffUnavailabilities', function($q) use ($date) {
                $q->where('unavailable_date', $date);
            });

        if ($specialization) {
            $query->whereHas('staffSpecializations', function($q) use ($specialization) {
                $q->where('specialization', $specialization);
            });
        }

        $availableStaff = $query->with(['staffSchedules' => function($q) use ($normalizedDay) {
            $q->whereRaw('LOWER(day_of_week) = ?', [$normalizedDay]);
        }, 'staffSpecializations'])->get();

        return response()->json([
            'available_staff' => $availableStaff->map(function($staff) {
                $schedule = $staff->staffSchedules->first();
                return [
                    'id' => $staff->id,
                    'name' => $staff->full_name,
                    'working_hours' => $schedule ? $schedule->formatted_hours : 'N/A',
                    'specializations' => $staff->getSpecializations(),
                    'primary_specialization' => $staff->getPrimarySpecialization()?->specialization,
                ];
            }),
            'date' => $date,
            'day_of_week' => $dayOfWeek,
        ]);
    }

    /**
     * Display staff time slot availability.
     */
    public function timeSlotAvailability(Request $request)
    {
        $selectedDate = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedStaffId = $request->get('staff_id');
        
        $date = Carbon::parse($selectedDate);
        $dayOfWeek = $date->format('l'); // Monday, Tuesday, etc.
        
        // Get all staff
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])
            ->orderBy('first_name')
            ->get();
        
        // Get time slot availability for selected date
        $timeSlotData = [];
        
        foreach ($staff as $staffMember) {
            // Filter by selected staff if provided
            if ($selectedStaffId && $staffMember->id != $selectedStaffId) {
                continue;
            }
            
            // Get staff schedule for the day
            $schedule = StaffSchedule::where('staff_id', $staffMember->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();
            
            if (!$schedule || !$schedule->is_available || !$schedule->start_time || !$schedule->end_time) {
                continue; // Skip if staff is not available on this day
            }
            
            // Generate time slots (30-minute intervals)
            $timeSlots = $this->generateTimeSlots($schedule->start_time, $schedule->end_time);
            
            // Get appointments for this staff on this date
            // Occupied slots: pending, confirmed, in_progress
            // Available slots: completed, cancelled, no_show, or no appointment
            $appointments = Appointment::where('staff_id', $staffMember->id)
                ->whereDate('appointment_date', $date->format('Y-m-d'))
                ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
                ->with('service')
                ->get();
            
            // Mark slots as occupied based on appointments
            $slotsWithStatus = [];
            foreach ($timeSlots as $slot) {
                $isOccupied = false;
                $appointment = null;
                
                // Parse slot time (already in H:i format from generateTimeSlots)
                $slotStart = Carbon::createFromFormat('H:i', $slot);
                $slotEnd = $slotStart->copy()->addMinutes(30);
                
                foreach ($appointments as $apt) {
                    // Normalize appointment time format
                    $aptTimeStr = strlen($apt->appointment_time) > 5 
                        ? substr($apt->appointment_time, 0, 5) 
                        : $apt->appointment_time;
                    
                    try {
                        $aptTime = Carbon::createFromFormat('H:i', $aptTimeStr);
                    } catch (\Exception $e) {
                        // Skip this appointment if time format is invalid
                        continue;
                    }
                    
                    // Get service duration (default to 60 minutes if not available)
                    $serviceDuration = $apt->service ? ($apt->service->duration ?? 60) : 60;
                    $aptEnd = $aptTime->copy()->addMinutes($serviceDuration);
                    
                    // Check if appointment overlaps with this time slot
                    // Occupied if: appointment starts before slot ends AND appointment ends after slot starts
                    if ($aptTime->lt($slotEnd) && $aptEnd->gt($slotStart)) {
                        $isOccupied = true;
                        $appointment = $apt;
                        break; // One appointment per slot is enough
                    }
                }
                
                $slotsWithStatus[] = [
                    'time' => $slot,
                    'status' => $isOccupied ? 'occupied' : 'available',
                    'appointment' => $appointment,
                ];
            }
            
            $timeSlotData[] = [
                'staff' => $staffMember,
                'schedule' => $schedule,
                'slots' => $slotsWithStatus,
            ];
        }
        
        return view('admin.staff-schedule.time-slot-availability', compact(
            'staff',
            'timeSlotData',
            'selectedDate',
            'selectedStaffId',
            'date',
            'dayOfWeek'
        ));
    }
    
    /**
     * Generate time slots between start and end time (30-minute intervals).
     */
    private function generateTimeSlots(string $startTime, string $endTime): array
    {
        $slots = [];
        
        // Normalize time format - handle both H:i and H:i:s
        $startTimeNormalized = strlen($startTime) > 5 ? substr($startTime, 0, 5) : $startTime;
        $endTimeNormalized = strlen($endTime) > 5 ? substr($endTime, 0, 5) : $endTime;
        
        try {
            $start = Carbon::createFromFormat('H:i', $startTimeNormalized);
            $end = Carbon::createFromFormat('H:i', $endTimeNormalized);
        } catch (\Exception $e) {
            // Fallback: try parsing as H:i:s if H:i fails
            try {
                $start = Carbon::createFromFormat('H:i:s', $startTime);
                $end = Carbon::createFromFormat('H:i:s', $endTime);
            } catch (\Exception $e2) {
                // If both fail, return empty array
                \Log::error('Failed to parse time slots', [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'error' => $e2->getMessage()
                ]);
                return [];
            }
        }
        
        $current = $start->copy();
        
        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes(30);
        }
        
        return $slots;
    }
}
