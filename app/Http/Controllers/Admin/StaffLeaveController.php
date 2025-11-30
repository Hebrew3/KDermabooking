<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffUnavailability;
use Illuminate\Http\Request;
use App\Services\PHPMailerService;
use Illuminate\Support\Facades\Log;

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
     * Get pending leave requests count for notification badge.
     */
    public function pendingCount()
    {
        $pendingCount = StaffUnavailability::where('approval_status', 'pending')->count();
        
        return response()->json([
            'pending_count' => $pendingCount
        ]);
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

        // Send email notification to staff
        $this->sendLeaveRequestNotification($leave, 'approved');

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

        // Send email notification to staff
        $this->sendLeaveRequestNotification($leave, 'rejected');

        return redirect()->back()->with('success', 'Leave request rejected.');
    }

    /**
     * Send leave request status notification email to staff.
     */
    private function sendLeaveRequestNotification(StaffUnavailability $leave, string $status): void
    {
        try {
            $leave->load('staff');
            
            if (!$leave->staff || !$leave->staff->email) {
                Log::warning('Cannot send leave request notification: staff or email not found', [
                    'leave_id' => $leave->id,
                    'staff_id' => $leave->staff_id,
                ]);
                return;
            }

            $phpMailerService = new PHPMailerService();
            
            $userName = $leave->staff->first_name ? 
                ($leave->staff->first_name . ' ' . $leave->staff->last_name) : 
                $leave->staff->name;
            
            $leaveDate = $leave->unavailable_date instanceof \Carbon\Carbon 
                ? $leave->unavailable_date->format('F d, Y')
                : \Carbon\Carbon::parse($leave->unavailable_date)->format('F d, Y');
            $timeRange = $leave->formatted_time_range ?? 'All Day';
            $reason = $leave->formatted_reason ?? ucfirst($leave->reason);
            $notes = $leave->notes;

            $emailSent = $phpMailerService->sendLeaveRequestStatusEmail(
                $leave->staff->email,
                $userName,
                $status,
                $leaveDate,
                $timeRange,
                $reason,
                $notes
            );

            if (!$emailSent) {
                Log::warning('Leave request notification email could not be sent', [
                    'leave_id' => $leave->id,
                    'staff_id' => $leave->staff_id,
                    'email' => $leave->staff->email,
                    'status' => $status,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to send leave request notification email', [
                'leave_id' => $leave->id,
                'staff_id' => $leave->staff_id,
                'status' => $status,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
