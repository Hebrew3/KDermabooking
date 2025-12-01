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

        // Verify reCAPTCHA if enabled
        if (config('services.recaptcha.site_key') && config('services.recaptcha.secret_key')) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            
            if (!$recaptchaResponse) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Please complete the reCAPTCHA verification.',
                ]);
            }

            $verifyResponse = $this->verifyRecaptcha($recaptchaResponse, $request->ip());
            
            if (!$verifyResponse['success']) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.',
                ]);
            }
        }

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
     * Verify reCAPTCHA response with Google
     */
    protected function verifyRecaptcha($response, $remoteIp = null)
    {
        $secretKey = config('services.recaptcha.secret_key');
        
        if (!$secretKey) {
            return ['success' => false];
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $response,
        ];

        if ($remoteIp) {
            $data['remoteip'] = $remoteIp;
        }

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result === false) {
            return ['success' => false];
        }

        return json_decode($result, true);
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
