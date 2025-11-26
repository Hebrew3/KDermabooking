<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffUnavailability;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * Show leave requests for the logged-in staff and a form to request leave.
     */
    public function index(Request $request)
    {
        $staff = $request->user();

        $leaveRequests = StaffUnavailability::where('staff_id', $staff->id)
            ->orderByDesc('unavailable_date')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $reasonTypes = StaffUnavailability::getReasonTypes();

        return view('staff.leave', compact('staff', 'leaveRequests', 'reasonTypes'));
    }

    /**
     * Store a new leave request (pending approval).
     */
    public function store(Request $request)
    {
        $staff = $request->user();

        $data = $request->validate([
            'unavailable_date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'reason' => 'required|in:emergency,sick_leave,personal_leave,vacation,training,other',
            'notes' => 'nullable|string|max:1000',
        ]);

        StaffUnavailability::create([
            'staff_id' => $staff->id,
            'unavailable_date' => $data['unavailable_date'],
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'is_emergency' => $data['reason'] === 'emergency',
            'reported_at' => now(),
            'reported_by' => $staff->id,
            'approval_status' => 'pending',
        ]);

        return redirect()->route('staff.leave.index')
            ->with('success', 'Your leave request has been submitted and is pending admin approval.');
    }
}
