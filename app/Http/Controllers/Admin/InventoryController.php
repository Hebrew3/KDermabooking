<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
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
            'content_unit' => 'required|string|max:20',
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
        
        // Initialize total_volume_ml if volume_per_container is set
        if (!empty($data['volume_per_container']) && $data['volume_per_container'] > 0) {
            $data['total_volume_ml'] = ($data['current_stock'] ?? 0) * $data['volume_per_container'];
            $data['remaining_volume_per_container'] = min($data['volume_per_container'], $data['total_volume_ml']);
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
            'content_unit' => 'required|string|max:20',
            'volume_per_container' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
            'storage_location' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Update total_volume_ml if volume_per_container is set and changed
        if (!empty($data['volume_per_container']) && $data['volume_per_container'] > 0) {
            // If total_volume_ml is not being explicitly set, calculate from current_stock
            if (!isset($data['total_volume_ml']) || $data['total_volume_ml'] == 0) {
                $data['total_volume_ml'] = ($data['current_stock'] ?? 0) * $data['volume_per_container'];
            }
            // Initialize remaining_volume_per_container if not set
            if (!isset($data['remaining_volume_per_container']) || $data['remaining_volume_per_container'] === null) {
                $data['remaining_volume_per_container'] = min($data['volume_per_container'], $data['total_volume_ml']);
            }
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

        $oldStock = $inventoryItem->current_stock;

        switch ($request->type) {
            case 'add':
                $newStock = $oldStock + $request->quantity;
                break;
            case 'subtract':
                $newStock = max(0, $oldStock - $request->quantity);
                break;
            case 'set':
                $newStock = max(0, $request->quantity);
                break;
        }

        $inventoryItem->update(['current_stock' => $newStock]);

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
}
