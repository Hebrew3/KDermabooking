<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryItem;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesController extends Controller
{
    /**
     * Display sales list and analytics.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'client', 'items']);

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            // Default to last 30 days
            $query->where('created_at', '>=', now()->subDays(30));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by sale number
        if ($request->filled('search')) {
            $query->where('sale_number', 'like', '%' . $request->search . '%');
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get analytics
        $startDate = $request->filled('start_date') ? $request->start_date : now()->subDays(30)->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->format('Y-m-d');
        
        $analytics = $this->getAnalytics($startDate, $endDate);

        return view('admin.sales.index', compact('sales', 'analytics', 'startDate', 'endDate'));
    }

    /**
     * Show sale details.
     */
    public function show(Sale $sale)
    {
        $sale->load(['items.inventoryItem', 'user', 'client']);
        
        return view('admin.sales.show', compact('sale'));
    }

    /**
     * Get sales analytics.
     */
    private function getAnalytics($startDate, $endDate)
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        $sales = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $totalDiscount = $sales->sum('discount');
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Sales by payment method
        $salesByPayment = $sales->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount'),
                ];
            });

        // Top selling products
        $topProducts = SaleItem::whereHas('sale', function($query) use ($start, $end) {
                $query->where('status', 'completed')
                      ->whereBetween('created_at', [$start, $end]);
            })
            ->select('inventory_item_id', 'item_name', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total) as total_revenue'))
            ->groupBy('inventory_item_id', 'item_name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Daily sales trend
        $dailySales = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sales by staff
        $salesByStaff = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('user_id')
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'user' => $group->first()->user,
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount'),
                ];
            })
            ->sortByDesc('total')
            ->take(10);

        return [
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_discount' => $totalDiscount,
            'average_sale' => $averageSale,
            'sales_by_payment' => $salesByPayment,
            'top_products' => $topProducts,
            'daily_sales' => $dailySales,
            'sales_by_staff' => $salesByStaff,
        ];
    }

    /**
     * Export sales data with all analytics.
     */
    public function export(Request $request)
    {
        // Determine date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } else {
            // Default to last 30 days
            $startDate = now()->subDays(30)->startOfDay();
            $endDate = now()->endOfDay();
        }

        // Get sales transactions
        $query = Sale::with(['user', 'client', 'items']);
        $query->whereBetween('created_at', [$startDate, $endDate]);
        $sales = $query->orderBy('created_at', 'desc')->get();

        // Get all analytics
        $salesAnalytics = $this->getAnalytics($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
        $appointmentAnalytics = $this->getAppointmentAnalytics($startDate, $endDate);
        $serviceAnalytics = $this->getServiceAnalytics($startDate, $endDate);
        $clientAnalytics = $this->getClientAnalytics($startDate, $endDate);
        $staffAnalytics = $this->getStaffAnalytics($startDate, $endDate);
        $staffSchedulingAnalytics = $this->getStaffSchedulingAnalytics($startDate, $endDate);

        // Generate CSV
        $filename = 'sales_analytics_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($sales, $salesAnalytics, $appointmentAnalytics, $serviceAnalytics, $clientAnalytics, $staffAnalytics, $staffSchedulingAnalytics, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Export Header
            fputcsv($file, ['K-DERMA SALES & ANALYTICS EXPORT']);
            fputcsv($file, ['Export Date', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Period', $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);

            // ========== SALES ANALYTICS SUMMARY ==========
            fputcsv($file, ['========== SALES ANALYTICS SUMMARY ==========']);
            fputcsv($file, ['Total Sales', $salesAnalytics['total_sales']]);
            fputcsv($file, ['Total Revenue', '₱' . number_format($salesAnalytics['total_revenue'], 2)]);
            fputcsv($file, ['Total Discount', '₱' . number_format($salesAnalytics['total_discount'], 2)]);
            fputcsv($file, ['Average Sale', '₱' . number_format($salesAnalytics['average_sale'], 2)]);
            fputcsv($file, []);

            // Sales by Payment Method
            fputcsv($file, ['--- Sales by Payment Method ---']);
            fputcsv($file, ['Payment Method', 'Count', 'Total Amount']);
            foreach ($salesAnalytics['sales_by_payment'] as $method => $data) {
                fputcsv($file, [
                    ucfirst($method ?? 'N/A'),
                    $data['count'],
                    '₱' . number_format($data['total'], 2)
                ]);
            }
            fputcsv($file, []);

            // Top Selling Products
            fputcsv($file, ['--- Top Selling Products ---']);
            fputcsv($file, ['Product Name', 'Quantity Sold', 'Total Revenue']);
            foreach ($salesAnalytics['top_products'] as $product) {
                fputcsv($file, [
                    $product->item_name,
                    $product->total_quantity,
                    '₱' . number_format($product->total_revenue, 2)
                ]);
            }
            fputcsv($file, []);

            // Sales by Staff
            fputcsv($file, ['--- Sales by Staff ---']);
            fputcsv($file, ['Staff Name', 'Sales Count', 'Total Revenue']);
            foreach ($salesAnalytics['sales_by_staff'] as $staffData) {
                fputcsv($file, [
                    $staffData['user'] ? $staffData['user']->name : 'N/A',
                    $staffData['count'],
                    '₱' . number_format($staffData['total'], 2)
                ]);
            }
            fputcsv($file, []);

            // Daily Sales Trend
            fputcsv($file, ['--- Daily Sales Trend ---']);
            fputcsv($file, ['Date', 'Sales Count', 'Revenue']);
            foreach ($salesAnalytics['daily_sales'] as $daily) {
                fputcsv($file, [
                    $daily->date,
                    $daily->count,
                    '₱' . number_format($daily->revenue, 2)
                ]);
            }
            fputcsv($file, []);

            // ========== APPOINTMENT ANALYTICS ==========
            fputcsv($file, ['========== APPOINTMENT ANALYTICS ==========']);
            fputcsv($file, ['Total Appointments', $appointmentAnalytics['total_appointments']]);
            fputcsv($file, ['Completed Appointments', $appointmentAnalytics['completed_appointments']]);
            fputcsv($file, ['Pending Appointments', $appointmentAnalytics['pending_appointments']]);
            fputcsv($file, ['Confirmed Appointments', $appointmentAnalytics['confirmed_appointments']]);
            fputcsv($file, ['Cancelled Appointments', $appointmentAnalytics['cancelled_appointments']]);
            fputcsv($file, ['Completion Rate', number_format($appointmentAnalytics['completion_rate'], 2) . '%']);
            fputcsv($file, ['Cancellation Rate', number_format($appointmentAnalytics['cancellation_rate'], 2) . '%']);
            fputcsv($file, ['Total Appointment Revenue', '₱' . number_format($appointmentAnalytics['total_revenue'], 2)]);
            fputcsv($file, []);

            // ========== SERVICE ANALYTICS ==========
            fputcsv($file, ['========== SERVICE ANALYTICS ==========']);
            fputcsv($file, ['--- Popular Services (by appointment count) ---']);
            fputcsv($file, ['Service Name', 'Appointment Count']);
            foreach ($serviceAnalytics['popular_services'] as $service) {
                fputcsv($file, [
                    $service->name,
                    $service->appointments_count
                ]);
            }
            fputcsv($file, []);

            fputcsv($file, ['--- Top Services by Revenue ---']);
            fputcsv($file, ['Service Name', 'Price', 'Duration (min)', 'Bookings', 'Revenue']);
            foreach ($serviceAnalytics['service_revenue'] as $service) {
                fputcsv($file, [
                    $service->name,
                    '₱' . number_format($service->price, 2),
                    $service->duration,
                    $service->booking_count,
                    '₱' . number_format($service->revenue ?? 0, 2)
                ]);
            }
            fputcsv($file, []);

            // ========== CLIENT ANALYTICS ==========
            fputcsv($file, ['========== CLIENT ANALYTICS ==========']);
            fputcsv($file, ['Total Clients', $clientAnalytics['total_clients']]);
            fputcsv($file, ['New Clients (Period)', $clientAnalytics['new_clients']]);
            fputcsv($file, ['Active Clients (Period)', $clientAnalytics['active_clients']]);
            fputcsv($file, []);

            fputcsv($file, ['--- Top Clients by Revenue ---']);
            fputcsv($file, ['Client Name', 'Email', 'Appointments', 'Total Spent']);
            foreach ($clientAnalytics['top_clients'] as $client) {
                fputcsv($file, [
                    $client->name,
                    $client->email,
                    $client->client_appointments_count,
                    '₱' . number_format($client->client_appointments_sum_total_amount ?? 0, 2)
                ]);
            }
            fputcsv($file, []);

            // ========== STAFF ANALYTICS ==========
            fputcsv($file, ['========== STAFF PERFORMANCE ANALYTICS ==========']);
            fputcsv($file, ['Staff Name', 'Role', 'Completed Appointments', 'Total Revenue']);
            foreach ($staffAnalytics['staff_performance'] as $staff) {
                fputcsv($file, [
                    $staff->name,
                    ucfirst($staff->role),
                    $staff->staff_appointments_count,
                    '₱' . number_format($staff->staff_appointments_sum_total_amount ?? 0, 2)
                ]);
            }
            fputcsv($file, []);

            // Staff Scheduling Analytics
            fputcsv($file, ['--- Staff Scheduling (Services Completed) ---']);
            fputcsv($file, ['Staff Name', 'Services Completed']);
            for ($i = 0; $i < count($staffSchedulingAnalytics['staff']); $i++) {
                fputcsv($file, [
                    $staffSchedulingAnalytics['staff'][$i],
                    $staffSchedulingAnalytics['services_completed'][$i]
                ]);
            }
            fputcsv($file, []);

            // ========== SALES TRANSACTIONS ==========
            fputcsv($file, ['========== SALES TRANSACTIONS ==========']);
            fputcsv($file, [
                'Sale Number',
                'Date',
                'Staff',
                'Client',
                'Items',
                'Subtotal',
                'Discount',
                'Tax',
                'Total',
                'Payment Method',
                'Status'
            ]);

            foreach ($sales as $sale) {
                $items = $sale->items->map(function($item) {
                    return "{$item->item_name} (x{$item->quantity})";
                })->implode(', ');

                fputcsv($file, [
                    $sale->sale_number,
                    $sale->created_at->format('Y-m-d H:i:s'),
                    $sale->user ? $sale->user->name : 'N/A',
                    $sale->client ? $sale->client->name : 'Walk-in',
                    $items,
                    number_format($sale->subtotal ?? 0, 2),
                    number_format($sale->discount ?? 0, 2),
                    number_format($sale->tax ?? 0, 2),
                    number_format($sale->total_amount ?? 0, 2),
                    ucfirst($sale->payment_method ?? 'cash'),
                    ucfirst($sale->status ?? 'completed'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get appointment analytics.
     */
    private function getAppointmentAnalytics($startDate, $endDate)
    {
        $appointments = Appointment::whereBetween('appointment_date', [$startDate, $endDate])->get();
        
        $totalAppointments = $appointments->count();
        $completedAppointments = $appointments->where('status', 'completed')->count();
        $pendingAppointments = $appointments->where('status', 'pending')->count();
        $confirmedAppointments = $appointments->where('status', 'confirmed')->count();
        $cancelledAppointments = $appointments->where('status', 'cancelled')->count();
        
        $completionRate = $totalAppointments > 0 ? ($completedAppointments / $totalAppointments) * 100 : 0;
        $cancellationRate = $totalAppointments > 0 ? ($cancelledAppointments / $totalAppointments) * 100 : 0;
        
        $totalRevenue = Appointment::where('status', 'completed')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->sum('total_amount');

        return [
            'total_appointments' => $totalAppointments,
            'completed_appointments' => $completedAppointments,
            'pending_appointments' => $pendingAppointments,
            'confirmed_appointments' => $confirmedAppointments,
            'cancelled_appointments' => $cancelledAppointments,
            'completion_rate' => $completionRate,
            'cancellation_rate' => $cancellationRate,
            'total_revenue' => $totalRevenue,
        ];
    }

    /**
     * Get service analytics.
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
     * Get client analytics.
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
     * Get staff analytics.
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
     * Get staff scheduling analytics.
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
}
