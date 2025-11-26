<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\MessageSent;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'type',
        'attachment_url',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            // Update conversation's last message time
            $message->conversation->update([
                'last_message_at' => now(),
            ]);

            // Note: Event is now dispatched from ChatController::sendMessage()
            // to ensure proper timing and avoid duplicate broadcasts

            // Send Google Chat notification if enabled
            try {
                $conversation = $message->conversation;
                $recipient = $conversation->client_id === $message->sender_id 
                    ? $conversation->staff 
                    : $conversation->client;
                
                if ($recipient) {
                    app(\App\Services\GoogleChatService::class)->notifyNewMessage(
                        $message,
                        $conversation,
                        $recipient
                    );
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to send Google Chat notification: ' . $e->getMessage());
            }
        });
    }

    /**
     * Get the conversation that owns this message.
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Get the user who sent this message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Mark message as read.
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Check if message is from a specific user.
     */
    public function isFromUser($userId)
    {
        return $this->sender_id == $userId;
    }

    /**
     * Get formatted time.
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('g:i A');
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute()
    {
        if ($this->created_at->isToday()) {
            return 'Today';
        } elseif ($this->created_at->isYesterday()) {
            return 'Yesterday';
        } else {
            return $this->created_at->format('M d, Y');
        }
    }

    /**
     * Scope to get unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get messages for a user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('conversation', function($q) use ($userId) {
            $q->where('client_id', $userId)
              ->orWhere('staff_id', $userId);
        });
    }
}
