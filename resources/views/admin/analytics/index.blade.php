<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-pink-500 via-rose-500 to-pink-600 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 bg-white/20 backdrop-blur-sm rounded-xl border border-white/30 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold mb-2">Analytics Dashboard</h1>
                            <p class="text-pink-100 text-lg">Business insights and performance metrics</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full md:w-auto">
                        <select id="periodSelect" class="bg-white/90 backdrop-blur-sm border-2 border-white/30 rounded-xl px-4 py-2.5 text-gray-900 font-medium focus:ring-2 focus:ring-white focus:border-white shadow-lg transition-all duration-200">
                            <option value="7" {{ $period == '7' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="30" {{ $period == '30' ? 'selected' : '' }}>Last 30 days</option>
                            <option value="90" {{ $period == '90' ? 'selected' : '' }}>Last 90 days</option>
                            <option value="365" {{ $period == '365' ? 'selected' : '' }}>Last year</option>
                        </select>
                        <a href="{{ route('admin.analytics.export', ['period' => $period]) }}" class="bg-white text-pink-600 hover:bg-pink-50 font-semibold px-6 py-2.5 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Export Data</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl border border-white/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full border border-white/30">
                        {{ $revenueData['growth_percentage'] >= 0 ? '↑' : '↓' }} {{ abs($revenueData['growth_percentage']) }}%
                    </span>
                </div>
                <p class="text-green-100 text-sm font-medium mb-1">Total Revenue</p>
                <p class="text-3xl font-bold mb-2">₱{{ number_format($revenueData['total_revenue'], 2) }}</p>
                <p class="text-green-100 text-xs">Revenue growth</p>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl border border-white/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full border border-white/30">
                        {{ $appointmentData['completion_rate'] }}%
                    </span>
                </div>
                <p class="text-blue-100 text-sm font-medium mb-1">Total Appointments</p>
                <p class="text-3xl font-bold mb-2">{{ $appointmentData['total_appointments'] }}</p>
                <p class="text-blue-100 text-xs">Completion rate</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl shadow-xl p-6 text-white hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl border border-white/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full border border-white/30">
                        +{{ $clientData['new_clients'] }}
                    </span>
                </div>
                <p class="text-purple-100 text-sm font-medium mb-1">Active Clients</p>
                <p class="text-3xl font-bold mb-2">{{ $clientData['active_clients'] }}</p>
                <p class="text-purple-100 text-xs">New this period</p>
            </div>

            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl border border-white/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-amber-100 text-sm font-medium mb-1">Avg Order Value</p>
                <p class="text-3xl font-bold mb-2">₱{{ number_format($revenueData['avg_order_value'], 2) }}</p>
                <p class="text-amber-100 text-xs">Per appointment</p>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Trends -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Monthly Trends</h2>
                <div class="space-y-3">
                    @foreach($monthlyTrends as $trend)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div class="font-medium text-gray-800">{{ $trend['month'] }}</div>
                        <div class="flex space-x-4 text-sm">
                            <span class="text-green-600">₱{{ number_format($trend['revenue'], 0) }}</span>
                            <span class="text-blue-600">{{ $trend['appointments'] }} appts</span>
                            <span class="text-purple-600">{{ $trend['new_clients'] }} clients</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Appointment Status Distribution -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Appointment Status</h2>
                <div class="flex flex-col lg:flex-row gap-6">
                    <!-- Pie Chart -->
                    <div class="flex-1">
                        <canvas id="appointmentStatusChart" style="max-height: 400px;"></canvas>
                    </div>
                    <!-- Legend -->
                    <div class="lg:w-64">
                        <div class="space-y-3">
                            @php
                                $statusColors = [
                                    'pending' => '#F59E0B',      // yellow
                                    'confirmed' => '#10B981',   // green
                                    'in_progress' => '#3B82F6', // blue
                                    'completed' => '#8B5CF6',   // purple
                                    'cancelled' => '#EF4444',    // red
                                    'no_show' => '#6B7280'       // gray
                                ];
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                    'no_show' => 'No Show'
                                ];
                            @endphp
                            @foreach($appointmentData['status_distribution'] as $status => $count)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 rounded-full" style="background-color: {{ $statusColors[$status] ?? '#6B7280' }};"></div>
                                    <span class="font-medium text-gray-800">{{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-gray-900">{{ $count }}</span>
                                    <span class="text-sm text-gray-600">
                                        ({{ $appointmentData['total_appointments'] > 0 ? round(($count / $appointmentData['total_appointments']) * 100, 1) : 0 }}%)
                                    </span>
                                </div>
                            </div>
                            @endforeach
                            @if(empty($appointmentData['status_distribution']))
                            <div class="text-center py-8 text-gray-500">
                                <p>No appointment data available</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Services -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Top Services by Revenue</h2>
                <div class="space-y-4">
                    @forelse($serviceData['service_revenue'] as $service)
                    <div class="flex justify-between items-center p-3 bg-{{ ['pink', 'blue', 'green', 'purple', 'yellow'][($loop->index) % 5] }}-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-800">{{ $service->name }}</div>
                            <div class="text-sm text-gray-600">{{ $service->booking_count }} bookings</div>
                        </div>
                        <div class="text-lg font-bold text-{{ ['pink', 'blue', 'green', 'purple', 'yellow'][($loop->index) % 5] }}-600">
                            ₱{{ number_format($service->revenue, 0) }}
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No service data available for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- Staff Performance -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Staff Performance</h2>
                <div class="space-y-4">
                    @forelse($staffData['staff_performance'] as $staff)
                    <div class="flex justify-between items-center p-3 bg-{{ ['pink', 'blue', 'green', 'purple', 'yellow'][($loop->index) % 5] }}-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-800">{{ $staff->name }}</div>
                            <div class="text-sm text-gray-600">{{ ucfirst($staff->role) }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-{{ ['pink', 'blue', 'green', 'purple', 'yellow'][($loop->index) % 5] }}-600">
                                {{ $staff->staff_appointments_count }}
                            </div>
                            <div class="text-sm text-gray-600">
                                ₱{{ number_format($staff->staff_appointments_sum_total_amount ?? 0, 0) }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No staff data available for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Clients -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Top Clients</h2>
                <div class="space-y-4">
                    @forelse($clientData['top_clients'] as $client)
                    <div class="flex justify-between items-center p-3 bg-{{ ['pink', 'blue', 'green', 'purple', 'yellow'][($loop->index) % 5] }}-50 rounded-lg">
                        <div>
                            <div class="font-medium text-gray-800">{{ $client->name }}</div>
                            <div class="text-sm text-gray-600">{{ $client->client_appointments_count }} appointments</div>
                        </div>
                        <div class="text-lg font-bold text-{{ ['pink', 'blue', 'green', 'purple', 'yellow'][($loop->index) % 5] }}-600">
                            ₱{{ number_format($client->client_appointments_sum_total_amount, 0) }}
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No client data available for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- Inventory Overview -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Inventory Overview</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <div class="font-medium text-gray-800">Total Items</div>
                        <div class="text-lg font-bold text-blue-600">{{ $inventoryData['total_items'] }}</div>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <div class="font-medium text-gray-800">Low Stock Items</div>
                        <div class="text-lg font-bold text-red-600">{{ $inventoryData['low_stock_items'] }}</div>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                        <div class="font-medium text-gray-800">Expiring Soon</div>
                        <div class="text-lg font-bold text-yellow-600">{{ $inventoryData['expiring_soon_items'] }}</div>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <div class="font-medium text-gray-800">Total Value</div>
                        <div class="text-lg font-bold text-green-600">₱{{ number_format($inventoryData['total_inventory_value'], 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visualization Charts Section -->
        <div class="mt-10">
            <!-- Section Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="p-2 bg-gradient-to-br from-pink-500 to-rose-500 rounded-lg shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Analytics Visualizations</h2>
                        <p class="text-gray-600 mt-1">Interactive charts and insights for better decision making</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Staff Scheduling - Services Completed by Each Staff Member -->
                <div class="bg-gradient-to-br from-white to-pink-50 rounded-2xl shadow-lg border border-pink-100 p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Staff Scheduling Analytics</h3>
                                <p class="text-sm text-gray-600">Services completed by each staff member</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-inner border border-gray-100">
                        <div class="h-80">
                            <canvas id="staffSchedulingChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Services - Highest Demand Per Month -->
                <div class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-lg border border-blue-100 p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Services Analytics</h3>
                                <p class="text-sm text-gray-600">Service demand per month (Top 5 services)</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-inner border border-gray-100">
                        <div class="h-80">
                            <canvas id="servicesMonthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Sales Analytics -->
            <div class="mb-8">
                <div class="bg-gradient-to-br from-white to-green-50 rounded-2xl shadow-lg border border-green-100 p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Product Sales Analytics</h3>
                                <p class="text-sm text-gray-600">Top-selling products comparison</p>
                            </div>
                        </div>
                        <!-- Filters -->
                        <div class="flex flex-wrap gap-3">
                            <select id="yearFilter" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition-all duration-200">
                                <option value="">All Years</option>
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <select id="monthFilter" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition-all duration-200">
                                <option value="">All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                            <select id="productFilter" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition-all duration-200 min-w-[200px]">
                                <option value="">All Products</option>
                                @foreach($productSalesData['all_products'] ?? [] as $prod)
                                    <option value="{{ $prod->id }}" {{ $productId == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-inner border border-gray-100">
                        <div class="h-80">
                            <canvas id="productSalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

                <!-- Reorder Alerts -->
                <div class="bg-gradient-to-br from-white to-red-50 rounded-2xl shadow-lg border border-red-100 p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-gradient-to-br from-red-500 to-rose-500 rounded-xl shadow-md">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Reorder Alerts</h3>
                                <p class="text-sm text-gray-600">Products that need to be reordered</p>
                            </div>
                        </div>
                        @if(count($productSalesData['reorder_alerts']) > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800 border border-red-200">
                            {{ count($productSalesData['reorder_alerts']) }} Alert{{ count($productSalesData['reorder_alerts']) > 1 ? 's' : '' }}
                        </span>
                        @endif
                    </div>
                    <div class="max-h-80 overflow-y-auto custom-scrollbar">
                        @if(count($productSalesData['reorder_alerts']) > 0)
                        <div class="space-y-3">
                            @foreach($productSalesData['reorder_alerts'] as $alert)
                            <div class="group p-4 bg-white border-2 border-red-200 rounded-xl hover:border-red-400 hover:shadow-md transition-all duration-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h4 class="font-bold text-gray-900 text-lg">{{ $alert['name'] }}</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800">
                                                Low Stock
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-3">
                                            <span class="font-medium">SKU:</span> {{ $alert['sku'] }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="p-2 bg-red-50 rounded-lg border border-red-100">
                                                <p class="text-xs font-medium text-gray-600 mb-1">Current Stock</p>
                                                <p class="text-lg font-bold text-red-600">{{ $alert['current_stock'] }}</p>
                                            </div>
                                            <div class="p-2 bg-gray-50 rounded-lg border border-gray-200">
                                                <p class="text-xs font-medium text-gray-600 mb-1">Minimum Required</p>
                                                <p class="text-lg font-bold text-gray-900">{{ $alert['minimum_stock'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex flex-col items-center justify-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-800 border border-red-300 whitespace-nowrap">
                                            Need: {{ $alert['needed'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-12 bg-white rounded-xl border-2 border-dashed border-green-200">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 mb-2">All Products Well-Stocked!</h4>
                            <p class="text-sm text-gray-500">No reorder alerts at this time</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #ec4899;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #db2777;
    }
</style>

<script>
    // Handle period selection change
    document.getElementById('periodSelect').addEventListener('change', function() {
        const period = this.value;
        window.location.href = `{{ route('admin.analytics') }}?period=${period}`;
    });

    // Staff Scheduling Chart (Bar Chart)
    const staffSchedulingCtx = document.getElementById('staffSchedulingChart').getContext('2d');
    new Chart(staffSchedulingCtx, {
        type: 'bar',
        data: {
            labels: @json($staffSchedulingData['staff']),
            datasets: [{
                label: 'Services Completed',
                data: @json($staffSchedulingData['services_completed']),
                backgroundColor: 'rgba(236, 72, 153, 0.8)',
                borderColor: 'rgba(236, 72, 153, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Services: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Number of Services Completed'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Staff Members'
                    }
                }
            }
        }
    });

    // Services Monthly Chart (Line Chart)
    const servicesMonthlyCtx = document.getElementById('servicesMonthlyChart').getContext('2d');
    
    // Prepare data for top services across months
    const topServicesLabels = @json($servicesMonthlyData['top_services']);
    const months = @json($servicesMonthlyData['months']);
    
    // Create datasets for top 5 services
    const top5Services = topServicesLabels.slice(0, 5);
    const datasets = top5Services.map((serviceName, index) => {
        const colors = [
            'rgba(236, 72, 153, 0.8)',  // pink
            'rgba(59, 130, 246, 0.8)',  // blue
            'rgba(16, 185, 129, 0.8)',  // green
            'rgba(139, 92, 246, 0.8)',  // purple
            'rgba(245, 158, 11, 0.8)',  // yellow
        ];
        
        const data = months.map(month => {
            const monthData = @json($servicesMonthlyData['services_data']).find(m => m.month === month);
            if (monthData) {
                const serviceIndex = monthData.services.indexOf(serviceName);
                return serviceIndex !== -1 ? monthData.counts[serviceIndex] : 0;
            }
            return 0;
        });
        
        return {
            label: serviceName,
            data: data,
            borderColor: colors[index],
            backgroundColor: colors[index].replace('0.8', '0.1'),
            borderWidth: 2,
            fill: true,
            tension: 0.4
        };
    });

    new Chart(servicesMonthlyCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Number of Appointments'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // Appointment Status Pie Chart
    @if(!empty($appointmentData['status_distribution']))
    const appointmentStatusCtx = document.getElementById('appointmentStatusChart');
    if (appointmentStatusCtx) {
        const appointmentStatusChart = new Chart(appointmentStatusCtx.getContext('2d'), {
            type: 'pie',
            data: {
                labels: [
                    @foreach($appointmentData['status_distribution'] as $status => $count)
                        '{{ ucfirst(str_replace("_", " ", $status)) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($appointmentData['status_distribution'] as $status => $count)
                            {{ $count }},
                        @endforeach
                    ],
                    backgroundColor: [
                        @php
                            $statusColors = [
                                'pending' => '#F59E0B',
                                'confirmed' => '#10B981',
                                'in_progress' => '#3B82F6',
                                'completed' => '#8B5CF6',
                                'cancelled' => '#EF4444',
                                'no_show' => '#6B7280'
                            ];
                        @endphp
                        @foreach($appointmentData['status_distribution'] as $status => $count)
                            '{{ $statusColors[$status] ?? '#6B7280' }}',
                        @endforeach
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    // Product Sales Chart (Bar Chart with Comparison)
    const productSalesCtx = document.getElementById('productSalesChart').getContext('2d');
    const productSalesChart = new Chart(productSalesCtx, {
        type: 'bar',
        data: {
            labels: @json($productSalesData['top_products'] ?? []),
            datasets: [{
                label: 'Selected Period',
                data: @json($productSalesData['selected_period_quantities'] ?? []),
                backgroundColor: 'rgba(59, 130, 246, 0.8)', // Blue
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }, {
                label: 'Previous Period',
                data: @json($productSalesData['previous_period_quantities'] ?? []),
                backgroundColor: 'rgba(156, 163, 175, 0.8)', // Light grey
                borderColor: 'rgba(156, 163, 175, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' units';
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Top-Selling Products (Units Sold)',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 50,
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Units Sold'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Products'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Handle filter changes
    document.getElementById('yearFilter')?.addEventListener('change', function() {
        applyFilters();
    });
    
    document.getElementById('monthFilter')?.addEventListener('change', function() {
        applyFilters();
    });
    
    document.getElementById('productFilter')?.addEventListener('change', function() {
        applyFilters();
    });

    function applyFilters() {
        const year = document.getElementById('yearFilter')?.value || '';
        const month = document.getElementById('monthFilter')?.value || '';
        const product = document.getElementById('productFilter')?.value || '';
        const period = document.getElementById('periodSelect')?.value || '30';
        
        const params = new URLSearchParams();
        if (period) params.append('period', period);
        if (year) params.append('year', year);
        if (month) params.append('month', month);
        if (product) params.append('product', product);
        
        window.location.href = `{{ route('admin.analytics') }}?${params.toString()}`;
    }
</script>

</x-app-layout>
