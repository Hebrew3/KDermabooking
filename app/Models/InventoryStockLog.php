<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStockLog extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'activity_type',
        'stock_before',
        'quantity_change',
        'stock_after',
        'unit',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'stock_before' => 'decimal:2',
        'quantity_change' => 'decimal:2',
        'stock_after' => 'decimal:2',
    ];

    /**
     * Get the inventory item.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the user who made the change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
