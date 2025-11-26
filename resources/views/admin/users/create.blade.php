<x-app-layout>
    <x-mobile-header />

    <!-- Include Admin Sidebar -->
    <x-admin-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <a href="{{ route('admin.users.index') }}" 
                       class="text-pink-600 hover:text-pink-800 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
                </div>
                <p class="text-gray-600">Add a new user to the system</p>
            </div>

            <!-- Create User Form -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Personal Information Section -->
                    <div class="bg-gray-50 p-6 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Personal Information
                        </h3>

                        <!-- Name Fields Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                <input type="text" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="{{ old('first_name') }}" 
                                       required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('first_name') border-red-500 @enderror">
                                @error('first_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                                <input type="text" 
                                       id="middle_name" 
                                       name="middle_name" 
                                       value="{{ old('middle_name') }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('middle_name') border-red-500 @enderror">
                                @error('middle_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                <input type="text" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="{{ old('last_name') }}" 
                                       required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('last_name') border-red-500 @enderror">
                                @error('last_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Gender and Birth Date Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Gender -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                                <select id="gender" 
                                        name="gender" 
                                        required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('gender') border-red-500 @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Birth Date -->
                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">Birth Date *</label>
                                <input type="date" 
                                       id="birth_date" 
                                       name="birth_date" 
                                       value="{{ old('birth_date') }}" 
                                       required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('birth_date') border-red-500 @enderror">
                                @error('birth_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="bg-gray-50 p-6 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Contact Information
                        </h3>

                        <!-- Email Address -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Mobile Number -->
                        <div class="mb-4">
                            <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number *</label>
                            <input type="tel" 
                                   id="mobile_number" 
                                   name="mobile_number" 
                                   value="{{ old('mobile_number') }}" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('mobile_number') border-red-500 @enderror">
                            @error('mobile_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                            <textarea id="address" 
                                      name="address" 
                                      required 
                                      rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('address') border-red-500 @enderror resize-none">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Account Settings Section -->
                    <div class="bg-gray-50 p-6 rounded-xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Account Settings
                        </h3>

                        <div id="passwordFields" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>
                        </div>

                        <div id="autoPasswordNotice" class="hidden bg-blue-50 border border-blue-200 text-blue-900 text-sm rounded-xl p-4 mb-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12A9 9 0 11 3 12a9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="font-semibold">Password is generated automatically</p>
                                    <p>Staff members receive a secure password via email along with their login instructions. You don't need to set it manually.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Role and Profile Picture -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                                <select id="role" 
                                        name="role" 
                                        required 
                                        onchange="handleRoleChange()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('role') border-red-500 @enderror">
                                    <option value="">Select Role</option>
                                    <option value="nurse" {{ old('role') === 'nurse' ? 'selected' : '' }}>Nurse</option>
                                    <option value="aesthetician" {{ old('role') === 'aesthetician' ? 'selected' : '' }}>Aesthetician</option>
                                    <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>Client</option>
                                </select>
                                @error('role')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Profile Picture -->
                            <div>
                                <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                                <input type="file" 
                                       id="profile_picture" 
                                       name="profile_picture" 
                                       accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('profile_picture') border-red-500 @enderror">
                                <p class="text-xs text-gray-500 mt-1">Max size: 2MB. Supported formats: JPEG, PNG, JPG, GIF</p>
                                @error('profile_picture')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Assignment Section (for Nurse and Aesthetician) -->
                        <div id="serviceAssignmentSection" class="mt-6 hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <div class="flex items-center space-x-2 mb-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-blue-900">
                                        <span id="roleServiceText"></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Nurse Service Assignment (Auto Gluta) -->
                            <div id="nurseServiceSection" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Assigned Service
                                    </span>
                                </label>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <p class="text-sm font-semibold text-green-900">Gluta</p>
                                    <p class="text-xs text-green-700 mt-1">This service will be automatically assigned to the nurse</p>
                                </div>
                            </div>

                            <!-- Aesthetician Service Assignment (Multiple Selection) -->
                            <div id="aestheticianServiceSection" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Select Services <span class="text-red-500 ml-1">*</span>
                                    </span>
                                </label>
                                <div class="border-2 border-gray-200 rounded-xl p-4 max-h-80 overflow-y-auto bg-gray-50">
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($services as $service)
                                        <label class="flex items-start space-x-3 p-3 hover:bg-white rounded-lg cursor-pointer transition-colors border border-transparent hover:border-pink-200 hover:shadow-sm group">
                                            <input type="checkbox" 
                                                   name="service_ids[]" 
                                                   value="{{ $service->id }}"
                                                   class="mt-1 h-5 w-5 text-pink-600 focus:ring-pink-500 border-gray-300 rounded cursor-pointer transition-all"
                                                   {{ (is_array(old('service_ids')) && in_array($service->id, old('service_ids'))) ? 'checked' : '' }}>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">{{ $service->name }}</span>
                                                    <span class="text-sm font-bold text-pink-600 ml-2">{{ $service->formatted_price }}</span>
                                                </div>
                                                <div class="flex items-center space-x-3 mt-1">
                                                    <div class="flex items-center text-xs text-gray-500">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $service->formatted_duration }}
                                                    </div>
                                                    @if($service->category)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-700">
                                                        {{ ucfirst($service->category) }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="mt-3 text-xs text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Select one or more services that this aesthetician can perform
                                </p>
                                @error('service_ids')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </a>
                        <x-confirm-button action="create" title="Create User" text="Are you sure you want to create this user account?" class="px-6 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg font-medium">
                            Create User
                        </x-confirm-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function handleRoleChange() {
            const roleSelect = document.getElementById('role');
            const selectedRole = roleSelect.value;
            const serviceSection = document.getElementById('serviceAssignmentSection');
            const nurseSection = document.getElementById('nurseServiceSection');
            const aestheticianSection = document.getElementById('aestheticianServiceSection');
            const roleServiceText = document.getElementById('roleServiceText');
            const passwordFields = document.getElementById('passwordFields');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const autoPasswordNotice = document.getElementById('autoPasswordNotice');

            const isStaffRole = selectedRole === 'nurse' || selectedRole === 'aesthetician';

            if (isStaffRole) {
                passwordFields.classList.add('hidden');
                autoPasswordNotice.classList.remove('hidden');
                passwordInput.removeAttribute('required');
                passwordConfirmationInput.removeAttribute('required');
                passwordInput.value = '';
                passwordConfirmationInput.value = '';
            } else {
                passwordFields.classList.remove('hidden');
                autoPasswordNotice.classList.add('hidden');
                passwordInput.setAttribute('required', 'required');
                passwordConfirmationInput.setAttribute('required', 'required');
            }

            if (selectedRole === 'nurse') {
                serviceSection.classList.remove('hidden');
                nurseSection.classList.remove('hidden');
                aestheticianSection.classList.add('hidden');
                roleServiceText.innerHTML = '<strong>Nurse Role:</strong> Gluta service will be automatically assigned';
            } else if (selectedRole === 'aesthetician') {
                serviceSection.classList.remove('hidden');
                nurseSection.classList.add('hidden');
                aestheticianSection.classList.remove('hidden');
                roleServiceText.innerHTML = '<strong>Aesthetician Role:</strong> Please select the services this aesthetician can perform';
            } else {
                serviceSection.classList.add('hidden');
                nurseSection.classList.add('hidden');
                aestheticianSection.classList.add('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            handleRoleChange();
        });
    </script>
</x-app-layout>