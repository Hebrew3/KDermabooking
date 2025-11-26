<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channel authorization
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    \Log::info('=== CHANNEL AUTHORIZATION CALLED ===', [
        'conversation_id' => $conversationId,
        'user_id' => $user->id ?? 'no user',
        'user_role' => $user->role ?? 'unknown',
        'conversation_id_type' => gettype($conversationId)
    ]);
    
    try {
        // Convert conversationId to integer if it's a string
        $conversationId = (int) $conversationId;
        
        \Log::info('Looking for conversation', ['conversation_id' => $conversationId]);
        
        $conversation = \App\Models\ChatConversation::find($conversationId);
        
        if (!$conversation) {
            \Log::warning('Chat conversation not found for broadcasting auth', [
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
                'user_role' => $user->role ?? 'unknown',
                'all_conversations' => \App\Models\ChatConversation::pluck('id')->toArray()
            ]);
            return false;
        }

        // Convert IDs to integers for comparison
        $userId = (int) $user->id;
        $clientId = $conversation->client_id ? (int) $conversation->client_id : null;
        $staffId = $conversation->staff_id ? (int) $conversation->staff_id : null;
        
        // Allow access if user is either the client or staff in the conversation, or is admin
        $clientMatch = $clientId !== null && $clientId === $userId;
        $staffMatch = $staffId !== null && $staffId === $userId;
        $isAdmin = method_exists($user, 'isAdmin') ? $user->isAdmin() : ($user->role === 'admin');
        
        $hasAccess = $clientMatch || $staffMatch || $isAdmin;
        
        \Log::info('Broadcasting auth check result', [
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'user_role' => $user->role ?? 'unknown',
            'client_id' => $clientId,
            'staff_id' => $staffId,
            'is_admin' => $isAdmin,
            'has_access' => $hasAccess,
            'client_match' => $clientMatch,
            'staff_match' => $staffMatch
        ]);
        
        if (!$hasAccess) {
            \Log::warning('Broadcasting auth DENIED', [
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'user_role' => $user->role ?? 'unknown',
                'client_id' => $clientId,
                'staff_id' => $staffId,
                'client_match' => $clientMatch,
                'staff_match' => $staffMatch,
                'is_admin' => $isAdmin
            ]);
        } else {
            \Log::info('Broadcasting auth GRANTED', [
                'conversation_id' => $conversationId,
                'user_id' => $userId
            ]);
        }
        
        return $hasAccess;
    } catch (\Exception $e) {
        \Log::error('EXCEPTION in chat channel authorization: ' . $e->getMessage(), [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
            'conversation_id' => $conversationId ?? 'unknown',
            'user_id' => $user->id ?? 'unknown',
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        return false;
    }
});

