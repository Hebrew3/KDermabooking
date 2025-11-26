<x-app-layout>
<x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen bg-gray-50">
        <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Assigned Services</h1>
                    <p class="text-gray-600 mt-2">Services you are qualified and assigned to perform</p>
                </div>
                <div class="bg-gradient-to-r from-pink-500 to-rose-500 text-white px-4 py-2 rounded-lg">
                    <span class="font-semibold">{{ $assignedServices->total() }}</span> Services Assigned
                </div>
            </div>
        </div>

        <!-- Specializations Overview -->
        @if($specializations)
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Your Specializations</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($specializations as $specialization)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ ucwords(str_replace('_', ' ', $specialization)) }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Primary Services -->
        @if($primaryServices->count() > 0)
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-green-900 mb-2">Primary Services</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($primaryServices as $service)
                        <div class="bg-white border border-green-200 rounded-lg p-3">
                            <h4 class="font-medium text-gray-900">{{ $service->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $service->formatted_price }}</p>
                            @if($service->pivot->custom_price)
                                <p class="text-sm text-green-600 font-medium">
                                    Your Rate: ₱{{ number_format($service->pivot->custom_price, 2) }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Services Grid -->
        @if($assignedServices->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($assignedServices as $service)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Service Image -->
                        <div class="h-48 bg-gradient-to-br from-pink-100 to-rose-100 relative overflow-hidden">
                            @if($service->image_url)
                                <img src="{{ $service->image_url }}" alt="{{ $service->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                              d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Badges -->
                            <div class="absolute top-3 left-3 flex flex-col gap-2">
                                @if($service->pivot->is_primary)
                                    <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                        Primary
                                    </span>
                                @endif
                                <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                    Level {{ $service->pivot->proficiency_level }}
                                </span>
                            </div>
                        </div>

                        <!-- Service Content -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $service->name }}</h3>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $service->short_description }}</p>

                            <!-- Service Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Duration:</span>
                                    <span class="font-medium">{{ $service->formatted_duration }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Base Price:</span>
                                    <span class="font-medium">{{ $service->formatted_price }}</span>
                                </div>
                                @if($service->pivot->custom_price)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">Your Rate:</span>
                                        <span class="font-medium text-green-600">₱{{ number_format($service->pivot->custom_price, 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">Proficiency:</span>
                                    <span class="font-medium">{{ $service->pivot->proficiency_text }}</span>
                                </div>
                            </div>

                            <!-- Booking Statistics -->
                            <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-gray-50 rounded-lg">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-pink-600">{{ $service->total_bookings }}</div>
                                    <div class="text-xs text-gray-600">Total Bookings</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-600">{{ $service->completed_bookings }}</div>
                                    <div class="text-xs text-gray-600">Completed</div>
                                </div>
                            </div>

                            <!-- Notes -->
                            @if($service->pivot->notes)
                                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                                    <h4 class="text-sm font-medium text-blue-900 mb-1">Assignment Notes</h4>
                                    <p class="text-sm text-blue-800">{{ $service->pivot->notes }}</p>
                                </div>
                            @endif

                            <!-- Action Button -->
                            <a href="{{ route('staff.services.show', $service) }}" 
                               class="w-full bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white py-2 px-4 rounded-lg font-medium text-center block transition-all duration-200">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($assignedServices->hasPages())
                <div class="mt-8">
                    {{ $assignedServices->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Services Assigned</h3>
                <p class="text-gray-600 mb-6">You haven't been assigned to any services yet. Contact your administrator to get started.</p>
                <a href="{{ route('staff.services') }}" 
                   class="inline-flex items-center px-4 py-2 bg-pink-500 hover:bg-pink-600 text-white font-medium rounded-lg transition-colors duration-200">
                    View All Services
                </a>
            </div>
        @endif
        </div>
    </div>
</x-app-layout>
