<x-app-layout>
<x-mobile-header />

    <!-- Include Client Sidebar -->
    <x-client-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li><a href="{{ route('index.landing') }}" class="hover:text-pink-600">Dashboard</a></li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('client.services') }}" class="hover:text-pink-600">Services</a>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-900">{{ $service->name }}</span>
                    </li>
                </ol>
            </nav>

            <!-- Service Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <!-- Service Image -->
                        @if($service->image)
                        <div class="relative">
                            <img src="{{ $service->image_url }}" alt="{{ $service->name }}" class="w-full h-64 lg:h-80 object-cover">
                            @if($service->is_featured)
                            <span class="absolute top-4 left-4 bg-pink-500 text-white text-sm font-medium px-3 py-1 rounded-full">Featured Service</span>
                            @endif
                        </div>
                        @endif

                        <!-- Service Info -->
                        <div class="p-8">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $service->name }}</h1>
                                    @if($service->category)
                                    <span class="bg-pink-100 text-pink-800 text-sm font-medium px-3 py-1 rounded-full">{{ $service->formatted_category }}</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-pink-600">{{ $service->formatted_price }}</div>
                                    <div class="text-sm text-gray-500">{{ $service->formatted_duration }}</div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">About This Service</h2>
                                <div class="prose prose-gray max-w-none">
                                    {!! nl2br(e($service->description)) !!}
                                </div>
                            </div>

                            <!-- Service Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2">Duration</h3>
                                    <p class="text-gray-600">{{ $service->formatted_duration }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2">Price</h3>
                                    <p class="text-gray-600">{{ $service->formatted_price }}</p>
                                </div>
                                @if($service->category)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2">Category</h3>
                                    <p class="text-gray-600">{{ $service->formatted_category }}</p>
                                </div>
                                @endif
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2">Availability</h3>
                                    <p class="text-gray-600">Available for booking</p>
                                </div>
                            </div>

                            <!-- What to Expect -->
                            @if(isset($service->what_to_expect) && $service->what_to_expect)
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">What to Expect</h2>
                                <div class="prose prose-gray max-w-none">
                                    {!! nl2br(e($service->what_to_expect)) !!}
                                </div>
                            </div>
                            @endif

                            <!-- Aftercare Instructions -->
                            @if(isset($service->aftercare_instructions) && $service->aftercare_instructions)
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Aftercare Instructions</h2>
                                <div class="prose prose-gray max-w-none">
                                    {!! nl2br(e($service->aftercare_instructions)) !!}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Booking Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Book This Service</h3>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Price:</span>
                                <span class="font-semibold text-gray-900">{{ $service->formatted_price }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Duration:</span>
                                <span class="font-semibold text-gray-900">{{ $service->formatted_duration }}</span>
                            </div>
                        </div>

                        <a href="{{ route('client.appointments.create', ['service' => $service->id]) }}"
                           class="w-full bg-pink-500 hover:bg-pink-600 text-white font-medium py-3 px-4 rounded-lg text-center transition-colors duration-200 block">
                            Book Appointment
                        </a>

                        <p class="text-xs text-gray-500 text-center mt-3">
                            You'll be able to select your preferred date and time
                        </p>
                    </div>

                    <!-- Contact Info -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Need Help?</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Have questions about this service? Our team is here to help.
                        </p>
                        <div class="space-y-2">
                            <a href="tel:+1234567890" class="flex items-center text-sm text-gray-600 hover:text-pink-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                </svg>
                                (123) 456-7890
                            </a>
                            <a href="mailto:info@clinic.com" class="flex items-center text-sm text-gray-600 hover:text-pink-600">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                info@clinic.com
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Services -->
            @if($relatedServices->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Services</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedServices as $relatedService)
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300 group">
                        @if($relatedService->image)
                        <div class="relative overflow-hidden">
                            <img src="{{ $relatedService->image_url }}" alt="{{ $relatedService->name }}" class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        @endif

                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 text-sm mb-2">{{ $relatedService->name }}</h3>
                            <p class="text-gray-600 text-xs mb-3 line-clamp-2">{{ $relatedService->short_description }}</p>

                            <div class="flex items-center justify-between mb-3">
                                <div class="text-xs text-gray-500">
                                    <div class="font-medium text-pink-600">{{ $relatedService->formatted_price }}</div>
                                    <div>{{ $relatedService->formatted_duration }}</div>
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('client.services.show', $relatedService) }}"
                                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-xs text-center transition-colors duration-200">
                                    View
                                </a>
                                <a href="{{ route('client.appointments.create', ['service' => $relatedService->id]) }}"
                                   class="flex-1 bg-pink-500 hover:bg-pink-600 text-white px-3 py-2 rounded-lg text-xs text-center transition-colors duration-200">
                                    Book
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
