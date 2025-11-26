<x-app-layout>
<x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">All Services</h1>
                        <p class="text-gray-600 mt-2">Browse all available services - assigned services are highlighted</p>
                    </div>
                    <a href="{{ route('staff.my-services') }}" 
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-check-circle mr-2"></i>My Assigned Services
                    </a>
                </div>
                
                <!-- Quick Stats -->
                <div class="flex items-center space-x-6 mt-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Assigned to you ({{ count($assignedServiceIds) }})</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                        <span class="text-sm text-gray-600">Not assigned ({{ $services->total() - count($assignedServiceIds) }})</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Total: {{ $services->total() }} services
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <form method="GET" action="{{ route('staff.services') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search services..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <div>
                        <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Services Grid -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Services ({{ $services->total() }} total)</h2>
                </div>

                @if($services->count() > 0)
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($services as $service)
                        @php
                            $isAssigned = in_array($service->id, $assignedServiceIds);
                        @endphp
                        <div class="bg-white border-2 {{ $isAssigned ? 'border-green-200 bg-green-50' : 'border-gray-200' }} rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 group">
                            @if($service->image)
                            <div class="relative overflow-hidden">
                                <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute top-3 left-3 flex flex-col gap-2">
                                    @if($service->is_featured)
                                    <span class="bg-pink-500 text-white text-xs font-medium px-2 py-1 rounded-full">Featured</span>
                                    @endif
                                    @if($isAssigned)
                                    <span class="bg-green-500 text-white text-xs font-medium px-2 py-1 rounded-full">✓ Assigned</span>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="h-48 bg-gradient-to-br from-pink-100 to-rose-100 relative flex items-center justify-center">
                                <svg class="w-16 h-16 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                                <div class="absolute top-3 left-3 flex flex-col gap-2">
                                    @if($service->is_featured)
                                    <span class="bg-pink-500 text-white text-xs font-medium px-2 py-1 rounded-full">Featured</span>
                                    @endif
                                    @if($isAssigned)
                                    <span class="bg-green-500 text-white text-xs font-medium px-2 py-1 rounded-full">✓ Assigned</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $service->name }}</h3>
                                    @if($service->category)
                                    <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded-full">{{ ucfirst($service->category) }}</span>
                                    @endif
                                </div>

                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $service->short_description }}</p>

                                <div class="flex items-center justify-between mb-4">
                                    <div class="text-sm text-gray-500">
                                        <div class="font-medium text-pink-600 text-lg">{{ $service->formatted_price }}</div>
                                        <div>{{ $service->formatted_duration }}</div>
                                    </div>
                                </div>

                                <!-- Assignment Status -->
                                @if($isAssigned)
                                    <div class="mb-4 p-3 bg-green-100 border border-green-200 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                <span class="text-sm font-medium text-green-800">You are assigned to this service</span>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-4 p-3 bg-gray-100 border border-gray-200 rounded-lg">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                                <span class="text-sm text-gray-600">Not assigned to you</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Service Statistics (only show for assigned services) -->
                                @if($isAssigned)
                                    <div class="grid grid-cols-2 gap-4 mb-4 p-3 bg-blue-50 rounded-lg">
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-blue-900">{{ $service->total_bookings }}</div>
                                            <div class="text-xs text-blue-600">Your Bookings</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-semibold text-green-600">{{ $service->completed_bookings }}</div>
                                            <div class="text-xs text-blue-600">Completed</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex space-x-2">
                                    <a href="{{ route('staff.services.show', $service) }}"
                                       class="flex-1 {{ $isAssigned ? 'bg-green-100 hover:bg-green-200 text-green-800' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }} px-4 py-2 rounded-lg text-sm text-center transition-colors duration-200">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $services->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                    <p class="text-gray-500">No services found</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
