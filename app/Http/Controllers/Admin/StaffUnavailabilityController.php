<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffUnavailability;
use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StaffUnavailabilityController extends Controller
{
    /**
     * Display a listing of staff unavailabilities.
     */
    public function index(Request $request)
    {
        $query = StaffUnavailability::with(['staff', 'reportedBy']);

        // Filter by staff
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('unavailable_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('unavailable_date', '<=', $request->date_to);
        }

        // Filter by reason
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        // Filter by emergency status
        if ($request->filled('is_emergency')) {
            $query->where('is_emergency', $request->is_emergency === '1');
        }

        $unavailabilities = $query->orderBy('unavailable_date', 'desc')
                                 ->orderBy('reported_at', 'desc')
                                 ->paginate(15);

        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->orderBy('first_name')->get();
        $reasons = StaffUnavailability::getReasonTypes();

        return view('admin.staff-unavailability.index', compact('unavailabilities', 'staff', 'reasons'));
    }

    /**
     * Show the form for creating a new staff unavailability.
     */
    public function create()
    {
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->orderBy('first_name')->get();
        $reasons = StaffUnavailability::getReasonTypes();

        return view('admin.staff-unavailability.create', compact('staff', 'reasons'));
    }

    /**
     * Store a newly created staff unavailability.
     */
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'unavailable_date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'reason' => 'required|in:emergency,sick_leave,personal_leave,vacation,training,other',
            'notes' => 'nullable|string|max:1000',
            'is_emergency' => 'boolean',
        ]);

        // Normalize time format to H:i (remove seconds if present)
        $startTime = $request->start_time ? $this->normalizeTimeFormat($request->start_time) : null;
        $endTime = $request->end_time ? $this->normalizeTimeFormat($request->end_time) : null;

        $unavailability = StaffUnavailability::create([
            'staff_id' => $request->staff_id,
            'unavailable_date' => $request->unavailable_date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'is_emergency' => $request->boolean('is_emergency'),
            'reported_by' => Auth::id(),
        ]);

        // If this is an emergency, trigger the emergency protocol
        if ($unavailability->is_emergency) {
            $this->triggerEmergencyProtocol($unavailability);
        }

        return redirect()->route('admin.staff-unavailability.index')
                        ->with('success', 'Staff unavailability recorded successfully.');
    }

    /**
     * Display the specified staff unavailability.
     */
    public function show(StaffUnavailability $staffUnavailability)
    {
        $staffUnavailability->load(['staff', 'reportedBy']);
        $affectedAppointments = $staffUnavailability->getAffectedAppointments();

        return view('admin.staff-unavailability.show', compact('staffUnavailability', 'affectedAppointments'));
    }

    /**
     * Show the form for editing the specified staff unavailability.
     */
    public function edit(StaffUnavailability $staffUnavailability)
    {
        $staff = User::whereIn('role', ['nurse', 'aesthetician'])->orderBy('first_name')->get();
        $reasons = StaffUnavailability::getReasonTypes();

        return view('admin.staff-unavailability.edit', compact('staffUnavailability', 'staff', 'reasons'));
    }

    /**
     * Update the specified staff unavailability.
     */
    public function update(Request $request, StaffUnavailability $staffUnavailability)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'unavailable_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'reason' => 'required|in:emergency,sick_leave,personal_leave,vacation,training,other',
            'notes' => 'nullable|string|max:1000',
            'is_emergency' => 'boolean',
        ]);

        $wasEmergency = $staffUnavailability->is_emergency;
        
        // Normalize time format to H:i (remove seconds if present)
        $startTime = $request->start_time ? $this->normalizeTimeFormat($request->start_time) : null;
        $endTime = $request->end_time ? $this->normalizeTimeFormat($request->end_time) : null;
        
        $staffUnavailability->update([
            'staff_id' => $request->staff_id,
            'unavailable_date' => $request->unavailable_date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'is_emergency' => $request->boolean('is_emergency'),
        ]);

        // If this became an emergency, trigger the protocol
        if (!$wasEmergency && $staffUnavailability->is_emergency) {
            $this->triggerEmergencyProtocol($staffUnavailability);
        }

        return redirect()->route('admin.staff-unavailability.index')
                        ->with('success', 'Staff unavailability updated successfully.');
    }

    /**
     * Remove the specified staff unavailability.
     */
    public function destroy(StaffUnavailability $staffUnavailability)
    {
        $staffUnavailability->delete();

        return redirect()->route('admin.staff-unavailability.index')
                        ->with('success', 'Staff unavailability deleted successfully.');
    }

    /**
     * Normalize time format to H:i (remove seconds and trim).
     */
    private function normalizeTimeFormat($timeString): ?string
    {
        if (empty($timeString)) {
            return null;
        }

        // Trim whitespace
        $timeString = trim($timeString);

        // If time includes seconds (H:i:s), remove them
        if (strlen($timeString) > 5 && strpos($timeString, ':') !== false) {
            $parts = explode(':', $timeString);
            if (count($parts) >= 2) {
                // Take only hours and minutes
                $timeString = $parts[0] . ':' . $parts[1];
            }
        }

        // Validate format (should be H:i)
        if (preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $timeString)) {
            return $timeString;
        }

        // If validation fails, try to parse and reformat
        try {
            $carbon = Carbon::parse($timeString);
            return $carbon->format('H:i');
        } catch (\Exception $e) {
            \Log::warning("Failed to normalize time format: {$timeString}", [
                'error' => $e->getMessage()
            ]);
            return $timeString; // Return as-is if parsing fails
        }
    }

    /**
     * Trigger emergency protocol for staff unavailability.
     */
    private function triggerEmergencyProtocol(StaffUnavailability $unavailability)
    {
        $affectedAppointments = $unavailability->getAffectedAppointments();

        foreach ($affectedAppointments as $appointment) {
            // Find alternative staff with same specialization
            $alternativeStaff = $this->findAlternativeStaff(
                $appointment->service_id,
                $appointment->appointment_date,
                $appointment->appointment_time,
                $unavailability->staff_id
            );

            // Create notification for client
            AppointmentNotification::create([
                'appointment_id' => $appointment->id,
                'client_id' => $appointment->client_id,
                'type' => 'staff_unavailable',
                'title' => 'Staff Unavailable - Action Required',
                'message' => "We're sorry, but {$unavailability->staff->full_name} is unavailable for your appointment on {$appointment->appointment_date->format('M d, Y')} at {$appointment->appointment_time}. Please choose an option below.",
                'data' => [
                    'unavailability_reason' => $unavailability->formatted_reason,
                    'original_staff' => $unavailability->staff->full_name,
                    'alternative_staff' => $alternativeStaff->map(function($staff) {
                        return [
                            'id' => $staff->id,
                            'name' => $staff->full_name,
                            'specializations' => $staff->getSpecializations(),
                        ];
                    })->toArray(),
                    'appointment_details' => [
                        'date' => $appointment->appointment_date->format('M d, Y'),
                        'time' => $appointment->appointment_time,
                        'service' => $appointment->service->name,
                    ],
                ],
                'requires_action' => true,
                'expires_at' => now()->addHours(24), // Client has 24 hours to respond
            ]);

            // Update appointment status to pending reassignment
            $appointment->update([
                'status' => 'pending',
                'notes' => ($appointment->notes ? $appointment->notes . "\n\n" : '') . 
                          "Staff unavailable due to {$unavailability->formatted_reason}. Awaiting client response for reassignment."
            ]);
        }
    }

    /**
     * Find alternative staff with matching specializations.
     */
    private function findAlternativeStaff($serviceId, $date, $time, $excludeStaffId)
    {
        // Get the service to determine required specialization
        $service = \App\Models\Service::find($serviceId);
        $requiredSpecialization = $service->category; // Assuming service category matches specialization

        return User::whereIn('role', ['nurse', 'aesthetician'])
            ->where('id', '!=', $excludeStaffId)
            ->whereHas('staffSpecializations', function($query) use ($requiredSpecialization) {
                $query->where('specialization', $requiredSpecialization);
            })
            ->get()
            ->filter(function($staff) use ($date, $time) {
                return $staff->isAvailableAt($date, $time);
            });
    }

    /**
     * Handle client response to staff unavailability.
     */
    public function handleClientResponse(Request $request, AppointmentNotification $notification)
    {
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
                'message' => 'Your appointment has been cancelled as requested.',
                'requires_action' => false,
            ]);

        } else {
            // Reassign to new staff
            $newStaff = User::find($request->new_staff_id);
            
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
                'title' => 'Staff Reassigned',
                'message' => "Your appointment has been reassigned to {$newStaff->full_name}.",
                'data' => [
                    'new_staff' => [
                        'name' => $newStaff->full_name,
                        'specializations' => $newStaff->getSpecializations(),
                    ],
                ],
                'requires_action' => false,
            ]);
        }

        // Mark the original notification as read
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => $request->action === 'cancel' ? 
                        'Appointment cancelled successfully.' : 
                        'Staff reassigned successfully.',
        ]);
    }
}
