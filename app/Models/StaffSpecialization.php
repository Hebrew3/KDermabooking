<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffSpecialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'specialization',
        'proficiency_level',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the staff member for this specialization.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get formatted proficiency level.
     */
    public function getFormattedProficiencyAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->proficiency_level));
    }

    /**
     * Get formatted specialization name.
     */
    public function getFormattedSpecializationAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->specialization));
    }

    /**
     * Scope to get primary specializations.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to filter by specialization.
     */
    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', $specialization);
    }

    /**
     * Scope to filter by proficiency level.
     */
    public function scopeByProficiency($query, $level)
    {
        return $query->where('proficiency_level', $level);
    }

    /**
     * Get available specialization types.
     */
    public static function getSpecializationTypes(): array
    {
        return [
            'facial_treatments' => 'Facial Treatments',
            'body_treatments' => 'Body Treatments',
            'laser_therapy' => 'Laser Therapy',
            'chemical_peels' => 'Chemical Peels',
            'microdermabrasion' => 'Microdermabrasion',
            'anti_aging' => 'Anti-Aging Treatments',
            'acne_treatment' => 'Acne Treatment',
            'skin_analysis' => 'Skin Analysis',
            'medical_procedures' => 'Medical Procedures',
            'consultation' => 'Consultation Services',
        ];
    }

    /**
     * Get proficiency levels.
     */
    public static function getProficiencyLevels(): array
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert',
        ];
    }
}
