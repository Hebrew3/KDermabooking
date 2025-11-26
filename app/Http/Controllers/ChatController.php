<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{
    /**
     * Display the messenger page.
     */
    public function index()
    {
        return view('chat.index');
    }

    /**
     * Get or create conversation for an appointment.
     */
    public function getConversation($appointmentId)
    {
        $appointment = Appointment::with(['client', 'staff'])->findOrFail($appointmentId);
        $user = Auth::user();

        // Check if user has access to this appointment
        if ($user->role === 'client' && $appointment->client_id !== $user->id) {
            abort(403, 'Unauthorized access to this appointment.');
        }

        if (in_array($user->role, ['nurse', 'aesthetician', 'admin']) && $appointment->staff_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'Unauthorized access to this appointment.');
        }

        // Check if appointment is confirmed
        if (!in_array($appointment->status, ['confirmed', 'in_progress'])) {
            return response()->json([
                'error' => 'Chat is only available for confirmed appointments.',
            ], 403);
        }

        try {
            $conversation = ChatConversation::getOrCreateForAppointment($appointmentId);
            $conversation->load(['client', 'staff', 'appointment']);

            $messages = $conversation->messages()->with('sender')->orderBy('created_at', 'asc')->get();
            $unreadCount = $conversation->getUnreadCountForUser($user->id);

            return response()->json([
                'success' => true,
                'conversation' => $conversation,
                'messages' => $messages,
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'type' => 'nullable|in:text,image,file',
        ]);

        $conversation = ChatConversation::findOrFail($conversationId);
        $user = Auth::user();

        // Check if user is part of this conversation
        if ($conversation->client_id !== $user->id && $conversation->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        // Check if conversation is active
        if (!$conversation->is_active) {
            return response()->json([
                'error' => 'This conversation is no longer active.',
            ], 403);
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversationId,
            'sender_id' => $user->id,
            'message' => $request->message,
            'type' => $request->type ?? 'text',
            'attachment_url' => $request->attachment_url ?? null,
        ]);

        $message->load('sender');

        // Explicitly dispatch the event (the model boot will also fire it, but this ensures it's dispatched)
        \Log::info('Dispatching MessageSent event from controller', [
            'message_id' => $message->id,
            'conversation_id' => $conversationId
        ]);
        
        event(new \App\Events\MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead($conversationId)
    {
        $conversation = ChatConversation::findOrFail($conversationId);
        $user = Auth::user();

        // Check if user is part of this conversation
        if ($conversation->client_id !== $user->id && $conversation->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $conversation->markAsReadForUser($user->id);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Get user's conversations.
     */
    public function getConversations()
    {
        $user = Auth::user();
        
        $conversations = ChatConversation::forUser($user->id)
            ->active()
            ->with(['appointment', 'client', 'staff'])
            ->withCount(['messages' => function($query) use ($user) {
                $query->where('sender_id', '!=', $user->id)
                      ->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) {
                $lastMessage = $conversation->messages()->latest()->first();
                $conversation->last_message = $lastMessage ? $lastMessage->message : null;
                $conversation->messages_count = $conversation->messages_count ?? 0;
                return $conversation;
            });

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    /**
     * Get messages for a conversation.
     */
    public function getMessages($conversationId)
    {
        $conversation = ChatConversation::findOrFail($conversationId);
        $user = Auth::user();

        // Check if user is part of this conversation
        if ($conversation->client_id !== $user->id && $conversation->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        $conversation->markAsReadForUser($user->id);

        // Format messages for frontend
        $formattedMessages = $messages->map(function($message) {
            return [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'message' => $message->message,
                'type' => $message->type,
                'created_at' => $message->created_at->toIso8601String(),
                'formatted_time' => $message->formatted_time,
                'sender' => $message->sender ? [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name ?? ($message->sender->first_name . ' ' . $message->sender->last_name),
                    'first_name' => $message->sender->first_name,
                    'last_name' => $message->sender->last_name,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages,
            'count' => $formattedMessages->count(),
        ]);
    }

    /**
     * Get total unread messages count for the current user.
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $unreadCount = ChatMessage::whereHas('conversation', function($query) use ($user) {
            $query->where(function($q) use ($user) {
                $q->where('client_id', $user->id)
                  ->orWhere('staff_id', $user->id);
            })
            ->where('is_active', true);
        })
        ->where('sender_id', '!=', $user->id)
        ->where('is_read', false)
        ->count();

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Handle typing indicator.
     */
    public function handleTyping(Request $request, $conversationId)
    {
        $conversation = ChatConversation::findOrFail($conversationId);
        $user = Auth::user();

        // Check if user is part of this conversation
        if ($conversation->client_id !== $user->id && $conversation->staff_id !== $user->id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $isTyping = $request->input('is_typing', false);
        
        \Log::info('Typing event received', [
            'conversation_id' => $conversationId,
            'user_id' => $user->id,
            'is_typing' => $isTyping
        ]);

        // Broadcast typing event (use toOthers() to exclude the sender)
        try {
            if ($isTyping) {
                broadcast(new \App\Events\UserTyping($conversationId, $user->id))->toOthers();
                \Log::info('UserTyping event broadcasted (toOthers)', [
                    'conversation_id' => $conversationId,
                    'user_id' => $user->id,
                    'other_user_will_receive' => true
                ]);
            } else {
                broadcast(new \App\Events\UserStoppedTyping($conversationId, $user->id))->toOthers();
                \Log::info('UserStoppedTyping event broadcasted (toOthers)', [
                    'conversation_id' => $conversationId,
                    'user_id' => $user->id,
                    'other_user_will_receive' => true
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error broadcasting typing event: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
