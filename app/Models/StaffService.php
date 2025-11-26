<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffService extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'service_id',
        'is_primary',
        'custom_price',
        'proficiency_level',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'custom_price' => 'decimal:2',
        'proficiency_level' => 'integer',
    ];

    /**
     * Get the staff member.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the service.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the effective price for this staff-service combination.
     */
    public function getEffectivePriceAttribute()
    {
        return $this->custom_price ?? $this->service->price;
    }

    /**
     * Get proficiency level as text.
     */
    public function getProficiencyTextAttribute()
    {
        return match($this->proficiency_level) {
            1 => 'Beginner',
            2 => 'Novice',
            3 => 'Intermediate',
            4 => 'Advanced',
            5 => 'Expert',
            default => 'Unknown'
        };
    }
}
