<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfWrongRole
{
    /**
     * Handle an incoming request.
     * Redirects admin and staff users away from client-facing appointment booking routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply this middleware to authenticated users
        if (auth()->check()) {
            $user = auth()->user();
            
            // Redirect admin and staff away from client appointment booking routes
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('info', 'Admin users should use the admin panel for appointment management.');
            }
            
            if ($user->isStaffMember()) {
                return redirect()->route('staff.dashboard')
                    ->with('info', 'Staff users should use the staff panel for appointment management.');
            }
            
            // Only clients can access these routes
        }

        return $next($request);
    }
}
