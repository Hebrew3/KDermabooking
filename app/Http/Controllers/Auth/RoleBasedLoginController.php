<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RoleBasedLoginController extends Controller
{
    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on user role
            return $this->redirectBasedOnRole(Auth::user());
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Admin ' . $user->first_name . '!');
            
            case 'nurse':
            case 'aesthetician':
                return redirect()->route('staff.dashboard')->with('success', 'Welcome back, ' . $user->first_name . '!');
            
            case 'client':
            default:
                // Redirect clients to landing page with authenticated features
                return redirect()->route('index.landing')->with('success', 'Welcome back, ' . $user->first_name . '!');
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been successfully logged out.');
    }
}
