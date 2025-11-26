<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the client profile view.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        
        // Return appropriate view based on user role
        if ($user->isStaffMember()) {
            return view('staff.profile', [
                'user' => $user,
            ]);
        }
        
        return view('client.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle profile picture removal
        if ($request->has('remove_profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = null;
        }
        // Handle profile picture upload
        elseif ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the staff profile information.
     */
    public function updateStaff(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->user()->id),
            ],
            'mobile_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'birth_date' => ['required', 'date', 'before:today'],
        ]);

        $user = $request->user();
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('staff.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update the client profile information.
     */
    public function updateClient(Request $request): RedirectResponse
    {
        $user = $request->user();
        $action = $request->input('action', 'personal_info');

        if ($action === 'profile_picture') {
            return $this->updateProfilePicture($request);
        } elseif ($action === 'remove_picture') {
            return $this->removeProfilePicture($request);
        } elseif ($action === 'change_password') {
            return $this->updatePassword($request);
        } else {
            return $this->updatePersonalInfo($request);
        }
    }

    /**
     * Update profile picture.
     */
    private function updateProfilePicture(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = $request->user();

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        $user->profile_picture = $path;
        $user->save();

        return Redirect::route('client.profile')->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Remove profile picture.
     */
    private function removeProfilePicture(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
            $user->profile_picture = null;
            $user->save();
        }

        return Redirect::route('client.profile')->with('success', 'Profile picture removed successfully!');
    }

    /**
     * Update personal information.
     */
    private function updatePersonalInfo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,other'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->user()->id),
            ],
            'mobile_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'birth_date' => ['required', 'date', 'before:today'],
        ]);

        $user = $request->user();
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('client.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password.
     */
    private function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return Redirect::route('client.profile')->with('success', 'Password updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
