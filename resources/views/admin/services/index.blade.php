<x-app-layout>
    <x-mobile-header />

    <!-- Include Admin Sidebar -->
    <x-admin-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">Services Management</h1>
                        <p class="text-gray-600 mt-2 text-lg">Manage all available services and treatments</p>
                    </div>
                    <div class="hidden md:flex items-center space-x-2 text-sm text-gray-500">
                        <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Total: <strong class="text-pink-600">{{ $services->total() }}</strong> services</span>
                    </div>
                </div>
            </div>

            <!-- Services Content -->
            <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-6 py-4">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="text-white">
                            <h2 class="text-xl font-semibold">All Services</h2>
                            <p class="text-pink-100 text-sm mt-1">{{ $services->total() }} total services available</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="{{ route('admin.services.export', request()->query()) }}"
                               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2 rounded-lg transition-all duration-200 text-center flex items-center justify-center space-x-2 border border-white/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Export CSV</span>
                            </a>
                            <a href="{{ route('admin.services.create') }}"
                               class="bg-white hover:bg-gray-50 text-pink-600 px-6 py-2 rounded-lg transition-all duration-200 text-center font-semibold shadow-lg flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add New Service</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6">

                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('admin.services.index') }}" class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center space-x-1">
                                <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>Search</span>
                            </label>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search services..."
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 bg-white">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                            <select id="category"
                                    name="category"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 bg-white">
                                <option value="">All Categories</option>
                                @php
                                    $categoryMap = [
                                        // New categories
                                        'complete_facial' => 'Complete Facial',
                                        'laser_hair_removal' => 'Laser Hair Removal',
                                        'laser_skin_treatment' => 'Laser Skin Treatment',
                                        'skin_peeling' => 'Skin Peeling',
                                        'tightening_contouring' => 'Tightening and Contouring',
                                        'mesolipo' => 'Mesolipo',
                                        'gluta_drip' => 'Gluta Drip',
                                        'waxing' => 'Waxing',
                                        'pathologic_non_invasive' => 'Pathologic / Non-Invasive',
                                        // Old categories (for backward compatibility)
                                        'facial' => 'Complete Facial',
                                        'laser' => 'Laser Skin Treatment',
                                        'injection' => 'Mesolipo',
                                        'peel' => 'Skin Peeling',
                                        'consultation' => 'Pathologic / Non-Invasive',
                                        'other' => 'Other',
                                    ];
                                @endphp
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ $categoryMap[$category] ?? ucfirst(str_replace('_', ' ', $category)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select id="status"
                                    name="status"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 bg-white">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Featured Filter -->
                        <div>
                            <label for="featured" class="block text-sm font-semibold text-gray-700 mb-2">Featured</label>
                            <select id="featured"
                                    name="featured"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 bg-white">
                                <option value="">All Services</option>
                                <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Featured Only</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <button type="submit"
                                class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-2.5 rounded-lg transition-all duration-200 font-semibold shadow-lg flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            <span>Apply Filters</span>
                        </button>

                        <a href="{{ route('admin.services.index') }}"
                           class="text-gray-500 hover:text-pink-600 text-sm font-medium transition-colors duration-200 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Clear Filters</span>
                        </a>
                    </div>
                </form>

                <!-- Services Grid -->
                @if($services->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($services as $service)
                            <div class="group bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-pink-300 transform hover:-translate-y-1">
                                <!-- Service Image -->
                                @if($service->image)
                                    <div class="relative overflow-hidden bg-gradient-to-br from-pink-100 to-rose-100">
                                        <img src="{{ $service->image_url }}"
                                             alt="{{ $service->name }}"
                                             class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-500">
                                        @if($service->is_featured)
                                            <div class="absolute top-3 right-3 bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <span>Featured</span>
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                                            <span class="text-white text-2xl font-bold">{{ $service->formatted_price }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="relative h-56 bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center">
                                        <div class="text-center text-white">
                                            <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-sm opacity-75">No Image</p>
                                        </div>
                                        @if($service->is_featured)
                                            <div class="absolute top-3 right-3 bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <span>Featured</span>
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                                            <span class="text-white text-2xl font-bold">{{ $service->formatted_price }}</span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Service Info -->
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-pink-600 transition-colors duration-200">{{ $service->name }}</h3>
                                    </div>

                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {{ $service->short_description ?: Str::limit($service->description, 100) }}
                                    </p>

                                    <!-- Service Details -->
                                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-100">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-500 flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Duration</span>
                                            </span>
                                            <span class="text-gray-900 font-semibold">{{ $service->formatted_duration }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-500 flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                <span>Category</span>
                                            </span>
                                            <span class="px-2 py-1 bg-pink-100 text-pink-700 rounded-md text-xs font-semibold">{{ $service->formatted_category }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-500 flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Status</span>
                                            </span>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $service->is_active ? '✓ Active' : '✗ Inactive' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.services.show', $service) }}"
                                           class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 text-center flex items-center justify-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <span>View</span>
                                        </a>
                                        <a href="{{ route('admin.services.edit', $service) }}"
                                           class="flex-1 bg-pink-50 hover:bg-pink-100 text-pink-700 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 text-center flex items-center justify-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            <span>Edit</span>
                                        </a>
                                    </div>

                                    <!-- Quick Actions -->
                                    <div class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between">
                                        <div class="flex space-x-2">
                                            <!-- Toggle Status -->
                                            <form method="POST" action="{{ route('admin.services.toggle-status', $service) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-xs px-2 py-1 rounded-md {{ $service->is_active ? 'bg-orange-50 text-orange-600 hover:bg-orange-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }} transition-colors duration-200">
                                                    {{ $service->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>

                                            <!-- Toggle Featured -->
                                            <form method="POST" action="{{ route('admin.services.toggle-featured', $service) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="text-xs px-2 py-1 rounded-md {{ $service->is_featured ? 'bg-gray-50 text-gray-600 hover:bg-gray-100' : 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100' }} transition-colors duration-200">
                                                    {{ $service->is_featured ? 'Unfeature' : 'Feature' }}
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-confirm-button action="delete" title="Delete Service" text="Are you sure you want to delete this service? This action cannot be undone!" class="text-xs px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition-colors duration-200">
                                                Delete
                                            </x-confirm-button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $services->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-pink-100 to-rose-100 mb-6">
                            <svg class="w-12 h-12 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No services found</h3>
                        <p class="text-gray-500 mb-8 max-w-md mx-auto">Try adjusting your search criteria or add a new service to get started.</p>
                        <a href="{{ route('admin.services.create') }}"
                           class="inline-flex items-center space-x-2 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-8 py-3 rounded-lg transition-all duration-200 font-semibold shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Add First Service</span>
                        </a>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
