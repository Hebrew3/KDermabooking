<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Picture Upload -->
        <div class="flex items-center space-x-6">
            <div class="shrink-0" id="profile-preview">
                <x-user-avatar :user="$user" size="lg" />
            </div>
            <div class="flex-1">
                <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">
                    Profile Picture
                </label>
                <div class="flex items-center space-x-4">
                    <input type="file" 
                           id="profile_picture" 
                           name="profile_picture" 
                           accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                    @if($user->profile_picture)
                        <button type="button" onclick="removeProfilePicture()" 
                                class="text-sm text-red-600 hover:text-red-800">
                            Remove
                        </button>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                @error('profile_picture')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autofocus autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>
            <div>
                <x-input-label for="middle_name" :value="__('Middle Name')" />
                <x-text-input id="middle_name" name="middle_name" type="text" class="mt-1 block w-full" :value="old('middle_name', $user->middle_name)" autocomplete="additional-name" />
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>
            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="mobile_number" :value="__('Mobile Number')" />
                <x-text-input id="mobile_number" name="mobile_number" type="tel" class="mt-1 block w-full" :value="old('mobile_number', $user->mobile_number)" required autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('mobile_number')" />
            </div>
            <div>
                <x-input-label for="gender" :value="__('Gender')" />
                <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>
        </div>

        <div>
            <x-input-label for="birth_date" :value="__('Birth Date')" />
            <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
        </div>

        <div>
            <x-input-label for="address" :value="__('Address')" />
            <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('address', $user->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <!-- JavaScript for profile picture handling -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileInput = document.getElementById('profile_picture');
            const profilePreview = document.getElementById('profile-preview');

            // Handle file selection and preview
            profileInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilePreview.innerHTML = `
                            <img class="h-16 w-16 object-cover rounded-full ring-2 ring-pink-200" 
                                 src="${e.target.result}" 
                                 alt="Profile Preview">
                        `;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Function to remove profile picture
        function removeProfilePicture() {
            Swal.fire({
                title: 'Remove Profile Picture?',
                text: 'Are you sure you want to remove your profile picture?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ec4899',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                // Create a hidden input to indicate removal
                const form = document.querySelector('form[action="{{ route('profile.update') }}"]');
                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_profile_picture';
                removeInput.value = '1';
                form.appendChild(removeInput);
                
                // Reset the preview to default
                const profilePreview = document.getElementById('profile-preview');
                profilePreview.innerHTML = `
                    <div class="h-16 w-16 bg-pink-100 rounded-full flex items-center justify-center ring-2 ring-pink-200">
                        <svg class="h-8 w-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                `;
                
                // Clear the file input
                document.getElementById('profile_picture').value = '';
                
                // Hide the remove button
                event.target.style.display = 'none';
                }
            });
        }
    </script>
</section>
