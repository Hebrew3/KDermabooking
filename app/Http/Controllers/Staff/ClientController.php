<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request)
    {
        $staff = $request->user();

        $query = User::where('role', 'client')
            ->withCount(['appointments as total_appointments' => function($query) use ($staff) {
                $query->where('staff_id', $staff->id);
            }])
            ->withCount(['appointments as completed_appointments' => function($query) use ($staff) {
                $query->where('staff_id', $staff->id)->where('status', 'completed');
            }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('first_name')->paginate(15);

        // Get new appointments assigned to this staff (created in the last 24 hours)
        $newAppointments = Appointment::where('staff_id', $staff->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['client', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.clients', compact('clients', 'newAppointments'));
    }

    /**
     * Display the specified client.
     */
    public function show(User $client)
    {
        $staff = auth()->user();

        // Get client's appointments with this staff
        $appointments = Appointment::where('client_id', $client->id)
            ->where('staff_id', $staff->id)
            ->with('service')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10);

        return view('staff.client-details', compact('client', 'appointments'));
    }
}
