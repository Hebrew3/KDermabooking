<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'client_id',
        'staff_id',
        'conversation_key',
        'is_active',
        'last_message_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conversation) {
            if (empty($conversation->conversation_key)) {
                $conversation->conversation_key = 'chat_' . Str::random(32);
            }
        });
    }

    /**
     * Get the appointment for this conversation.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the client in this conversation.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the staff member in this conversation.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get unread messages count for a user.
     */
    public function getUnreadCountForUser($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark all messages as read for a user.
     */
    public function markAsReadForUser($userId)
    {
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get or create conversation for an appointment.
     */
    public static function getOrCreateForAppointment($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        
        // Only create conversation if appointment is confirmed
        if ($appointment->status !== 'confirmed' && $appointment->status !== 'in_progress') {
            throw new \Exception('Chat is only available for confirmed appointments.');
        }

        $conversation = static::where('appointment_id', $appointmentId)
            ->where('is_active', true)
            ->first();

        if (!$conversation) {
            $conversation = static::create([
                'appointment_id' => $appointmentId,
                'client_id' => $appointment->client_id,
                'staff_id' => $appointment->staff_id,
                'is_active' => true,
            ]);
        }

        return $conversation;
    }

    /**
     * Scope to get active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get conversations for a user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('client_id', $userId)
              ->orWhere('staff_id', $userId);
        });
    }
}
