<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'K-Derma') }} - Help</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Include Mobile Header -->
    <x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Help & Support</h1>
                <p class="text-gray-600 mt-2">Get help and support for your daily tasks</p>
            </div>

            <!-- Help Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Quick Help -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Quick Help</h2>
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">How to schedule an appointment?</h3>
                            <p class="text-sm text-gray-600">Go to My Schedule and click "Add Appointment" to book a new client appointment.</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">How to update client information?</h3>
                            <p class="text-sm text-gray-600">Visit the Clients section and click on any client to view and edit their profile.</p>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">How to send messages to clients?</h3>
                            <p class="text-sm text-gray-600">Use the Messages section to communicate with clients and team members.</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Contact Support</h2>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-800">Email Support</div>
                                <div class="text-sm text-gray-600">support@kderma.com</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-800">Phone Support</div>
                                <div class="text-sm text-gray-600">+1 (555) 123-4567</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="h-8 w-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-800">Live Chat</div>
                                <div class="text-sm text-gray-600">Available 9 AM - 6 PM</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
