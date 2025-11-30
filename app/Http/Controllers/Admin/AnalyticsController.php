<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // Default to 30 days
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $productId = $request->get('product', null);
        $staffYear = $request->get('staff_year', now()->year);
        $staffMonth = $request->get('staff_month', now()->month);
        
        $startDate = now()->subDays($period);
        $endDate = now();
        
        // Calculate previous period for comparison
        $daysDiff = $endDate->diffInDays($startDate);
        $previousStartDate = $startDate->copy()->subDays($daysDiff);
        $previousEndDate = $startDate;

        // Revenue Analytics
        $revenueData = $this->getRevenueAnalytics($startDate, $endDate);
        
        // Appointment Analytics
        $appointmentData = $this->getAppointmentAnalytics($startDate, $endDate);
        
        // Service Analytics
        $serviceData = $this->getServiceAnalytics($startDate, $endDate);
        
        // Client Analytics
        $clientData = $this->getClientAnalytics($startDate, $endDate);
        
        // Staff Performance
        $staffData = $this->getStaffAnalytics($startDate, $endDate);
        
        // Inventory Analytics
        $inventoryData = $this->getInventoryAnalytics();

        // Monthly trends (last 12 months)
        $monthlyTrends = $this->getMonthlyTrends();

        // Staff Scheduling Analytics (Services completed by each staff member)
        // Use separate date range based on staff_year and staff_month filters
        if ($staffYear && $staffMonth) {
            // If both year and month are specified, use that specific month
            $staffSchedulingStartDate = Carbon::create($staffYear, $staffMonth, 1)->startOfMonth();
            $staffSchedulingEndDate = Carbon::create($staffYear, $staffMonth, 1)->endOfMonth();
        } elseif ($staffYear) {
            // If only year is specified, use the entire year
            $staffSchedulingStartDate = Carbon::create($staffYear, 1, 1)->startOfYear();
            $staffSchedulingEndDate = Carbon::create($staffYear, 12, 31)->endOfYear();
        } else {
            // Default to current month
            $staffSchedulingStartDate = now()->startOfMonth();
            $staffSchedulingEndDate = now()->endOfMonth();
        }
        $staffSchedulingData = $this->getStaffSchedulingAnalytics($staffSchedulingStartDate, $staffSchedulingEndDate);

        // Services Monthly Analytics (Highest demand per month)
        $servicesMonthlyData = $this->getServicesMonthlyAnalytics();

        // Product Sales Analytics (Top-selling products and reorder alerts)
        $productSalesData = $this->getProductSalesAnalytics($startDate, $endDate, $previousStartDate, $previousEndDate, $year, $month, $productId);

        // Inventory Usage Analytics (Product usage from services)
        $inventoryUsageData = $this->getInventoryUsageAnalytics($startDate, $endDate);

        return view('admin.analytics.index', compact(
            'revenueData',
            'appointmentData',
            'serviceData',
            'clientData',
            'staffData',
            'inventoryData',
            'monthlyTrends',
            'staffSchedulingData',
            'servicesMonthlyData',
            'productSalesData',
            'inventoryUsageData',
            'period',
            'year',
            'month',
            'productId',
            'staffYear',
            'staffMonth'
        ));
    }

    /**
     * Get revenue analytics data.
     */
    private function getRevenueAnalytics($startDate, $endDate)
    {
        $totalRevenue = Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->sum('total_amount');

        $previousPeriodRevenue = Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [
                $startDate->copy()->subDays($endDate->diffInDays($startDate)),
                $startDate
            ])
            ->sum('total_amount');

        $revenueGrowth = $previousPeriodRevenue > 0 
            ? (($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 
            : 0;

        // Daily revenue for chart
        $dailyRevenue = Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(appointment_date) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Average order value
        $avgOrderValue = Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->avg('total_amount');

        return [
            'total_revenue' => $totalRevenue,
            'previous_revenue' => $previousPeriodRevenue,
            'growth_percentage' => round($revenueGrowth, 2),
            'daily_revenue' => $dailyRevenue,
            'avg_order_value' => $avgOrderValue,
        ];
    }

    /**
     * Get appointment analytics data.
     */
    private function getAppointmentAnalytics($startDate, $endDate)
    {
        $totalAppointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])->count();
        
        $completedAppointments = Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->count();

        $cancelledAppointments = Appointment::where('status', 'cancelled')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->count();

        $noShowAppointments = Appointment::where('status', 'no_show')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->count();

        $completionRate = $totalAppointments > 0 
            ? ($completedAppointments / $totalAppointments) * 100 
            : 0;

        $cancellationRate = $totalAppointments > 0 
            ? ($cancelledAppointments / $totalAppointments) * 100 
            : 0;

        // Status distribution
        $statusDistribution = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'cancelled_appointments' => $cancelledAppointments,
            'no_show_appointments' => $noShowAppointments,
            'completion_rate' => round($completionRate, 2),
            'cancellation_rate' => round($cancellationRate, 2),
            'status_distribution' => $statusDistribution,
        ];
    }

    /**
     * Get service analytics data.
     */
    private function getServiceAnalytics($startDate, $endDate)
    {
        $popularServices = Service::withCount(['appointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
            }])
            ->having('appointments_count', '>', 0)
            ->orderBy('appointments_count', 'desc')
            ->limit(10)
            ->get();

        $serviceRevenue = Service::select('services.id', 'services.name', 'services.price', 'services.duration')
            ->selectRaw('SUM(appointments.total_amount) as revenue')
            ->selectRaw('COUNT(appointments.id) as booking_count')
            ->join('appointments', 'services.id', '=', 'appointments.service_id')
            ->where('appointments.status', 'completed')
            ->whereBetween('appointments.appointment_date', [$startDate, $endDate])
            ->groupBy('services.id', 'services.name', 'services.price', 'services.duration')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        return [
            'popular_services' => $popularServices,
            'service_revenue' => $serviceRevenue,
        ];
    }

    /**
     * Get client analytics data.
     */
    private function getClientAnalytics($startDate, $endDate)
    {
        $totalClients = User::where('role', 'client')->count();
        
        $newClients = User::where('role', 'client')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $activeClients = User::where('role', 'client')
            ->whereHas('clientAppointments', function($query) use ($startDate, $endDate) {
                $query->whereBetween('appointment_date', [$startDate, $endDate]);
            })
            ->count();

        $topClients = User::select('users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.email')
            ->where('role', 'client')
            ->withSum(['clientAppointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
            }], 'total_amount')
            ->withCount(['clientAppointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
            }])
            ->having('client_appointments_sum_total_amount', '>', 0)
            ->orderBy('client_appointments_sum_total_amount', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_clients' => $totalClients,
            'new_clients' => $newClients,
            'active_clients' => $activeClients,
            'top_clients' => $topClients,
        ];
    }

    /**
     * Get staff analytics data.
     */
    private function getStaffAnalytics($startDate, $endDate)
    {
        $staffPerformance = User::select('users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.email', 'users.role')
            ->whereIn('role', ['nurse', 'aesthetician'])
            ->withCount(['staffAppointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
            }])
            ->withSum(['staffAppointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
            }], 'total_amount')
            ->orderBy('staff_appointments_sum_total_amount', 'desc')
            ->get();

        return [
            'staff_performance' => $staffPerformance,
        ];
    }

    /**
     * Get inventory analytics data.
     */
    private function getInventoryAnalytics()
    {
        $totalItems = InventoryItem::active()->count();
        $lowStockItems = InventoryItem::active()->needsReordering()->count();
        $outOfStockItems = InventoryItem::active()->outOfStock()->count();
        $expiringSoonItems = InventoryItem::active()->expiringSoon()->count();
        $totalInventoryValue = InventoryItem::active()->sum(DB::raw('current_stock * cost_price'));

        $categoryDistribution = InventoryItem::active()
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return [
            'total_items' => $totalItems,
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'expiring_soon_items' => $expiringSoonItems,
            'total_inventory_value' => $totalInventoryValue,
            'category_distribution' => $categoryDistribution,
        ];
    }

    /**
     * Get inventory usage analytics data.
     */
    private function getInventoryUsageAnalytics($startDate, $endDate)
    {
        // Total usage count
        $totalUsageCount = InventoryUsageLog::whereBetween('inventory_usage_logs.created_at', [$startDate, $endDate])
            ->count();

        // Total quantity deducted (non-mL items)
        $totalQuantityDeducted = InventoryUsageLog::whereBetween('inventory_usage_logs.created_at', [$startDate, $endDate])
            ->where('is_ml_tracking', false)
            ->sum('quantity_deducted');

        // Total volume deducted (mL items)
        $totalVolumeDeducted = InventoryUsageLog::whereBetween('inventory_usage_logs.created_at', [$startDate, $endDate])
            ->where('is_ml_tracking', true)
            ->sum('volume_ml_deducted');

        // Top used products (by quantity or volume)
        $topUsedProducts = InventoryUsageLog::whereBetween('inventory_usage_logs.created_at', [$startDate, $endDate])
            ->select(
                'inventory_item_id',
                'item_name',
                'item_sku',
                DB::raw('SUM(quantity_deducted) as total_quantity'),
                DB::raw('SUM(volume_ml_deducted) as total_volume_ml'),
                DB::raw('COUNT(*) as usage_count')
            )
            ->groupBy('inventory_item_id', 'item_name', 'item_sku')
            ->orderByDesc(DB::raw('COALESCE(SUM(quantity_deducted), 0) + COALESCE(SUM(volume_ml_deducted), 0)'))
            ->limit(10)
            ->get();

        // Usage by service
        $usageByService = InventoryUsageLog::whereBetween('inventory_usage_logs.created_at', [$startDate, $endDate])
            ->whereNotNull('service_id')
            ->join('services', 'inventory_usage_logs.service_id', '=', 'services.id')
            ->select(
                'services.id',
                'services.name as service_name',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(quantity_deducted) as total_quantity'),
                DB::raw('SUM(volume_ml_deducted) as total_volume_ml')
            )
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get();

        // Daily usage trend
        $dailyUsageTrend = InventoryUsageLog::whereBetween('inventory_usage_logs.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(inventory_usage_logs.created_at) as date'),
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(quantity_deducted) as total_quantity'),
                DB::raw('SUM(volume_ml_deducted) as total_volume_ml')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_usage_count' => $totalUsageCount,
            'total_quantity_deducted' => $totalQuantityDeducted,
            'total_volume_deducted' => $totalVolumeDeducted,
            'top_used_products' => $topUsedProducts,
            'usage_by_service' => $usageByService,
            'daily_usage_trend' => $dailyUsageTrend,
        ];
    }

    /**
     * Get monthly trends data.
     */
    private function getMonthlyTrends()
    {
        $trends = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $revenue = Appointment::where('status', 'completed')
                ->whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
                ->sum('total_amount');
                
            $appointments = Appointment::whereBetween('appointment_date', [$startOfMonth, $endOfMonth])
                ->count();
                
            $newClients = User::where('role', 'client')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
                'appointments' => $appointments,
                'new_clients' => $newClients,
            ];
        }
        
        return $trends;
    }

    /**
     * Get staff scheduling analytics (services completed by each staff member).
     */
    private function getStaffSchedulingAnalytics($startDate, $endDate)
    {
        $staffServices = User::select('users.id', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.role')
            ->whereIn('role', ['nurse', 'aesthetician'])
            ->withCount(['staffAppointments' => function($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [$startDate, $endDate]);
            }])
            ->orderBy('staff_appointments_count', 'desc')
            ->get();

        return [
            'staff' => $staffServices->map(function($staff) {
                return $staff->name . ' (' . ucfirst($staff->role) . ')';
            })->toArray(),
            'services_completed' => $staffServices->pluck('staff_appointments_count')->toArray(),
        ];
    }

    /**
     * Get services monthly analytics (highest demand per month).
     */
    private function getServicesMonthlyAnalytics()
    {
        $months = [];
        $servicesData = [];

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

        // Get top services across all months
        $topServices = Service::select('services.id', 'services.name')
            ->withCount(['appointments' => function($query) {
                $query->where('status', 'completed')
                      ->whereBetween('appointment_date', [now()->subMonths(6)->startOfMonth(), now()->endOfMonth()]);
            }])
            ->having('appointments_count', '>', 0)
            ->orderBy('appointments_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'months' => $months,
            'services_data' => $servicesData,
            'top_services' => $topServices->pluck('name')->toArray(),
            'top_services_counts' => $topServices->pluck('appointments_count')->toArray(),
        ];
    }

    /**
     * Get product sales analytics (top-selling products and reorder alerts).
     */
    private function getProductSalesAnalytics($startDate, $endDate, $previousStartDate, $previousEndDate, $year = null, $month = null, $productId = null)
    {
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
        $comparisonData = [];
        foreach ($topProducts as $product) {
            $previousData = $previousProducts->get($product->inventory_item_id);
            $comparisonData[] = [
                'name' => $product->item_name,
                'selected_period' => (int)$product->total_quantity_sold,
                'previous_period' => $previousData ? (int)$previousData->total_quantity_sold : 0,
            ];
        }

        // Get products that need reordering (low stock)
        $reorderAlerts = InventoryItem::active()
            ->where(function($query) {
                $query->whereColumn('current_stock', '<=', 'minimum_stock')
                      ->orWhere('current_stock', '<=', 0);
            })
            ->orderBy('current_stock', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'current_stock' => $item->current_stock,
                    'minimum_stock' => $item->minimum_stock,
                    'needed' => max(0, $item->minimum_stock - $item->current_stock),
                ];
            });

        // Get monthly product sales trend
        $monthlyProductSales = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $monthlySales = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('sales.status', 'completed')
                ->whereBetween('sales.created_at', [$startOfMonth, $endOfMonth])
                ->sum('sale_items.quantity');

            $monthlyProductSales[] = [
                'month' => $date->format('M Y'),
                'quantity' => $monthlySales ?? 0,
            ];
        }

        // Get all products for filter dropdown
        $allProducts = InventoryItem::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'comparison_data' => $comparisonData,
            'top_products' => collect($comparisonData)->pluck('name')->toArray(),
            'selected_period_quantities' => collect($comparisonData)->pluck('selected_period')->toArray(),
            'previous_period_quantities' => collect($comparisonData)->pluck('previous_period')->toArray(),
            'reorder_alerts' => $reorderAlerts,
            'monthly_sales' => $monthlyProductSales,
            'all_products' => $allProducts,
        ];
    }

    /**
     * Get when to buy products data (Monthly and Annual).
     * Combines treatment products and after care products.
     */
    private function getWhenToBuyProducts()
    {
        // Get all products used in services (treatment products and after care products)
        // These are inventory items linked to services
        $productsUsedInServices = InventoryItem::active()
            ->whereHas('services')
            ->with(['services' => function($query) {
                $query->withCount('appointments');
            }])
            ->get();

        // Calculate consumption for last 3 months and last year
        $threeMonthsAgo = now()->subMonths(3);
        $oneYearAgo = now()->subYear();

        // Get appointment-based consumption (from completed appointments)
        $completedAppointments = Appointment::where('status', 'completed')
            ->where('appointment_date', '>=', $oneYearAgo)
            ->with(['services.treatmentProducts'])
            ->get();

        // Calculate monthly and annual consumption per product
        $monthlyProducts = [];
        $annualProducts = [];

        foreach ($productsUsedInServices as $product) {
            // Calculate consumption from appointments
            $monthlyConsumption = 0;
            $annualConsumption = 0;

            // Get consumption from last 3 months
            $recentAppointments = $completedAppointments->filter(function($apt) use ($threeMonthsAgo) {
                return Carbon::parse($apt->appointment_date)->gte($threeMonthsAgo);
            });

            foreach ($recentAppointments as $appointment) {
                foreach ($appointment->services as $service) {
                    $treatmentProduct = $service->treatmentProducts->where('id', $product->id)->first();
                    if ($treatmentProduct) {
                        $quantity = $treatmentProduct->pivot->quantity ?? 1;
                        $monthlyConsumption += $quantity;
                    }
                }
            }

            // Get consumption from last year
            foreach ($completedAppointments as $appointment) {
                foreach ($appointment->services as $service) {
                    $treatmentProduct = $service->treatmentProducts->where('id', $product->id)->first();
                    if ($treatmentProduct) {
                        $quantity = $treatmentProduct->pivot->quantity ?? 1;
                        $annualConsumption += $quantity;
                    }
                }
            }

            // Calculate average monthly consumption (from 3 months data)
            $avgMonthlyConsumption = $monthlyConsumption > 0 ? $monthlyConsumption / 3 : 0;

            // Calculate projected annual consumption
            $projectedAnnualConsumption = $avgMonthlyConsumption * 12;

            // Determine if product should be bought monthly or annually
            // If monthly consumption is significant (>= 5 units/month), recommend monthly purchase
            // Otherwise, recommend annual purchase
            if ($avgMonthlyConsumption >= 5) {
                $monthlyProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category,
                    'current_stock' => $product->current_stock,
                    'monthly_consumption' => round($avgMonthlyConsumption, 2),
                    'recommended_quantity' => ceil($avgMonthlyConsumption * 1.5), // 1.5x buffer
                    'estimated_cost' => $product->cost_price * ceil($avgMonthlyConsumption * 1.5),
                    'unit' => $product->unit,
                ];
            } else {
                $annualProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category,
                    'current_stock' => $product->current_stock,
                    'annual_consumption' => round($annualConsumption, 2),
                    'projected_annual' => round($projectedAnnualConsumption, 2),
                    'recommended_quantity' => ceil($projectedAnnualConsumption * 1.2), // 1.2x buffer
                    'estimated_cost' => $product->cost_price * ceil($projectedAnnualConsumption * 1.2),
                    'unit' => $product->unit,
                ];
            }
        }

        // Also include products from POS sales (after care products)
        $salesProducts = SaleItem::select('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->selectRaw('SUM(sale_items.quantity) as total_quantity_sold')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->where('sales.created_at', '>=', $threeMonthsAgo)
            ->groupBy('sale_items.inventory_item_id', 'sale_items.item_name', 'sale_items.item_sku')
            ->get();

        foreach ($salesProducts as $saleProduct) {
            $product = InventoryItem::find($saleProduct->inventory_item_id);
            if (!$product || !$product->is_active) {
                continue;
            }

            // Check if already in monthly or annual list
            $existsInMonthly = collect($monthlyProducts)->contains('id', $product->id);
            $existsInAnnual = collect($annualProducts)->contains('id', $product->id);

            if ($existsInMonthly || $existsInAnnual) {
                continue; // Skip if already added
            }

            $avgMonthlySales = $saleProduct->total_quantity_sold / 3;

            if ($avgMonthlySales >= 5) {
                $monthlyProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category,
                    'current_stock' => $product->current_stock,
                    'monthly_consumption' => round($avgMonthlySales, 2),
                    'recommended_quantity' => ceil($avgMonthlySales * 1.5),
                    'estimated_cost' => $product->cost_price * ceil($avgMonthlySales * 1.5),
                    'unit' => $product->unit,
                ];
            } else {
                $annualSales = SaleItem::where('inventory_item_id', $product->id)
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sales.status', 'completed')
                    ->where('sales.created_at', '>=', $oneYearAgo)
                    ->sum('sale_items.quantity');

                $annualProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category,
                    'current_stock' => $product->current_stock,
                    'annual_consumption' => round($annualSales, 2),
                    'projected_annual' => round($avgMonthlySales * 12, 2),
                    'recommended_quantity' => ceil($avgMonthlySales * 12 * 1.2),
                    'estimated_cost' => $product->cost_price * ceil($avgMonthlySales * 12 * 1.2),
                    'unit' => $product->unit,
                ];
            }
        }

        // Sort by consumption/importance
        usort($monthlyProducts, function($a, $b) {
            return $b['monthly_consumption'] <=> $a['monthly_consumption'];
        });

        usort($annualProducts, function($a, $b) {
            return $b['projected_annual'] <=> $a['projected_annual'];
        });

        return [
            'monthly' => $monthlyProducts,
            'annual' => $annualProducts,
            'total_monthly_cost' => collect($monthlyProducts)->sum('estimated_cost'),
            'total_annual_cost' => collect($annualProducts)->sum('estimated_cost'),
        ];
    }

    /**
     * Import analytics data from file.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'data_type' => 'required|in:appointments,sales,inventory,products',
                'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240', // 10MB max
                'skip_headers' => 'nullable|boolean',
            ], [
                'data_type.required' => 'Please select a data type',
                'data_type.in' => 'Invalid data type selected',
                'file.required' => 'Please select a file to upload',
                'file.file' => 'The uploaded file is not valid',
                'file.mimes' => 'The file must be a CSV or Excel file (CSV, XLSX, XLS)',
                'file.max' => 'The file size must not exceed 10MB',
            ]);

            $file = $request->file('file');
            $dataType = $request->input('data_type');
            $skipHeaders = $request->has('skip_headers');

            // Get file extension
            $extension = $file->getClientOriginalExtension();
            
            // Read file based on extension
            if ($extension === 'csv') {
                $data = $this->readCSV($file, $skipHeaders);
            } else {
                $data = $this->readExcel($file, $skipHeaders);
            }

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found in the file'
                ], 400);
            }

            // Process data based on type
            $result = $this->processImportedData($dataType, $data);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$result['imported']} {$dataType} records",
                'imported' => $result['imported'],
                'failed' => $result['failed'],
                'errors' => $result['errors']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $errorMessages[] = ucfirst($field) . ': ' . implode(', ', $messages);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(' | ', $errorMessages),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Analytics import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to import data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Read CSV file.
     */
    private function readCSV($file, $skipHeaders = true)
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        if ($handle === false) {
            throw new \Exception('Unable to open file');
        }
        
        $isFirstRow = true;
        $lineNumber = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $lineNumber++;
            
            // Skip empty rows
            if (empty(array_filter($row, function($cell) { return trim($cell) !== ''; }))) {
                continue;
            }
            
            if ($skipHeaders && $isFirstRow) {
                $isFirstRow = false;
                continue;
            }
            
            // Clean up the row data
            $cleanedRow = array_map('trim', $row);
            $data[] = $cleanedRow;
        }

        fclose($handle);
        
        if (empty($data)) {
            throw new \Exception('No data found in file. Please check if the file has data rows.');
        }
        
        return $data;
    }

    /**
     * Read Excel file.
     */
    private function readExcel($file, $skipHeaders = true)
    {
        // Check if PhpSpreadsheet is available
        $ioFactoryClass = '\PhpOffice\PhpSpreadsheet\IOFactory';
        if (class_exists($ioFactoryClass)) {
            try {
                // @phpstan-ignore-next-line
                $spreadsheet = $ioFactoryClass::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $data = [];
                $isFirstRow = true;

                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    
                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getCalculatedValue();
                    }
                    
                    // Skip empty rows
                    if (empty(array_filter($rowData, function($cell) { return trim($cell) !== ''; }))) {
                        continue;
                    }
                    
                    if ($skipHeaders && $isFirstRow) {
                        $isFirstRow = false;
                        continue;
                    }
                    
                    $data[] = array_map('trim', $rowData);
                }
                
                if (empty($data)) {
                    throw new \Exception('No data found in Excel file. Please check if the file has data rows.');
                }
                
                return $data;
            } catch (\Exception $e) {
                throw new \Exception('Error reading Excel file: ' . $e->getMessage());
            }
        } else {
            // Fallback: Try to read as CSV (some Excel files can be read as CSV)
            return $this->readCSV($file, $skipHeaders);
        }
    }

    /**
     * Process imported data based on type.
     */
    private function processImportedData($dataType, $data)
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        try {
            switch ($dataType) {
                case 'appointments':
                    $result = $this->importAppointments($data);
                    break;
                case 'sales':
                    $result = $this->importSales($data);
                    break;
                case 'inventory':
                    $result = $this->importInventory($data);
                    break;
                case 'products':
                    $result = $this->importProducts($data);
                    break;
                default:
                    throw new \Exception('Unknown data type');
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error("Error processing {$dataType} import: " . $e->getMessage());
            return [
                'imported' => 0,
                'failed' => count($data),
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Import appointments data.
     */
    private function importAppointments($data)
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Expected format: client_id, service_id, appointment_date, appointment_time, status, total_amount
                if (count($row) < 4) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": Insufficient columns";
                    continue;
                }

                Appointment::create([
                    'client_id' => $row[0] ?? null,
                    'service_id' => $row[1] ?? null,
                    'appointment_date' => $row[2] ?? now()->format('Y-m-d'),
                    'appointment_time' => $row[3] ?? '09:00:00',
                    'status' => $row[4] ?? 'pending',
                    'total_amount' => $row[5] ?? 0,
                    'appointment_number' => Appointment::generateAppointmentNumber(),
                ]);

                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return ['imported' => $imported, 'failed' => $failed, 'errors' => $errors];
    }

    /**
     * Import sales data.
     */
    private function importSales($data)
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Expected format: client_id, total_amount, status, payment_method
                if (count($row) < 2) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": Insufficient columns";
                    continue;
                }

                $sale = Sale::create([
                    'client_id' => $row[0] ?? null,
                    'total_amount' => $row[1] ?? 0,
                    'status' => $row[2] ?? 'completed',
                    'payment_method' => $row[3] ?? 'cash',
                ]);

                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return ['imported' => $imported, 'failed' => $failed, 'errors' => $errors];
    }

    /**
     * Import inventory data.
     */
    private function importInventory($data)
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Expected format: name, sku, category, current_stock, cost_price, unit, content_per_unit, content_unit
                // Minimum required: name, category
                if (count($row) < 2) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": Insufficient columns (need at least name and category)";
                    continue;
                }

                // Generate SKU if not provided
                $sku = !empty($row[1]) ? $row[1] : 'SKU-' . strtoupper(substr(md5($row[0] . time() . $index), 0, 8));
                
                // Check if SKU already exists
                if (InventoryItem::where('sku', $sku)->exists()) {
                    $sku = 'SKU-' . strtoupper(substr(md5($row[0] . time() . $index . rand()), 0, 8));
                }

                InventoryItem::create([
                    'name' => trim($row[0]) ?? 'Unknown',
                    'sku' => $sku,
                    'category' => trim($row[2]) ?? 'general',
                    'current_stock' => isset($row[3]) && is_numeric($row[3]) ? (int)$row[3] : 0,
                    'minimum_stock' => isset($row[5]) && is_numeric($row[5]) ? (int)$row[5] : 10,
                    'cost_price' => isset($row[4]) && is_numeric($row[4]) ? (float)$row[4] : 0,
                    'unit' => isset($row[6]) && !empty($row[6]) ? trim($row[6]) : 'piece',
                    'content_per_unit' => isset($row[7]) && is_numeric($row[7]) ? (float)$row[7] : null,
                    'content_unit' => isset($row[8]) && !empty($row[8]) ? trim($row[8]) : null,
                    'is_active' => true,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                \Log::error("Inventory import error for row " . ($index + 1) . ": " . $e->getMessage(), [
                    'row_data' => $row,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return ['imported' => $imported, 'failed' => $failed, 'errors' => $errors];
    }

    /**
     * Import products data (same as inventory).
     */
    private function importProducts($data)
    {
        return $this->importInventory($data);
    }

    /**
     * Export analytics data.
     */
    public function export(Request $request)
    {
        $period = $request->get('period', '30');
        $format = $request->get('format', 'csv');
        
        // Generate export data
        $data = $this->generateExportData($period);
        
        if ($format === 'pdf') {
            return $this->exportToPDF($data);
        }
        
        return $this->exportToCSV($data);
    }

    /**
     * Generate export data.
     */
    private function generateExportData($period)
    {
        $startDate = now()->subDays($period);
        $endDate = now();
        
        return [
            'revenue' => $this->getRevenueAnalytics($startDate, $endDate),
            'appointments' => $this->getAppointmentAnalytics($startDate, $endDate),
            'services' => $this->getServiceAnalytics($startDate, $endDate),
            'clients' => $this->getClientAnalytics($startDate, $endDate),
        ];
    }

    /**
     * Export to CSV.
     */
    private function exportToCSV($data)
    {
        $filename = 'analytics_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Revenue data
            fputcsv($file, ['REVENUE ANALYTICS']);
            fputcsv($file, ['Total Revenue', '₱' . number_format($data['revenue']['total_revenue'], 2)]);
            fputcsv($file, ['Growth Percentage', $data['revenue']['growth_percentage'] . '%']);
            fputcsv($file, ['Average Order Value', '₱' . number_format($data['revenue']['avg_order_value'], 2)]);
            fputcsv($file, []);
            
            // Appointment data
            fputcsv($file, ['APPOINTMENT ANALYTICS']);
            fputcsv($file, ['Total Appointments', $data['appointments']['total_appointments']]);
            fputcsv($file, ['Completed Appointments', $data['appointments']['completed_appointments']]);
            fputcsv($file, ['Completion Rate', $data['appointments']['completion_rate'] . '%']);
            fputcsv($file, ['Cancellation Rate', $data['appointments']['cancellation_rate'] . '%']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF.
     */
    private function exportToPDF($data)
    {
        // This would require a PDF library like DomPDF
        // For now, return a simple response
        return response()->json(['message' => 'PDF export feature coming soon']);
    }
}
