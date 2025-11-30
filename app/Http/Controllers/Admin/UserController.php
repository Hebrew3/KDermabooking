<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\PHPMailerService;

class UserController extends Controller
{
    public function __construct(private PHPMailerService $phpMailerService)
    {
    }

    /**
     * Display a listing of users with search and filter functionality.
     */
    public function index(Request $request): View
    {
        $query = User::query();

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

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Sort by specified column
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // If filtering by client role and no custom sort is specified, 
        // prioritize clients based on appointment status (active appointments first)
        if (($request->get('role') === 'client' || ($request->get('role') === null && !$request->filled('role'))) && $sortBy === 'created_at') {
            // Get appointment statistics for each client
            $appointmentStats = DB::table('appointments')
                ->select('client_id',
                    DB::raw('MAX(CASE 
                        WHEN status IN (\'pending\', \'confirmed\', \'in_progress\') THEN 1
                        WHEN status = \'completed\' THEN 2
                        WHEN status IN (\'cancelled\', \'no_show\') THEN 3
                        ELSE 4
                    END) as status_priority'),
                    DB::raw('MAX(CASE 
                        WHEN status IN (\'pending\', \'confirmed\', \'in_progress\') THEN created_at
                        ELSE NULL
                    END) as latest_active_appointment')
                )
                ->whereNotNull('client_id')
                ->groupBy('client_id')
                ->get()
                ->keyBy('client_id');
            
            // Get all users and sort them in memory to avoid GROUP BY issues
            $allUsers = $query->get();
            
            // Sort users based on appointment statistics
            $sortedUsers = $allUsers->sortBy(function($user) use ($appointmentStats) {
                $stats = $appointmentStats->get($user->id);
                $statusPriority = $stats ? $stats->status_priority : 4;
                $latestAppointment = $stats ? $stats->latest_active_appointment : null;
                
                // Return a sort key: priority first, then appointment date (nulls last), then created_at
                return [
                    $statusPriority,
                    $latestAppointment ? 0 : 1, // 0 = has appointment, 1 = no appointment (nulls last)
                    $latestAppointment ? strtotime($latestAppointment) : 0,
                    strtotime($user->created_at) * -1 // Descending order
                ];
            })->values();
            
            // Manually paginate the sorted collection
            $page = $request->get('page', 1);
            $perPage = 10;
            $total = $sortedUsers->count();
            $items = $sortedUsers->forPage($page, $perPage);
            
            // Create a LengthAwarePaginator instance
            $users = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $query->orderBy($sortBy, $sortOrder);
            $users = $query->paginate(10)->withQueryString();
        }

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        $services = \App\Models\Service::active()->orderBy('name')->get();
        return view('admin.users.create', compact('services'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $plainPassword = $validated['password'] ?? null;
        $isStaffRole = $this->isStaffRole($validated['role'] ?? null);

        // Hash the password
        if ($isStaffRole) {
            $plainPassword = Str::random(12);
            $validated['password'] = Hash::make($plainPassword);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }
        $validated['email_verified_at'] = now(); // Auto-verify admin created users

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            /** @var UploadedFile $file */
            $file = $request->file('profile_picture');
            $validated['profile_picture'] = $file->store('profile_pictures', 'public');
        }

        $user = User::create($validated);

        // Assign services based on role
        if ($user->isStaffMember()) {
            $this->assignServicesToStaff($user, $request);
            $this->sendStaffCredentialsEmail($user, $plainPassword);
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $services = \App\Models\Service::active()->orderBy('name')->get();
        $assignedServiceIds = $user->assignedServices()->pluck('service_id')->toArray();
        return view('admin.users.edit', compact('user', 'services', 'assignedServiceIds'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        // Hash the password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            /** @var UploadedFile $file */
            $file = $request->file('profile_picture');
            $validated['profile_picture'] = $file->store('profile_pictures', 'public');
        }

        $oldRole = $user->role;
        $user->update($validated);

        // Handle service assignments if role changed to/from nurse/aesthetician
        if (in_array($user->role, ['nurse', 'aesthetician'])) {
            // If role changed, clear old assignments and assign new ones
            if ($oldRole !== $user->role) {
                \App\Models\StaffService::where('staff_id', $user->id)->delete();
            }
            
            // Update service assignments
            if ($user->role === 'nurse') {
                // For nurses, ensure Gluta is assigned
                $glutaService = \App\Models\Service::where('name', 'Gluta')->first();
                if ($glutaService) {
                    \App\Models\StaffService::firstOrCreate(
                        [
                            'staff_id' => $user->id,
                            'service_id' => $glutaService->id,
                        ],
                        [
                            'is_primary' => true,
                            'proficiency_level' => 5,
                            'notes' => 'Auto-assigned Gluta service for nurse role',
                        ]
                    );
                }
                // Remove any other services for nurses
                \App\Models\StaffService::where('staff_id', $user->id)
                    ->where('service_id', '!=', $glutaService->id ?? 0)
                    ->delete();
            } elseif ($user->role === 'aesthetician') {
                // For aestheticians, sync selected services
                if ($request->has('service_ids') && is_array($request->service_ids)) {
                    // Get current assignments
                    $currentServiceIds = \App\Models\StaffService::where('staff_id', $user->id)
                        ->pluck('service_id')
                        ->toArray();
                    
                    // Services to add
                    $servicesToAdd = array_diff($request->service_ids, $currentServiceIds);
                    foreach ($servicesToAdd as $serviceId) {
                        \App\Models\StaffService::create([
                            'staff_id' => $user->id,
                            'service_id' => $serviceId,
                            'is_primary' => false,
                            'proficiency_level' => 4,
                            'notes' => 'Assigned during user update',
                        ]);
                    }
                    
                    // Services to remove
                    $servicesToRemove = array_diff($currentServiceIds, $request->service_ids);
                    if (!empty($servicesToRemove)) {
                        \App\Models\StaffService::where('staff_id', $user->id)
                            ->whereIn('service_id', $servicesToRemove)
                            ->delete();
                    }
                } else {
                    // If no services selected, remove all assignments
                    \App\Models\StaffService::where('staff_id', $user->id)->delete();
                }
            }
        } elseif (in_array($oldRole, ['nurse', 'aesthetician']) && !in_array($user->role, ['nurse', 'aesthetician'])) {
            // If role changed from staff to non-staff, remove service assignments
            \App\Models\StaffService::where('staff_id', $user->id)->delete();
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Delete profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        $query = User::query();

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->get();

        $filename = 'users_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');

            // CSV header
            fputcsv($file, [
                'ID', 'First Name', 'Middle Name', 'Last Name', 'Email', 'Gender',
                'Mobile Number', 'Address', 'Birth Date', 'Role', 'Created At'
            ]);

            // CSV data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->first_name,
                    $user->middle_name,
                    $user->last_name,
                    $user->email,
                    $user->gender,
                    $user->mobile_number,
                    $user->address,
                    $user->birth_date ? $user->birth_date->format('Y-m-d') : '',
                    $user->role,
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Toggle user status (activate/deactivate).
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Prevent admin from deactivating themselves
        if ($user->id === Auth::id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'You cannot change your own status.');
        }

        // Toggle email_verified_at as a status indicator
        $user->email_verified_at = $user->email_verified_at ? null : now();
        $user->save();

        $status = $user->email_verified_at ? 'activated' : 'deactivated';

        return redirect()
            ->route('admin.users.index')
            ->with('success', "User {$status} successfully!");
    }

    /**
     * Assign services to staff member based on role.
     */
    private function assignServicesToStaff(User $user, Request $request): void
    {
        if ($user->role === 'nurse') {
            // Auto-assign Gluta service to nurses
            $glutaService = \App\Models\Service::where('name', 'Gluta')->first();
            if ($glutaService) {
                \App\Models\StaffService::firstOrCreate(
                    [
                        'staff_id' => $user->id,
                        'service_id' => $glutaService->id,
                    ],
                    [
                        'is_primary' => true,
                        'proficiency_level' => 5, // Expert level for default assignment
                        'notes' => 'Auto-assigned Gluta service for nurse role',
                    ]
                );
            }
        } elseif ($user->role === 'aesthetician') {
            // Assign selected services to aestheticians
            if ($request->has('service_ids') && is_array($request->service_ids)) {
                foreach ($request->service_ids as $serviceId) {
                    \App\Models\StaffService::firstOrCreate(
                        [
                            'staff_id' => $user->id,
                            'service_id' => $serviceId,
                        ],
                        [
                            'is_primary' => false,
                            'proficiency_level' => 4, // Advanced level for default assignment
                            'notes' => 'Assigned during user creation',
                        ]
                    );
                }
            }
        }
    }

    private function sendStaffCredentialsEmail(User $user, ?string $plainPassword): void
    {
        if (!$plainPassword) {
            return;
        }

        try {
            $loginUrl = route('login');
            $htmlBody = view('emails.staff-credentials', [
                'user' => $user,
                'plainPassword' => $plainPassword,
                'loginUrl' => $loginUrl,
            ])->render();

            $textBody = <<<TEXT
Hi {$user->full_name},

Welcome to K-Derma! Your staff account is ready.

Email: {$user->email}
Temporary Password: {$plainPassword}
Sign in: {$loginUrl}

Please sign in and change your password right away from the profile settings page.

Thank you,
K-Derma Booking System
TEXT;

            $emailSent = $this->phpMailerService->sendCustomEmail(
                $user->email,
                'Your K-Derma staff account credentials',
                $htmlBody,
                $textBody,
                $user->full_name
            );

            if (!$emailSent) {
                Log::warning('Staff credentials email could not be sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Failed to send staff credentials email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function isStaffRole(?string $role): bool
    {
        return in_array($role, ['nurse', 'aesthetician'], true);
    }
}
