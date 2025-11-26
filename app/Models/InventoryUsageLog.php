<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryUsageLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appointment_id',
        'inventory_item_id',
        'service_id',
        'item_name',
        'item_sku',
        'usage_type',
        'quantity_deducted',
        'volume_ml_deducted',
        'stock_before',
        'stock_after',
        'unit',
        'is_ml_tracking',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity_deducted' => 'decimal:3',
        'volume_ml_deducted' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
        'is_ml_tracking' => 'boolean',
    ];

    /**
     * Get the appointment that used this inventory item.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the inventory item that was used.
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the service that used this inventory item.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
