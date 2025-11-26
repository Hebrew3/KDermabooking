<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class StaffSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'is_available' => 'boolean',
    ];

    /**
     * Get the staff member for this schedule.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Check if staff is available on a specific day.
     */
    public function isAvailableOnDay($dayOfWeek): bool
    {
        return $this->day_of_week === strtolower($dayOfWeek) &&
               $this->is_available &&
               $this->start_time &&
               $this->end_time;
    }

    /**
     * Check if staff is available at a specific time.
     */
    public function isAvailableAtTime($time): bool
    {
        if (!$this->is_available || !$this->start_time || !$this->end_time) {
            return false;
        }

        $checkTime = Carbon::createFromFormat('H:i', $time);

        // Handle both H:i and H:i:s formats
        $startTime = strlen($this->start_time) > 5 ?
            Carbon::createFromFormat('H:i:s', $this->start_time) :
            Carbon::createFromFormat('H:i', $this->start_time);

        $endTime = strlen($this->end_time) > 5 ?
            Carbon::createFromFormat('H:i:s', $this->end_time) :
            Carbon::createFromFormat('H:i', $this->end_time);

        return $checkTime->between($startTime, $endTime);
    }

    /**
     * Get formatted working hours.
     */
    public function getFormattedHoursAttribute(): string
    {
        if (!$this->is_available || !$this->start_time || !$this->end_time) {
            return 'Day Off';
        }

        // Handle both H:i and H:i:s formats
        $startTime = strlen($this->start_time) > 5 ?
            Carbon::createFromFormat('H:i:s', $this->start_time) :
            Carbon::createFromFormat('H:i', $this->start_time);

        $endTime = strlen($this->end_time) > 5 ?
            Carbon::createFromFormat('H:i:s', $this->end_time) :
            Carbon::createFromFormat('H:i', $this->end_time);

        return $startTime->format('g:i A') . ' - ' . $endTime->format('g:i A');
    }

    /**
     * Scope to get available schedules.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                    ->whereNotNull('start_time')
                    ->whereNotNull('end_time');
    }

    /**
     * Scope to get schedules for a specific day.
     */
    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->whereRaw('LOWER(day_of_week) = ?', [strtolower($dayOfWeek)]);
    }
}
