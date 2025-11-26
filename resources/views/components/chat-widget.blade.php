@props(['appointmentId', 'conversationId' => null])

<div id="chatWidget" class="fixed bottom-4 right-4 z-50" x-data="chatWidget({{ $appointmentId }}, {{ $conversationId ? "'{$conversationId}'" : 'null' }})" x-cloak>
    <!-- Chat Toggle Button -->
    <button @click="toggleChat" class="bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isOpen">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="isOpen">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <span x-show="unreadCount > 0" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center" x-text="unreadCount"></span>
    </button>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute bottom-20 right-0 w-96 h-[600px] bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col"
         style="display: none;">
        
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-pink-500 to-rose-500 text-white p-4 rounded-t-2xl flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold" x-text="otherUserName || 'Chat'"></h3>
                    <p class="text-xs text-pink-100" x-text="isTyping ? 'Typing...' : 'Online'"></p>
                </div>
            </div>
            <button @click="toggleChat" class="text-white hover:text-pink-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Messages Container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="messagesContainer" x-ref="messagesContainer">
            <template x-for="message in messages" :key="message.id">
                <div class="flex" :class="message.sender_id === currentUserId ? 'justify-end' : 'justify-start'">
                    <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-2xl" 
                         :class="message.sender_id === currentUserId 
                             ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white' 
                             : 'bg-white text-gray-900 border border-gray-200'">
                        <p class="text-sm" x-text="message.message"></p>
                        <p class="text-xs mt-1" 
                           :class="message.sender_id === currentUserId ? 'text-pink-100' : 'text-gray-500'"
                           x-text="message.formatted_time"></p>
                    </div>
                </div>
            </template>
            <div x-show="messages.length === 0" class="text-center text-gray-500 py-8">
                <p>No messages yet. Start the conversation!</p>
            </div>
        </div>

        <!-- Typing Indicator -->
        <div x-show="isTyping" class="px-4 py-2 bg-gray-50 border-t border-gray-200">
            <p class="text-sm text-gray-500 italic" x-text="otherUserName + ' is typing...'"></p>
        </div>

        <!-- Message Input -->
        <div class="p-4 bg-white border-t border-gray-200 rounded-b-2xl">
            <form @submit.prevent="sendMessage" class="flex space-x-2">
                <input type="text" 
                       x-model="newMessage" 
                       @keydown.enter.prevent="sendMessage"
                       placeholder="Type your message..." 
                       class="flex-1 border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                       :disabled="loading">
                <button type="submit" 
                        :disabled="!newMessage.trim() || loading"
                        class="bg-gradient-to-r from-pink-500 to-rose-500 text-white px-4 py-2 rounded-xl hover:from-pink-600 hover:to-rose-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function chatWidget(appointmentId, conversationId) {
    return {
        isOpen: false,
        messages: [],
        newMessage: '',
        loading: false,
        conversationId: conversationId,
        currentUserId: {{ auth()->id() }},
        otherUserName: '',
        unreadCount: 0,
        isTyping: false,
        echo: null,

        async init() {
            await this.loadConversation();
            // Setup broadcasting after conversation is loaded
            // Wait a bit to ensure conversation is fully loaded
            setTimeout(() => {
                this.setupBroadcasting();
            }, 500);
        },

        async loadConversation() {
            try {
                const response = await fetch(`/chat/appointment/${appointmentId}`);
                const data = await response.json();
                
                if (data.success && data.conversation) {
                    this.conversationId = data.conversation.id;
                    this.messages = (data.messages || []).map(msg => ({
                        id: msg.id,
                        conversation_id: msg.conversation_id,
                        sender_id: msg.sender_id,
                        message: msg.message,
                        type: msg.type || 'text',
                        created_at: msg.created_at,
                        formatted_time: msg.formatted_time || new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                        sender_name: msg.sender?.name || 'Unknown',
                    }));
                    this.otherUserName = this.getOtherUserName(data.conversation);
                    this.unreadCount = data.unread_count || 0;
                    this.scrollToBottom();
                } else if (data.conversation) {
                    // Fallback for different response format
                    this.conversationId = data.conversation.id;
                    this.messages = (data.messages || []).map(msg => ({
                        id: msg.id,
                        conversation_id: msg.conversation_id,
                        sender_id: msg.sender_id,
                        message: msg.message,
                        type: msg.type || 'text',
                        created_at: msg.created_at,
                        formatted_time: msg.formatted_time || new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                        sender_name: msg.sender?.name || 'Unknown',
                    }));
                    this.otherUserName = this.getOtherUserName(data.conversation);
                    this.unreadCount = 0;
                    this.scrollToBottom();
                }
            } catch (error) {
                console.error('Error loading conversation:', error);
            }
        },

        getOtherUserName(conversation) {
            const currentUser = {{ auth()->id() }};
            if (conversation.client_id === currentUser) {
                return conversation.staff ? conversation.staff.name : 'Staff';
            } else {
                return conversation.client ? conversation.client.name : 'Client';
            }
        },

        setupBroadcasting() {
            if (!this.conversationId) {
                console.log('No conversation ID, skipping broadcasting setup');
                return;
            }

            // Disconnect previous listener if exists
            if (this.echo) {
                try {
                    this.echo.stopListening('.message.sent');
                    this.echo.leave(`private-chat.${this.conversationId}`);
                } catch (e) {
                    console.log('Error disconnecting previous listener:', e);
                }
            }

            // Initialize Laravel Echo if available
            if (window.Echo) {
                console.log('=== SETTING UP BROADCASTING ===');
                console.log('Conversation ID:', this.conversationId);
                console.log('Conversation ID type:', typeof this.conversationId);
                console.log('Current user ID:', {{ auth()->id() }});
                try {
                    const channelName = `chat.${this.conversationId}`;
                    console.log('Subscribing to channel:', channelName);
                    console.log('Full channel name (with private- prefix):', `private-${channelName}`);
                    
                    this.echo = window.Echo.private(channelName)
                        .listen('.message.sent', (e) => {
                            console.log('=== RECEIVED BROADCAST MESSAGE ===');
                            console.log('Event data:', e);
                            console.log('Message ID:', e.id);
                            console.log('Sender ID:', e.sender_id);
                            console.log('Current user ID:', this.currentUserId);
                            // The event data structure from broadcastWith()
                            const messageData = {
                                id: e.id,
                                conversation_id: e.conversation_id,
                                sender_id: e.sender_id,
                                message: e.message,
                                type: e.type || 'text',
                                created_at: e.created_at,
                                formatted_time: e.formatted_time || new Date(e.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                                sender_name: e.sender_name || 'Unknown',
                            };
                            
                            // Only add if message doesn't already exist (avoid duplicates)
                            if (!this.messages.find(m => m.id === messageData.id)) {
                                this.messages.push(messageData);
                                this.scrollToBottom();
                                
                                // Mark as read if chat is open
                                if (this.isOpen) {
                                    this.markAsRead();
                                } else {
                                    this.unreadCount++;
                                }
                            }
                        })
                        .error((error) => {
                            console.error('Echo subscription error:', error);
                        })
                        .subscribed(() => {
                            console.log('Successfully subscribed to channel:', channelName);
                        });
                    console.log('Broadcasting setup complete');
                } catch (error) {
                    console.error('Error setting up broadcasting:', error);
                }
            } else {
                console.warn('Laravel Echo is not available. Make sure Pusher is configured.');
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || !this.conversationId || this.loading) return;

            this.loading = true;
            const messageText = this.newMessage.trim();
            this.newMessage = '';

            try {
                const response = await fetch(`/chat/${this.conversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: messageText,
                        type: 'text'
                    })
                });

                const data = await response.json();
                
                if (data.success && data.message) {
                    // Format the message to match the expected structure
                    const messageData = {
                        id: data.message.id,
                        conversation_id: data.message.conversation_id,
                        sender_id: data.message.sender_id,
                        message: data.message.message,
                        type: data.message.type || 'text',
                        created_at: data.message.created_at,
                        formatted_time: data.message.formatted_time || new Date(data.message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                        sender_name: data.message.sender?.name || '{{ auth()->user()->name }}',
                    };
                    
                    // Only add if message doesn't already exist (broadcast might have added it)
                    if (!this.messages.find(m => m.id === messageData.id)) {
                        this.messages.push(messageData);
                    }
                    this.scrollToBottom();
                } else {
                    alert('Failed to send message. Please try again.');
                    this.newMessage = messageText; // Restore message
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
                this.newMessage = messageText; // Restore message
            } finally {
                this.loading = false;
            }
        },

        async markAsRead() {
            if (!this.conversationId) return;

            try {
                await fetch(`/chat/${this.conversationId}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.unreadCount = 0;
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            const chatWindow = document.querySelector('#chatWidget [x-show="isOpen"]');
            if (this.isOpen) {
                if (chatWindow) chatWindow.style.display = 'flex';
                this.scrollToBottom();
                this.markAsRead();
            } else {
                if (chatWindow) chatWindow.style.display = 'none';
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    }
}
</script>

