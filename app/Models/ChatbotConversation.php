<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'session_id',
        'status',
        'client_preferences',
        'conversation_context',
        'last_activity',
    ];

    protected $casts = [
        'client_preferences' => 'array',
        'conversation_context' => 'array',
        'last_activity' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conversation) {
            if (empty($conversation->session_id)) {
                $conversation->session_id = 'chat_' . Str::random(32);
            }
        });
    }

    /**
     * Get the client that owns the conversation.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the messages for the conversation.
     */
    public function messages()
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id');
    }

    /**
     * Get recent messages.
     */
    public function recentMessages($limit = 10)
    {
        return $this->messages()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Update last activity.
     */
    public function updateActivity()
    {
        $this->update(['last_activity' => now()]);
    }

    /**
     * Check if conversation is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * End the conversation.
     */
    public function endConversation()
    {
        $this->update(['status' => 'ended']);
    }

    /**
     * Scope to get active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get conversations for a specific client.
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Get or create active conversation for client.
     */
    public static function getOrCreateForClient($clientId)
    {
        $conversation = static::active()
            ->forClient($clientId)
            ->orderBy('last_activity', 'desc')
            ->first();

        if (!$conversation) {
            $conversation = static::create([
                'client_id' => $clientId,
                'status' => 'active',
                'last_activity' => now(),
            ]);
        }

        return $conversation;
    }
}
