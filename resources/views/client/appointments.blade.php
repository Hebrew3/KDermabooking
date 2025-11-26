<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'K-Derma') }} - My Appointments</title>

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
                <h1 class="text-3xl font-bold text-gray-900">My Appointments</h1>
                <p class="text-gray-600 mt-2">View and manage your appointments</p>
            </div>

            <!-- Appointments Content -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Upcoming Appointments</h2>
                    <button class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Book New Appointment
                    </button>
                </div>

                <!-- Appointments List -->
                <div class="space-y-4">
                    <!-- Appointment 1 -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-pink-50 to-rose-50 rounded-lg border border-pink-100">
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                                FT
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Facial Treatment</h3>
                                <p class="text-sm text-gray-600">Sep 15, 2024 - 10:00 AM</p>
                                <p class="text-sm text-gray-600">with Emily Chen</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                            <div class="mt-2 flex space-x-2">
                                <button class="text-pink-600 hover:text-pink-800 text-sm">Reschedule</button>
                                <button class="text-red-600 hover:text-red-800 text-sm">Cancel</button>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment 2 -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                SC
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Skin Consultation</h3>
                                <p class="text-sm text-gray-600">Sep 20, 2024 - 2:00 PM</p>
                                <p class="text-sm text-gray-600">with Michael Rodriguez</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            <div class="mt-2 flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-800 text-sm">Confirm</button>
                                <button class="text-red-600 hover:text-red-800 text-sm">Cancel</button>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment 3 -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100">
                        <div class="flex items-center space-x-4">
                            <div class="h-12 w-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                                LT
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Laser Treatment</h3>
                                <p class="text-sm text-gray-600">Sep 25, 2024 - 11:30 AM</p>
                                <p class="text-sm text-gray-600">with Emily Chen</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                            <div class="mt-2 flex space-x-2">
                                <button class="text-green-600 hover:text-green-800 text-sm">Reschedule</button>
                                <button class="text-red-600 hover:text-red-800 text-sm">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
