<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'mobile_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'birth_date' => ['required', 'date', 'before:today'],
            'role' => ['nullable', 'in:admin,nurse,aesthetician,client'],
        ]);

        // Generate 6-digit verification code
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'mobile_number' => $request->mobile_number,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'role' => $request->role ?? 'client', // Default to client if not specified
            'email_verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Send verification code email
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode, $user->first_name));
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
            // Continue even if email fails - user can request resend
        }

        // Redirect to verification page instead of login
        return redirect(route('verification.show', ['email' => $user->email]))
            ->with('success', 'Registration successful! Please check your email for the verification code.');
    }

    /**
     * Show the email verification form.
     */
    public function showVerificationForm(Request $request): View|RedirectResponse
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect(route('login'))
                ->with('error', 'Please provide your email address.');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect(route('login'))
                ->with('error', 'User not found.');
        }

        if ($user->email_verified_at) {
            return redirect(route('login'))
                ->with('success', 'Your email is already verified. Please log in.');
        }

        return view('auth.verify-email-code', compact('email'));
    }

    /**
     * Verify the email with the verification code.
     */
    public function verifyEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'verification_code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Check if code matches
        if ($user->email_verification_code !== $request->verification_code) {
            return back()->withErrors(['verification_code' => 'Invalid verification code. Please try again.']);
        }

        // Check if code has expired
        if ($user->verification_code_expires_at && Carbon::now()->gt($user->verification_code_expires_at)) {
            return back()->withErrors(['verification_code' => 'Verification code has expired. Please request a new one.']);
        }

        // Verify email
        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        event(new Registered($user));

        return redirect(route('login'))
            ->with('success', 'Email verified successfully! You can now log in.');
    }

    /**
     * Resend verification code.
     */
    public function resendVerificationCode(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if ($user->email_verified_at) {
            return redirect(route('login'))
                ->with('success', 'Your email is already verified. Please log in.');
        }

        // Generate new verification code
        $verificationCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->email_verification_code = $verificationCode;
        $user->verification_code_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        // Send verification code email
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode, $user->first_name));
            return back()->with('success', 'Verification code has been resent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to resend verification email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again later.']);
        }
    }
}
