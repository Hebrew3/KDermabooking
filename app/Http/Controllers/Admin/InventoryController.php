<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryStockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsActivity;

class InventoryController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the inventory.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by status (combined stock and expiry status)
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
                case 'low_stock':
                    $query->needsReordering();
                    break;
                case 'in_stock':
                    // In stock: has stock and is above minimum stock (not low stock, not out of stock)
                    $query->where('current_stock', '>', 0)
                          ->whereRaw('current_stock > minimum_stock');
                    break;
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by active status
        if ($request->filled('active_status')) {
            $query->where('is_active', $request->active_status === 'active');
        }

        $items = $query->orderBy('name')
                      ->paginate(15);

        // Get statistics
        $stats = [
            'total_items' => InventoryItem::active()->count(),
            'out_of_stock' => InventoryItem::active()->outOfStock()->count(),
            'low_stock' => InventoryItem::active()->needsReordering()->count(),
            'expiring_soon' => InventoryItem::active()->expiringSoon()->count(),
            'total_value' => InventoryItem::active()->sum(DB::raw('current_stock * cost_price')),
        ];

        // Get low stock and out of stock items for alert
        $lowStockItems = InventoryItem::active()
            ->needsReordering()
            ->orderBy('current_stock')
            ->get();
        
        $outOfStockItems = InventoryItem::active()
            ->outOfStock()
            ->orderBy('name')
            ->get();

        // Get categories
        $categories = InventoryItem::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort();

        return view('admin.inventory.index', compact('items', 'stats', 'categories', 'lowStockItems', 'outOfStockItems'));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        // Get existing categories for dropdown
        $categories = InventoryItem::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort();

        return view('admin.inventory.create', compact('categories'));
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:inventory_items,sku',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'unit' => 'required|string|max:50',
            'content_per_unit' => 'required|numeric|min:0',
            'content_unit' => 'required|in:mL,pc',
            'volume_per_container' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'storage_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Generate SKU if not provided
        if (empty($request->sku)) {
            $request->merge(['sku' => $this->generateSKU($request->name, $request->category)]);
        }

        $data = $request->all();
        
        // Calculate volume_per_container from content_per_unit if not explicitly set
        if (empty($data['volume_per_container']) && !empty($data['content_per_unit']) && !empty($data['content_unit'])) {
            $contentPerUnit = (float) $data['content_per_unit'];
            $contentUnit = $data['content_unit'];
            $data['volume_per_container'] = $this->convertContentToMl($contentPerUnit, $contentUnit);
        }
        
        // Initialize total_volume_ml from volume_per_container and current_stock
        if (!empty($data['volume_per_container']) && $data['volume_per_container'] > 0) {
            $volumePerContainer = (float) $data['volume_per_container'];
            $currentStock = (int) ($data['current_stock'] ?? 0);
            $data['total_volume_ml'] = $currentStock * $volumePerContainer;
            $data['remaining_volume_per_container'] = min($volumePerContainer, $data['total_volume_ml']);
        }

        $item = InventoryItem::create($data);

        // Log activity
        $this->logActivity('created', 'inventory', "Created inventory item: {$item->name} (SKU: {$item->sku})");

        return redirect()->route('admin.inventory.index')
                        ->with('success', 'Inventory item created successfully.');
    }

    /**
     * Display the specified inventory item.
     */
    public function show(InventoryItem $inventoryItem)
    {
        return view('admin.inventory.show', compact('inventoryItem'));
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(InventoryItem $inventoryItem)
    {
        // Get existing categories for dropdown
        $categories = InventoryItem::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort();

        return view('admin.inventory.edit', compact('inventoryItem', 'categories'));
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:inventory_items,sku,' . $inventoryItem->id,
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'unit' => 'required|string|max:50',
            'content_per_unit' => 'required|numeric|min:0',
            'content_unit' => 'required|in:mL,pc',
            'volume_per_container' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'storage_location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Calculate volume_per_container from content_per_unit if not explicitly set
        if (empty($data['volume_per_container']) && !empty($data['content_per_unit']) && !empty($data['content_unit'])) {
            $contentPerUnit = (float) $data['content_per_unit'];
            $contentUnit = $data['content_unit'];
            $data['volume_per_container'] = $this->convertContentToMl($contentPerUnit, $contentUnit);
        }
        
        // For mL-tracked items: Auto-calculate current_stock from total_volume_ml
        // For non-mL items: Use the provided current_stock
        if (!empty($data['volume_per_container']) && $data['volume_per_container'] > 0) {
            $volumePerContainer = (float) $data['volume_per_container'];
            
            // If total_volume_ml exists (from virtual deduction), calculate current_stock from it
            // Otherwise, calculate total_volume_ml from current_stock
            if (isset($inventoryItem->total_volume_ml) && $inventoryItem->total_volume_ml > 0) {
                // Use existing total_volume_ml (preserve virtual deduction state)
                $data['total_volume_ml'] = (float) $inventoryItem->total_volume_ml;
                // Auto-calculate current_stock from total_volume_ml
                $data['current_stock'] = (int) ceil($data['total_volume_ml'] / $volumePerContainer);
            } else {
                // New item or no virtual deduction yet: calculate from current_stock
                $currentStock = (int) ($data['current_stock'] ?? $inventoryItem->current_stock ?? 0);
                $data['total_volume_ml'] = $currentStock * $volumePerContainer;
                $data['current_stock'] = $currentStock;
            }
            
            // Initialize remaining_volume_per_container if not set or if it exceeds volume_per_container
            if (!isset($data['remaining_volume_per_container']) || $data['remaining_volume_per_container'] === null) {
                $data['remaining_volume_per_container'] = min($volumePerContainer, $data['total_volume_ml']);
            } elseif ((float) $data['remaining_volume_per_container'] > $volumePerContainer) {
                // Ensure remaining doesn't exceed volume_per_container
                $data['remaining_volume_per_container'] = min($volumePerContainer, $data['total_volume_ml']);
            }
        } else {
            // For non-mL tracked items, use the provided current_stock as-is
            $data['current_stock'] = (int) ($data['current_stock'] ?? $inventoryItem->current_stock ?? 0);
        }

        $inventoryItem->update($data);

        // Log activity
        $this->logActivity('updated', 'inventory', "Updated inventory item: {$inventoryItem->name} (SKU: {$inventoryItem->sku})");

        return redirect()->route('admin.inventory.index')
                        ->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        $itemName = $inventoryItem->name;
        $itemSku = $inventoryItem->sku;
        
        $inventoryItem->delete();

        // Log activity
        $this->logActivity('deleted', 'inventory', "Deleted inventory item: {$itemName} (SKU: {$itemSku})");

        return redirect()->route('admin.inventory.index')
                        ->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * Update stock quantity.
     */
    public function updateStock(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:add,subtract,set',
            'notes' => 'nullable|string|max:500',
        ]);

        // Reload to get latest values
        $inventoryItem->refresh();
        
        $oldStock = $inventoryItem->current_stock;
        $oldTotalVolume = $inventoryItem->usesMlTracking() 
            ? (float) ($inventoryItem->total_volume_ml ?? 0) 
            : null;
        $updateData = [];

        // Handle mL-tracked items differently
        if ($inventoryItem->usesMlTracking()) {
            $volumePerContainer = (float) $inventoryItem->volume_per_container;
            
            // Calculate volume change based on quantity change
            $quantityChange = 0;
            switch ($request->type) {
                case 'add':
                    $quantityChange = $request->quantity;
                    break;
                case 'subtract':
                    $quantityChange = -$request->quantity;
                    break;
                case 'set':
                    $quantityChange = $request->quantity - $oldStock;
                    break;
            }
            
            // Calculate new total volume
            $volumeChange = $quantityChange * $volumePerContainer;
            $newTotalVolume = max(0, $oldTotalVolume + $volumeChange);
            
            // Calculate new stock from total volume
            $newStock = (int) ceil($newTotalVolume / $volumePerContainer);
            
            // Update mL-related fields
            $updateData['total_volume_ml'] = $newTotalVolume;
            $updateData['current_stock'] = $newStock;
            
            // Update remaining volume per container
            if ($newTotalVolume > 0) {
                $fullContainers = (int) floor($newTotalVolume / $volumePerContainer);
                $remainingVolume = $newTotalVolume - ($fullContainers * $volumePerContainer);
                $updateData['remaining_volume_per_container'] = min($volumePerContainer, $remainingVolume);
            } else {
                $updateData['remaining_volume_per_container'] = 0;
            }
            
            // For logging, use the quantity change (in units)
            $logQuantityChange = $quantityChange;
            $logStockBefore = $oldTotalVolume;
            $logStockAfter = $newTotalVolume;
        } else {
            // For non-mL tracked items, update current_stock directly
            switch ($request->type) {
                case 'add':
                    $newStock = $oldStock + $request->quantity;
                    $logQuantityChange = $request->quantity;
                    break;
                case 'subtract':
                    $newStock = max(0, $oldStock - $request->quantity);
                    $logQuantityChange = -$request->quantity;
                    break;
                case 'set':
                    $newStock = max(0, $request->quantity);
                    $logQuantityChange = $newStock - $oldStock;
                    break;
            }
            
            $updateData['current_stock'] = $newStock;
            $logStockBefore = (float) $oldStock;
            $logStockAfter = (float) $newStock;
        }

        // Update the inventory item
        $inventoryItem->update($updateData);
        
        // Reload to get updated values
        $inventoryItem->refresh();
        
        // Determine activity type
        $activityType = 'adjusted';
        if ($request->type === 'add') {
            $activityType = 'restock';
        } elseif ($request->type === 'subtract') {
            $activityType = 'used';
        }

        // Log stock change
        InventoryStockLog::create([
            'inventory_item_id' => $inventoryItem->id,
            'activity_type' => $activityType,
            'stock_before' => $logStockBefore,
            'quantity_change' => $logQuantityChange,
            'stock_after' => $logStockAfter,
            'unit' => $inventoryItem->unit,
            'notes' => $request->notes ?? "Stock {$request->type} by {$request->quantity}",
            'user_id' => Auth::id(),
        ]);

        // Log activity
        $action = $request->type === 'add' ? 'added' : ($request->type === 'subtract' ? 'subtracted' : 'set');
        $this->logActivity('updated', 'inventory', "Stock {$action} for {$inventoryItem->name} (SKU: {$inventoryItem->sku}): {$oldStock} → {$newStock} {$inventoryItem->unit}");

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully.',
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
        ]);
    }

    /**
     * Get low stock items.
     */
    public function lowStock()
    {
        $items = InventoryItem::active()
            ->needsReordering()
            ->orderBy('current_stock')
            ->paginate(15);

        return view('admin.inventory.low-stock', compact('items'));
    }

    /**
     * Get expiring items.
     */
    public function expiring()
    {
        $items = InventoryItem::active()
            ->where(function($query) {
                $query->expiringSoon()->orWhere(function($q) {
                    $q->expired();
                });
            })
            ->orderBy('expiry_date')
            ->paginate(15);

        return view('admin.inventory.expiring', compact('items'));
    }

    /**
     * Toggle inventory item status.
     */
    public function toggleStatus(InventoryItem $inventoryItem)
    {
        $inventoryItem->is_active = !$inventoryItem->is_active;
        $inventoryItem->save();

        $status = $inventoryItem->is_active ? 'activated' : 'deactivated';
        
        // Log activity
        $this->logActivity('updated', 'inventory', "{$status} inventory item: {$inventoryItem->name} (SKU: {$inventoryItem->sku})");
        
        return redirect()->route('admin.inventory.index')
                        ->with('success', "Inventory item {$status} successfully.");
    }

    /**
     * Generate SKU for inventory item.
     */
    private function generateSKU($name, $category): string
    {
        $prefix = strtoupper(substr($category, 0, 3));
        $namePart = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $timestamp = now()->format('ymd');
        
        $baseSKU = $prefix . $namePart . $timestamp;
        
        // Ensure uniqueness
        $counter = 1;
        $sku = $baseSKU;
        
        while (InventoryItem::where('sku', $sku)->exists()) {
            $sku = $baseSKU . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $sku;
    }

    /**
     * Convert content_per_unit to mL based on the unit.
     * 
     * @param float $value The value to convert
     * @param string $unit The unit of the value (mL, L, g, kg, oz, fl oz, etc.)
     * @return float The value converted to mL
     */
    private function convertContentToMl(float $value, string $unit): float
    {
        $unit = strtolower(trim($unit));
        
        switch ($unit) {
            case 'ml':
            case 'milliliter':
            case 'millilitre':
                return $value;
            
            case 'l':
            case 'liter':
            case 'litre':
                return $value * 1000; // 1 L = 1000 mL
            
            case 'fl oz':
            case 'fluid ounce':
            case 'fluid oz':
                return $value * 29.5735; // 1 fl oz ≈ 29.5735 mL
            
            case 'oz':
            case 'ounce':
                // For weight (oz), we can't directly convert to mL without density
                // Assume it's fluid ounce if not specified
                return $value * 29.5735;
            
            case 'g':
            case 'gram':
            case 'grams':
                // For weight, we can't directly convert to mL without density
                // For water: 1g = 1mL, but for other substances it varies
                // We'll assume 1g = 1mL as a reasonable default for most liquids
                return $value;
            
            case 'kg':
            case 'kilogram':
            case 'kilograms':
                // For weight, assume 1kg = 1000mL (for water-like density)
                return $value * 1000;
            
            case 'pc':
            case 'pcs':
            case 'piece':
            case 'pieces':
                // For pieces, we can't convert to mL
                // Return 0 to indicate conversion not possible
                return 0;
            
            default:
                // Unknown unit, assume it's already in mL
                \Log::warning("Unknown unit '{$unit}' for content conversion in InventoryController. Assuming mL.");
                return $value;
        }
    }
}
