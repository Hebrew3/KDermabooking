<x-app-layout>
    <x-mobile-header />

    <!-- Include Admin Sidebar -->
    <x-admin-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl shadow-xl p-6 text-white">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h1 class="text-3xl font-bold">{{ $service->name }}</h1>
                                @if($service->is_featured)
                                    <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        <span>Featured</span>
                                    </span>
                                @endif
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $service->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <p class="text-pink-100 text-lg">Service Details and Information</p>
                            <div class="mt-4 flex items-center space-x-6 text-pink-100">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-2xl font-bold text-white">{{ $service->formatted_price }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-semibold">{{ $service->formatted_duration }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <a href="{{ route('admin.services.edit', $service) }}"
                               class="bg-white hover:bg-gray-50 text-pink-600 px-6 py-2.5 rounded-lg transition-all duration-200 font-semibold shadow-lg flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span>Edit Service</span>
                            </a>
                            <a href="{{ route('admin.services.index') }}"
                               class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-6 py-2.5 rounded-lg transition-all duration-200 font-semibold border border-white/30 flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                <span>Back to Services</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages are handled by SweetAlert in layout -->

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Service Images -->
                    @if($service->image || ($service->gallery_images && count($service->gallery_images) > 0))
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                                    <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Service Images</span>
                                </h2>
                            </div>
                            <div class="p-6">

                            @if($service->image)
                                <div class="mb-6">
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Main Image</h3>
                                    <div class="relative rounded-xl overflow-hidden shadow-lg group">
                                        <img src="{{ $service->image_url }}"
                                             alt="{{ $service->name }}"
                                             class="w-full h-96 object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </div>
                                </div>
                            @endif

                            @if($service->gallery_images && count($service->gallery_images) > 0)
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Gallery Images</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        @foreach($service->gallery_image_urls as $index => $imageUrl)
                                            <div class="relative group rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
                                                <img src="{{ $imageUrl }}"
                                                     alt="Gallery image {{ $index + 1 }}"
                                                     class="w-full h-40 object-cover group-hover:scale-110 transition-transform duration-500">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-4">
                                                    <span class="text-white font-semibold text-sm">
                                                        Image {{ $index + 1 }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            </div>
                        </div>
                    @endif

                    <!-- Service Description -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                                </svg>
                                <span>Description</span>
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="prose max-w-none">
                                <p class="text-gray-700 leading-relaxed text-lg">{{ $service->description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Service Details -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Service Details</span>
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Category</h3>
                                    <p class="text-lg font-bold text-gray-900">{{ $service->formatted_category }}</p>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Duration</h3>
                                    <p class="text-lg font-bold text-gray-900">{{ $service->formatted_duration }}</p>
                                </div>

                                <div class="bg-gradient-to-br from-pink-50 to-rose-50 rounded-lg p-4 border border-pink-200">
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Price</h3>
                                    <p class="text-2xl font-bold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">{{ $service->formatted_price }}</p>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Sort Order</h3>
                                    <p class="text-lg font-bold text-gray-900">{{ $service->sort_order }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($service->tags && count($service->tags) > 0)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                                    <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span>Tags</span>
                                </h2>
                            </div>
                            <div class="p-6">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($service->tags as $tag)
                                        <span class="px-4 py-2 bg-gradient-to-r from-pink-100 to-rose-100 text-pink-800 rounded-lg text-sm font-semibold border border-pink-200 hover:from-pink-200 hover:to-rose-200 transition-all duration-200">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- SEO Information -->
                    @if($service->meta_title || $service->meta_description)
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">SEO Information</h2>

                            @if($service->meta_title)
                                <div class="mb-4">
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Meta Title</h3>
                                    <p class="mt-1 text-gray-900">{{ $service->meta_title }}</p>
                                </div>
                            @endif

                            @if($service->meta_description)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Meta Description</h3>
                                    <p class="mt-1 text-gray-900">{{ $service->meta_description }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Service Status -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Service Status</span>
                            </h2>
                        </div>
                        <div class="p-6">

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Status</span>
                                <span class="px-3 py-1 rounded-full text-sm {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Featured</span>
                                <span class="px-3 py-1 rounded-full text-sm {{ $service->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $service->is_featured ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <!-- Toggle Status -->
                            <form method="POST" action="{{ route('admin.services.toggle-status', $service) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full px-4 py-3 {{ $service->is_active ? 'bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600' : 'bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600' }} text-white rounded-lg transition-all duration-200 font-semibold shadow-lg flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M{{ $service->is_active ? '18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : '9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path>
                                    </svg>
                                    <span>{{ $service->is_active ? 'Deactivate Service' : 'Activate Service' }}</span>
                                </button>
                            </form>

                            <!-- Toggle Featured -->
                            <form method="POST" action="{{ route('admin.services.toggle-featured', $service) }}" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full px-4 py-3 {{ $service->is_featured ? 'bg-gradient-to-r from-gray-500 to-slate-500 hover:from-gray-600 hover:to-slate-600' : 'bg-gradient-to-r from-yellow-400 to-orange-400 hover:from-yellow-500 hover:to-orange-500' }} text-white rounded-lg transition-all duration-200 font-semibold shadow-lg flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <span>{{ $service->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}</span>
                                </button>
                            </form>
                        </div>
                        </div>
                    </div>

                    <!-- Service Information -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Service Information</span>
                            </h2>
                        </div>
                        <div class="p-6">

                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Created</span>
                                <p class="text-sm text-gray-900">{{ $service->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>

                            <div>
                                <span class="text-sm font-medium text-gray-500">Last Updated</span>
                                <p class="text-sm text-gray-900">{{ $service->updated_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>

                            <div>
                                <span class="text-sm font-medium text-gray-500">Service ID</span>
                                <p class="text-sm text-gray-900">#{{ $service->id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-pink-50 to-rose-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-bold text-gray-800 flex items-center space-x-2">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span>Quick Actions</span>
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <a href="{{ route('admin.services.edit', $service) }}"
                                   class="block w-full px-4 py-3 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white text-center rounded-lg transition-all duration-200 font-semibold shadow-lg flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit Service</span>
                                </a>

                                <a href="{{ route('admin.services.index') }}"
                                   class="block w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white text-center rounded-lg transition-all duration-200 font-semibold shadow-lg flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    <span>View All Services</span>
                                </a>

                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this service? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="block w-full px-4 py-3 bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-white rounded-lg transition-all duration-200 font-semibold shadow-lg flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <span>Delete Service</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
