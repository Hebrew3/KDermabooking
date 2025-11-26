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
                        <h1 class="text-3xl font-bold text-gray-900">My Schedule</h1>
                        <p class="text-gray-600 mt-2">Manage your availability, appointments, and working hours</p>
                    </div>
                    
                    <!-- Week Navigation -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('staff.schedule', ['week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}" 
                           class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        
                        <div class="text-center">
                            <div class="text-lg font-semibold text-gray-900">
                                {{ $weekStart->format('M d') }} - {{ $weekStart->copy()->endOfWeek()->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-600">
                                @if($weekStart->isCurrentWeek())
                                    This Week
                                @elseif($weekStart->copy()->addWeek()->isCurrentWeek())
                                    Last Week
                                @elseif($weekStart->copy()->subWeek()->isCurrentWeek())
                                    Next Week
                                @else
                                    Week of {{ $weekStart->format('M d') }}
                                @endif
                            </div>
                        </div>
                        
                        <a href="{{ route('staff.schedule', ['week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}" 
                           class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        
                        @if(!$weekStart->isCurrentWeek())
                            <a href="{{ route('staff.schedule') }}" 
                               class="bg-pink-500 hover:bg-pink-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                Current Week
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Weekly Schedule Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-7 gap-4 mb-8">
                @foreach($weeklySchedule as $day => $schedule)
                    <div class="bg-white rounded-xl shadow-sm border {{ $schedule['is_today'] ? 'border-pink-300 bg-pink-50' : 'border-gray-200' }} overflow-hidden">
                        <!-- Day Header -->
                        <div class="p-4 {{ $schedule['is_today'] ? 'bg-pink-500 text-white' : ($schedule['is_past'] ? 'bg-gray-100 text-gray-600' : 'bg-gray-50 text-gray-800') }}">
                            <div class="text-center">
                                <div class="font-semibold">{{ $day }}</div>
                                <div class="text-sm {{ $schedule['is_today'] ? 'text-pink-100' : 'opacity-75' }}">
                                    {{ $schedule['formatted_date'] }}
                                </div>
                                @if($schedule['is_today'])
                                    <div class="text-xs text-pink-100 mt-1">Today</div>
                                @endif
                            </div>
                        </div>

                        <!-- Schedule Status -->
                        <div class="p-4">
                            @if($schedule['is_available'])
                                <div class="text-center mb-3">
                                    <div class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                        Available
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        {{ Carbon\Carbon::parse($schedule['start_time'])->format('g:i A') }} - 
                                        {{ Carbon\Carbon::parse($schedule['end_time'])->format('g:i A') }}
                                    </div>
                                </div>
                            @else
                                <div class="text-center mb-3">
                                    <div class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full mr-1"></div>
                                        Not Available
                                    </div>
                                </div>
                            @endif

                            <!-- Appointments for this day -->
                            @if(isset($weeklyAppointments[$day]) && $weeklyAppointments[$day]->count() > 0)
                                <div class="space-y-2 mb-3">
                                    <div class="text-xs font-medium text-gray-700 uppercase tracking-wide">Appointments</div>
                                    @foreach($weeklyAppointments[$day] as $appointment)
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-2">
                                            <div class="flex items-center justify-between">
                                                <div class="text-xs font-medium text-blue-900">
                                                    {{ Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') }}
                                                </div>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                                    @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-blue-700 mt-1">{{ $appointment->client->name }}</div>
                                            <div class="text-xs text-blue-600">{{ $appointment->service->name }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Unavailabilities for this day -->
                            @if(isset($weeklyUnavailabilities[$day]) && $weeklyUnavailabilities[$day]->count() > 0)
                                <div class="space-y-2 mb-3">
                                    <div class="text-xs font-medium text-gray-700 uppercase tracking-wide">Unavailable</div>
                                    @foreach($weeklyUnavailabilities[$day] as $unavailability)
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-2">
                                            <div class="flex items-center justify-between">
                                                <div class="text-xs font-medium text-red-900">
                                                    @if($unavailability->start_time && $unavailability->end_time)
                                                        {{ Carbon\Carbon::parse($unavailability->start_time)->format('g:i A') }} - 
                                                        {{ Carbon\Carbon::parse($unavailability->end_time)->format('g:i A') }}
                                                    @else
                                                        All Day
                                                    @endif
                                                </div>
                                                <button onclick="removeUnavailability({{ $unavailability->id }})" 
                                                        class="text-red-600 hover:text-red-800 text-xs">
                                                    ✕
                                                </button>
                                            </div>
                                            <div class="text-xs text-red-700 mt-1">{{ $unavailability->reason }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Quick Actions -->
                            @if(!$schedule['is_past'])
                                <div class="flex space-x-1">
                                    <button onclick="openUnavailabilityModal('{{ $schedule['date']->format('Y-m-d') }}', '{{ $day }}')" 
                                            class="flex-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs py-1 px-2 rounded transition-colors">
                                        Mark Unavailable
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Schedule Management Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Weekly Schedule Settings -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Weekly Schedule Settings</h2>
                    <p class="text-gray-600 text-sm mb-4">Set your regular working hours for each day of the week.</p>

                    <form method="POST" action="{{ route('staff.schedule.update') }}" class="space-y-4">
                        @csrf
                        @foreach($weeklySchedule as $day => $schedule)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <input type="hidden" name="schedules[{{ $loop->index }}][day_of_week]" value="{{ $day }}">
                            
                            <div class="flex items-center space-x-4">
                                <div class="w-20 text-sm font-medium text-gray-700">{{ $day }}</div>
                                
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="schedules[{{ $loop->index }}][is_available]" 
                                           value="1"
                                           {{ $schedule['is_available'] ? 'checked' : '' }}
                                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded"
                                           onchange="toggleTimeInputs(this, {{ $loop->index }})">
                                    <span class="ml-2 text-sm text-gray-600">Available</span>
                                </label>
                            </div>

                            <div class="flex items-center space-x-2" id="time-inputs-{{ $loop->index }}">
                                <input type="time" 
                                       name="schedules[{{ $loop->index }}][start_time]" 
                                       value="{{ $schedule['start_time'] }}"
                                       class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                       {{ !$schedule['is_available'] ? 'disabled' : '' }}>
                                <span class="text-gray-400">to</span>
                                <input type="time" 
                                       name="schedules[{{ $loop->index }}][end_time]" 
                                       value="{{ $schedule['end_time'] }}"
                                       class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                       {{ !$schedule['is_available'] ? 'disabled' : '' }}>
                            </div>
                        </div>
                        @endforeach

                        <div class="flex justify-end">
                            <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                Update Schedule
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Monthly Unavailabilities -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Unavailabilities This Month</h2>
                    
                    @if($monthlyUnavailabilities->count() > 0)
                        <div class="space-y-3 mb-6">
                            @foreach($monthlyUnavailabilities as $unavailability)
                                <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div>
                                        <div class="font-medium text-red-900">
                                            {{ Carbon\Carbon::parse($unavailability->unavailable_date)->format('M d, Y (l)') }}
                                        </div>
                                        <div class="text-sm text-red-700">
                                            @if($unavailability->start_time && $unavailability->end_time)
                                                {{ Carbon\Carbon::parse($unavailability->start_time)->format('g:i A') }} - 
                                                {{ Carbon\Carbon::parse($unavailability->end_time)->format('g:i A') }}
                                            @else
                                                All Day
                                            @endif
                                        </div>
                                        <div class="text-sm text-red-600">{{ $unavailability->reason }}</div>
                                    </div>
                                    <button onclick="removeUnavailability({{ $unavailability->id }})" 
                                            class="text-red-600 hover:text-red-800 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No unavailabilities scheduled this month</p>
                    @endif

                    <button onclick="openUnavailabilityModal()" 
                            class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                        Add Unavailability
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Unavailability Modal -->
    <div id="unavailabilityModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Mark Unavailable</h3>
                        <button onclick="closeUnavailabilityModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('staff.schedule.add-unavailability') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="unavailable_date" id="unavailableDate" required
                                       min="{{ Carbon\Carbon::today()->format('Y-m-d') }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time (Optional)</label>
                                    <input type="time" name="start_time"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time (Optional)</label>
                                    <input type="time" name="end_time"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <textarea name="reason" rows="3" required placeholder="e.g., Personal appointment, Sick leave, etc."
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"></textarea>
                            </div>
                        </div>

                        <div class="flex space-x-3 mt-6">
                            <button type="button" onclick="closeUnavailabilityModal()" 
                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                                Mark Unavailable
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for removing unavailability -->
    <form id="removeUnavailabilityForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function toggleTimeInputs(checkbox, index) {
            const timeInputs = document.querySelectorAll(`#time-inputs-${index} input`);
            timeInputs.forEach(input => {
                input.disabled = !checkbox.checked;
                if (!checkbox.checked) {
                    input.value = '';
                }
            });
        }

        function openUnavailabilityModal(date = null, day = null) {
            document.getElementById('unavailabilityModal').classList.remove('hidden');
            if (date) {
                document.getElementById('unavailableDate').value = date;
            }
        }

        function closeUnavailabilityModal() {
            document.getElementById('unavailabilityModal').classList.add('hidden');
        }

        function removeUnavailability(id) {
            if (confirm('Are you sure you want to remove this unavailability?')) {
                const form = document.getElementById('removeUnavailabilityForm');
                form.action = `/staff/schedule/unavailability/${id}`;
                form.submit();
            }
        }

        // Close modal when clicking outside
        document.getElementById('unavailabilityModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUnavailabilityModal();
            }
        });
    </script>
</x-app-layout>
