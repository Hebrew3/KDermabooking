<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\StaffService;
use Illuminate\Http\Request;

class StaffServiceController extends Controller
{
    /**
     * Display staff service assignments.
     */
    public function index()
    {
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])
                    ->with(['staffSpecializations'])
                    ->paginate(10);

        // Get staff service assignments separately for better control
        $staffServices = StaffService::with(['staff', 'service'])
                                    ->get()
                                    ->groupBy('staff_id');

        $services = Service::active()->get();

        return view('admin.staff-services.index', compact('staff', 'services', 'staffServices'));
    }

    /**
     * Show the form for assigning services to staff.
     */
    public function create()
    {
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->get();
        $services = Service::active()->get();

        return view('admin.staff-services.create', compact('staff', 'services'));
    }

    /**
     * Store staff service assignment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'is_primary' => 'boolean',
            'custom_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'proficiency_level' => 'required|integer|between:1,5',
            'notes' => 'nullable|string|max:500',
        ], [
            'custom_price.max' => 'The custom price cannot exceed ₱99,999,999.99.',
            'custom_price.regex' => 'The custom price must be a valid number with up to 2 decimal places.',
            'custom_price.numeric' => 'The custom price must be a valid number.',
        ]);

        $staff = User::findOrFail($request->staff_id);
        $service = Service::findOrFail($request->service_id);

        // Check if staff can perform this service based on specialization
        if (!$service->canBePerformedBy($staff)) {
            return back()->withErrors([
                'service_id' => 'This staff member does not have the required specialization for this service.'
            ])->withInput();
        }

        // Check if assignment already exists
        if ($staff->isAssignedToService($request->service_id)) {
            return back()->withErrors([
                'service_id' => 'This staff member is already assigned to this service.'
            ])->withInput();
        }

        // Sanitize custom_price: ensure it's within valid range and properly formatted
        $customPrice = null;
        if ($request->filled('custom_price')) {
            $customPrice = (float) $request->custom_price;
            $customPrice = round($customPrice, 2);
            $customPrice = min($customPrice, 99999999.99);
            $customPrice = max($customPrice, 0);
        }

        StaffService::create([
            'staff_id' => $request->staff_id,
            'service_id' => $request->service_id,
            'is_primary' => $request->boolean('is_primary'),
            'custom_price' => $customPrice,
            'proficiency_level' => $request->proficiency_level,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.staff-services.index')
                        ->with('success', 'Staff service assignment created successfully!');
    }

    /**
     * Show the form for editing staff service assignment.
     */
    public function edit(StaffService $staffService)
    {
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->get();
        $services = Service::active()->get();

        return view('admin.staff-services.edit', compact('staffService', 'staff', 'services'));
    }

    /**
     * Update staff service assignment.
     */
    public function update(Request $request, StaffService $staffService)
    {
        $request->validate([
            'is_primary' => 'boolean',
            'custom_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'proficiency_level' => 'required|integer|between:1,5',
            'notes' => 'nullable|string|max:500',
        ], [
            'custom_price.max' => 'The custom price cannot exceed ₱99,999,999.99.',
            'custom_price.regex' => 'The custom price must be a valid number with up to 2 decimal places.',
            'custom_price.numeric' => 'The custom price must be a valid number.',
        ]);

        // Sanitize custom_price: ensure it's within valid range and properly formatted
        $customPrice = null;
        if ($request->filled('custom_price')) {
            $customPrice = (float) $request->custom_price;
            $customPrice = round($customPrice, 2);
            $customPrice = min($customPrice, 99999999.99);
            $customPrice = max($customPrice, 0);
        }

        $staffService->update([
            'is_primary' => $request->boolean('is_primary'),
            'custom_price' => $customPrice,
            'proficiency_level' => $request->proficiency_level,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.staff-services.index')
                        ->with('success', 'Staff service assignment updated successfully!');
    }

    /**
     * Remove staff service assignment.
     */
    public function destroy(StaffService $staffService)
    {
        $staffService->delete();

        return redirect()->route('admin.staff-services.index')
                        ->with('success', 'Staff service assignment removed successfully!');
    }

    /**
     * Get services available for a specific staff member.
     */
    public function getAvailableServices(Request $request)
    {
        $staffId = $request->get('staff_id');
        
        if (!$staffId) {
            return response()->json([]);
        }

        $staff = User::find($staffId);
        
        if (!$staff || !$staff->isStaffMember()) {
            return response()->json([]);
        }

        $availableServices = $staff->getQualifiedServices()
                                  ->filter(function($service) use ($staff) {
                                      return !$staff->isAssignedToService($service->id);
                                  })
                                  ->values();

        return response()->json($availableServices);
    }
}
