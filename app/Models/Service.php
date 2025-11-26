<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'price',
        'duration',
        'category',
        'required_specialization',
        'requires_specialization',
        'image',
        'gallery_images',
        'is_active',
        'is_featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'tags',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'requires_specialization' => 'boolean',
        'sort_order' => 'integer',
        'gallery_images' => 'array',
        'tags' => 'array',
    ];

    /**
     * Get the service's image URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // Use asset() helper for more reliable URL generation
        return asset('storage/' . $this->image);
    }

    /**
     * Get the service's gallery images URLs.
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        if (!$this->gallery_images) {
            return [];
        }

        return collect($this->gallery_images)->map(function ($image) {
            // Use asset() helper for more reliable URL generation
            return asset('storage/' . $image);
        })->toArray();
    }

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        $priceValue = $this->price ?? 0;
        // Ensure we have a float value for number_format
        $priceFloat = is_numeric($priceValue) ? (float)$priceValue : 0.0;
        return '₱' . number_format($priceFloat, 2);
    }

    /**
     * Get the formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration < 60) {
            return $this->duration . ' minutes';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($minutes === 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        }

        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ' . $minutes . ' minutes';
    }

    /**
     * Scope a query to only include active services.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured services.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to search services.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    /**
     * Get appointments for this service.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get staff members assigned to this service.
     */
    public function assignedStaff()
    {
        return $this->belongsToMany(User::class, 'staff_services', 'service_id', 'staff_id')
                    ->whereIn('role', ['nurse', 'aesthetician'])
                    ->withPivot(['is_primary', 'custom_price', 'proficiency_level', 'notes'])
                    ->withTimestamps();
    }

    /**
     * Get staff members who can perform this service.
     */
    public function getQualifiedStaff()
    {
        $staffQuery = User::whereIn('role', ['nurse', 'aesthetician'])->where('is_active', true);

        // If this service requires a specific specialization
        if ($this->requires_specialization && $this->required_specialization) {
            $staffQuery->whereHas('staffSpecializations', function($query) {
                $query->where('specialization', $this->required_specialization);
            });
        }

        return $staffQuery->get();
    }

    /**
     * Get treatment products used in this service.
     */
    public function treatmentProducts()
    {
        return $this->belongsToMany(InventoryItem::class, 'service_treatment_products', 'service_id', 'inventory_item_id')
                    ->withPivot('quantity', 'volume_used_per_service')
                    ->withTimestamps();
    }

    /**
     * Check if a staff member can perform this service.
     */
    public function canBePerformedBy(User $staff): bool
    {
        if (!$staff->isStaffMember()) {
            return false;
        }

        // If service doesn't require specialization, any staff can perform it
        if (!$this->requires_specialization) {
            return true;
        }

        // Check if staff has the required specialization
        return $staff->hasSpecialization($this->required_specialization);
    }

    /**
     * Scope to filter services by staff specialization.
     */
    public function scopeForStaff($query, User $staff)
    {
        if (!$staff->isStaffMember()) {
            return $query->whereRaw('1 = 0'); // Return no results
        }

        $staffSpecializations = $staff->getSpecializations();

        return $query->where(function($q) use ($staffSpecializations) {
            // Services that don't require specialization
            $q->where('requires_specialization', false)
              // OR services that require a specialization the staff has
              ->orWhere(function($subQ) use ($staffSpecializations) {
                  $subQ->where('requires_specialization', true)
                       ->whereIn('required_specialization', $staffSpecializations);
              });
        });
    }
}
