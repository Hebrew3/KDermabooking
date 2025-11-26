<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use App\Models\StaffSchedule;
use App\Models\StaffUnavailability;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the staff dashboard.
     */
    public function index(Request $request)
    {
        $staff = $request->user();

        // Get today's date
        $today = Carbon::today();

        // Get dashboard statistics
        $stats = $this->getDashboardStats($staff, $today);

        // Get today's appointments
        $todayAppointments = $this->getTodayAppointments($staff, $today);

        // Get upcoming appointments
        $upcomingAppointments = $this->getUpcomingAppointments($staff, $today);

        // Get recent appointments
        $recentAppointments = $this->getRecentAppointments($staff);

        // Get staff schedule for the week
        $weeklySchedule = $this->getWeeklyScheduleWithDates($staff, Carbon::now()->startOfWeek());

        // Get assigned services statistics
        $assignedServicesStats = $this->getAssignedServicesStats($staff);

        return view('staff.dashboard', compact(
            'stats',
            'todayAppointments',
            'upcomingAppointments',
            'recentAppointments',
            'weeklySchedule',
            'assignedServicesStats',
            'staff'
        ));
    }

    /**
     * Get dashboard statistics.
     */
    private function getDashboardStats($staff, $today)
    {
        return [
            'total_appointments' => Appointment::where('staff_id', $staff->id)->count(),
            'completed_appointments' => Appointment::where('staff_id', $staff->id)
                ->where('status', 'completed')->count(),
            'today_appointments' => Appointment::where('staff_id', $staff->id)
                ->whereDate('appointment_date', $today)->count(),
            'upcoming_appointments' => Appointment::where('staff_id', $staff->id)
                ->where('appointment_date', '>', $today)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'total_clients' => User::where('role', 'client')->count(),
            'pending_appointments' => Appointment::where('staff_id', $staff->id)
                ->where('status', 'pending')->count(),
        ];
    }

    /**
     * Get today's appointments.
     */
    private function getTodayAppointments($staff, $today)
    {
        return Appointment::where('staff_id', $staff->id)
            ->whereDate('appointment_date', $today)
            ->with(['client', 'service'])
            ->orderBy('appointment_time')
            ->get();
    }

    /**
     * Get upcoming appointments.
     */
    private function getUpcomingAppointments($staff, $today)
    {
        return Appointment::where('staff_id', $staff->id)
            ->where('appointment_date', '>', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['client', 'service'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent appointments.
     */
    private function getRecentAppointments($staff)
    {
        return Appointment::where('staff_id', $staff->id)
            ->where('appointment_date', '<', Carbon::today())
            ->with(['client', 'service'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get weekly schedule.
     */
    private function getWeeklySchedule($staff)
    {
        // Get all schedules for this staff member
        $schedules = StaffSchedule::where('staff_id', $staff->id)
            ->get()
            ->keyBy('day_of_week');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weeklySchedule = [];

        foreach ($days as $day) {
            $weeklySchedule[$day] = $schedules->get($day, null);
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
     * Get assigned services statistics.
     */
    private function getAssignedServicesStats($staff)
    {
        // Get assigned services with booking statistics
        $assignedServices = $staff->assignedServices()
            ->withCount([
                'appointments as total_bookings' => function($query) use ($staff) {
                    $query->where('staff_id', $staff->id);
                },
                'appointments as completed_bookings' => function($query) use ($staff) {
                    $query->where('staff_id', $staff->id)->where('status', 'completed');
                },
                'appointments as pending_bookings' => function($query) use ($staff) {
                    $query->where('staff_id', $staff->id)->where('status', 'pending');
                },
                'appointments as today_bookings' => function($query) use ($staff) {
                    $query->where('staff_id', $staff->id)
                          ->whereDate('appointment_date', Carbon::today());
                },
                'appointments as upcoming_bookings' => function($query) use ($staff) {
                    $query->where('staff_id', $staff->id)
                          ->where('appointment_date', '>', Carbon::today())
                          ->whereIn('status', ['pending', 'confirmed']);
                }
            ])
            ->orderByPivot('is_primary', 'desc')
            ->orderByPivot('proficiency_level', 'desc')
            ->get();

        // Calculate total revenue for assigned services
        $totalRevenue = 0;
        $monthlyRevenue = 0;
        $currentMonth = Carbon::now()->startOfMonth();

        foreach ($assignedServices as $service) {
            // Calculate revenue using custom price if available, otherwise service price
            $servicePrice = $service->pivot->custom_price ?? $service->price;
            
            $completedBookings = Appointment::where('staff_id', $staff->id)
                ->where('service_id', $service->id)
                ->where('status', 'completed')
                ->count();
            
            $monthlyBookings = Appointment::where('staff_id', $staff->id)
                ->where('service_id', $service->id)
                ->where('status', 'completed')
                ->where('appointment_date', '>=', $currentMonth)
                ->count();

            $totalRevenue += $completedBookings * $servicePrice;
            $monthlyRevenue += $monthlyBookings * $servicePrice;
        }

        return [
            'assigned_services' => $assignedServices,
            'total_assigned' => $assignedServices->count(),
            'primary_services' => $assignedServices->where('pivot.is_primary', true)->count(),
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
        ];
    }
}
