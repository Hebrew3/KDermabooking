<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Get the redirect URI for Google OAuth.
     * Ensures consistency between redirect and callback.
     */
    private function getRedirectUri()
    {
        // First, try to get from env variable
        $redirectUri = env('GOOGLE_REDIRECT_URI');
        
        if ($redirectUri) {
            return rtrim($redirectUri, '/');
        }
        
        // Otherwise, construct from APP_URL
        $appUrl = rtrim(env('APP_URL', config('app.url')), '/');
        
        // If APP_URL doesn't include port but we're running on a specific port, detect it
        $port = request()->getPort();
        if ($port && $port != 80 && $port != 443) {
            $parsedUrl = parse_url($appUrl);
            if (!isset($parsedUrl['port'])) {
                $appUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . ':' . $port;
            }
        }
        
        return $appUrl . '/auth/google/callback';
    }

    /**
     * Redirect to Google OAuth provider.
     */
    public function redirectToGoogle()
    {
        try {
            // Check if we have the required configuration
            if (!config('services.google.client_id') || !config('services.google.client_secret')) {
                \Log::error('Google OAuth configuration missing');
                return redirect()->route('login')->with('error', 'Google authentication is not properly configured.');
            }

            // Get redirect URI (ensure consistency)
            $redirectUri = $this->getRedirectUri();
            
            \Log::info('Google OAuth redirect', [
                'redirect_uri' => $redirectUri,
                'app_url' => config('app.url'),
                'request_url' => request()->fullUrl(),
                'request_port' => request()->getPort(),
                'client_id' => config('services.google.client_id') ? 'set' : 'missing'
            ]);
            
            // Use redirectUrl() to explicitly set the redirect URI
            /** @var \Laravel\Socialite\Two\GoogleProvider $socialite */
            $socialite = Socialite::driver('google');
            return $socialite->redirectUrl($redirectUri)->redirect();
        } catch (\Exception $e) {
            \Log::error('Google OAuth redirect error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'redirect_uri' => $this->getRedirectUri()
            ]);
            return redirect()->route('login')->with('error', 'Google authentication failed. Please try again.');
        }
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback()
    {
        try {
            // Check if we have the required configuration
            if (!config('services.google.client_id') || !config('services.google.client_secret')) {
                \Log::error('Google OAuth configuration missing');
                return redirect()->route('login')->with('error', 'Google authentication is not properly configured.');
            }

            // Get redirect URI (must match what was used in redirect)
            $redirectUri = $this->getRedirectUri();
            
            \Log::info('Google OAuth callback', [
                'redirect_uri' => $redirectUri,
                'app_url' => config('app.url'),
                'request_url' => request()->fullUrl(),
                'request_port' => request()->getPort()
            ]);

            // Use redirectUrl() to match the redirect URI used in redirectToGoogle()
            /** @var \Laravel\Socialite\Two\GoogleProvider $socialite */
            $socialite = Socialite::driver('google');
            /** @var \Laravel\Socialite\Contracts\User $googleUser */
            $googleUser = $socialite->redirectUrl($redirectUri)->user();
            
            \Log::info('Google user data received', [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);
            
            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                // User exists with Google ID, log them in
                Auth::login($user);
                \Log::info('Existing Google user logged in', ['user_id' => $user->id]);
                return $this->redirectBasedOnRole($user);
            }
            
            // Check if user exists with this email
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // User exists with email, link Google account
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    // Don't overwrite existing profile_picture unless they don't have one
                    'profile_picture' => $existingUser->profile_picture ?: null,
                ]);
                
                Auth::login($existingUser);
                \Log::info('Existing user linked with Google', ['user_id' => $existingUser->id]);
                return $this->redirectBasedOnRole($existingUser);
            }
            
            // Create new user
            $googleName = $googleUser->getName();
            $newUser = User::create([
                'first_name' => $googleName ? explode(' ', $googleName)[0] : 'User',
                'last_name' => $googleName && count(explode(' ', $googleName)) > 1 
                    ? implode(' ', array_slice(explode(' ', $googleName), 1)) 
                    : '',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(24)), // Random password since they'll use Google
                'role' => 'client', // Default role for Google sign-ups
                'gender' => null, // Will be filled later by user
                'mobile_number' => null, // Will be filled later by user
                'address' => null, // Will be filled later by user
                'birth_date' => null, // Will be filled later by user
                'email_verified_at' => now(), // Google emails are pre-verified
            ]);
            
            Auth::login($newUser);
            \Log::info('New Google user created and logged in', ['user_id' => $newUser->id]);
            return $this->redirectBasedOnRole($newUser);
            
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            \Log::error('Google OAuth Invalid State Exception', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Authentication session expired. Please try again.');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Handle redirect_uri_mismatch and other OAuth errors
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 0;
            $responseBody = $response ? $response->getBody()->getContents() : '';
            
            \Log::error('Google OAuth Client Exception', [
                'status_code' => $statusCode,
                'response' => $responseBody,
                'error' => $e->getMessage(),
                'redirect_uri' => config('services.google.redirect'),
                'app_url' => config('app.url')
            ]);
            
            // Check if it's a redirect_uri_mismatch error
            if (strpos($responseBody, 'redirect_uri_mismatch') !== false || strpos($e->getMessage(), 'redirect_uri_mismatch') !== false) {
                $redirectUri = config('services.google.redirect');
                return redirect()->route('login')->with('error', 
                    'Google Sign-In Configuration Error: The redirect URI in your Google Cloud Console does not match the application redirect URI. ' .
                    'Please ensure the following URI is added to your Google OAuth 2.0 Client ID authorized redirect URIs: ' . $redirectUri
                );
            }
            
            return redirect()->route('login')->with('error', 'Google authentication failed. Please check your configuration.');
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'redirect_uri' => config('services.google.redirect'),
                'app_url' => config('app.url')
            ]);
            
            // Check if error message contains redirect_uri_mismatch
            if (strpos($e->getMessage(), 'redirect_uri_mismatch') !== false) {
                $redirectUri = config('services.google.redirect');
                return redirect()->route('login')->with('error', 
                    'Google Sign-In Configuration Error: The redirect URI in your Google Cloud Console does not match. ' .
                    'Required redirect URI: ' . $redirectUri . ' ' .
                    'Please add this exact URI to your Google OAuth 2.0 Client ID authorized redirect URIs.'
                );
            }
            
            return redirect()->route('login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Redirect user based on their role after successful authentication.
     */
    private function redirectBasedOnRole($user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->first_name . '!');
        } elseif ($user->isStaffMember()) {
            return redirect()->route('staff.dashboard')->with('success', 'Welcome back, ' . $user->first_name . '!');
        } else {
            // Client role - redirect to landing page
            return redirect()->route('index.landing')->with('success', 'Welcome, ' . $user->first_name . '!');
        }
    }
}
