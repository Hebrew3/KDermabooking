<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'K-Derma') }} - Services</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Include Mobile Header -->
    <x-mobile-header />

    <!-- Include Client Sidebar -->
    <x-client-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Our Services</h1>
                <p class="text-gray-600 mt-2">Explore our range of aesthetic treatments</p>
            </div>

            <!-- Services Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Service Card 1 -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-200">
                    <div class="h-48 bg-gradient-to-br from-pink-100 to-rose-100 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-16 w-16 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Facial Treatment</h3>
                    <p class="text-gray-600 text-sm mb-4">Deep cleansing facial with hydrating mask and massage for glowing skin</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-pink-600">$120</span>
                        <button class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Service Card 2 -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-200">
                    <div class="h-48 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-16 w-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Skin Consultation</h3>
                    <p class="text-gray-600 text-sm mb-4">Professional skin analysis and personalized treatment plan</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-blue-600">$80</span>
                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Service Card 3 -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-200">
                    <div class="h-48 bg-gradient-to-br from-green-100 to-emerald-100 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Laser Treatment</h3>
                    <p class="text-gray-600 text-sm mb-4">Advanced laser therapy for skin rejuvenation and anti-aging</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-green-600">$300</span>
                        <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Service Card 4 -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-200">
                    <div class="h-48 bg-gradient-to-br from-purple-100 to-violet-100 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-16 w-16 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Chemical Peel</h3>
                    <p class="text-gray-600 text-sm mb-4">Professional chemical peel for skin renewal and texture improvement</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-purple-600">$200</span>
                        <button class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Service Card 5 -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-200">
                    <div class="h-48 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-16 w-16 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v3m0 0H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2h-2M9 9h6m-6 4h6m-6 4h6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Microdermabrasion</h3>
                    <p class="text-gray-600 text-sm mb-4">Gentle exfoliation treatment for smoother, brighter skin</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-yellow-600">$150</span>
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            Book Now
                        </button>
                    </div>
                </div>

                <!-- Service Card 6 -->
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-200">
                    <div class="h-48 bg-gradient-to-br from-red-100 to-pink-100 rounded-lg mb-4 flex items-center justify-center">
                        <svg class="h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Botox Treatment</h3>
                    <p class="text-gray-600 text-sm mb-4">Anti-aging botox injection treatment for wrinkle reduction</p>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-red-600">$400</span>
                        <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            Book Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
