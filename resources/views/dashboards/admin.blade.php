<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<div class="lg:ml-64">
    <div class="p-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-gradient-to-br from-pink-500 to-rose-500 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80">Total Users</p>
                            <p class="text-3xl font-bold text-white">{{ $stats['users']['total'] }}</p>
                            <p class="text-sm text-white/90 flex items-center mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                                {{ $stats['users']['new_clients_this_month'] }} new this month
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Appointments -->
                <div class="bg-gradient-to-br from-blue-500 to-indigo-500 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80">Total Appointments</p>
                            <p class="text-3xl font-bold text-white">{{ $stats['appointments']['total'] }}</p>
                            <p class="text-sm text-white/90 flex items-center mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                {{ $stats['appointments']['today'] }} scheduled today
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80">Monthly Revenue</p>
                            <p class="text-3xl font-bold text-white">₱{{ number_format($stats['revenue']['this_month'], 2) }}</p>
                            <p class="text-sm text-white/90 flex items-center mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stats['revenue']['growth'] >= 0 ? 'M7 11l5-5m0 0l5 5m-5-5v12' : 'M17 13l-5 5m0 0l-5-5m5 5V6' }}"></path>
                                </svg>
                                {{ $stats['revenue']['growth'] >= 0 ? '+' : '' }}{{ number_format($stats['revenue']['growth'], 1) }}% from last month
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Services -->
                <div class="bg-gradient-to-br from-purple-500 to-violet-500 rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80">Active Services</p>
                            <p class="text-3xl font-bold text-white">{{ $stats['services']['active'] }}</p>
                            <p class="text-sm text-white/90 flex items-center mt-1">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                {{ $stats['services']['featured'] }} featured
                            </p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-8 mb-8">
                <!-- Monthly Revenue Bar Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Monthly Revenue</h3>
                        <!-- Filters and Export -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.dashboard.export.monthly-revenue', request()->only(['revenue_year', 'revenue_month'])) }}" 
                               class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Export CSV</span>
                            </a>
                            <select id="revenueYearSelect" name="revenue_year" class="px-4 py-2 border-2 {{ request()->get('revenue_year') ? 'border-pink-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition-all duration-200">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ request()->get('revenue_year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <select id="revenueMonthSelect" name="revenue_month" class="px-4 py-2 border-2 {{ request()->get('revenue_month') ? 'border-pink-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition-all duration-200">
                                <option value="all" {{ request()->get('revenue_month') == 'all' || !request()->get('revenue_month') ? 'selected' : '' }}>All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request()->get('revenue_month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Analytics Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Staff Scheduling Analytics -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-pink-500 to-rose-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Staff Scheduling Analytics</h3>
                                <p class="text-sm text-gray-600">Services completed by each staff member</p>
                            </div>
                        </div>
                        <!-- Year and Month Filter Dropdowns and Export -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.dashboard.export.staff-scheduling', request()->only(['staff_year', 'staff_month'])) }}" 
                               class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Export CSV</span>
                            </a>
                            <select id="staffYearSelect" name="staff_year" class="px-4 py-2 border-2 {{ request()->get('staff_year') ? 'border-pink-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition-all duration-200">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ request()->get('staff_year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <select id="staffMonthSelect" name="staff_month" class="px-4 py-2 border-2 {{ request()->get('staff_month') ? 'border-pink-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition-all duration-200">
                                <option value="all" {{ request()->get('staff_month') == 'all' || !request()->get('staff_month') ? 'selected' : '' }}>All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request()->get('staff_month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="staffSchedulingChart"></canvas>
                    </div>
                </div>

                <!-- Services Analytics -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Services Analytics</h3>
                                <p class="text-sm text-gray-600">Service demand per month (All services with appointments)</p>
                            </div>
                        </div>
                        <!-- Year and Month Filter Dropdowns and Export -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.dashboard.export.services-analytics', request()->only(['services_year', 'services_month'])) }}" 
                               class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Export CSV</span>
                            </a>
                            <select id="servicesYearSelect" name="services_year" class="px-4 py-2 border-2 {{ request()->get('services_year') ? 'border-pink-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition-all duration-200">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ request()->get('services_year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <select id="servicesMonthSelect" name="services_month" class="px-4 py-2 border-2 {{ request()->get('services_month') ? 'border-pink-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition-all duration-200">
                                <option value="all" {{ request()->get('services_month') == 'all' || !request()->get('services_month') ? 'selected' : '' }}>All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request()->get('services_month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="servicesMonthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Unified Product Analytics (Sales & Usage) -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Product Analytics</h3>
                            <p class="text-sm text-gray-600">Sales & Usage analytics</p>
                        </div>
                    </div>
                    <!-- Filters and Export -->
                    <div class="flex items-center space-x-2 flex-wrap">
                        <a href="{{ route('admin.dashboard.export.unified-analytics', request()->only(['analytics_year', 'analytics_month', 'product_category', 'metric_type', 'analytics_product'])) }}" 
                           class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Export CSV</span>
                        </a>
                        <!-- Metric Type Toggle -->
                        <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
                            <button type="button" id="metricTypeSales" class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ ($metricType ?? 'sales') === 'sales' ? 'bg-indigo-500 text-white' : 'text-gray-700 hover:text-gray-900' }}">
                                Sales
                            </button>
                            <button type="button" id="metricTypeUsage" class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ ($metricType ?? 'sales') === 'usage' ? 'bg-indigo-500 text-white' : 'text-gray-700 hover:text-gray-900' }}">
                                Usage
                            </button>
                        </div>
                        <select id="analyticsYearFilter" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all duration-200">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ ($analyticsYear ?? now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <select id="analyticsMonthFilter" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all duration-200">
                            <option value="">All Months</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ ($analyticsMonth ?? null) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                        <!-- Category filter is auto-set based on metric type (hidden) -->
                        <select id="productCategoryFilter" class="hidden">
                            <option value="aftercare" {{ ($metricType ?? 'sales') === 'sales' ? 'selected' : '' }}>Aftercare Products</option>
                            <option value="treatment" {{ ($metricType ?? 'sales') === 'usage' ? 'selected' : '' }}>Treatment Products</option>
                        </select>
                        <select id="analyticsProductFilter" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition-all duration-200 min-w-[200px]">
                            <option value="">All Products</option>
                            @foreach($allAnalyticsProducts ?? [] as $product)
                                <option value="{{ $product->id }}" {{ ($analyticsProductId ?? null) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="h-80">
                    <div id="unifiedAnalyticsEmptyState" class="hidden flex items-center justify-center h-full text-gray-500">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-lg font-medium">No data available</p>
                            <p class="text-sm mt-1">No data found for the selected filters.</p>
                        </div>
                    </div>
                    <canvas id="unifiedAnalyticsChart"></canvas>
                </div>
            </div>

            <!-- New Dashboard Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-1 gap-8 mb-8">
                <!-- Appointment Status Pie Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Appointment Status</h3>
                                <p class="text-sm text-gray-600">Current appointment breakdown</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="appointmentStatusPieChart"></canvas>
                    </div>
                    <!-- Status Legend with Percentages -->
                    <div class="mt-6 space-y-2">
                        @php
                            $totalAppointments = array_sum($appointmentStatusData);
                            $statusColors = [
                                'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-200'],
                                'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200'],
                                'in_progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-200'],
                                'completed' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-200'],
                                'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200'],
                                'no_show' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200'],
                            ];
                        @endphp
                        @foreach($appointmentStatusData as $status => $count)
                            @php
                                $percentage = $totalAppointments > 0 ? round(($count / $totalAppointments) * 100, 1) : 0;
                                $colors = $statusColors[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200'];
                            @endphp
                            <div class="flex items-center justify-between p-2 rounded-lg border {{ $colors['border'] }} {{ $colors['bg'] }}">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 rounded-full {{ $colors['bg'] }} border {{ $colors['border'] }}"></div>
                                    <span class="font-medium {{ $colors['text'] }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium {{ $colors['text'] }}">{{ $count }} appointments</span>
                                    <span class="font-bold {{ $colors['text'] }}">{{ $percentage }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Top Services by Revenue & Top Clients -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Top Services by Revenue -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-pink-500 to-rose-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Top Services by Revenue</h3>
                                <p class="text-sm text-gray-600">Top earners by period</p>
                            </div>
                        </div>
                        <!-- Year and Month Filter Dropdowns and Export -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.dashboard.export.top-services', request()->only(['services_revenue_year', 'services_revenue_month'])) }}" 
                               class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Export CSV</span>
                            </a>
                            <select id="servicesRevenueYearSelect" name="services_revenue_year" class="px-4 py-2 border-2 {{ request()->get('services_revenue_year') ? 'border-pink-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition-all duration-200">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ request()->get('services_revenue_year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <select id="servicesRevenueMonthSelect" name="services_revenue_month" class="px-4 py-2 border-2 {{ request()->get('services_revenue_month') ? 'border-pink-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 shadow-sm transition-all duration-200">
                                <option value="all" {{ request()->get('services_revenue_month') == 'all' || !request()->get('services_revenue_month') ? 'selected' : '' }}>All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request()->get('services_revenue_month', now()->month) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse($topServicesByRevenue as $index => $service)
                            <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-pink-50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-pink-400 to-rose-400 text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $service->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $service->completed_count ?? 0 }} completed</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-pink-600">₱{{ number_format($service->total_revenue ?? 0, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500">No revenue data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Clients -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-purple-500 to-violet-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Top Clients</h3>
                                <p class="text-sm text-gray-600">Highest spending clients by period</p>
                            </div>
                        </div>
                        <!-- Year and Month Filter Dropdowns and Export -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.dashboard.export.top-clients', request()->only(['clients_year', 'clients_month'])) }}" 
                               class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Export CSV</span>
                            </a>
                            <select id="clientsYearSelect" name="clients_year" class="px-4 py-2 border-2 {{ request()->get('clients_year') ? 'border-purple-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-sm transition-all duration-200">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ request()->get('clients_year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <select id="clientsMonthSelect" name="clients_month" class="px-4 py-2 border-2 {{ request()->get('clients_month') ? 'border-purple-500' : 'border-gray-300' }} rounded-lg bg-white text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 shadow-sm transition-all duration-200">
                                <option value="all" {{ request()->get('clients_month') == 'all' || !request()->get('clients_month') ? 'selected' : '' }}>All Months</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request()->get('clients_month', now()->month) == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse($topClients as $index => $client)
                            <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-purple-50 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-violet-400 text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $client->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $client->total_appointments ?? 0 }} appointments</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-purple-600">₱{{ number_format($client->total_spent ?? 0, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500">No client data available</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Inventory Overview & Reorder Alerts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Inventory Overview -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-orange-500 to-amber-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Inventory Overview</h3>
                                <p class="text-sm text-gray-600">Current inventory status</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.inventory.index') }}" class="text-orange-600 text-sm font-medium hover:text-orange-800">View all</a>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="p-4 bg-gradient-to-br from-orange-50 to-amber-50 rounded-lg border border-orange-200">
                            <p class="text-sm text-gray-600 mb-1">Total Items</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $totalInventoryItems }}</p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg border border-green-200">
                            <p class="text-sm text-gray-600 mb-1">Active Items</p>
                            <p class="text-2xl font-bold text-green-600">{{ $activeInventoryItems }}</p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-red-50 to-rose-50 rounded-lg border border-red-200">
                            <p class="text-sm text-gray-600 mb-1">Low Stock</p>
                            <p class="text-2xl font-bold text-red-600">{{ $lowStockItems }}</p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                            <p class="text-sm text-gray-600 mb-1">Total Value</p>
                            <p class="text-2xl font-bold text-blue-600">₱{{ number_format($totalInventoryValue, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reorder Alerts -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-gradient-to-br from-red-500 to-rose-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Reorder Alerts</h3>
                                <p class="text-sm text-gray-600">Items needing restock</p>
                            </div>
                        </div>
                        @if($reorderAlerts->count() > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $reorderAlerts->count() }} alerts
                            </span>
                        @endif
                    </div>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($reorderAlerts as $item)
                            <div class="flex items-center justify-between p-4 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 transition-colors">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800">{{ $item->name }}</p>
                                    <p class="text-sm text-gray-600">SKU: {{ $item->sku }}</p>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <span class="text-xs text-red-600 font-medium">
                                            Stock: {{ $item->current_stock }} {{ $item->unit ?? '' }}
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            Min: {{ $item->minimum_stock }} {{ $item->unit ?? '' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    @php
                                        $stockPercentage = $item->minimum_stock > 0 ? ($item->current_stock / $item->minimum_stock) * 100 : 0;
                                    @endphp
                                    @if($stockPercentage <= 50)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-600 text-white">
                                            Critical
                                        </span>
                                    @elseif($stockPercentage <= 75)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-600 text-white">
                                            Low
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-600 text-white">
                                            Warning
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">All items well stocked</h3>
                                <p class="mt-1 text-sm text-gray-500">No reorder alerts at this time.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Appointments</h3>
                    <a href="{{ route('admin.appointments.index') }}" class="text-pink-600 text-sm font-medium">View all</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentAppointments as $appointment)
                    <div class="flex items-center justify-between p-4 bg-{{ ['pink', 'blue', 'purple', 'green', 'indigo'][($loop->index) % 5] }}-50 rounded-xl border border-{{ ['pink', 'blue', 'purple', 'green', 'indigo'][($loop->index) % 5] }}-100">
                        <div class="flex items-center space-x-4">
                            <div class="h-10 w-10 bg-gradient-to-br from-{{ ['pink', 'blue', 'purple', 'green', 'indigo'][($loop->index) % 5] }}-400 to-{{ ['rose', 'indigo', 'violet', 'emerald', 'purple'][($loop->index) % 5] }}-400 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium text-sm">{{ strtoupper(substr($appointment->customer_name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-neutral-800">{{ $appointment->customer_name }}</p>
                                <p class="text-sm text-neutral-600">{{ $appointment->service ? $appointment->service->name : ($appointment->services->first() ? $appointment->services->first()->name : 'N/A') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-neutral-800">{{ $appointment->formatted_date_time }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $appointment->status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $appointment->status)) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No appointments yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Appointments will appear here once they are created.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Monthly Revenue Bar Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($monthlyRevenue as $month)
                    '{{ $month['month'] }}',
                @endforeach
            ],
            datasets: [{
                label: 'Revenue (₱)',
                data: [
                    @foreach($monthlyRevenue as $month)
                        {{ $month['revenue'] }},
                    @endforeach
                ],
                backgroundColor: [
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgba(236, 72, 153, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Appointment Status Pie Chart (for dashboard section)
    @php
        $totalAppointments = array_sum($appointmentStatusData);
        $statusOrder = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'];
        $statusLabels = [];
        $statusData = [];
        $statusColors = [];
        $statusPercentages = [];
        
        foreach ($statusOrder as $status) {
            if (isset($appointmentStatusData[$status])) {
                $statusLabels[] = ucfirst(str_replace('_', ' ', $status));
                $statusData[] = $appointmentStatusData[$status];
                $percentage = $totalAppointments > 0 ? round(($appointmentStatusData[$status] / $totalAppointments) * 100, 1) : 0;
                $statusPercentages[] = $percentage;
                
                // Match colors with the legend
                switch($status) {
                    case 'pending':
                        $statusColors[] = '#F59E0B'; // yellow
                        break;
                    case 'confirmed':
                        $statusColors[] = '#10B981'; // green
                        break;
                    case 'in_progress':
                        $statusColors[] = '#3B82F6'; // blue
                        break;
                    case 'completed':
                        $statusColors[] = '#8B5CF6'; // purple
                        break;
                    case 'cancelled':
                        $statusColors[] = '#EF4444'; // red
                        break;
                    case 'no_show':
                        $statusColors[] = '#6B7280'; // gray
                        break;
                    default:
                        $statusColors[] = '#9CA3AF'; // default gray
                }
            }
        }
    @endphp
    const appointmentStatusPieCtx = document.getElementById('appointmentStatusPieChart').getContext('2d');
    const appointmentStatusPieChart = new Chart(appointmentStatusPieCtx, {
        type: 'pie',
        data: {
            labels: @json($statusLabels),
            datasets: [{
                data: @json($statusData),
                backgroundColor: @json($statusColors),
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverBorderWidth: 3,
                hoverBorderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Hide legend since we have custom legend below
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

    // Staff Scheduling Analytics Chart
    let staffSchedulingChart;
    const staffSchedulingCtx = document.getElementById('staffSchedulingChart').getContext('2d');
    
    function initStaffSchedulingChart(data) {
        if (staffSchedulingChart) {
            staffSchedulingChart.destroy();
        }
        
        staffSchedulingChart = new Chart(staffSchedulingCtx, {
            type: 'bar',
            data: {
                labels: data.staff,
                datasets: [{
                    label: 'Services Completed',
                    data: data.services_completed,
                    backgroundColor: 'rgba(236, 72, 153, 0.8)',
                    borderColor: 'rgba(236, 72, 153, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        title: {
                            display: true,
                            text: 'Number of Services Completed'
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        },
                        title: {
                            display: true,
                            text: 'Staff Members'
                        }
                    }
                }
            }
        });
    }
    
    initStaffSchedulingChart({
        staff: @json($staffSchedulingData['staff']),
        services_completed: @json($staffSchedulingData['services_completed'])
    });

    // Staff Scheduling Filter Handlers
    const staffYearSelect = document.getElementById('staffYearSelect');
    const staffMonthSelect = document.getElementById('staffMonthSelect');

    // Helper function to build URL with all query parameters
    function buildDashboardUrl(newParams) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        
        // Update or add new parameters
        Object.keys(newParams).forEach(key => {
            if (newParams[key] !== null && newParams[key] !== undefined) {
                params.set(key, newParams[key]);
            }
        });
        
        return `{{ route('admin.dashboard') }}?${params.toString()}`;
    }

    // Handle year change
    if (staffYearSelect) {
        staffYearSelect.addEventListener('change', function() {
            const year = this.value;
            const month = staffMonthSelect?.value || 'all';
            const urlParams = new URLSearchParams(window.location.search);
            
            const params = { staff_year: year };
            if (month) params.staff_month = month;
            
            // Preserve all other filters
            ['revenue_year', 'revenue_month', 'services_year', 'services_month', 'year', 'month', 'product', 'services_revenue_year', 'services_revenue_month', 'clients_year', 'clients_month', 'treatment_products_year', 'treatment_products_month'].forEach(key => {
                const value = urlParams.get(key);
                if (value && !params[key]) params[key] = value;
            });
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    if (staffMonthSelect) {
        staffMonthSelect.addEventListener('change', function() {
            const month = this.value;
            const year = staffYearSelect?.value || '{{ now()->year }}';
            const urlParams = new URLSearchParams(window.location.search);
            
            const params = { staff_month: month };
            if (year) params.staff_year = year;
            
            // Preserve all other filters
            ['revenue_year', 'revenue_month', 'services_year', 'services_month', 'year', 'month', 'product', 'services_revenue_year', 'services_revenue_month', 'clients_year', 'clients_month', 'treatment_products_year', 'treatment_products_month'].forEach(key => {
                const value = urlParams.get(key);
                if (value && !params[key]) params[key] = value;
            });
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    

    // Services Monthly Analytics Chart
    let servicesMonthlyChart;
    const servicesMonthlyCtx = document.getElementById('servicesMonthlyChart').getContext('2d');
    
    const serviceColors = [
        'rgba(236, 72, 153, 0.8)',  // pink
        'rgba(59, 130, 246, 0.8)',  // blue
        'rgba(16, 185, 129, 0.8)',  // green
        'rgba(139, 92, 246, 0.8)',  // purple
        'rgba(245, 158, 11, 0.8)',  // yellow
    ];

    const serviceBorderColors = [
        'rgba(236, 72, 153, 1)',
        'rgba(59, 130, 246, 1)',
        'rgba(16, 185, 129, 1)',
        'rgba(139, 92, 246, 1)',
        'rgba(245, 158, 11, 1)',
    ];

    function initServicesMonthlyChart(data) {
        if (servicesMonthlyChart) {
            servicesMonthlyChart.destroy();
        }
        
        const servicesDatasets = data.chart_data.map((service, index) => ({
            label: service.name,
            data: service.data,
            backgroundColor: serviceColors[index % serviceColors.length],
            borderColor: serviceBorderColors[index % serviceBorderColors.length],
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }));

        servicesMonthlyChart = new Chart(servicesMonthlyCtx, {
            type: 'line',
            data: {
                labels: data.months,
                datasets: servicesDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
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
                }
            }
        });
    }
    
    initServicesMonthlyChart({
        months: @json($months),
        chart_data: @json($servicesMonthlyChartData),
        top_services: @json($topServices)
    });

    // Unified Product Analytics Chart
    let unifiedAnalyticsChart;
    const unifiedAnalyticsCtx = document.getElementById('unifiedAnalyticsChart');
    const unifiedAnalyticsEmptyState = document.getElementById('unifiedAnalyticsEmptyState');
    
    const unifiedAnalyticsData = @json($unifiedAnalyticsData ?? []);
    const currentMetricType = '{{ $metricType ?? "sales" }}';
    
    function updateUnifiedAnalyticsChart(data, metricType) {
        // Hide empty state
        if (unifiedAnalyticsEmptyState) {
            unifiedAnalyticsEmptyState.classList.add('hidden');
        }
        
        // Show chart
        if (unifiedAnalyticsCtx) {
            unifiedAnalyticsCtx.style.display = 'block';
        }
        
        // Destroy existing chart
        if (unifiedAnalyticsChart) {
            unifiedAnalyticsChart.destroy();
        }
        
        // Check if data is empty
        if (!data || data.length === 0) {
            if (unifiedAnalyticsCtx) {
                unifiedAnalyticsCtx.style.display = 'none';
            }
            if (unifiedAnalyticsEmptyState) {
                unifiedAnalyticsEmptyState.classList.remove('hidden');
            }
            return;
        }
        
        const ctx = unifiedAnalyticsCtx.getContext('2d');
        
        let datasets = [];
        let yAxisTitle = '';
        
        if (metricType === 'sales') {
            // Sales metric: Show comparison with previous period
            datasets = [
                {
                    label: 'Selected Period',
                    data: data.map(p => p.selected_period || 0),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                },
                {
                    label: 'Previous Period',
                    data: data.map(p => p.previous_period || 0),
                    backgroundColor: 'rgba(156, 163, 175, 0.8)',
                    borderColor: 'rgba(156, 163, 175, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                }
            ];
            yAxisTitle = 'Units Sold';
        } else {
            // Usage metric: Show total usage
            datasets = [{
                label: 'Total Usage',
                data: data.map(p => p.total_usage || 0),
                backgroundColor: 'rgba(147, 51, 234, 0.8)',
                borderColor: 'rgba(147, 51, 234, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }];
            yAxisTitle = 'Total Usage (per container)';
        }
        
        unifiedAnalyticsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(p => p.name),
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (metricType === 'usage') {
                                    label += context.parsed.y.toFixed(2);
                                    const dataIndex = context.dataIndex;
                                    if (data[dataIndex] && data[dataIndex].usage_count) {
                                        label += ' (Used ' + data[dataIndex].usage_count + ' times)';
                                    }
                                } else {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        },
                        title: {
                            display: true,
                            text: 'Products'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: yAxisTitle
                        }
                    }
                }
            }
        });
    }
    
    // Initialize chart with initial data
    if (unifiedAnalyticsCtx) {
        updateUnifiedAnalyticsChart(unifiedAnalyticsData, currentMetricType);
    }
    
    // Update product filter dropdown based on category
    function updateProductFilterDropdown() {
        const category = document.getElementById('productCategoryFilter')?.value || 'all';
        const productFilter = document.getElementById('analyticsProductFilter');
        
        if (!productFilter) return;
        
        // This will be handled by page reload, but we can update the options if needed
        // For now, we'll just reload the page when category changes
    }
    
    // Filter change handlers
    const analyticsYearFilter = document.getElementById('analyticsYearFilter');
    const analyticsMonthFilter = document.getElementById('analyticsMonthFilter');
    const productCategoryFilter = document.getElementById('productCategoryFilter');
    const analyticsProductFilter = document.getElementById('analyticsProductFilter');
    const metricTypeSales = document.getElementById('metricTypeSales');
    const metricTypeUsage = document.getElementById('metricTypeUsage');
    
    function updateUnifiedAnalytics() {
        const year = analyticsYearFilter?.value || new Date().getFullYear();
        const month = analyticsMonthFilter?.value || '';
        const product = analyticsProductFilter?.value || '';
        const metricType = metricTypeSales?.classList.contains('bg-indigo-500') ? 'sales' : 'usage';
        
        // Auto-set category based on metric type
        const category = (metricType === 'sales') ? 'aftercare' : 'treatment';
        
        // Update hidden category filter
        if (productCategoryFilter) {
            productCategoryFilter.value = category;
        }
        
        // Build params
        const params = new URLSearchParams();
        params.append('analytics_year', year);
        if (month) params.append('analytics_month', month);
        params.append('product_category', category);
        params.append('metric_type', metricType);
        if (product) params.append('analytics_product', product);
        
        // Preserve other unrelated filters from URL
        const urlParams = new URLSearchParams(window.location.search);
        const otherFilters = ['revenue_year', 'revenue_month', 'services_year', 'services_month', 
                             'staff_year', 'staff_month', 'clients_year', 'clients_month', 
                             'services_revenue_year', 'services_revenue_month'];
        
        otherFilters.forEach(filter => {
            const value = urlParams.get(filter);
            if (value) params.append(filter, value);
        });
        
        window.location.href = '{{ route("admin.dashboard") }}?' + params.toString();
    }
    
    if (analyticsYearFilter) {
        analyticsYearFilter.addEventListener('change', updateUnifiedAnalytics);
    }
    
    if (analyticsMonthFilter) {
        analyticsMonthFilter.addEventListener('change', updateUnifiedAnalytics);
    }
    
    // Category filter is auto-set, no need for change listener
    
    if (analyticsProductFilter) {
        analyticsProductFilter.addEventListener('change', updateUnifiedAnalytics);
    }
    
    if (metricTypeSales) {
        metricTypeSales.addEventListener('click', function() {
            metricTypeSales.classList.add('bg-indigo-500', 'text-white');
            metricTypeSales.classList.remove('text-gray-700');
            metricTypeUsage.classList.remove('bg-indigo-500', 'text-white');
            metricTypeUsage.classList.add('text-gray-700');
            // Auto-set category to 'aftercare' for Sales tab
            if (productCategoryFilter) {
                productCategoryFilter.value = 'aftercare';
            }
            updateUnifiedAnalytics();
        });
    }
    
    if (metricTypeUsage) {
        metricTypeUsage.addEventListener('click', function() {
            metricTypeUsage.classList.add('bg-indigo-500', 'text-white');
            metricTypeUsage.classList.remove('text-gray-700');
            metricTypeSales.classList.remove('bg-indigo-500', 'text-white');
            metricTypeSales.classList.add('text-gray-700');
            // Auto-set category to 'treatment' for Usage tab
            if (productCategoryFilter) {
                productCategoryFilter.value = 'treatment';
            }
            updateUnifiedAnalytics();
        });
    }
    
    // Filter change handlers for Services Analytics
    const servicesYearSelect = document.getElementById('servicesYearSelect');
    const servicesMonthSelect = document.getElementById('servicesMonthSelect');
    
    if (servicesYearSelect) {
        servicesYearSelect.addEventListener('change', function() {
            const servicesYear = this.value;
            const servicesMonth = document.getElementById('servicesMonthSelect')?.value || null;
            const staffYear = new URLSearchParams(window.location.search).get('staff_year') || null;
            const staffMonth = new URLSearchParams(window.location.search).get('staff_month') || null;
            const year = new URLSearchParams(window.location.search).get('year') || null;
            const month = new URLSearchParams(window.location.search).get('month') || null;
            const product = new URLSearchParams(window.location.search).get('product') || null;
            
            const params = { services_year: servicesYear };
            if (servicesMonth) params.services_month = servicesMonth;
            const revenueYear = new URLSearchParams(window.location.search).get('revenue_year') || null;
            const revenueMonth = new URLSearchParams(window.location.search).get('revenue_month') || null;
            if (revenueYear) params.revenue_year = revenueYear;
            if (revenueMonth) params.revenue_month = revenueMonth;
            if (staffYear) params.staff_year = staffYear;
            if (staffMonth) params.staff_month = staffMonth;
            if (year) params.year = year;
            if (month) params.month = month;
            if (product) params.product = product;
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    if (servicesMonthSelect) {
        servicesMonthSelect.addEventListener('change', function() {
            const servicesMonth = this.value;
            const servicesYear = document.getElementById('servicesYearSelect')?.value || null;
            const staffYear = new URLSearchParams(window.location.search).get('staff_year') || null;
            const staffMonth = new URLSearchParams(window.location.search).get('staff_month') || null;
            const year = new URLSearchParams(window.location.search).get('year') || null;
            const month = new URLSearchParams(window.location.search).get('month') || null;
            const product = new URLSearchParams(window.location.search).get('product') || null;
            
            const params = { services_month: servicesMonth };
            if (servicesYear) params.services_year = servicesYear;
            const revenueYear = new URLSearchParams(window.location.search).get('revenue_year') || null;
            const revenueMonth = new URLSearchParams(window.location.search).get('revenue_month') || null;
            const servicesRevenueYear = new URLSearchParams(window.location.search).get('services_revenue_year') || null;
            const servicesRevenueMonth = new URLSearchParams(window.location.search).get('services_revenue_month') || null;
            const clientsYear = new URLSearchParams(window.location.search).get('clients_year') || null;
            const clientsMonth = new URLSearchParams(window.location.search).get('clients_month') || null;
            if (revenueYear) params.revenue_year = revenueYear;
            if (revenueMonth) params.revenue_month = revenueMonth;
            if (servicesRevenueYear) params.services_revenue_year = servicesRevenueYear;
            if (servicesRevenueMonth) params.services_revenue_month = servicesRevenueMonth;
            if (clientsYear) params.clients_year = clientsYear;
            if (clientsMonth) params.clients_month = clientsMonth;
            if (staffYear) params.staff_year = staffYear;
            if (staffMonth) params.staff_month = staffMonth;
            if (year) params.year = year;
            if (month) params.month = month;
            if (product) params.product = product;
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    // Filter change handlers for Revenue Chart
    const revenueYearSelect = document.getElementById('revenueYearSelect');
    const revenueMonthSelect = document.getElementById('revenueMonthSelect');
    
    if (revenueYearSelect) {
        revenueYearSelect.addEventListener('change', function() {
            const revenueYear = this.value;
            const revenueMonth = document.getElementById('revenueMonthSelect')?.value || null;
            const servicesYear = new URLSearchParams(window.location.search).get('services_year') || null;
            const servicesMonth = new URLSearchParams(window.location.search).get('services_month') || null;
            const staffYear = new URLSearchParams(window.location.search).get('staff_year') || null;
            const staffMonth = new URLSearchParams(window.location.search).get('staff_month') || null;
            const year = new URLSearchParams(window.location.search).get('year') || null;
            const month = new URLSearchParams(window.location.search).get('month') || null;
            const product = new URLSearchParams(window.location.search).get('product') || null;
            
            const params = { revenue_year: revenueYear };
            if (revenueMonth) params.revenue_month = revenueMonth;
            if (servicesYear) params.services_year = servicesYear;
            if (servicesMonth) params.services_month = servicesMonth;
            if (staffYear) params.staff_year = staffYear;
            if (staffMonth) params.staff_month = staffMonth;
            if (year) params.year = year;
            if (month) params.month = month;
            if (product) params.product = product;
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    if (revenueMonthSelect) {
        revenueMonthSelect.addEventListener('change', function() {
            const revenueMonth = this.value;
            const revenueYear = document.getElementById('revenueYearSelect')?.value || null;
            const servicesYear = new URLSearchParams(window.location.search).get('services_year') || null;
            const servicesMonth = new URLSearchParams(window.location.search).get('services_month') || null;
            const staffYear = new URLSearchParams(window.location.search).get('staff_year') || null;
            const staffMonth = new URLSearchParams(window.location.search).get('staff_month') || null;
            const year = new URLSearchParams(window.location.search).get('year') || null;
            const month = new URLSearchParams(window.location.search).get('month') || null;
            const product = new URLSearchParams(window.location.search).get('product') || null;
            
            const params = { revenue_month: revenueMonth };
            if (revenueYear) params.revenue_year = revenueYear;
            const servicesRevenueYear = new URLSearchParams(window.location.search).get('services_revenue_year') || null;
            const servicesRevenueMonth = new URLSearchParams(window.location.search).get('services_revenue_month') || null;
            const clientsYear = new URLSearchParams(window.location.search).get('clients_year') || null;
            const clientsMonth = new URLSearchParams(window.location.search).get('clients_month') || null;
            if (servicesRevenueYear) params.services_revenue_year = servicesRevenueYear;
            if (servicesRevenueMonth) params.services_revenue_month = servicesRevenueMonth;
            if (clientsYear) params.clients_year = clientsYear;
            if (clientsMonth) params.clients_month = clientsMonth;
            if (servicesYear) params.services_year = servicesYear;
            if (servicesMonth) params.services_month = servicesMonth;
            if (staffYear) params.staff_year = staffYear;
            if (staffMonth) params.staff_month = staffMonth;
            if (year) params.year = year;
            if (month) params.month = month;
            if (product) params.product = product;
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    // Filter change handlers for Top Services by Revenue
    const servicesRevenueYearSelect = document.getElementById('servicesRevenueYearSelect');
    const servicesRevenueMonthSelect = document.getElementById('servicesRevenueMonthSelect');
    
    if (servicesRevenueYearSelect) {
        servicesRevenueYearSelect.addEventListener('change', function() {
            const servicesRevenueYear = this.value;
            const servicesRevenueMonth = document.getElementById('servicesRevenueMonthSelect')?.value || null;
            const urlParams = new URLSearchParams(window.location.search);
            
            const params = { services_revenue_year: servicesRevenueYear };
            if (servicesRevenueMonth) params.services_revenue_month = servicesRevenueMonth;
            
            // Preserve all other filters
            ['revenue_year', 'revenue_month', 'services_year', 'services_month', 'staff_year', 'staff_month', 'year', 'month', 'product', 'clients_year', 'clients_month', 'treatment_products_year', 'treatment_products_month'].forEach(key => {
                const value = urlParams.get(key);
                if (value && !params[key]) params[key] = value;
            });
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    if (servicesRevenueMonthSelect) {
        servicesRevenueMonthSelect.addEventListener('change', function() {
            const servicesRevenueMonth = this.value;
            const servicesRevenueYear = document.getElementById('servicesRevenueYearSelect')?.value || null;
            const urlParams = new URLSearchParams(window.location.search);
            
            const params = { services_revenue_month: servicesRevenueMonth };
            if (servicesRevenueYear) params.services_revenue_year = servicesRevenueYear;
            
            // Preserve all other filters
            ['revenue_year', 'revenue_month', 'services_year', 'services_month', 'staff_year', 'staff_month', 'year', 'month', 'product', 'clients_year', 'clients_month', 'treatment_products_year', 'treatment_products_month'].forEach(key => {
                const value = urlParams.get(key);
                if (value && !params[key]) params[key] = value;
            });
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    // Filter change handlers for Top Clients
    const clientsYearSelect = document.getElementById('clientsYearSelect');
    const clientsMonthSelect = document.getElementById('clientsMonthSelect');
    
    if (clientsYearSelect) {
        clientsYearSelect.addEventListener('change', function() {
            const clientsYear = this.value;
            const clientsMonth = document.getElementById('clientsMonthSelect')?.value || null;
            const urlParams = new URLSearchParams(window.location.search);
            
            const params = { clients_year: clientsYear };
            if (clientsMonth) params.clients_month = clientsMonth;
            
            // Preserve all other filters
            ['revenue_year', 'revenue_month', 'services_year', 'services_month', 'staff_year', 'staff_month', 'year', 'month', 'product', 'services_revenue_year', 'services_revenue_month', 'treatment_products_year', 'treatment_products_month'].forEach(key => {
                const value = urlParams.get(key);
                if (value && !params[key]) params[key] = value;
            });
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    if (clientsMonthSelect) {
        clientsMonthSelect.addEventListener('change', function() {
            const clientsMonth = this.value;
            const clientsYear = document.getElementById('clientsYearSelect')?.value || null;
            const urlParams = new URLSearchParams(window.location.search);
            
            const params = { clients_month: clientsMonth };
            if (clientsYear) params.clients_year = clientsYear;
            
            // Preserve all other filters
            ['revenue_year', 'revenue_month', 'services_year', 'services_month', 'staff_year', 'staff_month', 'year', 'month', 'product', 'services_revenue_year', 'services_revenue_month', 'treatment_products_year', 'treatment_products_month'].forEach(key => {
                const value = urlParams.get(key);
                if (value && !params[key]) params[key] = value;
            });
            
            window.location.href = buildDashboardUrl(params);
        });
    }
    
    // Helper function to build dashboard URL with all query parameters
    function buildDashboardUrl(newParams) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        
        // Update or add new parameters
        Object.keys(newParams).forEach(key => {
            if (newParams[key] !== null && newParams[key] !== undefined) {
                params.set(key, newParams[key]);
            }
        });
        
        return window.location.pathname + '?' + params.toString();
    }
    
    // Function to update all analytics charts via AJAX
    async function updateAllAnalytics() {
        // This function is deprecated - unified analytics now uses page reload
        // Keeping for backward compatibility but it won't update unified analytics
        const year = document.getElementById('analyticsYearFilter')?.value || '';
        const month = document.getElementById('analyticsMonthFilter')?.value || '';
        const product = document.getElementById('analyticsProductFilter')?.value || '';
        
        // Show loading indicator
        const charts = ['staffSchedulingChart', 'servicesMonthlyChart', 'unifiedAnalyticsChart'];
        charts.forEach(chartId => {
            const canvas = document.getElementById(chartId);
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#666';
                ctx.font = '16px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('Loading...', canvas.width / 2, canvas.height / 2);
                ctx.restore();
            }
        });
        
        try {
            // Get all filter values from URL to preserve them
            const urlParams = new URLSearchParams(window.location.search);
            const params = new URLSearchParams();
            
            // Product Sales filters
            if (year) params.append('year', year);
            if (month) params.append('month', month);
            if (product) params.append('product', product);
            
            // Preserve other filters
            const revenueYear = urlParams.get('revenue_year');
            const revenueMonth = urlParams.get('revenue_month');
            const servicesYear = urlParams.get('services_year');
            const servicesMonth = urlParams.get('services_month');
            const staffYear = urlParams.get('staff_year');
            const staffMonth = urlParams.get('staff_month');
            
            if (revenueYear) params.append('revenue_year', revenueYear);
            if (revenueMonth) params.append('revenue_month', revenueMonth);
            if (servicesYear) params.append('services_year', servicesYear);
            if (servicesMonth) params.append('services_month', servicesMonth);
            if (staffYear) params.append('staff_year', staffYear);
            if (staffMonth) params.append('staff_month', staffMonth);
            
            const response = await fetch(`{{ route('admin.dashboard.analytics-data') }}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                let errorMessage = 'Failed to fetch analytics data';
                try {
                    const errorData = JSON.parse(errorText);
                    errorMessage = errorData.message || errorData.error || errorMessage;
                } catch (e) {
                    errorMessage = errorText || errorMessage;
                }
                throw new Error(errorMessage);
            }
            
            const data = await response.json();
            
            // Check if there's an error in the response
            if (data.error) {
                throw new Error(data.message || data.error);
            }
            
            // Update Staff Scheduling Chart
            if (data.staff_scheduling) {
                initStaffSchedulingChart(data.staff_scheduling);
            }
            
            // Update Services Monthly Chart
            if (data.services_monthly) {
                initServicesMonthlyChart(data.services_monthly);
            }
            
            // Unified Analytics Chart is now handled by page reload
            // No need to update via AJAX
            
        } catch (error) {
            console.error('Error updating analytics:', error);
            console.error('Error details:', {
                message: error.message,
                stack: error.stack
            });
            alert('Failed to update analytics: ' + (error.message || 'Unknown error') + '. Please refresh the page.');
        }
    }
    
</script>
</x-app-layout>
