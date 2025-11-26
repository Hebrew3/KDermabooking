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

    <!-- Include Client Sidebar -->
    <x-client-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Messages</h1>
                <p class="text-gray-600 mt-2">Communicate with our team</p>
            </div>

            <!-- Messages Content -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="space-y-4">
                    <!-- Message 1 -->
                    <div class="flex items-start space-x-4 p-4 bg-pink-50 rounded-lg">
                        <div class="h-10 w-10 bg-pink-500 rounded-full flex items-center justify-center text-white font-bold">
                            EC
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-800">Emily Chen</h3>
                                    <p class="text-sm text-gray-600">Your appointment for tomorrow has been confirmed. See you at 10:00 AM!</p>
                                </div>
                                <span class="text-xs text-gray-500">1 hour ago</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">Unread</span>
                    </div>

                    <!-- Message 2 -->
                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                        <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            MR
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-800">Michael Rodriguez</h3>
                                    <p class="text-sm text-gray-600">Thank you for choosing K-Derma! We look forward to seeing you again.</p>
                                </div>
                                <span class="text-xs text-gray-500">2 days ago</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Read</span>
                    </div>

                    <!-- Message 3 -->
                    <div class="flex items-start space-x-4 p-4 bg-pink-50 rounded-lg">
                        <div class="h-10 w-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold">
                            SJ
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-semibold text-gray-800">Sarah Johnson</h3>
                                    <p class="text-sm text-gray-600">Your laser treatment results look amazing! Keep up with the aftercare routine.</p>
                                </div>
                                <span class="text-xs text-gray-500">3 days ago</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">Unread</span>
                    </div>
                </div>

                <!-- Send Message -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Send a Message</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">To</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option>Select staff member</option>
                                <option>Emily Chen</option>
                                <option>Michael Rodriguez</option>
                                <option>Sarah Johnson</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500" rows="4" placeholder="Type your message here..."></textarea>
                        </div>
                        <button class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            Send Message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
