@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
<x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <a href="{{ route('staff.clients') }}" class="text-pink-600 hover:text-pink-700 mb-2 inline-flex items-center text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Clients
                        </a>
                        <h1 class="text-3xl font-bold text-gray-900">Client Details</h1>
                        <p class="text-gray-600 mt-2">View client information and appointment history</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Client Information Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="text-center mb-6">
                            <div class="flex justify-center mb-4">
                                @if($client->profile_picture)
                                    <img src="{{ $client->profile_picture_url ?? asset('storage/' . $client->profile_picture) }}" alt="{{ $client->name }}" class="h-24 w-24 rounded-full object-cover border-4 border-pink-100">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-gradient-to-br from-pink-400 to-rose-400 flex items-center justify-center border-4 border-pink-100">
                                        <span class="text-3xl font-bold text-white">{{ substr($client->first_name, 0, 1) }}{{ substr($client->last_name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $client->name }}</h2>
                            <p class="text-gray-500 mt-1">{{ ucfirst($client->gender) }}{{ $client->birth_date ? ', ' . \Carbon\Carbon::parse($client->birth_date)->age . ' years old' : '' }}</p>
                        </div>

                        <div class="space-y-4 border-t border-gray-200 pt-6">
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                                <p class="text-sm text-gray-900 mt-1">{{ $client->email }}</p>
                            </div>

                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile Number</label>
                                <p class="text-sm text-gray-900 mt-1">{{ $client->mobile_number ?? 'N/A' }}</p>
                            </div>

                            @if($client->birth_date)
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Birth Date</label>
                                <p class="text-sm text-gray-900 mt-1">{{ \Carbon\Carbon::parse($client->birth_date)->format('F d, Y') }}</p>
                            </div>
                            @endif

                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Member Since</label>
                                <p class="text-sm text-gray-900 mt-1">{{ $client->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointments List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Appointment History ({{ $appointments->total() }} total)</h2>
                        </div>

                        @if($appointments->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($appointments as $appointment)
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0">
                                                <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-pink-400 to-rose-400 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $appointment->service->name }}</h3>
                                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                                    </div>
                                                    @if($appointment->appointment_time)
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                                    </div>
                                                    @endif
                                                    @if($appointment->total_amount)
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $appointment->formatted_total }}
                                                    </div>
                                                    @endif
                                                </div>
                                                @if($appointment->appointment_number)
                                                <div class="mt-2">
                                                    <span class="text-xs text-gray-500">Appointment #: {{ $appointment->appointment_number }}</span>
                                                </div>
                                                @endif
                                                @if($appointment->notes || $appointment->client_notes)
                                                <div class="mt-2 text-sm text-gray-600">
                                                    @if($appointment->client_notes)
                                                        <p class="italic">Client Notes: {{ $appointment->client_notes }}</p>
                                                    @endif
                                                    @if($appointment->notes)
                                                        <p class="mt-1">Notes: {{ $appointment->notes }}</p>
                                                    @endif
                                                </div>
                                                @endif
                                                
                                                <!-- Chat Button for Confirmed Appointments -->
                                                @if(in_array($appointment->status, ['confirmed', 'in_progress']) && $appointment->client_id && $appointment->staff_id)
                                                <div class="mt-3">
                                                    <a href="#chat-{{ $appointment->id }}" 
                                                       onclick="document.getElementById('chatWidget-{{ $appointment->id }}')?.querySelector('button')?.click(); return false;"
                                                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-rose-500 text-white text-sm font-medium rounded-lg hover:from-pink-600 hover:to-rose-600 transition-all duration-200 shadow-md hover:shadow-lg">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                        </svg>
                                                        Open Chat
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $appointment->status_color }}">
                                            {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                                        </span>
                                        @if($appointment->client_rating)
                                        <div class="flex items-center text-yellow-500">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $appointment->client_rating)
                                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $appointments->links() }}
                        </div>
                        @else
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-gray-500">No appointments found with this client</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Widgets for Confirmed Appointments -->
    @foreach($appointments as $appointment)
        @if(in_array($appointment->status, ['confirmed', 'in_progress']) && $appointment->client_id && $appointment->staff_id)
            <div id="chatWidget-{{ $appointment->id }}">
                <x-chat-widget :appointment-id="$appointment->id" />
            </div>
        @endif
    @endforeach
</x-app-layout>

