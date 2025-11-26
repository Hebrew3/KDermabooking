<x-app-layout>
<x-mobile-header />

    <!-- Include Staff Sidebar -->
    <x-staff-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Schedule</h1>
                <p class="text-gray-600 mt-2">Manage your availability and working hours</p>
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

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Schedule Management -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Weekly Schedule -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Weekly Schedule</h2>

                    <form method="POST" action="{{ route('staff.schedule.update') }}" class="space-y-4">
                        @csrf
                        @foreach($weeklySchedule as $day => $schedule)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <!-- Hidden field for day_of_week -->
                            <input type="hidden" name="schedules[{{ $loop->index }}][day_of_week]" value="{{ $day }}">
                            
                            <div class="flex items-center space-x-4">
                                <!-- Hidden field to ensure is_available is always sent -->
                                <input type="hidden" name="schedules[{{ $loop->index }}][is_available]" value="0">
                                <input type="checkbox"
                                       name="schedules[{{ $loop->index }}][is_available]"
                                       value="1"
                                       {{ $schedule->is_available ? 'checked' : '' }}
                                       onchange="toggleTimeInputs(this, {{ $loop->index }})"
                                       class="w-4 h-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                                <label class="text-sm font-medium text-gray-700">{{ $day }}</label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="time"
                                       name="schedules[{{ $loop->index }}][start_time]"
                                       value="{{ $schedule->start_time ?? '' }}"
                                       id="start_time_{{ $loop->index }}"
                                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 {{ $schedule->is_available ? '' : 'bg-gray-100' }}"
                                       {{ $schedule->is_available ? '' : 'disabled' }}>
                                <span class="text-gray-500">to</span>
                                <input type="time"
                                       name="schedules[{{ $loop->index }}][end_time]"
                                       value="{{ $schedule->end_time ?? '' }}"
                                       id="end_time_{{ $loop->index }}"
                                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 {{ $schedule->is_available ? '' : 'bg-gray-100' }}"
                                       {{ $schedule->is_available ? '' : 'disabled' }}>
                            </div>
                        </div>
                        @endforeach

                        <button type="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                            Save Schedule
                        </button>
                    </form>
                </div>

                <!-- Unavailability -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Mark Unavailability</h2>

                    <form method="POST" action="{{ route('staff.schedule.add-unavailability') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date"
                                   name="unavailable_date"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                   required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time (Optional)</label>
                                <input type="time"
                                       name="start_time"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time (Optional)</label>
                                <input type="time"
                                       name="end_time"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                            <textarea name="reason"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                      rows="3"
                                      placeholder="Reason for unavailability..."
                                      required></textarea>
                        </div>

                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                            Mark Unavailable
                        </button>
                    </form>

                    <!-- Current Unavailabilities -->
                    @if($unavailabilities->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Current Unavailabilities</h3>
                        <div class="space-y-2">
                            @foreach($unavailabilities as $unavailability)
                            <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($unavailability->unavailable_date)->format('M d, Y') }}
                                    </p>
                                    @if($unavailability->start_time && $unavailability->end_time)
                                        <p class="text-xs text-gray-600">
                                            {{ \Carbon\Carbon::parse($unavailability->start_time)->format('g:i A') }} -
                                            {{ \Carbon\Carbon::parse($unavailability->end_time)->format('g:i A') }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-600">{{ $unavailability->reason }}</p>
                                </div>
                                <form method="POST" action="{{ route('staff.schedule.remove-unavailability', $unavailability->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                        Remove
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleTimeInputs(checkbox, index) {
            const startTimeInput = document.getElementById(`start_time_${index}`);
            const endTimeInput = document.getElementById(`end_time_${index}`);
            
            if (startTimeInput && endTimeInput) {
                startTimeInput.disabled = !checkbox.checked;
                endTimeInput.disabled = !checkbox.checked;
                
                startTimeInput.classList.toggle('bg-gray-100', !checkbox.checked);
                endTimeInput.classList.toggle('bg-gray-100', !checkbox.checked);
                
                // Don't clear values - let the server handle the logic
                // This preserves the time values if user re-checks the box
            }
        }
    </script>
</x-app-layout>
