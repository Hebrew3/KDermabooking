<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class StaffUnavailability extends Model
{
    use HasFactory;

    protected $table = 'staff_unavailability';

    protected $fillable = [
        'staff_id',
        'unavailable_date',
        'start_time',
        'end_time',
        'reason',
        'notes',
        'is_emergency',
        'reported_at',
        'reported_by',
        'approval_status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'unavailable_date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
        'is_emergency' => 'boolean',
        'reported_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the staff member who is unavailable.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the user who reported this unavailability.
     */
    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get the admin who approved this leave.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Safely parse a time string to Carbon instance.
     * Handles both H:i and H:i:s formats, and trims whitespace.
     */
    private function parseTimeString($timeString): ?Carbon
    {
        if (empty($timeString)) {
            return null;
        }

        // Trim whitespace
        $timeString = trim($timeString);
        
        // Extract time pattern (HH:MM or HH:MM:SS) and ignore trailing data
        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?/', $timeString, $matches)) {
            // Reconstruct clean time string in H:i format
            $hours = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $minutes = $matches[2];
            $cleanTime = $hours . ':' . $minutes;
            
            // Validate the time values
            $hour = (int)$hours;
            $minute = (int)$minutes;
            
            if ($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59) {
                try {
                    // Use createFromFormat with the clean time string
                    return Carbon::createFromFormat('H:i', $cleanTime);
                } catch (\Exception $e) {
                    \Log::warning("Failed to parse cleaned time: {$cleanTime}", [
                        'error' => $e->getMessage(),
                        'original' => $timeString
                    ]);
                }
            }
        }

        // Fallback: try Carbon's flexible parsing
        try {
            $parsed = Carbon::parse($timeString);
            // Extract only hours and minutes to avoid trailing data issues
            return Carbon::createFromTime($parsed->hour, $parsed->minute, 0);
        } catch (\Exception $e) {
            \Log::warning("Failed to parse time string: {$timeString}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if unavailability affects a specific time.
     */
    public function affectsTime($time): bool
    {
        // If no specific time range, affects entire day
        if (!$this->start_time || !$this->end_time) {
            return true;
        }

        $checkTime = $this->parseTimeString($time);
        $startTime = $this->parseTimeString($this->start_time);
        $endTime = $this->parseTimeString($this->end_time);

        if (!$checkTime || !$startTime || !$endTime) {
            return false;
        }

        return $checkTime->between($startTime, $endTime);
    }

    /**
     * Get formatted time range.
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        if (!$this->start_time || !$this->end_time) {
            return 'All Day';
        }

        $startTime = $this->parseTimeString($this->start_time);
        $endTime = $this->parseTimeString($this->end_time);

        if (!$startTime || !$endTime) {
            // Fallback: return raw values if parsing fails
            return $this->start_time . ' - ' . $this->end_time;
        }

        return $startTime->format('g:i A') . ' - ' . $endTime->format('g:i A');
    }

    /**
     * Get formatted reason.
     */
    public function getFormattedReasonAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->reason));
    }

    /**
     * Scope to get emergency unavailabilities.
     */
    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true)
                     ->where('approval_status', 'approved');
    }

    /**
     * Scope to get unavailabilities for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('unavailable_date', $date)
                     ->where('approval_status', 'approved');
    }

    /**
     * Scope to get current unavailabilities.
     */
    public function scopeCurrent($query)
    {
        return $query->where('unavailable_date', '>=', now()->toDateString())
                     ->where('approval_status', 'approved');
    }

    /**
     * Get affected appointments for this unavailability.
     */
    public function getAffectedAppointments()
    {
        $query = Appointment::where('staff_id', $this->staff_id)
            ->where('appointment_date', $this->unavailable_date)
            ->whereIn('status', ['pending', 'confirmed']);

        // If specific time range, filter by time
        if ($this->start_time && $this->end_time) {
            $query->where(function($q) {
                $q->whereBetween('appointment_time', [$this->start_time, $this->end_time]);
            });
        }

        return $query->with(['client', 'service'])->get();
    }

    /**
     * Get reason types.
     */
    public static function getReasonTypes(): array
    {
        return [
            'emergency' => 'Emergency',
            'sick_leave' => 'Sick Leave',
            'personal_leave' => 'Personal Leave',
            'vacation' => 'Vacation',
            'training' => 'Training',
            'other' => 'Other',
        ];
    }
}
