<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffSchedule;
use App\Models\User;
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
        
        // Add custom validation for end_time after start_time
        $validator->after(function ($validator) use ($request, $daysOfWeek) {
            foreach ($daysOfWeek as $day) {
                $startTime = $request->input("schedules.{$day}.start_time");
                $endTime = $request->input("schedules.{$day}.end_time");
                $isAvailable = $request->input("schedules.{$day}.is_available");
                
                // Only validate if both times are provided and day is available
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
}
