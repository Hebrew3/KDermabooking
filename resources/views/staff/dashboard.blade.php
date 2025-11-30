<x-app-layout>
<x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Staff Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back, {{ $staff->name }}!</p>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Appointments -->
                <div class="bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80">Total Appointments</p>
                            <p class="text-3xl font-bold text-white">{{ $stats['total_appointments'] }}</p>
                            <p class="text-sm text-white/90 flex items-center mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                                All time total
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed Appointments -->
                <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80">Completed</p>
                            <p class="text-3xl font-bold text-white">{{ $stats['completed_appointments'] }}</p>
                            <p class="text-sm text-white/90 flex items-center mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Successfully finished
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80">Today's Appointments</p>
                            <p class="text-3xl font-bold text-white">{{ $stats['today_appointments'] }}</p>
                            <p class="text-sm text-white/90 flex items-center mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Scheduled for today
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="bg-gradient-to-br from-purple-500 to-violet-500 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80">Upcoming</p>
                            <p class="text-3xl font-bold text-white">{{ $stats['upcoming_appointments'] }}</p>
                            <p class="text-sm text-white/90 flex items-center mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Future appointments
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Services Section -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">My Assigned Services</h2>
                            <a href="{{ route('staff.my-services') }}" 
                               class="text-pink-600 hover:text-pink-700 text-sm font-medium">
                                View All →
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Services Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-900">Assigned Services</p>
                                        <p class="text-2xl font-bold text-green-700">{{ $assignedServicesStats['total_assigned'] }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-200">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-blue-900">Primary Services</p>
                                        <p class="text-2xl font-bold text-blue-700">{{ $assignedServicesStats['primary_services'] }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-purple-50 to-violet-50 p-4 rounded-lg border border-purple-200">
                                <div class="flex items-center">
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-purple-900">Total Revenue</p>
                                        <p class="text-2xl font-bold text-purple-700">₱{{ number_format($assignedServicesStats['total_revenue'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-4 rounded-lg border border-orange-200">
                                <div class="flex items-center">
                                    <div class="p-2 bg-orange-100 rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-orange-900">This Month</p>
                                        <p class="text-2xl font-bold text-orange-700">₱{{ number_format($assignedServicesStats['monthly_revenue'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Assigned Services -->
                        @if($assignedServicesStats['assigned_services']->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($assignedServicesStats['assigned_services']->take(6) as $service)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-medium text-gray-900 text-sm">{{ $service->name }}</h4>
                                            @if($service->pivot->is_primary)
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                                    Primary
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="space-y-1 text-xs text-gray-600 mb-3">
                                            <div class="flex justify-between">
                                                <span>Rate:</span>
                                                <span class="font-medium">
                                                    @if($service->pivot->custom_price)
                                                        ₱{{ number_format($service->pivot->custom_price, 2) }}
                                                    @else
                                                        {{ $service->formatted_price }}
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Proficiency:</span>
                                                <span class="font-medium">Level {{ $service->pivot->proficiency_level }}</span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-3 gap-2 text-center">
                                            <div>
                                                <div class="text-sm font-semibold text-blue-600">{{ $service->total_bookings }}</div>
                                                <div class="text-xs text-gray-500">Total</div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-green-600">{{ $service->completed_bookings }}</div>
                                                <div class="text-xs text-gray-500">Done</div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-orange-600">{{ $service->pending_bookings }}</div>
                                                <div class="text-xs text-gray-500">Pending</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                                <p class="text-gray-500 mb-4">No services assigned yet</p>
                                <a href="{{ route('staff.services') }}" 
                                   class="text-pink-600 hover:text-pink-700 font-medium">
                                    Browse Available Services →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Today's Appointments -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Today's Appointments</h2>
                        </div>
                        <div class="p-6">
                            @if($todayAppointments->count() > 0)
                                <div class="space-y-4">
                                    @foreach($todayAppointments as $appointment)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4 flex-1">
                                            <div class="bg-pink-100 p-2 rounded-full">
                                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-medium text-gray-900">{{ $appointment->customer_name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $appointment->service ? $appointment->service->name : 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <div class="text-right">
                                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                                    @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </div>
                                            @if(in_array($appointment->status, ['confirmed', 'in_progress']) && $appointment->client_id && $appointment->staff_id)
                                            <a href="#chat-{{ $appointment->id }}" 
                                               class="p-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-lg hover:from-pink-600 hover:to-rose-600 transition-all duration-200 shadow-md hover:shadow-lg"
                                               title="Open Chat"
                                               onclick="document.getElementById('chatWidget-{{ $appointment->id }}')?.querySelector('button')?.click(); return false;">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-gray-500">No appointments scheduled for today</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Weekly Schedule -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">This Week's Schedule</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($weeklySchedule as $day => $schedule)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">{{ $day }}</span>
                                    @if($schedule && isset($schedule['is_available']) && $schedule['is_available'] && $schedule['start_time'] && $schedule['end_time'])
                                        <span class="text-xs text-green-600">
                                            {{ \Carbon\Carbon::createFromFormat('H:i', substr($schedule['start_time'], 0, 5))->format('g:i A') }} -
                                            {{ \Carbon\Carbon::createFromFormat('H:i', substr($schedule['end_time'], 0, 5))->format('g:i A') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Not available</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('staff.schedule') }}" class="text-pink-600 hover:text-pink-700 text-sm font-medium">
                                    Manage Schedule →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            <div class="mt-8">
                <div class="bg-white rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Upcoming Appointments</h2>
                    </div>
                    <div class="p-6">
                        @if($upcomingAppointments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($upcomingAppointments as $appointment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $appointment->customer_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $appointment->customer_email ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $appointment->service ? $appointment->service->name : 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                                    @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-500">No upcoming appointments</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chat widgets removed - use the Messages page instead --}}
</x-app-layout>
