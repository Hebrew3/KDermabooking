<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AppointmentNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display client notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->appointmentNotifications()
            ->with('appointment.service')
            ->active()
            ->orderBy('requires_action', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = Auth::user()->appointmentNotifications()
            ->unread()
            ->active()
            ->count();

        $actionRequiredCount = Auth::user()->appointmentNotifications()
            ->requireingAction()
            ->count();

        return view('client.notifications.index', compact('notifications', 'unreadCount', 'actionRequiredCount'));
    }

    /**
     * Show a specific notification.
     */
    public function show(AppointmentNotification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->client_id !== Auth::id()) {
            abort(403);
        }

        $notification->load(['appointment.service', 'appointment.staff']);

        // Mark as read when viewed
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return view('client.notifications.show', compact('notification'));
    }

    /**
     * Handle client response to staff unavailability.
     */
    public function respondToUnavailability(Request $request, AppointmentNotification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->client_id !== Auth::id()) {
            abort(403);
        }

        // Validate the notification type and status
        if ($notification->type !== 'staff_unavailable' || !$notification->requires_action || $notification->is_read) {
            return response()->json(['error' => 'Invalid notification or already processed.'], 400);
        }

        $request->validate([
            'action' => 'required|in:cancel,reassign',
            'new_staff_id' => 'required_if:action,reassign|exists:users,id',
        ]);

        $appointment = $notification->appointment;

        if ($request->action === 'cancel') {
            // Cancel the appointment
            $appointment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Client cancelled due to staff unavailability',
            ]);

            // Create confirmation notification
            AppointmentNotification::create([
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
                'type' => 'appointment_cancelled',
                'title' => 'Appointment Cancelled',
                'message' => 'Your appointment has been cancelled as requested. We apologize for any inconvenience.',
                'requires_action' => false,
            ]);

            $message = 'Your appointment has been cancelled successfully.';

        } else {
            // Reassign to new staff
            $newStaff = User::find($request->new_staff_id);
            
            // Verify the new staff is available
            if (!$newStaff->isAvailableAt($appointment->appointment_date, $appointment->appointment_time)) {
                return response()->json(['error' => 'Selected staff is not available at the requested time.'], 400);
            }
            
            $appointment->update([
                'staff_id' => $request->new_staff_id,
                'status' => 'confirmed',
                'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                          "Reassigned to {$newStaff->full_name} due to original staff unavailability.",
            ]);

            // Create confirmation notification
            AppointmentNotification::create([
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
                'type' => 'staff_reassigned',
                'title' => 'Staff Reassigned Successfully',
                'message' => "Your appointment has been reassigned to {$newStaff->full_name}. All other details remain the same.",
                'data' => [
                    'new_staff' => [
                        'name' => $newStaff->full_name,
                        'specializations' => $newStaff->getSpecializations(),
                    ],
                    'appointment_details' => [
                        'date' => $appointment->appointment_date->format('M d, Y'),
                        'time' => $appointment->appointment_time,
                        'service' => $appointment->service->name,
                    ],
                ],
                'requires_action' => false,
            ]);

            $message = "Your appointment has been reassigned to {$newStaff->full_name}.";
        }

        // Mark the original notification as read and processed
        $notification->update([
            'is_read' => true,
            'requires_action' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
            'redirect' => route('client.appointments.index'),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(AppointmentNotification $notification)
    {
        // Ensure the notification belongs to the authenticated user
        if ($notification->client_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->appointmentNotifications()
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Get unread notification count.
     */
    public function getUnreadCount()
    {
        $unreadCount = Auth::user()->appointmentNotifications()
            ->unread()
            ->active()
            ->count();

        $actionRequiredCount = Auth::user()->appointmentNotifications()
            ->requireingAction()
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'action_required_count' => $actionRequiredCount,
        ]);
    }
}
