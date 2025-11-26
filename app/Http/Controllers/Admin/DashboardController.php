<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\SaleItem;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
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

        // Monthly Revenue Chart Data (last 6 months)
        $monthlyRevenue = [];
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

        // Appointment Status Distribution
        $appointmentStatusData = Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Analytics Data
        $startDate = now()->subDays(30);
        $endDate = now();
        
        // Staff Scheduling Analytics (Services completed by each staff member)
        $staffServices = User::select('users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.role')
            ->whereIn('role', ['nurse', 'aesthetician'])
            ->withCount(['staffAppointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
            }])
            ->orderBy('staff_appointments_count', 'desc')
            ->get();

        $staffSchedulingData = [
            'staff' => $staffServices->map(function($staff) {
                return $staff->name . ' (' . ucfirst($staff->role) . ')';
            })->toArray(),
            'services_completed' => $staffServices->pluck('staff_appointments_count')->toArray(),
        ];

        // Services Monthly Analytics (Service demand per month - Top 5 services)
        $months = [];
        $servicesData = [];
        $topServices = [];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $months[] = $date->format('M Y');

            // Get service counts for this month
            $serviceCounts = Service::select('services.id', 'services.name')
                ->withCount(['appointments' => function($query) use ($startOfMonth, $endOfMonth) {
                    $query->where('status', 'completed')
                          ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth]);
                }])
                ->having('appointments_count', '>', 0)
                ->orderBy('appointments_count', 'desc')
                ->limit(10)
                ->get();

            $servicesData[] = [
                'month' => $date->format('M Y'),
                'services' => $serviceCounts->pluck('name')->toArray(),
                'counts' => $serviceCounts->pluck('appointments_count')->toArray(),
            ];
        }

        // Get top 5 services across all months
        $topServicesList = Service::select('services.id', 'services.name')
            ->withCount(['appointments' => function($query) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [now()->subMonths(6)->startOfMonth(), now()->endOfMonth()]);
            }])
            ->having('appointments_count', '>', 0)
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get();

        $topServices = $topServicesList->pluck('name')->toArray();
        
        // Build services monthly data for chart
        $servicesMonthlyChartData = [];
        foreach ($topServices as $serviceName) {
            $serviceData = [];
            foreach ($months as $month) {
                $monthData = collect($servicesData)->firstWhere('month', $month);
                $serviceIndex = $monthData ? array_search($serviceName, $monthData['services']) : false;
                $serviceData[] = $serviceIndex !== false ? $monthData['counts'][$serviceIndex] : 0;
            }
            $servicesMonthlyChartData[] = [
                'name' => $serviceName,
                'data' => $serviceData
            ];
        }

        // Product Sales Analytics (Top-selling products comparison)
        // Get filter parameters from request
        $year = request()->get('year', null);
        $month = request()->get('month', null);
        $productId = request()->get('product', null);
        
        $previousStartDate = $startDate->copy()->subDays(30);
        $previousEndDate = $startDate;
        
        // Build query for selected period
        $selectedPeriodQuery = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$startDate, $endDate]);

        // Build query for previous period
        $previousPeriodQuery = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$previousStartDate, $previousEndDate]);

        // Apply filters if provided
        if ($year && $month) {
            // If both year and month are specified, compare with same month previous year
            $selectedPeriodQuery->whereYear('sales.created_at', $year)
                                ->whereMonth('sales.created_at', $month);
            $previousPeriodQuery->whereYear('sales.created_at', $year - 1)
                                ->whereMonth('sales.created_at', $month);
        } elseif ($year) {
            // If only year is specified, compare with previous year
            $selectedPeriodQuery->whereYear('sales.created_at', $year);
            $previousPeriodQuery->whereYear('sales.created_at', $year - 1);
        } elseif ($month) {
            // If only month is specified, compare with same month previous year
            $selectedPeriodQuery->whereMonth('sales.created_at', $month);
            $previousPeriodQuery->whereMonth('sales.created_at', $month)
                                ->whereYear('sales.created_at', now()->year - 1);
        }
        
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
            'productId'
        ));
    }

    /**
     * Get analytics data via AJAX for real-time updates.
     */
    public function getAnalyticsData(Request $request)
    {
        $startDate = now()->subDays(30);
        $endDate = now();
        
        // Get filter parameters
        $year = $request->get('year', null);
        $month = $request->get('month', null);
        $productId = $request->get('product', null);

        // Staff Scheduling Analytics
        $staffServices = User::select('users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.role')
            ->whereIn('role', ['nurse', 'aesthetician'])
            ->withCount(['staffAppointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
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
        $topServices = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $months[] = $date->format('M Y');

            $serviceCounts = Service::select('services.id', 'services.name')
                ->withCount(['appointments' => function($query) use ($startOfMonth, $endOfMonth) {
                    $query->where('status', 'completed')
                          ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth]);
                }])
                ->having('appointments_count', '>', 0)
                ->orderBy('appointments_count', 'desc')
                ->limit(10)
                ->get();

            $servicesData[] = [
                'month' => $date->format('M Y'),
                'services' => $serviceCounts->pluck('name')->toArray(),
                'counts' => $serviceCounts->pluck('appointments_count')->toArray(),
            ];
        }

        $topServicesList = Service::select('services.id', 'services.name')
            ->withCount(['appointments' => function($query) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [now()->subMonths(6)->startOfMonth(), now()->endOfMonth()]);
            }])
            ->having('appointments_count', '>', 0)
            ->orderBy('appointments_count', 'desc')
            ->limit(5)
            ->get();

        $topServices = $topServicesList->pluck('name')->toArray();
        
        $servicesMonthlyChartData = [];
        foreach ($topServices as $serviceName) {
            $serviceData = [];
            foreach ($months as $month) {
                $monthData = collect($servicesData)->firstWhere('month', $month);
                $serviceIndex = $monthData ? array_search($serviceName, $monthData['services']) : false;
                $serviceData[] = $serviceIndex !== false ? $monthData['counts'][$serviceIndex] : 0;
            }
            $servicesMonthlyChartData[] = [
                'name' => $serviceName,
                'data' => $serviceData
            ];
        }

        // Product Sales Analytics
        $previousStartDate = $startDate->copy()->subDays(30);
        $previousEndDate = $startDate;
        
        $selectedPeriodQuery = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$startDate, $endDate]);

        $previousPeriodQuery = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$previousStartDate, $previousEndDate]);

        if ($year && $month) {
            $selectedPeriodQuery->whereYear('sales.created_at', $year)
                                ->whereMonth('sales.created_at', $month);
            $previousPeriodQuery->whereYear('sales.created_at', $year - 1)
                                ->whereMonth('sales.created_at', $month);
        } elseif ($year) {
            $selectedPeriodQuery->whereYear('sales.created_at', $year);
            $previousPeriodQuery->whereYear('sales.created_at', $year - 1);
        } elseif ($month) {
            $selectedPeriodQuery->whereMonth('sales.created_at', $month);
            $previousPeriodQuery->whereMonth('sales.created_at', $month)
                                ->whereYear('sales.created_at', now()->year - 1);
        }
        
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
                'name' => $product->item_name,
                'selected_period' => (int)$product->total_quantity_sold,
                'previous_period' => $previousData ? (int)$previousData->total_quantity_sold : 0,
            ];
        }

        return response()->json([
            'staff_scheduling' => $staffSchedulingData,
            'services_monthly' => [
                'months' => $months,
                'chart_data' => $servicesMonthlyChartData,
                'top_services' => $topServices
            ],
            'product_sales' => $productSalesData
        ]);
    }
}
