<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class InventoryItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'brand',
        'cost_price',
        'selling_price',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'unit',
        'content_per_unit',
        'content_unit',
        'volume_per_container',
        'total_volume_ml',
        'remaining_volume_per_container',
        'supplier',
        'expiry_date',
        'storage_location',
        'is_active',
        'images',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'volume_per_container' => 'decimal:2',
        'total_volume_ml' => 'decimal:2',
        'remaining_volume_per_container' => 'decimal:2',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
        'images' => 'array',
    ];

    /**
     * Get the formatted cost price.
     */
    public function getFormattedCostPriceAttribute(): string
    {
        $price = $this->cost_price === null ? 0.0 : (float) $this->cost_price;
        return '₱' . number_format($price, 2);
    }

    /**
     * Get the formatted selling price.
     */
    public function getFormattedSellingPriceAttribute(): string
    {
        if ($this->selling_price === null) {
            return 'N/A';
        }
        $price = (float) $this->selling_price;
        return '₱' . number_format($price, 2);
    }

    /**
     * Get the formatted content per unit.
     */
    public function getFormattedContentPerUnitAttribute(): ?string
    {
        if (!$this->content_per_unit || !$this->content_unit) {
            return null;
        }
        
        // Format number, removing unnecessary decimals (e.g., 20.00 -> 20)
        $formattedNumber = rtrim(rtrim(number_format($this->content_per_unit, 2, '.', ''), '0'), '.');
        
        return $formattedNumber . ' ' . $this->content_unit;
    }

    /**
     * Get the stock status.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        } elseif ($this->current_stock <= $this->minimum_stock) {
            return 'low_stock';
        } elseif ($this->maximum_stock && $this->current_stock >= $this->maximum_stock) {
            return 'overstock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get the stock status color.
     */
    public function getStockStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'bg-red-100 text-red-800',
            'low_stock' => 'bg-yellow-100 text-yellow-800',
            'overstock' => 'bg-purple-100 text-purple-800',
            'in_stock' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the expiry status.
     */
    public function getExpiryStatusAttribute(): ?string
    {
        if (!$this->expiry_date) {
            return null;
        }

        $daysToExpiry = now()->diffInDays($this->expiry_date, false);

        if ($daysToExpiry < 0) {
            return 'expired';
        } elseif ($daysToExpiry <= 30) {
            return 'expiring_soon';
        } else {
            return 'good';
        }
    }

    /**
     * Get the expiry status color.
     */
    public function getExpiryStatusColorAttribute(): string
    {
        return match($this->expiry_status) {
            'expired' => 'bg-red-100 text-red-800',
            'expiring_soon' => 'bg-yellow-100 text-yellow-800',
            'good' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if item needs reordering.
     */
    public function needsReordering(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Check if item is expired or expiring soon.
     */
    public function isExpiringSoon(): bool
    {
        return in_array($this->expiry_status, ['expired', 'expiring_soon']);
    }

    /**
     * Scope a query to only include active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include items that need reordering.
     */
    public function scopeNeedsReordering($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }

    /**
     * Scope a query to only include out of stock items.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    /**
     * Scope a query to only include items expiring soon.
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('expiry_date', '<=', now()->addDays(30))
                    ->where('expiry_date', '>=', now());
    }

    /**
     * Scope a query to only include expired items.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to search items.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('supplier', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Get sale items for this inventory item.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get services that use this treatment product.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_treatment_products', 'inventory_item_id', 'service_id')
                    ->withPivot('quantity', 'volume_used_per_service')
                    ->withTimestamps();
    }

    /**
     * Get the inventory item's image URLs.
     */
    public function getImageUrlsAttribute(): array
    {
        if (!$this->images || !is_array($this->images)) {
            return [];
        }

        return collect($this->images)->map(function ($image) {
            // Use asset() helper for more reliable URL generation
            return asset('storage/' . $image);
        })->toArray();
    }

    /**
     * Get the first image URL (for single image display).
     */
    public function getFirstImageUrlAttribute(): ?string
    {
        $imageUrls = $this->image_urls;
        return !empty($imageUrls) ? $imageUrls[0] : null;
    }

    /**
     * Check if this item uses mL-based tracking.
     */
    public function usesMlTracking(): bool
    {
        return $this->volume_per_container !== null && $this->volume_per_container > 0;
    }

    /**
     * Check if this item uses portion-based deduction.
     * Returns true if:
     * 1. Item uses mL tracking, OR
     * 2. Item is used in services with volume_used_per_service, OR
     * 3. Item is used in services with decimal quantities
     */
    public function usesPortionBasedDeduction(): bool
    {
        // Check if using mL tracking
        if ($this->usesMlTracking()) {
            return true;
        }

        // Check if used in services with portion-based settings
        return $this->services()
            ->where(function($q) {
                $q->whereNotNull('service_treatment_products.volume_used_per_service')
                  ->where('service_treatment_products.volume_used_per_service', '>', 0)
                  ->orWhere(function($q2) {
                      $q2->whereRaw('service_treatment_products.quantity < 1')
                         ->orWhereRaw('service_treatment_products.quantity != FLOOR(service_treatment_products.quantity)');
                  });
            })
            ->exists();
    }

    /**
     * Get the number of full containers based on total volume.
     */
    public function getFullContainersAttribute(): int
    {
        if (!$this->usesMlTracking() || $this->volume_per_container <= 0) {
            return $this->current_stock; // Fallback to current_stock if not using mL tracking
        }

        return (int) floor($this->total_volume_ml / $this->volume_per_container);
    }

    /**
     * Get the remaining mL after full containers.
     */
    public function getRemainingMlAttribute(): float
    {
        if (!$this->usesMlTracking() || $this->volume_per_container <= 0) {
            return 0;
        }

        return $this->total_volume_ml - ($this->full_containers * $this->volume_per_container);
    }

    /**
     * Get formatted display of containers and mL.
     */
    public function getFormattedStockDisplayAttribute(): string
    {
        if (!$this->usesMlTracking()) {
            return $this->current_stock . ' ' . $this->unit;
        }

        $display = $this->full_containers . ' ' . $this->unit;
        if ($this->remaining_ml > 0) {
            $display .= ' + ' . number_format((float) $this->remaining_ml, 2) . ' mL';
        }
        $totalVolume = $this->total_volume_ml === null ? 0.0 : (float) $this->total_volume_ml;
        $display .= ' (' . number_format($totalVolume, 2) . ' mL total)';

        return $display;
    }

    /**
     * Deduct volume in mL from inventory.
     */
    public function deductVolumeMl(float $volumeMl): bool
    {
        // Ensure volumeMl is always positive
        $volumeMl = abs($volumeMl);
        
        if ($volumeMl <= 0) {
            \Log::warning("Invalid volume to deduct for {$this->name}: {$volumeMl}. Must be greater than 0.");
            return false;
        }
        
        if (!$this->usesMlTracking()) {
            // Fallback to old behavior if not using mL tracking
            $quantity = (int) ceil($volumeMl);
            if ($this->current_stock >= $quantity) {
                $this->decrement('current_stock', $quantity);
                return true;
            }
            return false;
        }

        // Ensure total_volume_ml is initialized
        if ($this->total_volume_ml === null || $this->total_volume_ml < 0) {
            $this->setAttribute('total_volume_ml', '0.00');
        }

        if ($this->total_volume_ml < $volumeMl) {
            \Log::warning("Insufficient volume for {$this->name}: Available={$this->total_volume_ml} mL, Required={$volumeMl} mL");
            return false; // Insufficient volume
        }

        // Store original value for logging
        $originalVolume = $this->total_volume_ml;
        
        // Deduct from total volume
        $currentVolume = $this->total_volume_ml === null ? 0.0 : (float) $this->total_volume_ml;
        $newVolume = max(0.0, $currentVolume - $volumeMl);
        $this->setAttribute('total_volume_ml', (string) $newVolume);
        
        \Log::info("Deducting {$volumeMl} mL from {$this->name}: {$originalVolume} mL -> {$this->total_volume_ml} mL");

        // Update remaining volume per container
        $currentTotalVolume = (float) $this->total_volume_ml;
        $volumePerContainer = $this->volume_per_container === null ? 0.0 : (float) $this->volume_per_container;
        
        if ($this->remaining_volume_per_container !== null && $this->remaining_volume_per_container > 0) {
            // Deduct from current container first
            $currentRemaining = (float) $this->remaining_volume_per_container;
            $newRemaining = max(0.0, $currentRemaining - $volumeMl);
            $this->setAttribute('remaining_volume_per_container', (string) $newRemaining);
            
            // If we used more than what was in the current container, we need to account for that
            if ($this->remaining_volume_per_container <= 0 && $currentTotalVolume > 0) {
                // Current container is empty, calculate remaining from next container
                $newRemaining = (float) min($volumePerContainer, $currentTotalVolume);
                $this->setAttribute('remaining_volume_per_container', (string) $newRemaining);
            }
        } else {
            // Initialize remaining volume if not set or was 0
            if ($currentTotalVolume > 0) {
                $newRemaining = min($volumePerContainer, $currentTotalVolume);
                $this->setAttribute('remaining_volume_per_container', (string) $newRemaining);
            } else {
                $this->setAttribute('remaining_volume_per_container', '0.00');
            }
        }

        // Update current_stock (number of containers) based on total volume
        $this->current_stock = $this->total_volume_ml > 0 ? (int) ceil($this->total_volume_ml / $this->volume_per_container) : 0;

        $this->save();
        return true;
    }

    /**
     * Restore volume in mL to inventory.
     */
    public function restoreVolumeMl(float $volumeMl): void
    {
        // Ensure volumeMl is always positive
        $volumeMl = abs($volumeMl);
        
        if ($volumeMl <= 0) {
            \Log::warning("Invalid volume to restore for {$this->name}: {$volumeMl}. Must be greater than 0.");
            return;
        }
        
        if (!$this->usesMlTracking()) {
            // Fallback to old behavior if not using mL tracking
            $quantity = (int) ceil($volumeMl);
            $this->increment('current_stock', $quantity);
            return;
        }

        // Ensure total_volume_ml is initialized
        if ($this->total_volume_ml === null || $this->total_volume_ml < 0) {
            $this->setAttribute('total_volume_ml', '0.00');
        }

        // Store original value for logging
        $originalVolume = (float) $this->total_volume_ml;
        
        // Add to total volume
        $newVolume = $originalVolume + $volumeMl;
        $this->setAttribute('total_volume_ml', (string) $newVolume);
        
        \Log::info("Restoring {$volumeMl} mL to {$this->name}: {$originalVolume} mL -> {$this->total_volume_ml} mL");

        // Update remaining volume per container
        $currentTotalVolume = (float) $this->total_volume_ml;
        $volumePerContainer = $this->volume_per_container === null ? 0.0 : (float) $this->volume_per_container;
        
        if ($this->remaining_volume_per_container === null) {
            $newRemaining = min($volumePerContainer, $currentTotalVolume);
            $this->setAttribute('remaining_volume_per_container', (string) $newRemaining);
        } else {
            $currentRemaining = (float) $this->remaining_volume_per_container;
            $newRemaining = $currentRemaining + $volumeMl;
            $this->setAttribute('remaining_volume_per_container', (string) $newRemaining);
            
            // If current container is full, adjust
            if ($newRemaining >= $volumePerContainer) {
                $this->remaining_volume_per_container = $this->volume_per_container;
            }
        }

        // Update current_stock (number of containers) based on total volume
        $this->current_stock = (int) ceil($this->total_volume_ml / $this->volume_per_container);

        $this->save();
    }
}
