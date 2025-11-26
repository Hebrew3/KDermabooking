<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
            <p class="text-gray-600 mt-2">Configure system preferences and settings</p>
        </div>

        <!-- General Settings -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">General Settings</h2>
                <form method="POST" action="{{ route('admin.settings.update-general') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
                            <input type="text" name="app_name" id="app_name" value="{{ old('app_name', $settings['app_name']) }}" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                            <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                        </div>
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                            <select name="timezone" id="timezone" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="Asia/Manila" {{ $settings['timezone'] == 'Asia/Manila' ? 'selected' : '' }}>Asia/Manila</option>
                                <option value="UTC" {{ $settings['timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ $settings['timezone'] == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                            </select>
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                            <select name="currency" id="currency" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                                <option value="PHP" {{ $settings['currency'] == 'PHP' ? 'selected' : '' }}>PHP (₱)</option>
                                <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                            
                            <!-- Current Logo Preview -->
                            @if(isset($settings['logo']) && $settings['logo'])
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Current Logo:</p>
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ asset('storage/' . $settings['logo']) }}" alt="Current Logo" class="h-16 w-16 object-cover rounded-lg border border-gray-200 shadow-sm">
                                        <div class="text-sm text-gray-500">
                                            <p>Click "Choose File" to upload a new logo</p>
                                            <p class="text-xs">Recommended: 200x200px, PNG/JPG format</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="h-16 w-16 bg-gray-100 rounded-lg border border-gray-200 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <p>No logo uploaded yet</p>
                                            <p class="text-xs">Recommended: 200x200px, PNG/JPG format</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- File Input -->
                            <div class="relative">
                                <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Maximum file size: 2MB. Supported formats: JPEG, PNG, JPG, GIF</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="3" required class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('address', $settings['address']) }}</textarea>
                    </div>
                    <div class="mt-6">
                        <label for="app_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="app_description" id="app_description" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">{{ old('app_description', $settings['app_description']) }}</textarea>
                    </div>
                    <div class="mt-6">
                        <x-confirm-button action="update" title="Update Settings" text="Are you sure you want to save these settings?" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg">
                            Save General Settings
                        </x-confirm-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
