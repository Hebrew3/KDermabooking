<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatbotMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'message',
        'message_data',
        'message_type',
        'is_read',
        'metadata',
    ];

    protected $casts = [
        'message_data' => 'array',
        'metadata' => 'array',
        'is_read' => 'boolean',
    ];

    /**
     * Get the conversation that owns the message.
     */
    public function conversation()
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }

    /**
     * Check if message is from client.
     */
    public function isFromClient(): bool
    {
        return $this->sender_type === 'client';
    }

    /**
     * Check if message is from bot.
     */
    public function isFromBot(): bool
    {
        return $this->sender_type === 'bot';
    }

    /**
     * Mark message as read.
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Scope to get messages from client.
     */
    public function scopeFromClient($query)
    {
        return $query->where('sender_type', 'client');
    }

    /**
     * Scope to get messages from bot.
     */
    public function scopeFromBot($query)
    {
        return $query->where('sender_type', 'bot');
    }

    /**
     * Scope to get unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get messages by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    /**
     * Get formatted timestamp.
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('g:i A');
    }

    /**
     * Create a client message.
     */
    public static function createClientMessage($conversationId, $message)
    {
        return static::create([
            'conversation_id' => $conversationId,
            'sender_type' => 'client',
            'message' => $message,
            'message_type' => 'text',
        ]);
    }

    /**
     * Create a bot message.
     */
    public static function createBotMessage($conversationId, $message, $type = 'text', $data = null)
    {
        return static::create([
            'conversation_id' => $conversationId,
            'sender_type' => 'bot',
            'message' => $message,
            'message_type' => $type,
            'message_data' => $data,
        ]);
    }
}
