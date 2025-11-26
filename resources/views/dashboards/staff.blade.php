<x-app-layout>
<x-mobile-header />
<x-staff-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Today's Appointments -->
            <div class="bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/80">Today's Appointments</p>
                        <p class="text-3xl font-bold text-white">8</p>
                        <p class="text-sm text-white/90 flex items-center mt-1">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Next: 2:00 PM
                        </p>
                    </div>
                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed Today -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/80">Completed Today</p>
                        <p class="text-3xl font-bold text-white">5</p>
                        <p class="text-sm text-white/90 flex items-center mt-1">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            62% completion rate
                        </p>
                    </div>
                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/80">Pending Tasks</p>
                        <p class="text-3xl font-bold text-white">3</p>
                        <p class="text-sm text-white/90 flex items-center mt-1">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            2 follow-ups needed
                        </p>
                    </div>
                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Client Satisfaction -->
            <div class="bg-gradient-to-br from-purple-500 to-violet-500 rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/80">Client Rating</p>
                        <p class="text-3xl font-bold text-white">4.8</p>
                        <p class="text-sm text-white/90 flex items-center mt-1">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            Excellent feedback
                        </p>
                    </div>
                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Today's Schedule -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Today's Schedule</h3>
                        <a href="#" class="text-pink-600 text-sm font-medium">View all</a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-pink-50 rounded-xl border border-pink-100">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 bg-gradient-to-br from-pink-400 to-rose-400 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">JD</span>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-800">Jane Doe</p>
                                    <p class="text-sm text-neutral-600">Facial Treatment - 60 min</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-neutral-800">2:00 PM</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Confirmed
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 bg-gradient-to-br from-blue-400 to-indigo-400 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">JS</span>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-800">John Smith</p>
                                    <p class="text-sm text-neutral-600">Massage Therapy - 90 min</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-neutral-800">3:30 PM</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-purple-50 rounded-xl border border-purple-100">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 bg-gradient-to-br from-purple-400 to-violet-400 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">MJ</span>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-800">Mary Johnson</p>
                                    <p class="text-sm text-neutral-600">Skin Consultation - 30 min</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-neutral-800">5:00 PM</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Confirmed
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Messages</h3>
                        <a href="#" class="text-pink-600 text-sm font-medium">View all</a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-pink-50 rounded-xl border border-pink-100">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 bg-gradient-to-br from-pink-400 to-rose-400 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">JD</span>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-800">Jane Doe</p>
                                    <p class="text-sm text-neutral-600">Thank you for the amazing facial treatment!</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-neutral-800">2 hours ago</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    New
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100">
                            <div class="flex items-center space-x-4">
                                <div class="h-10 w-10 bg-gradient-to-br from-blue-400 to-indigo-400 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">JS</span>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-800">John Smith</p>
                                    <p class="text-sm text-neutral-600">Can we reschedule tomorrow's appointment?</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-neutral-800">4 hours ago</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
