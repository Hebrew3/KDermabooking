<x-app-layout>
    <x-mobile-header />
    <x-admin-sidebar />

    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Staff Time Slot Availability</h1>
                    <p class="text-gray-600 mt-2">View real-time availability and occupied time slots for staff members</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <form method="GET" action="{{ route('admin.staff-schedule.time-slots') }}" class="flex items-end space-x-4">
                    <div class="flex-1">
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                        <input type="date" 
                               name="date" 
                               id="date" 
                               value="{{ $selectedDate }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <div class="flex-1">
                        <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">Filter by Staff (Optional)</label>
                        <select name="staff_id" 
                                id="staff_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            <option value="">All Staff</option>
                            @foreach($staff as $staffMember)
                                <option value="{{ $staffMember->id }}" {{ $selectedStaffId == $staffMember->id ? 'selected' : '' }}>
                                    {{ $staffMember->full_name ?? $staffMember->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="px-6 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg font-medium transition-colors">
                            View Availability
                        </button>
                    </div>
                </form>
            </div>

            <!-- Legend -->
            <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-green-100 border-2 border-green-500 rounded"></div>
                        <span class="text-sm text-gray-700">Available</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-red-100 border-2 border-red-500 rounded"></div>
                        <span class="text-sm text-gray-700">Occupied / Unavailable</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 bg-gray-100 border-2 border-gray-300 rounded"></div>
                        <span class="text-sm text-gray-700">Not Scheduled</span>
                    </div>
                </div>
            </div>

            <!-- Time Slot Display -->
            @if(count($timeSlotData) > 0)
                <div class="space-y-6">
                    @foreach($timeSlotData as $data)
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <!-- Staff Header -->
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                                            <span class="text-pink-600 font-semibold text-lg">
                                                {{ substr($data['staff']->first_name, 0, 1) }}{{ substr($data['staff']->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ $data['staff']->full_name ?? $data['staff']->name }}
                                            </h3>
                                            <p class="text-sm text-gray-600">{{ $data['staff']->email }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                Schedule: {{ substr($data['schedule']->start_time, 0, 5) }} - {{ substr($data['schedule']->end_time, 0, 5) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-700">{{ $date->format('l, F d, Y') }}</p>
                                        <p class="text-xs text-gray-500">{{ $dayOfWeek }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Time Slots Grid -->
                            <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                                @foreach($data['slots'] as $slot)
                                    <div class="relative">
                                        <div class="p-3 rounded-lg border-2 text-center transition-all duration-200
                                            @if($slot['status'] === 'available')
                                                bg-green-50 border-green-500 hover:bg-green-100
                                            @elseif($slot['status'] === 'occupied')
                                                bg-red-50 border-red-500 hover:bg-red-100
                                            @else
                                                bg-gray-50 border-gray-300
                                            @endif">
                                            <p class="text-xs font-medium 
                                                @if($slot['status'] === 'available') text-green-700
                                                @elseif($slot['status'] === 'occupied') text-red-700
                                                @else text-gray-500 @endif">
                                                {{ \Carbon\Carbon::createFromFormat('H:i', $slot['time'])->format('g:i A') }}
                                            </p>
                                            @if($slot['status'] === 'occupied' && $slot['appointment'])
                                                <div class="mt-1">
                                                    <p class="text-xs font-semibold text-red-800">
                                                        {{ ucfirst($slot['appointment']->status) }}
                                                    </p>
                                                    <p class="text-xs text-red-600 truncate" title="{{ $slot['appointment']->customer_name }}">
                                                        {{ Str::limit($slot['appointment']->customer_name, 10) }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                        @if($slot['status'] === 'occupied' && $slot['appointment'])
                                            <div class="absolute -top-2 -right-2 w-4 h-4 bg-red-500 rounded-full border-2 border-white"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Summary -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-4">
                                        <span class="text-gray-600">
                                            Total Slots: <strong>{{ count($data['slots']) }}</strong>
                                        </span>
                                        <span class="text-green-600">
                                            Available: <strong>{{ collect($data['slots'])->where('status', 'available')->count() }}</strong>
                                        </span>
                                        <span class="text-red-600">
                                            Occupied: <strong>{{ collect($data['slots'])->where('status', 'occupied')->count() }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Available Staff</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if($selectedStaffId)
                            The selected staff member is not scheduled for {{ $dayOfWeek }}.
                        @else
                            No staff members are scheduled for {{ $dayOfWeek }} on {{ $date->format('F d, Y') }}.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds to update occupied/available status
        setInterval(function() {
            const currentUrl = window.location.href;
            const url = new URL(currentUrl);
            url.searchParams.set('refresh', Date.now());
            fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            }).then(() => {
                // Optionally reload the page to show updated status
                // window.location.reload();
            }).catch(() => {
                // Silently fail if refresh fails
            });
        }, 30000); // 30 seconds
    </script>
</x-app-layout>

