<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EmergencyStaffController extends Controller
{
    /**
     * Handle client acceptance of replacement staff.
     */
    public function accept(Request $request, Appointment $appointment)
    {
        try {
            // Verify token (for security)
            $token = $request->get('token');
            if (!$this->verifyToken($appointment, $token)) {
                $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
                return redirect()->route($redirectRoute)
                    ->with('error', 'Invalid or expired link. Please contact support.');
            }

            // Check if appointment is still valid
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
                return redirect()->route($redirectRoute)
                    ->with('error', 'This appointment is no longer available for modification.');
            }

            // Get the replacement staff from appointment notes or current staff_id
            // The replacement staff should already be assigned when email was sent
            $replacementStaff = $appointment->staff;

            if (!$replacementStaff) {
                $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
                return redirect()->route($redirectRoute)
                    ->with('error', 'No replacement staff assigned. Please contact support.');
            }

            // Confirm the appointment with replacement staff
            $appointment->update([
                'status' => 'confirmed',
                'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                          "Client accepted replacement staff ({$replacementStaff->first_name} {$replacementStaff->last_name}) on " . now()->format('Y-m-d H:i:s') . ".",
            ]);

            // Log the action
            Log::info('Client accepted emergency staff replacement', [
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
                'is_walkin' => $appointment->isWalkIn(),
                'replacement_staff_id' => $replacementStaff->id,
                'replacement_staff_name' => $replacementStaff->first_name . ' ' . $replacementStaff->last_name,
                'action_date' => now()->toDateTimeString(),
            ]);

            $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
            return redirect()->route($redirectRoute)
                ->with('success', "You have accepted {$replacementStaff->first_name} {$replacementStaff->last_name} as your new staff member. Your appointment is confirmed.");

        } catch (\Throwable $exception) {
            Log::error('Error processing emergency staff acceptance', [
                'appointment_id' => $appointment->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
            return redirect()->route($redirectRoute)
                ->with('error', 'An error occurred while processing your request. Please contact support.');
        }
    }

    /**
     * Handle client cancellation due to staff emergency.
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        try {
            // Verify token (for security)
            $token = $request->get('token');
            if (!$this->verifyToken($appointment, $token)) {
                $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
                return redirect()->route($redirectRoute)
                    ->with('error', 'Invalid or expired link. Please contact support.');
            }

            // Check if appointment is still valid
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
                return redirect()->route($redirectRoute)
                    ->with('error', 'This appointment is no longer available for cancellation.');
            }

            // Cancel the appointment
            $appointment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Cancelled by Client due to Staff Emergency',
                'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                          "Client cancelled appointment due to staff emergency on " . now()->format('Y-m-d H:i:s') . ".",
            ]);

            // Log the action
            Log::info('Client cancelled appointment due to staff emergency', [
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
                'is_walkin' => $appointment->isWalkIn(),
                'cancellation_reason' => 'Cancelled by Client due to Staff Emergency',
                'action_date' => now()->toDateTimeString(),
            ]);

            $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
            return redirect()->route($redirectRoute)
                ->with('success', 'Your appointment has been cancelled as requested. We apologize for the inconvenience.');

        } catch (\Throwable $exception) {
            Log::error('Error processing emergency staff cancellation', [
                'appointment_id' => $appointment->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            $redirectRoute = auth()->check() ? 'appointments.index' : 'index.landing';
            return redirect()->route($redirectRoute)
                ->with('error', 'An error occurred while processing your request. Please contact support.');
        }
    }

    /**
     * Verify token from appointment notes.
     */
    private function verifyToken(Appointment $appointment, ?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        // Check if token exists in appointment notes
        // Token format: "Emergency staff replacement token: {token}" or "Staff replacement token: {token}"
        $notes = $appointment->notes ?? '';
        
        // For simplicity, we'll check if token is in notes
        // In production, you might want to use a more secure method (e.g., separate table)
        return strpos($notes, $token) !== false;
    }
}

