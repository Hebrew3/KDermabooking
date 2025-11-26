<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request)
    {
        $staff = auth()->user();

        $query = Service::active()
            ->withCount(['appointments as total_bookings' => function($query) use ($staff) {
                $query->where('staff_id', $staff->id);
            }])
            ->withCount(['appointments as completed_bookings' => function($query) use ($staff) {
                $query->where('staff_id', $staff->id)->where('status', 'completed');
            }]);

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $services = $query->orderBy('sort_order')->orderBy('name')->paginate(12);

        // Get available categories
        $categories = Service::active()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort();

        // Get staff's assigned service IDs for display purposes
        $assignedServiceIds = $staff->assignedServices()->pluck('services.id')->toArray();

        return view('staff.services', compact('services', 'categories', 'assignedServiceIds'));
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $staff = auth()->user();

        // Get appointments for this service with this staff
        $appointments = Appointment::where('service_id', $service->id)
            ->where('staff_id', $staff->id)
            ->with('client')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10);

        return view('staff.service-details', compact('service', 'appointments'));
    }

    /**
     * Display services assigned to the current staff member.
     */
    public function myServices(Request $request)
    {
        $staff = auth()->user();

        // Get assigned services with pivot data
        $assignedServices = $staff->assignedServices()
            ->withCount(['appointments as total_bookings' => function($query) use ($staff) {
                $query->where('staff_id', $staff->id);
            }])
            ->withCount(['appointments as completed_bookings' => function($query) use ($staff) {
                $query->where('staff_id', $staff->id)->where('status', 'completed');
            }])
            ->orderByPivot('is_primary', 'desc')
            ->orderByPivot('proficiency_level', 'desc')
            ->orderBy('name')
            ->paginate(12);

        // Get staff specializations for context
        $specializations = $staff->getSpecializations();

        // Get primary services
        $primaryServices = $staff->primaryServices()->get();

        return view('staff.my-services', compact('assignedServices', 'specializations', 'primaryServices'));
    }
}
