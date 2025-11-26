<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryItem;
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
     * Export sales data.
     */
    public function export(Request $request)
    {
        $query = Sale::with(['user', 'client', 'items']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'sales_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // Headers
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

            // Data
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
                    number_format($sale->total_amount, 2),
                    ucfirst($sale->payment_method ?? 'cash'),
                    ucfirst($sale->status ?? 'completed'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
