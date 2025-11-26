<x-app-layout>
    <x-mobile-header />

    <!-- Include Admin Sidebar -->
    <x-admin-sidebar />

    <!-- Main Content -->
    <div class="lg:ml-64">
        <div class="p-8">
            <!-- Page Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Staff Schedule</h1>
                    <p class="text-gray-600 mt-2">View and manage weekly working hours for all staff members.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                            @foreach($daysOfWeek as $day)
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ ucfirst($day) }}</th>
                            @endforeach
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($staff as $staffMember)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $staffMember->full_name ?? $staffMember->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $staffMember->email }}</div>
                                </td>
                                @foreach($daysOfWeek as $day)
                                    @php
                                        $schedule = $staffMember->staffSchedules->firstWhere('day_of_week', $day);
                                    @endphp
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @if($schedule && $schedule->is_available && $schedule->start_time && $schedule->end_time)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-800 border border-green-200">
                                                {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Off</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.staff-schedule.edit', $staffMember) }}" class="text-pink-600 hover:text-pink-800">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($daysOfWeek) + 2 }}" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No staff records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
