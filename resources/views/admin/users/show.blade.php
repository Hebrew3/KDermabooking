<x-app-layout>
    <x-mobile-header />

    <!-- Include Admin Sidebar -->
    <x-admin-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <a href="{{ route('admin.users.index') }}" 
                           class="text-pink-600 hover:text-pink-800 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">User Details: {{ $user->name }}</h1>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Edit User
                        </a>
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                    Delete User
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <p class="text-gray-600">Complete user information and activity</p>
            </div>


            <!-- User Information Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- User Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="text-center">
                            @if($user->profile_picture)
                                <img class="h-32 w-32 rounded-full mx-auto object-cover" 
                                     src="{{ asset('storage/' . $user->profile_picture) }}" 
                                     alt="{{ $user->name }}">
                            @else
                                <div class="h-32 w-32 rounded-full bg-gray-300 mx-auto flex items-center justify-center">
                                    <span class="text-4xl text-gray-700 font-bold">{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                                </div>
                            @endif
                            
                            <h2 class="mt-4 text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                            
                            @php
                                $roleColors = [
                                    'admin' => 'bg-red-100 text-red-800',
                                    'staff' => 'bg-blue-100 text-blue-800',
                                    'client' => 'bg-green-100 text-green-800'
                                ];
                            @endphp
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            
                            <div class="mt-4">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="mt-6 space-y-2">
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="w-full">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-lg transition-colors duration-200">
                                        {{ $user->email_verified_at ? 'Deactivate' : 'Activate' }} User
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">First Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->first_name }}</p>
                            </div>
                            
                            @if($user->middle_name)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Middle Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->middle_name }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->last_name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Gender</label>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($user->gender) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Birth Date</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $user->birth_date ? $user->birth_date->format('F j, Y') : 'Not specified' }}
                                    @if($user->birth_date)
                                        <span class="text-gray-500">({{ $user->birth_date->age }} years old)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Contact Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email Address</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a href="mailto:{{ $user->email }}" class="text-pink-600 hover:text-pink-800">
                                        {{ $user->email }}
                                    </a>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Mobile Number</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <a href="tel:{{ $user->mobile_number }}" class="text-pink-600 hover:text-pink-800">
                                        {{ $user->mobile_number }}
                                    </a>
                                </p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Address</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $user->address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="h-5 w-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Account Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">User ID</label>
                                <p class="mt-1 text-sm text-gray-900 font-mono">#{{ $user->id }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Role</label>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($user->role) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Email Verified</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($user->email_verified_at)
                                        <span class="text-green-600">Verified</span>
                                        <span class="text-gray-500">({{ $user->email_verified_at->format('M j, Y') }})</span>
                                    @else
                                        <span class="text-red-600">Not verified</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Account Created</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $user->created_at->format('M j, Y \\a\\t g:i A') }}
                                    <span class="text-gray-500">({{ $user->created_at->diffForHumans() }})</span>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $user->updated_at->format('M j, Y \\a\\t g:i A') }}
                                    <span class="text-gray-500">({{ $user->updated_at->diffForHumans() }})</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>