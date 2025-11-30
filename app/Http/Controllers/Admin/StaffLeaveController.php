<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffUnavailability;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use App\Helpers\TimeHelper;
use Illuminate\Http\Request;
use App\Services\PHPMailerService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // If this is an emergency leave, handle affected appointments
        if ($leave->is_emergency) {
            $this->handleEmergencyLeaveAppointments($leave);
        }

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

    /**
     * Handle appointments affected by emergency leave approval.
     */
    private function handleEmergencyLeaveAppointments(StaffUnavailability $leave): void
    {
        try {
            $affectedAppointments = $leave->getAffectedAppointments();

            if ($affectedAppointments->isEmpty()) {
                Log::info('No appointments affected by emergency leave', [
                    'leave_id' => $leave->id,
                    'staff_id' => $leave->staff_id,
                ]);
                return;
            }

            Log::info('Processing emergency leave appointments', [
                'leave_id' => $leave->id,
                'staff_id' => $leave->staff_id,
                'affected_count' => $affectedAppointments->count(),
            ]);

            foreach ($affectedAppointments as $appointment) {
                // Find replacement staff
                $replacementStaff = $this->findReplacementStaff($appointment);

                if ($replacementStaff) {
                    // Temporarily assign replacement staff
                    $appointment->update([
                        'staff_id' => $replacementStaff->id,
                        'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                                  "Original staff ({$leave->staff->first_name} {$leave->staff->last_name}) had an emergency. " .
                                  "Temporarily assigned to {$replacementStaff->first_name} {$replacementStaff->last_name}. " .
                                  "Awaiting client confirmation.",
                    ]);

                    // Send email to client
                    $this->sendEmergencyStaffReplacementEmail($appointment, $leave, $replacementStaff);

                    // Log the action
                    Log::info('Emergency staff replacement processed', [
                        'appointment_id' => $appointment->id,
                        'original_staff_id' => $leave->staff_id,
                        'replacement_staff_id' => $replacementStaff->id,
                        'client_id' => $appointment->client_id,
                    ]);
                } else {
                    // No replacement staff found - notify admin
                    Log::warning('No replacement staff found for emergency leave appointment', [
                        'appointment_id' => $appointment->id,
                        'service_id' => $appointment->service_id,
                        'appointment_date' => $appointment->appointment_date,
                        'appointment_time' => $appointment->appointment_time,
                    ]);

                    // Still send email to client but inform them we're working on finding replacement
                    $this->sendEmergencyStaffReplacementEmail($appointment, $leave, null);
                }
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to handle emergency leave appointments', [
                'leave_id' => $leave->id,
                'staff_id' => $leave->staff_id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }
    }

    /**
     * Find replacement staff for an appointment.
     */
    private function findReplacementStaff(Appointment $appointment): ?User
    {
        try {
            $service = Service::find($appointment->service_id);
            if (!$service) {
                return null;
            }

            $date = $appointment->appointment_date;
            $time = $appointment->appointment_time;
            $dateCarbon = TimeHelper::parseDate($date);
            $dayOfWeek = $dateCarbon ? $dateCarbon->format('l') : date('l', strtotime($date));

            // Find staff who:
            // 1. Are active
            // 2. Are assigned to this service
            // 3. Have schedule for this day
            // 4. Are not the original staff
            // 5. Are not unavailable on this date/time
            $availableStaff = User::whereIn('role', ['nurse', 'aesthetician'])
                ->where('is_active', true)
                ->where('id', '!=', $appointment->staff_id)
                ->whereHas('assignedServices', function($query) use ($service) {
                    $query->where('services.id', $service->id);
                })
                ->whereHas('staffSchedules', function($query) use ($dayOfWeek) {
                    $query->where('day_of_week', $dayOfWeek)
                          ->where('is_available', true)
                          ->whereNotNull('start_time')
                          ->whereNotNull('end_time');
                })
                ->whereDoesntHave('staffUnavailabilities', function($query) use ($date, $time) {
                    $query->where('unavailable_date', $date)
                          ->where('approval_status', 'approved')
                          ->where(function($q) use ($time) {
                              $q->whereNull('start_time')
                                ->whereNull('end_time')
                                ->orWhere(function($q2) use ($time) {
                                    $q2->where('start_time', '<=', $time)
                                       ->where('end_time', '>=', $time);
                                });
                          });
                })
                ->whereDoesntHave('appointments', function($query) use ($date, $time) {
                    $query->where('appointment_date', $date)
                          ->where('appointment_time', $time)
                          ->whereIn('status', ['pending', 'confirmed', 'in_progress']);
                })
                ->with(['staffSchedules' => function($query) use ($dayOfWeek) {
                    $query->where('day_of_week', $dayOfWeek);
                }])
                ->orderBy('first_name')
                ->first();

            return $availableStaff;
        } catch (\Throwable $exception) {
            Log::error('Error finding replacement staff', [
                'appointment_id' => $appointment->id,
                'error' => $exception->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send email to client about staff emergency and replacement.
     */
    private function sendEmergencyStaffReplacementEmail(Appointment $appointment, StaffUnavailability $leave, ?User $replacementStaff): void
    {
        try {
            // Get client email (registered client or walk-in)
            $clientEmail = $appointment->customer_email;
            $clientName = $appointment->customer_name;
            
            if (!$clientEmail) {
                Log::warning('Cannot send emergency staff replacement email: no email found', [
                    'appointment_id' => $appointment->id,
                    'client_id' => $appointment->client_id,
                    'is_walkin' => $appointment->isWalkIn(),
                ]);
                return;
            }

            $phpMailerService = new PHPMailerService();

            $originalStaffName = $leave->staff->first_name ? 
                ($leave->staff->first_name . ' ' . $leave->staff->last_name) : 
                $leave->staff->name;

            $replacementStaffName = $replacementStaff ? 
                ($replacementStaff->first_name . ' ' . $replacementStaff->last_name) : 
                null;

            $appointmentDate = $appointment->appointment_date instanceof Carbon 
                ? $appointment->appointment_date->format('F d, Y')
                : Carbon::parse($appointment->appointment_date)->format('F d, Y');

            $appointmentTime = $appointment->appointment_time;
            $timeCarbon = TimeHelper::parseTime($appointmentTime);
            $formattedTime = $timeCarbon ? $timeCarbon->format('g:i A') : $appointmentTime;

            $serviceName = $appointment->service ? $appointment->service->name : 'Service';

            // Generate unique token for this appointment action
            $token = bin2hex(random_bytes(32));
            
            // Store token in appointment notes or create a separate tracking mechanism
            // For now, we'll encode it in the URL
            $acceptUrl = route('appointments.emergency-staff.accept', [
                'appointment' => $appointment->id,
                'token' => $token,
            ]);
            
            $cancelUrl = route('appointments.emergency-staff.cancel', [
                'appointment' => $appointment->id,
                'token' => $token,
            ]);

            // Store token in appointment for verification
            DB::table('appointments')
                ->where('id', $appointment->id)
                ->update([
                    'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                              "Emergency staff replacement token: {$token}",
                ]);

            $emailSent = $phpMailerService->sendEmergencyStaffReplacementEmail(
                $clientEmail,
                $clientName,
                $originalStaffName,
                $replacementStaffName,
                $appointmentDate,
                $formattedTime,
                $serviceName,
                $appointment->appointment_number,
                $acceptUrl,
                $cancelUrl
            );

            if ($emailSent) {
                Log::info('Emergency staff replacement email sent successfully', [
                    'appointment_id' => $appointment->id,
                    'client_email' => $clientEmail,
                    'is_walkin' => $appointment->isWalkIn(),
                ]);
            } else {
                Log::warning('Failed to send emergency staff replacement email', [
                    'appointment_id' => $appointment->id,
                    'client_email' => $clientEmail,
                    'is_walkin' => $appointment->isWalkIn(),
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Error sending emergency staff replacement email', [
                'appointment_id' => $appointment->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
