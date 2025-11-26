<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffSchedule;
use App\Models\StaffUnavailability;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display the staff schedule management page.
     */
    public function index(Request $request)
    {
        $staff = $request->user();

        // Get the week to display (default to current week)
        $weekStart = $request->get('week') ? 
            Carbon::parse($request->get('week'))->startOfWeek() : 
            Carbon::now()->startOfWeek();

        // Get current week's schedule
        $weeklySchedule = $this->getWeeklySchedule($staff);
        
        // Debug: Log the weekly schedule being passed to view
        \Log::info('Weekly schedule for calendar:', [
            'staff_id' => $staff->id,
            'schedule' => $weeklySchedule
        ]);

        // Get appointments for this week
        $weeklyAppointments = $this->getWeeklyAppointments($staff, $weekStart);

        // Get unavailabilities for this week
        $weeklyUnavailabilities = $this->getWeeklyUnavailabilities($staff, $weekStart);

        // Get unavailabilities for the current month (for the calendar)
        $monthlyUnavailabilities = $this->getMonthlyUnavailabilities($staff);

        // Get monthly appointments for calendar
        $monthlyAppointments = $this->getMonthlyAppointments($staff);

        return view('staff.schedule', compact(
            'weeklySchedule', 
            'weeklyAppointments',
            'weeklyUnavailabilities',
            'monthlyUnavailabilities',
            'monthlyAppointments',
            'staff',
            'weekStart'
        ));
    }

    /**
     * Update staff schedule.
     */
    public function update(Request $request)
    {
        abort(403, 'Your weekly schedule is managed by the clinic admin.');
    }

    /**
     * Add unavailability.
     */
    public function addUnavailability(Request $request)
    {
        abort(403, 'Unavailability is managed by the clinic admin.');
    }

    /**
     * Remove unavailability.
     */
    public function removeUnavailability(Request $request, $id)
    {
        abort(403, 'Unavailability is managed by the clinic admin.');
    }

    /**
     * Get weekly schedule.
     */
    private function getWeeklySchedule($staff)
    {
        $schedules = StaffSchedule::where('staff_id', $staff->id)->get()->keyBy('day_of_week');
        
        // Debug: Log what we found in the database
        \Log::info('Staff schedules from DB:', [
            'staff_id' => $staff->id,
            'schedules_count' => $schedules->count(),
            'schedules' => $schedules->toArray()
        ]);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weeklySchedule = [];

        foreach ($days as $day) {
            $existingSchedule = $schedules->get($day);
            if ($existingSchedule) {
                $weeklySchedule[$day] = [
                    'day_of_week' => $existingSchedule->day_of_week,
                    'is_available' => $existingSchedule->is_available,
                    'start_time' => $existingSchedule->start_time,
                    'end_time' => $existingSchedule->end_time,
                ];
            } else {
                $weeklySchedule[$day] = [
                    'day_of_week' => $day,
                    'is_available' => false,
                    'start_time' => null,
                    'end_time' => null,
                ];
            }
        }

        return $weeklySchedule;
    }

    /**
     * Get weekly schedule with actual dates.
     */
    private function getWeeklyScheduleWithDates($staff, $weekStart)
    {
        $schedules = StaffSchedule::where('staff_id', $staff->id)->get()->keyBy('day_of_week');
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weeklySchedule = [];

        foreach ($days as $index => $day) {
            $date = $weekStart->copy()->addDays($index);
            $existingSchedule = $schedules->get($day);
            
            if ($existingSchedule) {
                $scheduleData = $existingSchedule->toArray();
            } else {
                $scheduleData = [
                    'day_of_week' => $day,
                    'is_available' => false,
                    'start_time' => null,
                    'end_time' => null,
                ];
            }
            
            $scheduleData['date'] = $date;
            $scheduleData['formatted_date'] = $date->format('M d');
            $scheduleData['is_today'] = $date->isToday();
            $scheduleData['is_past'] = $date->isPast() && !$date->isToday();
            
            $weeklySchedule[$day] = $scheduleData;
        }

        return $weeklySchedule;
    }

    /**
     * Get appointments for the week.
     */
    private function getWeeklyAppointments($staff, $weekStart)
    {
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        return \App\Models\Appointment::where('staff_id', $staff->id)
            ->whereBetween('appointment_date', [$weekStart, $weekEnd])
            ->with(['client', 'service'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->groupBy(function($appointment) {
                return Carbon::parse($appointment->appointment_date)->format('l'); // Day name
            });
    }

    /**
     * Get unavailabilities for the week.
     */
    private function getWeeklyUnavailabilities($staff, $weekStart)
    {
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        return StaffUnavailability::where('staff_id', $staff->id)
            ->where('approval_status', 'approved')
            ->whereBetween('unavailable_date', [$weekStart, $weekEnd])
            ->orderBy('unavailable_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function($unavailability) {
                return Carbon::parse($unavailability->unavailable_date)->format('l'); // Day name
            });
    }

    /**
     * Get appointments for the month.
     */
    private function getMonthlyAppointments($staff)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        return \App\Models\Appointment::where('staff_id', $staff->id)
            ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
            ->with(['client', 'service'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                    'client_name' => $appointment->client->name,
                    'service_name' => $appointment->service->name,
                    'status' => $appointment->status,
                ];
            })
            ->groupBy('appointment_date');
    }

    /**
     * Get monthly unavailabilities.
     */
    private function getMonthlyUnavailabilities($staff)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $unavailabilities = StaffUnavailability::where('staff_id', $staff->id)
            ->where('approval_status', 'approved')
            ->whereBetween('unavailable_date', [$startOfMonth, $endOfMonth])
            ->orderBy('unavailable_date')
            ->get();

        // Debug: Log what unavailabilities are being retrieved
        \Log::info('Monthly unavailabilities retrieved:', [
            'staff_id' => $staff->id,
            'date_range' => [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')],
            'count' => $unavailabilities->count(),
            'unavailabilities' => $unavailabilities->toArray()
        ]);

        return $unavailabilities;
    }

    /**
     * Test staff availability integration (for debugging).
     */
    public function testAvailability(Request $request)
    {
        $staff = $request->user();
        $date = $request->get('date', now()->format('Y-m-d'));
        $time = $request->get('time', '10:00');

        $isAvailable = $staff->isAvailableAt($date, $time);
        $dayOfWeek = strtolower(date('l', strtotime($date)));
        
        // Get schedule for the day
        $schedule = $staff->staffSchedules()->forDay($dayOfWeek)->available()->first();
        
        // Get unavailabilities for the date
        $unavailabilities = $staff->staffUnavailabilities()->forDate($date)->get();

        return response()->json([
            'staff_id' => $staff->id,
            'staff_name' => $staff->name,
            'date' => $date,
            'time' => $time,
            'day_of_week' => ucfirst($dayOfWeek),
            'is_available' => $isAvailable,
            'schedule' => $schedule ? [
                'day' => $schedule->day_of_week,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'is_available' => $schedule->is_available,
                'formatted_hours' => $schedule->formatted_hours
            ] : null,
            'unavailabilities' => $unavailabilities->map(function($unavailability) {
                return [
                    'id' => $unavailability->id,
                    'date' => $unavailability->unavailable_date,
                    'start_time' => $unavailability->start_time,
                    'end_time' => $unavailability->end_time,
                    'reason' => $unavailability->reason,
                    'formatted_reason' => $unavailability->formatted_reason,
                    'affects_time' => $unavailability->affectsTime($time)
                ];
            })
        ]);
    }
}
