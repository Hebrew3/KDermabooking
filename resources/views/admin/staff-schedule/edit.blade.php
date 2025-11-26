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
                                    <input type="time"
                                           name="schedules[{{ $day }}][start_time]"
                                           value="{{ $schedule?->start_time }}"
                                           id="start-{{ $day }}"
                                           class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                           {{ !$schedule || !$schedule->is_available ? 'disabled' : '' }}>
                                    <span class="text-gray-400">to</span>
                                    <input type="time"
                                           name="schedules[{{ $day }}][end_time]"
                                           value="{{ $schedule?->end_time }}"
                                           id="end-{{ $day }}"
                                           class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                                           {{ !$schedule || !$schedule->is_available ? 'disabled' : '' }}>
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
                start.disabled = !enabled;
                end.disabled = !enabled;
            }
        }
    </script>
</x-app-layout>
