<x-app-layout>
<x-mobile-header />

    <!-- Include Client Sidebar -->
    <x-client-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Our Services</h1>
                <p class="text-gray-600 mt-2">Discover our range of professional dermatology and wellness services</p>
            </div>

            <!-- Featured Services -->
            @if($featuredServices->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Featured Services</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredServices as $service)
                    <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl p-6 border border-pink-200 hover:shadow-lg transition-all duration-300">
                        @if($service->image)
                        <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="w-full h-32 object-cover rounded-lg mb-4">
                        @endif
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-900">{{ $service->name }}</h3>
                            <span class="bg-pink-100 text-pink-800 text-xs font-medium px-2 py-1 rounded-full">Featured</span>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">{{ $service->short_description }}</p>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                <span class="font-medium text-pink-600">{{ $service->formatted_price }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $service->formatted_duration }}</span>
                            </div>
                            <a href="{{ route('client.appointments.create', ['service' => $service->id]) }}" 
                               class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                                Book Now
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <form method="GET" action="{{ route('client.services') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Services</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search services..." class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" id="category" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="price_min" class="block text-sm font-medium text-gray-700 mb-2">Min Price</label>
                        <input type="number" name="price_min" id="price_min" value="{{ request('price_min') }}" placeholder="₱0" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <div>
                        <label for="price_max" class="block text-sm font-medium text-gray-700 mb-2">Max Price</label>
                        <input type="number" name="price_max" id="price_max" value="{{ request('price_max') }}" placeholder="₱10000" class="w-full border-gray-300 rounded-lg focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <div class="md:col-span-4 flex justify-end space-x-3">
                        <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Apply Filters
                        </button>
                        <a href="{{ route('client.services') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Services Grid -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">All Services</h2>
                </div>

                @if($services->count() > 0)
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($services as $service)
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 group">
                            @if($service->image)
                            <div class="relative overflow-hidden">
                                <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                @if($service->is_featured)
                                <span class="absolute top-3 left-3 bg-pink-500 text-white text-xs font-medium px-2 py-1 rounded-full">Featured</span>
                                @endif
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
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('client.services.show', $service) }}" 
                                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm text-center transition-colors duration-200">
                                        View Details
                                    </a>
                                    <a href="{{ route('client.appointments.create', ['service' => $service->id]) }}" 
                                       class="flex-1 bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg text-sm text-center transition-colors duration-200">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $services->withQueryString()->links() }}
                </div>
                @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No services found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
