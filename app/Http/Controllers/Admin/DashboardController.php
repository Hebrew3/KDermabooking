<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\SaleItem;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get filter parameters for services analytics
        $servicesYear = request()->get('services_year', now()->year);
        $servicesMonth = request()->get('services_month', 'all');
        
        // Get filter parameters for revenue chart
        $revenueYear = request()->get('revenue_year', now()->year);
        $revenueMonth = request()->get('revenue_month', 'all');
        // Get current month and year
        $currentMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');

        // User Statistics
        $totalUsers = User::count();
        $totalClients = User::where('role', 'client')->count();
        $totalStaff = User::whereIn('role', ['nurse', 'aesthetician'])->count();
        $newClientsThisMonth = User::where('role', 'client')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Service Statistics
        $totalServices = Service::count();
        $activeServices = Service::active()->count();
        $featuredServices = Service::active()->featured()->count();

        // Appointment Statistics
        $totalAppointments = Appointment::count();
        $todayAppointments = Appointment::whereDate('appointment_date', today())->count();
        $thisMonthAppointments = Appointment::whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->count();
        $pendingAppointments = Appointment::where('status', 'pending')->count();
        $confirmedAppointments = Appointment::where('status', 'confirmed')->count();

        // Revenue Statistics
        $totalRevenue = Appointment::where('status', 'completed')->sum('total_amount');
        $thisMonthRevenue = Appointment::where('status', 'completed')
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->sum('total_amount');
        $lastMonthRevenue = Appointment::where('status', 'completed')
            ->whereMonth('appointment_date', now()->subMonth()->month)
            ->whereYear('appointment_date', now()->subMonth()->year)
            ->sum('total_amount');

        // Calculate revenue growth
        $revenueGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        // Recent Appointments
        $recentAppointments = Appointment::with(['client', 'service', 'services', 'staff'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Today's Appointments
        $todayAppointmentsList = Appointment::with(['client', 'service', 'services', 'staff'])
            ->whereDate('appointment_date', today())
            ->orderByRaw('CAST(appointment_time AS TIME)')
            ->get();

        // Popular Services (by appointment count)
        $popularServices = Service::withCount(['appointments' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get();

        // Monthly Revenue Chart Data (filtered by year and month)
        $monthlyRevenue = [];
        
        if ($revenueYear && $revenueMonth && $revenueMonth !== 'all') {
            // If both year and month are specified (and not "all"), show that specific month and 5 months before it
            $endDate = Carbon::create($revenueYear, $revenueMonth, 1)->endOfMonth();
            $startDate = $endDate->copy()->subMonths(5)->startOfMonth();
            
            // Generate months array
            for ($i = 5; $i >= 0; $i--) {
                $date = $endDate->copy()->subMonths($i);
                $revenue = Appointment::where('status', 'completed')
                    ->whereMonth('appointment_date', $date->month)
                    ->whereYear('appointment_date', $date->year)
                    ->sum('total_amount');
                
                $monthlyRevenue[] = [
                    'month' => $date->format('M Y'),
                    'revenue' => $revenue
                ];
            }
        } elseif ($revenueYear && ($revenueMonth === 'all' || !$revenueMonth)) {
            // If year is specified and month is "all" or not specified, show all 12 months of that year
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::create($revenueYear, $m, 1);
                $revenue = Appointment::where('status', 'completed')
                    ->whereMonth('appointment_date', $m)
                    ->whereYear('appointment_date', $revenueYear)
                    ->sum('total_amount');
                
                $monthlyRevenue[] = [
                    'month' => $date->format('M Y'),
                    'revenue' => $revenue
                ];
            }
        } else {
            // Default: Get last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $revenue = Appointment::where('status', 'completed')
                    ->whereMonth('appointment_date', $date->month)
                    ->whereYear('appointment_date', $date->year)
                    ->sum('total_amount');
                
                $monthlyRevenue[] = [
                    'month' => $date->format('M Y'),
                    'revenue' => $revenue
                ];
            }
        }

        // Appointment Status Distribution
        $appointmentStatusData = Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Analytics Data
        $startDate = now()->subDays(30);
        $endDate = now();
        
        // Staff Scheduling Analytics (Services completed by each staff member)
        // Get filter parameters for staff scheduling
        $staffYear = request()->get('staff_year', now()->year);
        $staffMonth = request()->get('staff_month', 'all');
        
        // Calculate date range based on filters
        if ($staffMonth && $staffMonth !== 'all') {
            // If specific month is selected, use that month
            $staffSchedulingStartDate = Carbon::create($staffYear, $staffMonth, 1)->startOfMonth();
            $staffSchedulingEndDate = Carbon::create($staffYear, $staffMonth, 1)->endOfMonth();
        } else {
            // If "all" months, use entire year
            $staffSchedulingStartDate = Carbon::create($staffYear, 1, 1)->startOfYear();
            $staffSchedulingEndDate = Carbon::create($staffYear, 12, 31)->endOfYear();
        }
        
        $staffServices = User::select('users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.role')
            ->whereIn('role', ['nurse', 'aesthetician'])
            ->withCount(['staffAppointments' => function($query) use ($staffSchedulingStartDate, $staffSchedulingEndDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$staffSchedulingStartDate, $staffSchedulingEndDate]);
            }])
            ->orderBy('staff_appointments_count', 'desc')
            ->get();

        $staffSchedulingData = [
            'staff' => $staffServices->map(function($staff) {
                return $staff->name . ' (' . ucfirst($staff->role) . ')';
            })->toArray(),
            'services_completed' => $staffServices->pluck('staff_appointments_count')->toArray(),
        ];

        // Services Monthly Analytics (Service demand per month - All services with appointments)
        $months = [];
        $servicesData = [];
        $allServiceIds = [];

        // Calculate date range based on filters
        if ($servicesYear && $servicesMonth && $servicesMonth !== 'all') {
            // If both year and month are specified (and not "all"), show that specific month and 5 months before it
            $endDate = Carbon::create($servicesYear, $servicesMonth, 1)->endOfMonth();
            $startDate = $endDate->copy()->subMonths(5)->startOfMonth();
            
            // Generate months array - ensure unique months
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = $endDate->copy()->subMonths($i);
                $monthStr = $date->format('M Y');
                if (!in_array($monthStr, $months)) {
                    $months[] = $monthStr;
                }
            }
        } elseif ($servicesYear && ($servicesMonth === 'all' || !$servicesMonth)) {
            // If year is specified and month is "all" or not specified, show all 12 months of that year
            $months = [];
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::create($servicesYear, $m, 1);
                $monthStr = $date->format('M Y');
                $months[] = $monthStr;
            }
            $startDate = Carbon::create($servicesYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($servicesYear, 12, 31)->endOfYear();
        } else {
            // Default: Get last 6 months - ensure unique months
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthStr = $date->format('M Y');
                if (!in_array($monthStr, $months)) {
                    $months[] = $monthStr;
                }
            }
            $endDate = now()->endOfMonth();
            $startDate = now()->subMonths(5)->startOfMonth();
        }

        // Get data for each month in the range - ensure we process unique months only
        $processedMonths = [];
        foreach ($months as $monthStr) {
            // Skip if we've already processed this month
            if (in_array($monthStr, $processedMonths)) {
                continue;
            }
            $processedMonths[] = $monthStr;
            
            // Parse month string to get date
            try {
                $monthDate = Carbon::createFromFormat('M Y', $monthStr);
                if (!$monthDate) {
                    continue; // Skip invalid dates
                }
            } catch (\Exception $e) {
                continue; // Skip if parsing fails
            }
            
            $startOfMonth = $monthDate->copy()->startOfMonth();
            $endOfMonth = $monthDate->copy()->endOfMonth();

            // Get service counts for this month using direct query to ensure accuracy
            $serviceCounts = DB::table('appointments')
                ->select('services.id', 'services.name', DB::raw('COUNT(appointments.id) as appointments_count'))
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->where('appointments.status', 'completed')
                ->whereBetween('appointments.appointment_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->groupBy('services.id', 'services.name')
                ->having('appointments_count', '>', 0)
                ->orderBy('appointments_count', 'desc')
                ->get();

            // Build arrays with service ID as key for easier lookup
            $serviceNames = [];
            $serviceCountsArray = [];
            foreach ($serviceCounts as $service) {
                $serviceId = (int)$service->id;
                $serviceNames[$serviceId] = $service->name;
                $serviceCountsArray[$serviceId] = (int)$service->appointments_count;
                $allServiceIds[] = $serviceId;
            }

            $servicesData[] = [
                'month' => $monthStr,
                'services' => $serviceNames,
                'counts' => $serviceCountsArray,
                'service_ids' => array_keys($serviceNames),
            ];
        }

        // Get unique service IDs
        $allServiceIds = array_unique($allServiceIds);
        
        // Get ALL services that have completed appointments in the selected date range
        // This ensures newly added services with appointments are included
        $topServicesList = DB::table('appointments')
            ->select('services.id', 'services.name', DB::raw('COUNT(appointments.id) as appointments_count'))
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'completed')
            ->whereBetween('appointments.appointment_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->groupBy('services.id', 'services.name')
            ->having('appointments_count', '>', 0)
            ->orderBy('appointments_count', 'desc')
            ->get();

        // Build top services array with ID as key
        $topServices = [];
        $topServiceIds = [];
        foreach ($topServicesList as $service) {
            $serviceId = (int)$service->id;
            $topServices[$serviceId] = $service->name;
            $topServiceIds[] = $serviceId;
        }
        
        // Build services monthly data for chart
        $servicesMonthlyChartData = [];
        foreach ($topServiceIds as $serviceId) {
            $serviceName = $topServices[$serviceId];
            $serviceData = [];
            foreach ($months as $month) {
                $monthData = collect($servicesData)->firstWhere('month', $month);
                // Check by service ID instead of name for accuracy
                if ($monthData && isset($monthData['counts'][$serviceId])) {
                    $serviceData[] = (int)$monthData['counts'][$serviceId];
                } else {
                    $serviceData[] = 0;
                }
            }
            $servicesMonthlyChartData[] = [
                'name' => $serviceName,
                'data' => $serviceData
            ];
        }
        
        // Ensure months array is unique and properly sorted
        $months = array_values(array_unique($months));

        // Product Sales Analytics (Top-selling products comparison)
        // Get filter parameters from request
        $year = request()->get('year', null);
        $month = request()->get('month', null);
        $productId = request()->get('product', null);
        
        // Calculate date ranges for selected period based on filters
        if ($year && $month) {
            // If both year and month are specified, use that specific month
            $selectedStartDate = Carbon::create($year, $month, 1)->startOfMonth();
            $selectedEndDate = Carbon::create($year, $month, 1)->endOfMonth();
            // Previous period: same month previous year
            $previousStartDate = Carbon::create($year - 1, $month, 1)->startOfMonth();
            $previousEndDate = Carbon::create($year - 1, $month, 1)->endOfMonth();
        } elseif ($year) {
            // If only year is specified, use entire year
            $selectedStartDate = Carbon::create($year, 1, 1)->startOfYear();
            $selectedEndDate = Carbon::create($year, 12, 31)->endOfYear();
            // Previous period: previous year
            $previousStartDate = Carbon::create($year - 1, 1, 1)->startOfYear();
            $previousEndDate = Carbon::create($year - 1, 12, 31)->endOfYear();
        } elseif ($month) {
            // If only month is specified, use current year
            $selectedStartDate = Carbon::create(now()->year, $month, 1)->startOfMonth();
            $selectedEndDate = Carbon::create(now()->year, $month, 1)->endOfMonth();
            // Previous period: same month previous year
            $previousStartDate = Carbon::create(now()->year - 1, $month, 1)->startOfMonth();
            $previousEndDate = Carbon::create(now()->year - 1, $month, 1)->endOfMonth();
        } else {
            // Default: last 30 days
            $selectedStartDate = now()->subDays(30)->startOfDay();
            $selectedEndDate = now()->endOfDay();
            // Previous period: 30 days before that
            $previousStartDate = $selectedStartDate->copy()->subDays(30);
            $previousEndDate = $selectedStartDate->copy()->subSeconds(1);
        }
        
        // Build query for selected period
        $selectedPeriodQuery = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$selectedStartDate, $selectedEndDate]);

        // Build query for previous period
        $previousPeriodQuery = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$previousStartDate, $previousEndDate]);
        
        if ($productId) {
            $selectedPeriodQuery->where('sale_items.inventory_item_id', $productId);
            $previousPeriodQuery->where('sale_items.inventory_item_id', $productId);
        }

        // Get top-selling products for selected period
        $topProducts = (clone $selectedPeriodQuery)
            ->groupBy('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->orderBy('total_quantity_sold', 'desc')
            ->limit(10)
            ->get();

        // Get previous period data for comparison
        $previousProducts = (clone $previousPeriodQuery)
            ->groupBy('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->get()
            ->keyBy('inventory_item_id');

        // Prepare comparison data
        $productSalesData = [];
        foreach ($topProducts as $product) {
            $previousData = $previousProducts->get($product->inventory_item_id);
            $productSalesData[] = [
                'name' => $product->item_name,
                'selected_period' => (int)$product->total_quantity_sold,
                'previous_period' => $previousData ? (int)$previousData->total_quantity_sold : 0,
            ];
        }

        // Get all products for filter dropdown
        $allProducts = InventoryItem::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        // Top Services by Revenue - Get filter parameters
        $servicesRevenueYear = request()->get('services_revenue_year', now()->year);
        $servicesRevenueMonth = request()->get('services_revenue_month', 'all');
        
        $topServicesByRevenueQuery = Service::select('services.id', 'services.name', 'services.price')
            ->selectRaw('SUM(CASE WHEN appointments.status = "completed" THEN appointments.total_amount ELSE 0 END) as total_revenue')
            ->selectRaw('COUNT(CASE WHEN appointments.status = "completed" THEN 1 END) as completed_count')
            ->leftJoin('appointments', function($join) use ($servicesRevenueYear, $servicesRevenueMonth) {
                $join->on('services.id', '=', 'appointments.service_id')
                     ->where('appointments.status', '=', 'completed');
                
                // Apply date filters in the join
                if ($servicesRevenueMonth && $servicesRevenueMonth !== 'all') {
                    $join->whereYear('appointments.appointment_date', $servicesRevenueYear)
                         ->whereMonth('appointments.appointment_date', $servicesRevenueMonth);
                } else {
                    $join->whereYear('appointments.appointment_date', $servicesRevenueYear);
                }
            });
        
        $topServicesByRevenue = $topServicesByRevenueQuery->groupBy('services.id', 'services.name', 'services.price')
            ->havingRaw('total_revenue > 0')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Top Clients (by total spending) - Get filter parameters
        $clientsYear = request()->get('clients_year', now()->year);
        $clientsMonth = request()->get('clients_month', 'all');
        
        $topClientsQuery = User::select('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->selectRaw('SUM(CASE WHEN appointments.status = "completed" THEN appointments.total_amount ELSE 0 END) as total_spent')
            ->selectRaw('COUNT(CASE WHEN appointments.status = "completed" THEN 1 END) as total_appointments')
            ->leftJoin('appointments', function($join) use ($clientsYear, $clientsMonth) {
                $join->on('users.id', '=', 'appointments.client_id')
                     ->where('appointments.status', '=', 'completed');
                
                // Apply date filters in the join
                if ($clientsMonth && $clientsMonth !== 'all') {
                    $join->whereYear('appointments.appointment_date', $clientsYear)
                         ->whereMonth('appointments.appointment_date', $clientsMonth);
                } else {
                    $join->whereYear('appointments.appointment_date', $clientsYear);
                }
            })
            ->where('users.role', 'client');
        
        $topClients = $topClientsQuery->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->havingRaw('total_spent > 0')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // Inventory Overview
        $totalInventoryItems = InventoryItem::count();
        $activeInventoryItems = InventoryItem::where('is_active', true)->count();
        $lowStockItems = InventoryItem::where('is_active', true)
            ->whereRaw('current_stock <= minimum_stock')
            ->count();
        $totalInventoryValue = InventoryItem::where('is_active', true)
            ->selectRaw('SUM(current_stock * cost_price) as total_value')
            ->value('total_value') ?? 0;

        // Reorder Alerts (items that need reordering)
        $reorderAlerts = InventoryItem::where('is_active', true)
            ->whereRaw('current_stock <= minimum_stock')
            ->orderByRaw('(current_stock / NULLIF(minimum_stock, 0)) ASC')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'current_stock', 'minimum_stock', 'maximum_stock', 'unit']);

        $stats = [
            'users' => [
                'total' => $totalUsers,
                'clients' => $totalClients,
                'staff' => $totalStaff,
                'new_clients_this_month' => $newClientsThisMonth,
            ],
            'services' => [
                'total' => $totalServices,
                'active' => $activeServices,
                'featured' => $featuredServices,
            ],
            'appointments' => [
                'total' => $totalAppointments,
                'today' => $todayAppointments,
                'this_month' => $thisMonthAppointments,
                'pending' => $pendingAppointments,
                'confirmed' => $confirmedAppointments,
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'this_month' => $thisMonthRevenue,
                'last_month' => $lastMonthRevenue,
                'growth' => $revenueGrowth,
            ],
        ];

        return view('dashboards.admin', compact(
            'stats',
            'recentAppointments',
            'todayAppointmentsList',
            'popularServices',
            'monthlyRevenue',
            'appointmentStatusData',
            'staffSchedulingData',
            'servicesMonthlyChartData',
            'months',
            'topServices',
            'productSalesData',
            'allProducts',
            'year',
            'month',
            'productId',
            'topServicesByRevenue',
            'topClients',
            'totalInventoryItems',
            'activeInventoryItems',
            'lowStockItems',
            'totalInventoryValue',
            'reorderAlerts',
            'servicesYear',
            'servicesMonth',
            'revenueYear',
            'revenueMonth',
            'servicesRevenueYear',
            'servicesRevenueMonth',
            'clientsYear',
            'clientsMonth'
        ));
    }

    /**
     * Get analytics data via AJAX for real-time updates.
     */
    public function getAnalyticsData(Request $request)
    {
        $startDate = now()->subDays(30);
        $endDate = now();
        
        // Get filter parameters and ensure proper types
        $year = $request->get('year', null);
        $month = $request->get('month', null);
        $productId = $request->get('product', null);
        $staffYear = $request->get('staff_year', now()->year);
        $staffMonth = $request->get('staff_month', 'all');
        $servicesYear = $request->get('services_year', now()->year);
        $servicesMonth = $request->get('services_month', 'all');
        
        // Convert to integers if provided, handle empty strings
        if ($year !== null && $year !== '') {
            $year = (int)$year;
        } else {
            $year = null;
        }
        if ($month !== null && $month !== '' && $month !== 'all') {
            $month = (int)$month;
        } else {
            $month = null;
        }
        if ($productId !== null && $productId !== '' && $productId !== 'all') {
            $productId = (int)$productId;
        } else {
            $productId = null;
        }
        if ($staffYear !== null && $staffYear !== '') {
            $staffYear = (int)$staffYear;
        }
        if ($servicesYear !== null && $servicesYear !== '') {
            $servicesYear = (int)$servicesYear;
        }

        // Staff Scheduling Analytics
        // Calculate date range based on filters
        if ($staffMonth && $staffMonth !== 'all') {
            // If specific month is selected, use that month
            $staffSchedulingStartDate = Carbon::create($staffYear, $staffMonth, 1)->startOfMonth();
            $staffSchedulingEndDate = Carbon::create($staffYear, $staffMonth, 1)->endOfMonth();
        } else {
            // If "all" months, use entire year
            $staffSchedulingStartDate = Carbon::create($staffYear, 1, 1)->startOfYear();
            $staffSchedulingEndDate = Carbon::create($staffYear, 12, 31)->endOfYear();
        }
        
        $staffServices = User::select('users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.role')
            ->whereIn('role', ['nurse', 'aesthetician'])
            ->withCount(['staffAppointments' => function($query) use ($staffSchedulingStartDate, $staffSchedulingEndDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$staffSchedulingStartDate, $staffSchedulingEndDate]);
            }])
            ->orderBy('staff_appointments_count', 'desc')
            ->get();

        $staffSchedulingData = [
            'staff' => $staffServices->map(function($staff) {
                return $staff->name . ' (' . ucfirst($staff->role) . ')';
            })->toArray(),
            'services_completed' => $staffServices->pluck('staff_appointments_count')->toArray(),
        ];

        // Services Monthly Analytics
        $months = [];
        $servicesData = [];
        $allServiceIds = [];

        // Calculate date range based on filters
        if ($servicesYear && $servicesMonth && $servicesMonth !== 'all') {
            // If both year and month are specified (and not "all"), show that specific month and 5 months before it
            $endDate = Carbon::create($servicesYear, $servicesMonth, 1)->endOfMonth();
            $startDate = $endDate->copy()->subMonths(5)->startOfMonth();
            
            // Generate months array
            for ($i = 5; $i >= 0; $i--) {
                $date = $endDate->copy()->subMonths($i);
                $monthStr = $date->format('M Y');
                if (!in_array($monthStr, $months)) {
                    $months[] = $monthStr;
                }
            }
        } elseif ($servicesYear && ($servicesMonth === 'all' || !$servicesMonth)) {
            // If year is specified and month is "all" or not specified, show all 12 months of that year
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::create($servicesYear, $m, 1);
                $monthStr = $date->format('M Y');
                $months[] = $monthStr;
            }
            $startDate = Carbon::create($servicesYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($servicesYear, 12, 31)->endOfYear();
        } else {
            // Default: Get last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $monthStr = $date->format('M Y');
                if (!in_array($monthStr, $months)) {
                    $months[] = $monthStr;
                }
            }
            $endDate = now()->endOfMonth();
            $startDate = now()->subMonths(5)->startOfMonth();
        }

        // Process each month in the range
        foreach ($months as $monthStr) {
            // Parse month string to get date
            try {
                $monthDate = Carbon::createFromFormat('M Y', $monthStr);
                if (!$monthDate) {
                    continue;
                }
            } catch (\Exception $e) {
                continue;
            }
            
            $startOfMonth = $monthDate->copy()->startOfMonth();
            $endOfMonth = $monthDate->copy()->endOfMonth();

            // Get service counts for this month using direct query to ensure accuracy
            $serviceCounts = DB::table('appointments')
                ->select('services.id', 'services.name', DB::raw('COUNT(appointments.id) as appointments_count'))
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->where('appointments.status', 'completed')
                ->whereBetween('appointments.appointment_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->groupBy('services.id', 'services.name')
                ->having('appointments_count', '>', 0)
                ->orderBy('appointments_count', 'desc')
                ->get();

            // Build arrays with service ID as key for easier lookup
            $serviceNames = [];
            $serviceCountsArray = [];
            foreach ($serviceCounts as $service) {
                $serviceId = (int)$service->id;
                $serviceNames[$serviceId] = $service->name;
                $serviceCountsArray[$serviceId] = (int)$service->appointments_count;
                $allServiceIds[] = $serviceId;
            }

            $servicesData[] = [
                'month' => $monthStr,
                'services' => $serviceNames,
                'counts' => $serviceCountsArray,
                'service_ids' => array_keys($serviceNames),
            ];
        }

        // Get unique service IDs
        $allServiceIds = array_unique($allServiceIds);
        
        // Get ALL services that have completed appointments in the date range
        // This ensures newly added services with appointments are included
        $topServicesList = DB::table('appointments')
            ->select('services.id', 'services.name', DB::raw('COUNT(appointments.id) as appointments_count'))
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->where('appointments.status', 'completed')
            ->whereBetween('appointments.appointment_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->groupBy('services.id', 'services.name')
            ->having('appointments_count', '>', 0)
            ->orderBy('appointments_count', 'desc')
            ->get();

        // Build top services array with ID as key
        $topServices = [];
        $topServiceIds = [];
        foreach ($topServicesList as $service) {
            $serviceId = (int)$service->id;
            $topServices[$serviceId] = $service->name;
            $topServiceIds[] = $serviceId;
        }
        
        $servicesMonthlyChartData = [];
        foreach ($topServiceIds as $serviceId) {
            $serviceName = $topServices[$serviceId];
            $serviceData = [];
            foreach ($months as $month) {
                $monthData = collect($servicesData)->firstWhere('month', $month);
                // Check by service ID instead of name for accuracy
                if ($monthData && isset($monthData['counts'][$serviceId])) {
                    $serviceData[] = (int)$monthData['counts'][$serviceId];
                } else {
                    $serviceData[] = 0;
                }
            }
            $servicesMonthlyChartData[] = [
                'name' => $serviceName,
                'data' => $serviceData
            ];
        }

        // Product Sales Analytics
        // Calculate date ranges for selected period based on filters
        try {
            if ($year && $month) {
                // If both year and month are specified, use that specific month
                $selectedStartDate = Carbon::create($year, $month, 1)->startOfMonth();
                $selectedEndDate = Carbon::create($year, $month, 1)->endOfMonth();
                // Previous period: same month previous year
                $previousStartDate = Carbon::create($year - 1, $month, 1)->startOfMonth();
                $previousEndDate = Carbon::create($year - 1, $month, 1)->endOfMonth();
            } elseif ($year) {
                // If only year is specified, use entire year
                $selectedStartDate = Carbon::create($year, 1, 1)->startOfYear();
                $selectedEndDate = Carbon::create($year, 12, 31)->endOfYear();
                // Previous period: previous year
                $previousStartDate = Carbon::create($year - 1, 1, 1)->startOfYear();
                $previousEndDate = Carbon::create($year - 1, 12, 31)->endOfYear();
            } elseif ($month) {
                // If only month is specified, use current year
                $selectedStartDate = Carbon::create(now()->year, $month, 1)->startOfMonth();
                $selectedEndDate = Carbon::create(now()->year, $month, 1)->endOfMonth();
                // Previous period: same month previous year
                $previousStartDate = Carbon::create(now()->year - 1, $month, 1)->startOfMonth();
                $previousEndDate = Carbon::create(now()->year - 1, $month, 1)->endOfMonth();
            } else {
                // Default: last 30 days
                $selectedStartDate = now()->subDays(30)->startOfDay();
                $selectedEndDate = now()->endOfDay();
                // Previous period: 30 days before that
                $previousStartDate = $selectedStartDate->copy()->subDays(30);
                $previousEndDate = $selectedStartDate->copy()->subSeconds(1);
            }
        } catch (\Exception $e) {
            // Fallback to default if date creation fails
            \Log::error('Error creating date ranges for product sales analytics: ' . $e->getMessage(), [
                'year' => $year,
                'month' => $month,
                'error' => $e->getTraceAsString()
            ]);
            $selectedStartDate = now()->subDays(30)->startOfDay();
            $selectedEndDate = now()->endOfDay();
            $previousStartDate = $selectedStartDate->copy()->subDays(30);
            $previousEndDate = $selectedStartDate->copy()->subSeconds(1);
        }
        
        // Build query for selected period
        $selectedPeriodQuery = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$selectedStartDate, $selectedEndDate]);

        // Build query for previous period
        $previousPeriodQuery = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$previousStartDate, $previousEndDate]);
        
        if ($productId) {
            $selectedPeriodQuery->where('sale_items.inventory_item_id', $productId);
            $previousPeriodQuery->where('sale_items.inventory_item_id', $productId);
        }

        $topProducts = (clone $selectedPeriodQuery)
            ->groupBy('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->orderBy('total_quantity_sold', 'desc')
            ->limit(10)
            ->get();

        $previousProducts = (clone $previousPeriodQuery)
            ->groupBy('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->get()
            ->keyBy('inventory_item_id');

        $productSalesData = [];
        foreach ($topProducts as $product) {
            $previousData = $previousProducts->get($product->inventory_item_id);
            $productSalesData[] = [
                'name' => $product->item_name ?? 'Unknown Product',
                'selected_period' => (int)($product->total_quantity_sold ?? 0),
                'previous_period' => $previousData ? (int)($previousData->total_quantity_sold ?? 0) : 0,
            ];
        }

        try {
            return response()->json([
                'staff_scheduling' => $staffSchedulingData,
                'services_monthly' => [
                    'months' => $months,
                    'chart_data' => $servicesMonthlyChartData,
                    'top_services' => $topServices
                ],
                'product_sales' => $productSalesData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error returning analytics data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to fetch analytics data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Monthly Revenue data to CSV
     */
    public function exportMonthlyRevenue(Request $request)
    {
        $revenueYear = $request->get('revenue_year', now()->year);
        $revenueMonth = $request->get('revenue_month', 'all');
        
        $monthlyRevenue = [];
        
        if ($revenueYear && $revenueMonth && $revenueMonth !== 'all') {
            $endDate = Carbon::create($revenueYear, $revenueMonth, 1)->endOfMonth();
            $startDate = $endDate->copy()->subMonths(5)->startOfMonth();
            
            for ($i = 5; $i >= 0; $i--) {
                $date = $endDate->copy()->subMonths($i);
                $revenue = Appointment::where('status', 'completed')
                    ->whereMonth('appointment_date', $date->month)
                    ->whereYear('appointment_date', $date->year)
                    ->sum('total_amount');
                
                $monthlyRevenue[] = [
                    'month' => $date->format('F Y'),
                    'revenue' => $revenue
                ];
            }
        } elseif ($revenueYear && ($revenueMonth === 'all' || !$revenueMonth)) {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::create($revenueYear, $m, 1);
                $revenue = Appointment::where('status', 'completed')
                    ->whereMonth('appointment_date', $m)
                    ->whereYear('appointment_date', $revenueYear)
                    ->sum('total_amount');
                
                $monthlyRevenue[] = [
                    'month' => $date->format('F Y'),
                    'revenue' => $revenue
                ];
            }
        } else {
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $revenue = Appointment::where('status', 'completed')
                    ->whereMonth('appointment_date', $date->month)
                    ->whereYear('appointment_date', $date->year)
                    ->sum('total_amount');
                
                $monthlyRevenue[] = [
                    'month' => $date->format('F Y'),
                    'revenue' => $revenue
                ];
            }
        }

        $filename = 'monthly_revenue_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($monthlyRevenue) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Month', 'Revenue (₱)']);
            
            foreach ($monthlyRevenue as $row) {
                fputcsv($file, [
                    $row['month'],
                    number_format($row['revenue'], 2)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Staff Scheduling Analytics to CSV
     */
    public function exportStaffScheduling(Request $request)
    {
        $staffYear = $request->get('staff_year', now()->year);
        $staffMonth = $request->get('staff_month', 'all');
        
        if ($staffMonth && $staffMonth !== 'all') {
            $startDate = Carbon::create($staffYear, $staffMonth, 1)->startOfMonth();
            $endDate = Carbon::create($staffYear, $staffMonth, 1)->endOfMonth();
        } else {
            $startDate = Carbon::create($staffYear, 1, 1)->startOfYear();
            $endDate = Carbon::create($staffYear, 12, 31)->endOfYear();
        }
        
        $staffServices = User::select('users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.role')
            ->whereIn('role', ['nurse', 'aesthetician'])
            ->withCount(['staffAppointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
            }])
            ->orderBy('staff_appointments_count', 'desc')
            ->get();

        $filename = 'staff_scheduling_analytics_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($staffServices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Staff Name', 'Role', 'Services Completed']);
            
            foreach ($staffServices as $staff) {
                fputcsv($file, [
                    $staff->name,
                    ucfirst($staff->role),
                    $staff->staff_appointments_count
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Services Analytics to CSV
     */
    public function exportServicesAnalytics(Request $request)
    {
        $servicesYear = $request->get('services_year', now()->year);
        $servicesMonth = $request->get('services_month', 'all');
        
        $months = [];
        $servicesData = [];
        $allServiceIds = [];

        if ($servicesYear && $servicesMonth && $servicesMonth !== 'all') {
            $endDate = Carbon::create($servicesYear, $servicesMonth, 1)->endOfMonth();
            $startDate = $endDate->copy()->subMonths(5)->startOfMonth();
            
            for ($i = 5; $i >= 0; $i--) {
                $date = $endDate->copy()->subMonths($i);
                $months[] = $date->format('M Y');
            }
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $date = Carbon::create($servicesYear, $m, 1);
                $months[] = $date->format('M Y');
            }
        }

        $services = Service::active()->orderBy('name')->get();
        
        foreach ($services as $service) {
            $serviceData = ['Service' => $service->name];
            
            foreach ($months as $monthStr) {
                $monthDate = Carbon::createFromFormat('M Y', $monthStr);
                $count = Appointment::where('service_id', $service->id)
                    ->where('status', 'completed')
                    ->whereMonth('appointment_date', $monthDate->month)
                    ->whereYear('appointment_date', $monthDate->year)
                    ->count();
                
                $serviceData[$monthStr] = $count;
            }
            
            $servicesData[] = $serviceData;
        }

        $filename = 'services_analytics_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($servicesData, $months) {
            $file = fopen('php://output', 'w');
            $header = ['Service'];
            $header = array_merge($header, $months);
            fputcsv($file, $header);
            
            foreach ($servicesData as $row) {
                $csvRow = [$row['Service']];
                foreach ($months as $month) {
                    $csvRow[] = $row[$month] ?? 0;
                }
                fputcsv($file, $csvRow);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Product Sales Analytics to CSV
     */
    public function exportProductSales(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = $request->get('month');
        $productId = $request->get('product_id');
        
        if ($month && $month !== 'all' && $month !== '') {
            $month = (int) $month;
            $selectedStartDate = Carbon::create($year, $month, 1)->startOfMonth();
            $selectedEndDate = Carbon::create($year, $month, 1)->endOfMonth();
            $previousStartDate = Carbon::create($year - 1, $month, 1)->startOfMonth();
            $previousEndDate = Carbon::create($year - 1, $month, 1)->endOfMonth();
        } elseif ($year) {
            $selectedStartDate = Carbon::create($year, 1, 1)->startOfYear();
            $selectedEndDate = Carbon::create($year, 12, 31)->endOfYear();
            $previousStartDate = Carbon::create($year - 1, 1, 1)->startOfYear();
            $previousEndDate = Carbon::create($year - 1, 12, 31)->endOfYear();
        } else {
            $selectedStartDate = now()->subDays(30)->startOfDay();
            $selectedEndDate = now()->endOfDay();
            $previousStartDate = now()->subDays(60)->startOfDay();
            $previousEndDate = now()->subDays(30)->endOfDay();
        }

        $selectedPeriodQuery = SaleItem::select('sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$selectedStartDate, $selectedEndDate]);
        
        $previousPeriodQuery = SaleItem::select('sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$previousStartDate, $previousEndDate]);
        
        if ($productId) {
            $selectedPeriodQuery->where('sale_items.inventory_item_id', $productId);
            $previousPeriodQuery->where('sale_items.inventory_item_id', $productId);
        }

        $topProducts = (clone $selectedPeriodQuery)
            ->groupBy('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->orderBy('total_quantity_sold', 'desc')
            ->limit(10)
            ->get();

        $previousProducts = (clone $previousPeriodQuery)
            ->groupBy('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->get()
            ->keyBy('inventory_item_id');

        $productSalesData = [];
        foreach ($topProducts as $product) {
            $previousData = $previousProducts->get($product->inventory_item_id);
            $productSalesData[] = [
                'name' => $product->item_name ?? 'Unknown Product',
                'sku' => $product->item_sku ?? 'N/A',
                'selected_period' => (int)($product->total_quantity_sold ?? 0),
                'previous_period' => $previousData ? (int)($previousData->total_quantity_sold ?? 0) : 0,
            ];
        }

        $filename = 'product_sales_analytics_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $periodLabel = $month && $month !== 'all' 
            ? Carbon::create($year, $month, 1)->format('F Y')
            : ($year ? $year . ' (All Months)' : 'Last 30 Days');
        $previousPeriodLabel = $month && $month !== 'all'
            ? Carbon::create($year - 1, $month, 1)->format('F Y')
            : ($year ? ($year - 1) . ' (All Months)' : 'Previous 30 Days');

        $callback = function() use ($productSalesData, $periodLabel, $previousPeriodLabel) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Product Name', 'SKU', $periodLabel, $previousPeriodLabel, 'Change']);
            
            foreach ($productSalesData as $row) {
                $change = $row['selected_period'] - $row['previous_period'];
                $changePercent = $row['previous_period'] > 0 
                    ? (($change / $row['previous_period']) * 100) 
                    : ($row['selected_period'] > 0 ? 100 : 0);
                
                fputcsv($file, [
                    $row['name'],
                    $row['sku'],
                    $row['selected_period'],
                    $row['previous_period'],
                    $change . ' (' . number_format($changePercent, 1) . '%)'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Top Services by Revenue to CSV
     */
    public function exportTopServicesByRevenue(Request $request)
    {
        $servicesRevenueYear = $request->get('services_revenue_year', now()->year);
        $servicesRevenueMonth = $request->get('services_revenue_month', 'all');
        
        $topServicesByRevenueQuery = Service::select('services.id', 'services.name', 'services.price')
            ->selectRaw('SUM(CASE WHEN appointments.status = "completed" THEN appointments.total_amount ELSE 0 END) as total_revenue')
            ->selectRaw('COUNT(CASE WHEN appointments.status = "completed" THEN 1 END) as completed_count')
            ->leftJoin('appointments', function($join) use ($servicesRevenueYear, $servicesRevenueMonth) {
                $join->on('services.id', '=', 'appointments.service_id')
                     ->where('appointments.status', '=', 'completed');
                
                if ($servicesRevenueMonth && $servicesRevenueMonth !== 'all') {
                    $join->whereYear('appointments.appointment_date', $servicesRevenueYear)
                         ->whereMonth('appointments.appointment_date', $servicesRevenueMonth);
                } else {
                    $join->whereYear('appointments.appointment_date', $servicesRevenueYear);
                }
            });
        
        $topServicesByRevenue = $topServicesByRevenueQuery->groupBy('services.id', 'services.name', 'services.price')
            ->havingRaw('total_revenue > 0')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        $filename = 'top_services_by_revenue_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($topServicesByRevenue) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Service Name', 'Price (₱)', 'Total Revenue (₱)', 'Completed Appointments']);
            
            foreach ($topServicesByRevenue as $service) {
                fputcsv($file, [
                    $service->name,
                    number_format($service->price, 2),
                    number_format($service->total_revenue, 2),
                    $service->completed_count
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Top Clients to CSV
     */
    public function exportTopClients(Request $request)
    {
        $clientsYear = $request->get('clients_year', now()->year);
        $clientsMonth = $request->get('clients_month', 'all');
        
        $topClientsQuery = User::select('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->selectRaw('SUM(CASE WHEN appointments.status = "completed" THEN appointments.total_amount ELSE 0 END) as total_spent')
            ->selectRaw('COUNT(CASE WHEN appointments.status = "completed" THEN 1 END) as total_appointments')
            ->leftJoin('appointments', function($join) use ($clientsYear, $clientsMonth) {
                $join->on('users.id', '=', 'appointments.client_id')
                     ->where('appointments.status', '=', 'completed');
                
                if ($clientsMonth && $clientsMonth !== 'all') {
                    $join->whereYear('appointments.appointment_date', $clientsYear)
                         ->whereMonth('appointments.appointment_date', $clientsMonth);
                } else {
                    $join->whereYear('appointments.appointment_date', $clientsYear);
                }
            })
            ->where('users.role', 'client');
        
        $topClients = $topClientsQuery->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->havingRaw('total_spent > 0')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        $filename = 'top_clients_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($topClients) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Client Name', 'Email', 'Total Spent (₱)', 'Total Appointments']);
            
            foreach ($topClients as $client) {
                fputcsv($file, [
                    $client->first_name . ' ' . $client->last_name,
                    $client->email,
                    number_format($client->total_spent, 2),
                    $client->total_appointments
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
