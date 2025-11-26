<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class AppointmentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'client_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'requires_action',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'requires_action' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the appointment for this notification.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the client for this notification.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Check if notification is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if notification is urgent.
     */
    public function isUrgent(): bool
    {
        return $this->type === 'staff_unavailable' && $this->requires_action;
    }

    /**
     * Get formatted type.
     */
    public function getFormattedTypeAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Get notification icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'staff_unavailable' => 'exclamation-triangle',
            'appointment_reminder' => 'clock',
            'appointment_confirmed' => 'check-circle',
            'appointment_cancelled' => 'x-circle',
            'staff_reassigned' => 'user-switch',
            default => 'bell',
        };
    }

    /**
     * Get notification color based on type.
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'staff_unavailable' => 'red',
            'appointment_reminder' => 'blue',
            'appointment_confirmed' => 'green',
            'appointment_cancelled' => 'red',
            'staff_reassigned' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get notifications requiring action.
     */
    public function scopeRequiringAction($query)
    {
        return $query->where('requires_action', true)->where('is_read', false);
    }

    /**
     * Scope to get notifications by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get non-expired notifications.
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Get notification types.
     */
    public static function getTypes(): array
    {
        return [
            'staff_unavailable' => 'Staff Unavailable',
            'appointment_reminder' => 'Appointment Reminder',
            'appointment_confirmed' => 'Appointment Confirmed',
            'appointment_cancelled' => 'Appointment Cancelled',
            'staff_reassigned' => 'Staff Reassigned',
        ];
    }
}
