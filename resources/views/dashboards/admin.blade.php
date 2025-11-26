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
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Monthly Revenue Bar Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Revenue</h3>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Appointment Status Doughnut Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Appointment Status Distribution</h3>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Analytics Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Staff Scheduling Analytics -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center space-x-3 mb-4">
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
                    <div class="h-80">
                        <canvas id="staffSchedulingChart"></canvas>
                    </div>
                </div>

                <!-- Services Analytics -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Services Analytics</h3>
                            <p class="text-sm text-gray-600">Service demand per month (Top 5 services)</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="servicesMonthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Product Sales Analytics -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Product Sales Analytics</h3>
                            <p class="text-sm text-gray-600">Top-selling products comparison</p>
                        </div>
                    </div>
                    <!-- Filters -->
                    <div class="flex items-center space-x-3">
                        <select id="yearFilter" class="bg-white border-2 border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm transition-all duration-200">
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
                            @foreach($allProducts ?? [] as $prod)
                                <option value="{{ $prod->id }}" {{ $productId == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="productSalesChart"></canvas>
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

    // Appointment Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: [
                @foreach($appointmentStatusData as $status => $count)
                    '{{ ucfirst(str_replace('_', ' ', $status)) }}',
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($appointmentStatusData as $status => $count)
                        {{ $count }},
                    @endforeach
                ],
                backgroundColor: [
                    '#10B981', // green - confirmed
                    '#F59E0B', // yellow - pending
                    '#3B82F6', // blue - in_progress
                    '#8B5CF6', // purple - completed
                    '#EF4444', // red - cancelled
                    '#6B7280'  // gray - no_show
                ],
                borderWidth: 0,
                hoverBorderWidth: 3,
                hoverBorderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            cutout: '60%'
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

    // Product Sales Analytics Chart
    let productSalesChart;
    const productSalesCtx = document.getElementById('productSalesChart').getContext('2d');
    
    function updateProductSalesChartData(productData) {
        if (productSalesChart) {
            productSalesChart.destroy();
        }
        
        productSalesChart = new Chart(productSalesCtx, {
            type: 'bar',
            data: {
                labels: productData.map(p => p.name),
                datasets: [
                    {
                        label: 'Selected Period',
                        data: productData.map(p => p.selected_period),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                    },
                    {
                        label: 'Previous Period',
                        data: productData.map(p => p.previous_period),
                        backgroundColor: 'rgba(156, 163, 175, 0.8)',
                        borderColor: 'rgba(156, 163, 175, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false,
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
                            text: 'Units Sold'
                        }
                    }
                }
            }
        });
    }
    
    // Initialize Product Sales Chart with initial data
    updateProductSalesChartData(@json($productSalesData));
    
    // Filter change handlers
    const yearFilter = document.getElementById('yearFilter');
    const monthFilter = document.getElementById('monthFilter');
    const productFilter = document.getElementById('productFilter');
    
    if (yearFilter) {
        yearFilter.addEventListener('change', function() {
            updateProductSalesChart();
        });
    }
    
    if (monthFilter) {
        monthFilter.addEventListener('change', function() {
            updateProductSalesChart();
        });
    }
    
    if (productFilter) {
        productFilter.addEventListener('change', function() {
            updateProductSalesChart();
        });
    }
    
    // Function to update all analytics charts via AJAX
    async function updateAllAnalytics() {
        const year = document.getElementById('yearFilter')?.value || '';
        const month = document.getElementById('monthFilter')?.value || '';
        const product = document.getElementById('productFilter')?.value || '';
        
        // Show loading indicator
        const charts = ['staffSchedulingChart', 'servicesMonthlyChart', 'productSalesChart'];
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
            const params = new URLSearchParams();
            if (year) params.append('year', year);
            if (month) params.append('month', month);
            if (product) params.append('product', product);
            
            const response = await fetch(`{{ route('admin.dashboard.analytics-data') }}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to fetch analytics data');
            }
            
            const data = await response.json();
            
            // Update Staff Scheduling Chart
            if (data.staff_scheduling) {
                initStaffSchedulingChart(data.staff_scheduling);
            }
            
            // Update Services Monthly Chart
            if (data.services_monthly) {
                initServicesMonthlyChart(data.services_monthly);
            }
            
            // Update Product Sales Chart
            if (data.product_sales) {
                updateProductSalesChartData(data.product_sales);
            }
            
        } catch (error) {
            console.error('Error updating analytics:', error);
            alert('Failed to update analytics. Please refresh the page.');
        }
    }
    
    function updateProductSalesChart() {
        // Update URL without reloading
        const year = document.getElementById('yearFilter')?.value || '';
        const month = document.getElementById('monthFilter')?.value || '';
        const product = document.getElementById('productFilter')?.value || '';
        
        const params = new URLSearchParams();
        if (year) params.append('year', year);
        if (month) params.append('month', month);
        if (product) params.append('product', product);
        
        // Update URL without reload
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);
        
        // Update all charts via AJAX
        updateAllAnalytics();
    }
</script>
</x-app-layout>
