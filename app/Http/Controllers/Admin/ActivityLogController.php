<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
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

        // Base query for inventory items
        $itemsQuery = InventoryItem::query();

        // Filter by product name
        if ($request->filled('product_name')) {
            $itemsQuery->where('name', 'like', '%' . $request->product_name . '%')
                      ->orWhere('sku', 'like', '%' . $request->product_name . '%');
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'all') {
            $itemsQuery->where('category', $request->category);
        }

        // Get all inventory items
        $items = $itemsQuery->orderBy('name')->get();

        // Build usage logs query
        $usageLogsQuery = InventoryUsageLog::query()
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by staff (via appointments)
        if ($request->filled('staff_id') && $request->staff_id !== 'all') {
            $usageLogsQuery->whereHas('appointment', function($q) use ($request) {
                $q->where('staff_id', $request->staff_id);
            });
        }

        // Filter by usage type
        if ($request->filled('usage_type') && $request->usage_type !== 'all') {
            $usageLogsQuery->where('usage_type', $request->usage_type);
        }

        // Filter by product (if specified)
        if ($request->filled('product_name')) {
            $productIds = InventoryItem::where('name', 'like', '%' . $request->product_name . '%')
                                       ->orWhere('sku', 'like', '%' . $request->product_name . '%')
                                       ->pluck('id');
            $usageLogsQuery->whereIn('inventory_item_id', $productIds);
        }

        // Get usage logs grouped by inventory item (eager load relationships)
        $usageLogs = $usageLogsQuery->with(['appointment.staff', 'inventoryItem'])
            ->get()
            ->groupBy('inventory_item_id');

        // Calculate stock data for each item
        $stockData = [];
        foreach ($items as $item) {
            // Get beginning stock (stock at start of period)
            // First, try to get the last usage log before the period starts
            $lastUsageBeforePeriod = InventoryUsageLog::where('inventory_item_id', $item->id)
                ->where('created_at', '<', $startDate)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastUsageBeforePeriod) {
                // Use stock_after from the last usage before period
                $beginningStock = $item->usesMlTracking() 
                    ? (float) $lastUsageBeforePeriod->stock_after 
                    : (float) $lastUsageBeforePeriod->stock_after;
            } else {
                // No usage before period, try to get first usage in period
                $firstUsageInPeriod = InventoryUsageLog::where('inventory_item_id', $item->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'asc')
                    ->first();

                if ($firstUsageInPeriod) {
                    // Use stock_before from first usage in period
                    $beginningStock = $item->usesMlTracking() 
                        ? (float) $firstUsageInPeriod->stock_before 
                        : (float) $firstUsageInPeriod->stock_before;
                } else {
                    // No usage logs at all, use current stock
                    $beginningStock = $item->usesMlTracking() 
                        ? (float) ($item->total_volume_ml ?? 0) 
                        : (int) ($item->current_stock ?? 0);
                }
            }

            // Get usage logs for this item in the period
            $itemUsageLogs = $usageLogs->get($item->id, collect());

            // Calculate used/consumed
            $used = 0;
            if ($item->usesMlTracking()) {
                $used = $itemUsageLogs->sum('volume_ml_deducted');
            } else {
                $used = $itemUsageLogs->sum('quantity_deducted');
            }

            // Calculate remaining stock
            $remainingStock = max(0, $beginningStock - $used);

            // Get staff who used this item (for display)
            $staffUsed = $itemUsageLogs->map(function($log) {
                return $log->appointment ? $log->appointment->staff : null;
            })->filter()->unique('id')->values();

            // Get first and last usage dates (when stock was deducted)
            $firstUsageDate = $itemUsageLogs->isNotEmpty() 
                ? $itemUsageLogs->min('created_at') 
                : null;
            $lastUsageDate = $itemUsageLogs->isNotEmpty() 
                ? $itemUsageLogs->max('created_at') 
                : null;

            // Get first and last stock addition dates from ActivityLog
            $firstAddDate = null;
            $lastAddDate = null;
            
            // Check ActivityLog for stock additions/updates in the period
            // Look for patterns like "Stock added", "Stock updated", or item creation
            $addActivityLogs = ActivityLog::where('module', 'inventory')
                ->where(function($query) use ($item) {
                    $query->where('description', 'like', '%Stock added%')
                          ->orWhere('description', 'like', '%Stock updated%')
                          ->orWhere('description', 'like', '%' . $item->name . '%')
                          ->orWhere('description', 'like', '%' . $item->sku . '%');
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'asc')
                ->get();
            
            if ($addActivityLogs->isNotEmpty()) {
                $firstAddDate = $addActivityLogs->first()->created_at;
                $lastAddDate = $addActivityLogs->last()->created_at;
            } else {
                // If no activity logs, check if item was created in this period
                if ($item->created_at && $item->created_at->between($startDate, $endDate)) {
                    $firstAddDate = $item->created_at;
                    $lastAddDate = $item->created_at;
                } elseif ($item->created_at && $item->created_at->lt($startDate)) {
                    // Item was created before period, check updated_at if it was updated in period
                    if ($item->updated_at && $item->updated_at->between($startDate, $endDate)) {
                        $firstAddDate = $item->updated_at;
                        $lastAddDate = $item->updated_at;
                    }
                }
            }

            $stockData[] = [
                'item' => $item,
                'beginning_stock' => $beginningStock,
                'used' => $used,
                'remaining_stock' => $remainingStock,
                'usage_logs' => $itemUsageLogs,
                'staff_used' => $staffUsed,
                'unit' => $item->usesMlTracking() ? 'mL' : $item->unit,
                'first_usage_date' => $firstUsageDate,
                'last_usage_date' => $lastUsageDate,
                'first_add_date' => $firstAddDate,
                'last_add_date' => $lastAddDate,
            ];
        }

        // Get filter options
        $categories = InventoryItem::distinct()->pluck('category')->sort()->values();
        $staffMembers = User::whereIn('role', ['nurse', 'aesthetician', 'admin'])
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();
        
        $usageTypes = InventoryUsageLog::distinct()->pluck('usage_type')->sort()->values();

        return view('admin.activity-logs.inventory-activity', compact(
            'stockData',
            'categories',
            'staffMembers',
            'usageTypes',
            'startDate',
            'endDate'
        ));
    }
}

