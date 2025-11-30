<x-app-layout>
    <x-mobile-header />

    <x-admin-sidebar />

    <div class="lg:ml-64">
        <div class="p-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Staff Schedule</h1>
                    <p class="text-gray-600 mt-2">Weekly schedule for {{ $staff->full_name ?? $staff->name }}</p>
                </div>
                <a href="{{ route('admin.staff-schedule.index') }}" class="text-sm text-gray-600 hover:text-gray-800">&larr; Back to Staff Schedule</a>
            </div>

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="POST" action="{{ route('admin.staff-schedule.update', $staff) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        @foreach($daysOfWeek as $day)
                            @php
                                $schedule = $staff->staffSchedules->firstWhere('day_of_week', $day);
                            @endphp
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="w-24 text-sm font-medium text-gray-700 capitalize">{{ $day }}</div>
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               name="schedules[{{ $day }}][is_available]"
                                               value="1"
                                               {{ ($schedule && $schedule->is_available) ? 'checked' : '' }}
                                               class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded"
                                               onchange="toggleDayInputs('{{ $day }}', this.checked)">
                                        <span class="ml-2 text-sm text-gray-600">Available</span>
                                    </label>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input type="hidden" name="schedules[{{ $day }}][day_of_week]" value="{{ $day }}">
                                    @php
                                        $startTimeFormatted = '';
                                        $endTimeFormatted = '';
                                        if ($schedule && $schedule->start_time) {
                                            try {
                                                $startTimeFormatted = \Carbon\Carbon::createFromFormat(
                                                    strlen($schedule->start_time) > 5 ? 'H:i:s' : 'H:i',
                                                    $schedule->start_time
                                                )->format('H:i');
                                            } catch (\Exception $e) {
                                                $startTimeFormatted = substr($schedule->start_time, 0, 5);
                                            }
                                        }
                                        if ($schedule && $schedule->end_time) {
                                            try {
                                                $endTimeFormatted = \Carbon\Carbon::createFromFormat(
                                                    strlen($schedule->end_time) > 5 ? 'H:i:s' : 'H:i',
                                                    $schedule->end_time
                                                )->format('H:i');
                                            } catch (\Exception $e) {
                                                $endTimeFormatted = substr($schedule->end_time, 0, 5);
                                            }
                                        }
                                    @endphp
                                    <div class="flex flex-col">
                                        <input type="time"
                                               name="schedules[{{ $day }}][start_time]"
                                               value="{{ $startTimeFormatted }}"
                                               id="start-{{ $day }}"
                                               class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('schedules.'.$day.'.start_time') border-red-500 @enderror"
                                               {{ !$schedule || !$schedule->is_available ? 'disabled' : '' }}
                                               {{ ($schedule && $schedule->is_available) ? 'required' : '' }}>
                                        @error('schedules.'.$day.'.start_time')
                                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <span class="text-gray-400">to</span>
                                    <div class="flex flex-col">
                                        <input type="time"
                                               name="schedules[{{ $day }}][end_time]"
                                               value="{{ $endTimeFormatted }}"
                                               id="end-{{ $day }}"
                                               class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 @error('schedules.'.$day.'.end_time') border-red-500 @enderror"
                                               {{ !$schedule || !$schedule->is_available ? 'disabled' : '' }}
                                               {{ ($schedule && $schedule->is_available) ? 'required' : '' }}>
                                        @error('schedules.'.$day.'.end_time')
                                            <span class="text-red-600 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('admin.staff-schedule.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">Cancel</a>
                        <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">Save Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleDayInputs(day, enabled) {
            const start = document.getElementById('start-' + day);
            const end = document.getElementById('end-' + day);
            if (start && end) {
                if (enabled) {
                    start.disabled = false;
                    end.disabled = false;
                    start.setAttribute('required', 'required');
                    end.setAttribute('required', 'required');
                    start.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    end.classList.remove('bg-gray-100', 'cursor-not-allowed');
                } else {
                    start.disabled = true;
                    end.disabled = true;
                    start.removeAttribute('required');
                    end.removeAttribute('required');
                    start.value = '';
                    end.value = '';
                    start.classList.add('bg-gray-100', 'cursor-not-allowed');
                    end.classList.add('bg-gray-100', 'cursor-not-allowed');
                }
            }
        }
        
        // Initialize required attributes on page load
        document.addEventListener('DOMContentLoaded', function() {
            const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            daysOfWeek.forEach(function(day) {
                const checkbox = document.querySelector('input[name="schedules[' + day + '][is_available]"]');
                if (checkbox && checkbox.checked) {
                    toggleDayInputs(day, true);
                }
            });
        });
    </script>
</x-app-layout>
