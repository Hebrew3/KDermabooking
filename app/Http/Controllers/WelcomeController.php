<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page with featured services.
     */
    public function index()
    {
        // Check if user is authenticated and redirect based on role
        if (auth()->check()) {
            $user = auth()->user();
            
            // Redirect admin and staff to their respective dashboards
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isStaffMember()) {
                return redirect()->route('staff.dashboard');
            }
            // Clients can access the landing page (they don't have a separate dashboard)
        }

        // Get only featured and active services for the landing page
        $services = Service::active()
            ->featured()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(6)
            ->get();

        return view('index', compact('services'));
    }
}
