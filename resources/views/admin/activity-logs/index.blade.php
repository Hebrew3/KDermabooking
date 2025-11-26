<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Activity Logs</h1>
                <p class="text-gray-600 mt-2">Track user activity across the system</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-gray-50 rounded-xl shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filters</h3>
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-wrap gap-4 items-end">
                <!-- Month Filter -->
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select name="month" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="all" {{ request('month') == 'all' || !request('month') ? 'selected' : '' }}>All Months</option>
                        @foreach($availableMonths as $month)
                            <option value="{{ $month['value'] }}" {{ request('month') == $month['value'] ? 'selected' : '' }}>
                                {{ $month['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Year Filter -->
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        <option value="all" {{ request('year') == 'all' || !request('year') ? 'selected' : '' }}>All Years</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="min-w-[150px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="text" name="date" value="{{ request('date') }}" placeholder="dd/mm/yyyy" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                           id="dateInput">
                </div>

                <!-- Clear Filters Button -->
                <div>
                    <a href="{{ route('admin.activity-logs.index') }}" 
                       class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Activity Log Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Activity Logs</h2>
            </div>

            @if($logs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">USER</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">EVENT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MODULE</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DESCRIPTION</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DATE</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $log->user ? $log->user->name : 'System' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($log->event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ucfirst($log->module) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at->format('M d, Y, h:i A') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $logs->withQueryString()->links() }}
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No activity logs found</h3>
                <p class="mt-1 text-sm text-gray-500">Activity logs will appear here as users perform actions in the inventory module.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Date input formatting
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('dateInput');
        if (dateInput) {
            dateInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2);
                }
                if (value.length >= 5) {
                    value = value.substring(0, 5) + '/' + value.substring(5, 9);
                }
                e.target.value = value;
            });
        }
    });
</script>
</x-app-layout>

