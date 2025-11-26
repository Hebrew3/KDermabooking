<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect based on user role
        return $this->redirectBasedOnRole(Auth::user());
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole($user)
    {
        $intendedUrl = session('url.intended');
        
        // If there's an intended URL, redirect there first
        if ($intendedUrl) {
            return redirect()->intended();
        }

        // Otherwise, redirect based on role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin ' . $user->first_name . '!');
            
            case 'staff':
                return redirect()->route('staff.dashboard')->with('success', 'Welcome back, ' . $user->first_name . '!');
            
            case 'client':
            default:
                // Redirect clients to landing page with authenticated features
                return redirect()->route('index.landing')->with('success', 'Welcome back, ' . $user->first_name . '!');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been successfully logged out.');
    }
}
