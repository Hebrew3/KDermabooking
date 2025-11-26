<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffUnavailability;
use Illuminate\Http\Request;

class StaffLeaveController extends Controller
{
    /**
     * Display staff leave requests (pending and past).
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending'); // pending, approved, rejected, all
        
        $query = StaffUnavailability::with(['staff', 'reportedBy', 'approver']);
        
        // Filter by status
        if ($status !== 'all') {
            $query->where('approval_status', $status);
        }
        
        // Order by date (most recent first for past, upcoming first for pending)
        if ($status === 'pending') {
            $query->orderBy('unavailable_date')
                  ->orderBy('created_at');
        } else {
            $query->orderBy('unavailable_date', 'desc')
                  ->orderBy('created_at', 'desc');
        }
        
        $leaveRequests = $query->paginate(20);
        
        // Get counts for tabs
        $counts = [
            'pending' => StaffUnavailability::where('approval_status', 'pending')->count(),
            'approved' => StaffUnavailability::where('approval_status', 'approved')->count(),
            'rejected' => StaffUnavailability::where('approval_status', 'rejected')->count(),
            'all' => StaffUnavailability::count(),
        ];

        return view('admin.staff-leave.index', compact('leaveRequests', 'status', 'counts'));
    }

    /**
     * Approve a leave request.
     */
    public function approve(StaffUnavailability $leave)
    {
        $leave->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Leave request approved.');
    }

    /**
     * Reject a leave request.
     */
    public function reject(StaffUnavailability $leave)
    {
        $leave->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Leave request rejected.');
    }
}
