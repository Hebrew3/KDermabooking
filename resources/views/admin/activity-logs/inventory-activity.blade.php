<x-app-layout>
    <x-mobile-header />
    <x-admin-sidebar />

    <div class="lg:ml-64">
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-pink-50">
            <!-- Enhanced Header -->
            <div class="bg-white/95 backdrop-blur-sm shadow-md border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3">
                                <div class="hidden sm:flex items-center justify-center w-12 h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-2xl sm:text-3xl font-extrabold bg-gradient-to-r from-pink-600 via-rose-500 to-pink-600 bg-clip-text text-transparent">
                                        Inventory Activity Log
                                    </h1>
                                    <p class="text-sm sm:text-base text-gray-600 mt-1">Track stock movement, usage, and remaining inventory</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button onclick="window.print()" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 sm:px-6 py-2.5 rounded-xl transition-all duration-200 flex items-center shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95 print:hidden">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                <span class="hidden sm:inline">Print Summary</span>
                                <span class="sm:hidden">Print</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <!-- Enhanced Filters Section -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 mb-6 print:hidden">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <h3 class="text-lg font-bold text-gray-800">Filters</h3>
                    </div>
                    <form method="GET" action="{{ route('admin.inventory-activity-log.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Product Name Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name/SKU</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" name="product_name" value="{{ request('product_name') }}" 
                                       placeholder="Search product..."
                                       class="w-full pl-10 pr-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all bg-white shadow-sm">
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-medium">
                                <option value="all" {{ request('category') == 'all' || !request('category') ? 'selected' : '' }}>All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range - Start Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-medium">
                        </div>

                        <!-- Date Range - End Date -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                                   class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-medium">
                        </div>

                        <!-- Staff Filter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Staff</label>
                            <select name="staff_id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-medium">
                                <option value="all" {{ request('staff_id') == 'all' || !request('staff_id') ? 'selected' : '' }}>All Staff</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Usage Type Filter -->
                        <div class="md:col-span-2 lg:col-span-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Usage Type</label>
                            <select name="usage_type" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-medium">
                                <option value="all" {{ request('usage_type') == 'all' || !request('usage_type') ? 'selected' : '' }}>All Types</option>
                                @foreach($usageTypes as $type)
                                    <option value="{{ $type }}" {{ request('usage_type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="flex gap-3 md:col-span-2 lg:col-span-5 pt-2">
                            <button type="submit" class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white px-6 py-2.5 rounded-xl transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                <span>Apply Filters</span>
                            </button>
                            <a href="{{ route('admin.inventory-activity-log.index') }}" 
                               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all duration-200 font-semibold flex items-center space-x-2 shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Clear</span>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Enhanced Summary Cards -->
                @if($activities && $activities->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6 print:hidden">
                    <div class="bg-white rounded-2xl shadow-lg border-2 border-blue-100 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Activities</p>
                                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">{{ $activities->count() }}</p>
                            </div>
                            <div class="h-12 w-12 sm:h-14 sm:w-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg border-2 border-red-100 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-semibold text-gray-600 uppercase tracking-wide">Used / Consumed</p>
                                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">
                                    {{ $activities->where('activity', 'Used / Consumed')->count() }}
                                </p>
                            </div>
                            <div class="h-12 w-12 sm:h-14 sm:w-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-lg border-2 border-green-100 p-4 sm:p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-semibold text-gray-600 uppercase tracking-wide">Restocks</p>
                                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">
                                    {{ $activities->where('activity', 'Restock')->count() }}
                                </p>
                            </div>
                            <div class="h-12 w-12 sm:h-14 sm:w-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="h-6 w-6 sm:h-7 sm:w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Report Header for Print -->
                <div class="hidden print:block mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Inventory Activity Log Report</h1>
                    <p class="text-gray-600 mt-1">Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
                    <p class="text-gray-600">Generated: {{ now()->format('M d, Y h:i A') }}</p>
                </div>

                <!-- Enhanced Inventory Activity Table -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b-2 border-gray-200 bg-gradient-to-r from-gray-50 to-white print:border-b-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Activity Log</h2>
                                <p class="text-sm text-gray-600 mt-1">
                                    <span class="font-semibold">Period:</span> {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($activities && $activities->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 print:bg-gray-100">
                                <tr>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Activity</th>
                                    <th class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Beginning Stock</th>
                                    <th class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Qty Change</th>
                                    <th class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Updated Stock</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($activities as $activity)
                                <tr class="hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 transition-all duration-200 print:hover:bg-white">
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($activity['date'])->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $activity['product_name'] }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $activity['product_sku'] }}</div>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full {{ $activity['activity'] === 'Restock' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300' }}">
                                            {{ $activity['activity'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-semibold text-gray-700">
                                            {{ number_format($activity['beginning_stock'], 0) }} {{ strtolower($activity['unit']) }}
                                        </span>
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right">
                                        @if($activity['quantity_change'] < 0)
                                            <span class="inline-flex items-center px-2 py-1 text-sm font-bold text-red-700 bg-red-50 rounded-lg border border-red-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                                </svg>
                                                −{{ number_format(abs($activity['quantity_change']), 0) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-sm font-bold text-green-700 bg-green-50 rounded-lg border border-green-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                +{{ number_format($activity['quantity_change'], 0) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-bold text-gray-900">
                                            {{ number_format($activity['updated_stock'], 0) }} {{ strtolower($activity['unit']) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="px-6 py-16 sm:py-20 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl mb-6 shadow-inner">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-2">No activity found</h3>
                        <p class="text-sm sm:text-base text-gray-600 max-w-md mx-auto">
                            Try adjusting your filters to see more results or check if there are any inventory activities in the selected period.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            body {
                background: white;
            }
            .print\:hidden {
                display: none !important;
            }
            .print\:block {
                display: block !important;
            }
            .print\:bg-gray-100 {
                background-color: #f3f4f6 !important;
            }
            .print\:border-b-2 {
                border-bottom-width: 2px !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</x-app-layout>
