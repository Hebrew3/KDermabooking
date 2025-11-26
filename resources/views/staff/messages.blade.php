<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'K-Derma') }} - Messages</title>

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
                <h1 class="text-3xl font-bold text-gray-900">Messages</h1>
                <p class="text-gray-600 mt-2">Communicate with clients and team members</p>
            </div>

            <!-- Messages Content -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="space-y-4">
                    <!-- Message 1 -->
                    <div class="flex items-start space-x-4 p-4 bg-blue-50 rounded-lg">
                        <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            JW
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-800">Jessica Williams</h3>
                                    <p class="text-sm text-gray-600">Can we reschedule my appointment for tomorrow?</p>
                                </div>
                                <span class="text-xs text-gray-500">2 min ago</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Unread</span>
                    </div>

                    <!-- Message 2 -->
                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="h-10 w-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                            DB
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-800">David Brown</h3>
                                    <p class="text-sm text-gray-600">Thank you for the great service today!</p>
                                </div>
                                <span class="text-xs text-gray-500">1 hour ago</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Read</span>
                    </div>

                    <!-- Message 3 -->
                    <div class="flex items-start space-x-4 p-4 bg-blue-50 rounded-lg">
                        <div class="h-10 w-10 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                            LD
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-800">Lisa Davis</h3>
                                    <p class="text-sm text-gray-600">I have a question about the laser treatment aftercare.</p>
                                </div>
                                <span class="text-xs text-gray-500">3 hours ago</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Unread</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
