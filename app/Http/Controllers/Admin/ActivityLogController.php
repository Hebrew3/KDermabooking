<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use App\Models\InventoryStockLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')
            ->forModule('inventory')
            ->orderBy('created_at', 'desc');

        // Filter by month
        if ($request->filled('month') && $request->month !== 'all') {
            $query->forMonth($request->month);
        }

        // Filter by year
        if ($request->filled('year') && $request->year !== 'all') {
            $query->forYear($request->year);
        }

        // Filter by specific date
        if ($request->filled('date')) {
            try {
                $date = Carbon::createFromFormat('d/m/Y', $request->date);
                $query->forDate($date);
            } catch (\Exception $e) {
                // Invalid date format, ignore filter
            }
        }

        $logs = $query->paginate(20);

        // Get available months and years for filter dropdowns
        $availableMonths = ActivityLog::forModule('inventory')
            ->selectRaw('MONTH(created_at) as month')
            ->distinct()
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->month,
                    'label' => Carbon::create()->month($item->month)->format('F')
                ];
            })
            ->values();

        $availableYears = ActivityLog::forModule('inventory')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('admin.activity-logs.index', compact('logs', 'availableMonths', 'availableYears'));
    }

    /**
     * Display inventory activity log with stock tracking.
     */
    public function inventoryActivityLog(Request $request)
    {
        // Get date range filters
        $startDate = $request->filled('start_date') 
            ? Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();
        
        $endDate = $request->filled('end_date')
            ? Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        // Get product filter
        $productFilter = $request->filled('product_name') ? $request->product_name : null;
        $categoryFilter = $request->filled('category') && $request->category !== 'all' ? $request->category : null;

        // Get usage logs (Used/Consumed activities)
        $usageLogsQuery = InventoryUsageLog::with(['inventoryItem'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Get stock logs (Restock activities)
        $stockLogsQuery = InventoryStockLog::with(['inventoryItem'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Apply product filter
        if ($productFilter) {
            $productIds = InventoryItem::where('name', 'like', '%' . $productFilter . '%')
                                       ->orWhere('sku', 'like', '%' . $productFilter . '%')
                                       ->pluck('id');
            $usageLogsQuery->whereIn('inventory_item_id', $productIds);
            $stockLogsQuery->whereIn('inventory_item_id', $productIds);
        }

        // Apply category filter
        if ($categoryFilter) {
            $productIds = InventoryItem::where('category', $categoryFilter)->pluck('id');
            $usageLogsQuery->whereIn('inventory_item_id', $productIds);
            $stockLogsQuery->whereIn('inventory_item_id', $productIds);
        }

        // Filter by staff (via appointments) for usage logs
        if ($request->filled('staff_id') && $request->staff_id !== 'all') {
            $usageLogsQuery->whereHas('appointment', function($q) use ($request) {
                $q->where('staff_id', $request->staff_id);
            });
        }

        // Filter by usage type
        if ($request->filled('usage_type') && $request->usage_type !== 'all') {
            $usageLogsQuery->where('usage_type', $request->usage_type);
        }

        // Get all logs
        $usageLogs = $usageLogsQuery->get();
        $stockLogs = $stockLogsQuery->get();

        // Combine and format activities
        $activities = collect();

        // Add usage logs as "Used / Consumed" activities
        foreach ($usageLogs as $log) {
            $item = $log->inventoryItem;
            if (!$item) continue;

            $quantityChange = $item->usesMlTracking() 
                ? (float) $log->volume_ml_deducted 
                : (float) $log->quantity_deducted;

            $activities->push([
                'date' => $log->created_at,
                'product_name' => $log->item_name ?? $item->name,
                'product_sku' => $log->item_sku ?? $item->sku,
                'activity' => 'Used / Consumed',
                'beginning_stock' => (float) $log->stock_before,
                'quantity_change' => -abs($quantityChange), // Negative for used
                'updated_stock' => (float) $log->stock_after,
                'unit' => $log->unit ?? ($item->usesMlTracking() ? 'mL' : $item->unit),
            ]);
        }

        // Add stock logs as "Restock" or "Used / Consumed" activities
        foreach ($stockLogs as $log) {
            $item = $log->inventoryItem;
            if (!$item) continue;

            // Show restock activities (positive quantity_change)
            if ($log->activity_type === 'restock' && $log->quantity_change > 0) {
                $activities->push([
                    'date' => $log->created_at,
                    'product_name' => $item->name,
                    'product_sku' => $item->sku,
                    'activity' => 'Restock',
                    'beginning_stock' => (float) $log->stock_before,
                    'quantity_change' => (float) $log->quantity_change, // Positive for restock
                    'updated_stock' => (float) $log->stock_after,
                    'unit' => $log->unit ?? $item->unit,
                ]);
            }
            // Show used/removed activities (negative quantity_change or activity_type is 'used')
            elseif ($log->activity_type === 'used' && $log->quantity_change < 0) {
                $activities->push([
                    'date' => $log->created_at,
                    'product_name' => $item->name,
                    'product_sku' => $item->sku,
                    'activity' => 'Used / Consumed',
                    'beginning_stock' => (float) $log->stock_before,
                    'quantity_change' => (float) $log->quantity_change, // Negative for used
                    'updated_stock' => (float) $log->stock_after,
                    'unit' => $log->unit ?? $item->unit,
                ]);
            }
            // Show adjusted activities (when stock is set to a specific value)
            elseif ($log->activity_type === 'adjusted') {
                $activities->push([
                    'date' => $log->created_at,
                    'product_name' => $item->name,
                    'product_sku' => $item->sku,
                    'activity' => $log->quantity_change > 0 ? 'Restock' : ($log->quantity_change < 0 ? 'Used / Consumed' : 'Adjusted'),
                    'beginning_stock' => (float) $log->stock_before,
                    'quantity_change' => (float) $log->quantity_change,
                    'updated_stock' => (float) $log->stock_after,
                    'unit' => $log->unit ?? $item->unit,
                ]);
            }
        }

        // Sort by date (newest first)
        $activities = $activities->sortByDesc('date')->values();

        // Get filter options
        $categories = InventoryItem::distinct()->pluck('category')->sort()->values();
        $staffMembers = User::whereIn('role', ['nurse', 'aesthetician', 'admin'])
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();
        
        $usageTypes = InventoryUsageLog::distinct()->pluck('usage_type')->sort()->values();

        return view('admin.activity-logs.inventory-activity', compact(
            'activities',
            'categories',
            'staffMembers',
            'usageTypes',
            'startDate',
            'endDate'
        ));
    }
}

