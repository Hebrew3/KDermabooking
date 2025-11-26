<x-app-layout>
<x-mobile-header />

@if(auth()->user()->isClient())
    <x-client-sidebar />
@elseif(auth()->user()->isStaffMember() || auth()->user()->isAdmin())
    <x-staff-sidebar />
@endif

<div class="lg:ml-64 h-screen flex flex-col overflow-hidden" style="height: 100vh;">
    <div class="flex-1 flex overflow-hidden bg-gray-50" style="min-height: 0; height: 100%;">
        <!-- Conversations List -->
        <div class="w-full md:w-1/3 lg:w-1/4 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-pink-500 to-rose-500">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Messages</h2>
                    <button class="text-white hover:text-pink-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="p-4 border-b border-gray-200">
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="Search conversations..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500"
                        x-model="searchQuery"
                        @input="filterConversations()"
                    >
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Conversations -->
            <div class="flex-1 overflow-y-auto" x-data="messenger()" x-init="loadConversations(); setInterval(() => loadConversations(), 30000)">
                <template x-if="loading">
                    <div class="flex items-center justify-center h-full">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-pink-500"></div>
                    </div>
                </template>

                <template x-if="!loading && filteredConversations.length === 0">
                    <div class="flex flex-col items-center justify-center h-full p-4 text-center">
                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-gray-500">No conversations yet</p>
                        <p class="text-sm text-gray-400 mt-2">Start chatting from your appointments</p>
                    </div>
                </template>

                <template x-for="conversation in filteredConversations" :key="conversation.id">
                    <div 
                        @click="selectConversation(conversation.id)"
                        :class="selectedConversationId === conversation.id ? 'bg-pink-50 border-l-4 border-pink-500' : 'hover:bg-gray-50'"
                        class="p-4 border-b border-gray-100 cursor-pointer transition-colors"
                    >
                        <div class="flex items-center space-x-3">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-pink-400 to-rose-400 flex items-center justify-center text-white font-semibold">
                                    <span x-text="getOtherUserInitial(conversation)"></span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900 truncate" x-text="getOtherUserName(conversation)"></p>
                                    <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_message_at)"></span>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-sm text-gray-600 truncate" x-text="conversation.last_message || 'No messages yet'"></p>
                                    <template x-if="conversation.unread_count > 0">
                                        <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-pink-500 rounded-full" x-text="conversation.unread_count"></span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-gray-50">
            <!-- Empty State -->
            <div id="emptyState" class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Select a conversation to start messaging</p>
                </div>
            </div>

            <!-- Chat Interface -->
            <div id="chatInterface" x-data="chatArea()" x-init="init()" class="flex-1 flex flex-col hidden h-full">
                <!-- Chat Header -->
                <div class="bg-white border-b border-gray-200 p-4 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-pink-400 to-rose-400 flex items-center justify-center text-white font-semibold">
                                <span x-text="getOtherUserInitial()"></span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900" x-text="otherUserName"></h3>
                                <p class="text-xs text-gray-500" x-show="!isTyping" x-transition>Online</p>
                                <p class="text-xs text-pink-600 font-medium" x-show="isTyping" x-transition>
                                    <span class="inline-flex items-center">
                                        <span class="animate-pulse">Typing</span>
                                        <span class="ml-1 inline-flex space-x-1">
                                            <span class="w-1.5 h-1.5 bg-pink-600 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                            <span class="w-1.5 h-1.5 bg-pink-600 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                            <span class="w-1.5 h-1.5 bg-pink-600 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                        </span>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages Container - Scrollable -->
                <div class="flex-1 overflow-y-auto bg-gray-50" style="min-height: 0;" id="messagesContainer" x-ref="messagesContainer">
                    <div class="p-4">
                        <template x-if="loadingMessages">
                            <div class="flex items-center justify-center py-8">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-pink-500"></div>
                            </div>
                        </template>

                        <template x-if="!loadingMessages && messages && messages.length === 0">
                            <div class="flex items-center justify-center py-8">
                                <p class="text-gray-500">No messages yet. Start the conversation!</p>
                            </div>
                        </template>

                        <template x-if="!loadingMessages && messages && messages.length > 0">
                            <template x-for="message in messages" :key="message.id">
                                <div class="flex mb-4" :class="message.sender_id === currentUserId ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-xs lg:max-w-md">
                                        <div 
                                            class="px-4 py-2 rounded-2xl shadow-sm"
                                            :class="message.sender_id === currentUserId 
                                                ? 'bg-gradient-to-r from-pink-500 to-rose-500 text-white' 
                                                : 'bg-white text-gray-900 border border-gray-200'"
                                        >
                                            <p class="text-sm whitespace-pre-wrap break-words" x-text="message.message"></p>
                                            <p 
                                                class="text-xs mt-1"
                                                :class="message.sender_id === currentUserId ? 'text-pink-100' : 'text-gray-500'"
                                                x-text="message.formatted_time || formatMessageTime(message.created_at)"
                                            ></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                        
                        <!-- Typing Indicator -->
                        <div x-show="isTyping" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="flex mb-4 justify-start">
                            <div class="max-w-xs lg:max-w-md">
                                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm px-4 py-3">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-sm text-gray-600" x-text="(otherUserName || 'Someone') + ' is typing'"></span>
                                        <div class="flex space-x-1 ml-2">
                                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                            <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Input - Fixed at Bottom -->
                <div class="bg-white border-t border-gray-200 p-4 flex-shrink-0 shadow-lg">
                    <form @submit.prevent="sendMessage()" class="flex space-x-2">
                        <input 
                            type="text" 
                            x-model="newMessage" 
                            @keydown.enter.prevent="sendMessage()"
                            @keydown.enter.shift.prevent="newMessage += '\n'"
                            @input="handleTyping()"
                            placeholder="Type your message..." 
                            class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 outline-none disabled:bg-gray-100 disabled:cursor-not-allowed"
                            :disabled="sending"
                            autocomplete="off"
                            x-ref="messageInput"
                        >
                        <button 
                            type="submit" 
                            :disabled="!newMessage.trim() || sending"
                            class="bg-gradient-to-r from-pink-500 to-rose-500 text-white px-6 py-3 rounded-xl hover:from-pink-600 hover:to-rose-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center min-w-[60px]"
                        >
                            <template x-if="sending">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                            </template>
                            <template x-if="!sending">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </template>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function messenger() {
    return {
        conversations: [],
        filteredConversations: [],
        searchQuery: '',
        loading: true,
        selectedConversationId: null,
        currentUserId: {{ auth()->id() }},
        echo: null,

        async loadConversations() {
            try {
                this.loading = true;
                const response = await fetch('/chat/conversations');
                const data = await response.json();
                
                this.conversations = data.conversations.map(conv => ({
                    ...conv,
                    last_message: conv.messages_count > 0 ? (conv.last_message || 'No messages yet') : 'No messages yet',
                    unread_count: conv.messages_count || 0
                }));
                
                this.filteredConversations = this.conversations;
                this.loading = false;
            } catch (error) {
                console.error('Error loading conversations:', error);
                this.loading = false;
            }
        },

        filterConversations() {
            if (!this.searchQuery.trim()) {
                this.filteredConversations = this.conversations;
                return;
            }

            const query = this.searchQuery.toLowerCase();
            this.filteredConversations = this.conversations.filter(conv => {
                const otherUserName = this.getOtherUserName(conv).toLowerCase();
                return otherUserName.includes(query);
            });
        },

        getOtherUserName(conversation) {
            if (!conversation) return 'Unknown';
            
            if (conversation.client_id === this.currentUserId) {
                if (conversation.staff) {
                    return conversation.staff.name || conversation.staff.first_name || 'Staff';
                }
                return 'Staff';
            } else {
                if (conversation.client) {
                    return conversation.client.name || conversation.client.first_name || 'Client';
                }
                return 'Client';
            }
        },

        getOtherUserInitial(conversation) {
            const name = this.getOtherUserName(conversation);
            if (!name || name === 'Unknown') return '?';
            return name.charAt(0).toUpperCase();
        },

        formatTime(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'Just now';
            if (minutes < 60) return `${minutes}m ago`;
            if (hours < 24) return `${hours}h ago`;
            if (days < 7) return `${days}d ago`;
            return date.toLocaleDateString();
        },

        selectConversation(conversationId) {
            this.selectedConversationId = conversationId;
            // Store in window for chat area access
            window.selectedConversationId = conversationId;
            // Trigger chat area to load messages
            window.dispatchEvent(new CustomEvent('conversation-selected', { detail: conversationId }));
        }
    }
}

function chatArea() {
    return {
        selectedConversationId: null,
        messages: [],
        newMessage: '',
        sending: false,
        loadingMessages: false,
        otherUserName: '',
        currentUserId: {{ auth()->id() }},
        echoChannel: null,
        messagesContainer: null,
        isTyping: false,
        typingTimeout: null,
        lastTypingEvent: null,

        init() {
            this.messagesContainer = this.$refs.messagesContainer;
            
            // Listen for conversation selection
            window.addEventListener('conversation-selected', (e) => {
                this.selectedConversationId = e.detail;
                // Reset typing state when switching conversations
                this.isTyping = false;
                clearTimeout(this.typingTimeout);
                // Show the chat interface and hide empty state
                const chatInterface = document.getElementById('chatInterface');
                const emptyState = document.getElementById('emptyState');
                if (chatInterface) {
                    chatInterface.classList.remove('hidden');
                }
                if (emptyState) {
                    emptyState.classList.add('hidden');
                }
                this.loadMessages();
                // Small delay to ensure messages are loaded before setting up broadcasting
                setTimeout(() => {
                    this.setupBroadcasting();
                    // Focus on input when conversation is selected
                    if (this.$refs.messageInput) {
                        this.$refs.messageInput.focus();
                    }
                }, 200);
            });
        },

        async loadMessages() {
            if (!this.selectedConversationId) return;

            try {
                this.loadingMessages = true;
                this.messages = []; // Clear previous messages
                
                const response = await fetch(`/chat/${this.selectedConversationId}/messages`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Loaded messages:', data);
                
                if (data.messages && Array.isArray(data.messages)) {
                    this.messages = data.messages.map(msg => ({
                        id: msg.id,
                        conversation_id: msg.conversation_id,
                        sender_id: msg.sender_id,
                        message: msg.message,
                        type: msg.type || 'text',
                        created_at: msg.created_at,
                        formatted_time: msg.formatted_time || this.formatMessageTime(msg.created_at),
                        sender_name: msg.sender ? (msg.sender.name || msg.sender.first_name) : 'Unknown'
                    }));
                    console.log('Processed messages count:', this.messages.length);
                } else {
                    this.messages = [];
                    console.warn('No messages array in response:', data);
                }

                // Get other user name
                const convResponse = await fetch(`/chat/conversations`);
                const convData = await convResponse.json();
                const conversation = convData.conversations.find(c => c.id === this.selectedConversationId);
                if (conversation) {
                    if (conversation.client_id === this.currentUserId) {
                        this.otherUserName = conversation.staff 
                            ? (conversation.staff.name || conversation.staff.first_name || 'Staff')
                            : 'Staff';
                    } else {
                        this.otherUserName = conversation.client 
                            ? (conversation.client.name || conversation.client.first_name || 'Client')
                            : 'Client';
                    }
                } else {
                    this.otherUserName = 'Unknown';
                }

                this.loadingMessages = false;
                // Scroll to bottom after messages are loaded
                setTimeout(() => {
                    this.scrollToBottom();
                }, 100);
            } catch (error) {
                console.error('Error loading messages:', error);
                this.messages = [];
                this.loadingMessages = false;
            }
        },

        async sendMessage() {
            const messageText = this.newMessage.trim();
            if (!messageText || !this.selectedConversationId || this.sending) {
                return;
            }

            this.sending = true;
            const messageToSend = messageText;
            this.newMessage = ''; // Clear input immediately for better UX

            try {
                const response = await fetch(`/chat/${this.selectedConversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        message: messageToSend,
                        type: 'text'
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.success && data.message) {
                    // Add message to local array if not already added by broadcast
                    const messageExists = this.messages.find(m => m.id === data.message.id);
                    if (!messageExists) {
                        this.messages.push({
                            ...data.message,
                            formatted_time: this.formatMessageTime(data.message.created_at)
                        });
                        // Scroll to bottom after sending
                        setTimeout(() => {
                            this.scrollToBottom();
                        }, 50);
                    }
                    // Stop typing indicator and focus back on input after sending
                    this.isTyping = false;
                    clearTimeout(this.typingTimeout);
                    // Send stop typing event
                    fetch(`/chat/${this.selectedConversationId}/typing`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            is_typing: false
                        })
                    }).catch(err => console.log('Stop typing broadcast error:', err));
                    
                    if (this.$refs.messageInput) {
                        this.$refs.messageInput.focus();
                    }
                } else {
                    // If send failed, restore the message
                    this.newMessage = messageToSend;
                    console.error('Failed to send message:', data);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                // Restore the message on error
                this.newMessage = messageToSend;
                alert('Failed to send message. Please try again.');
            } finally {
                this.sending = false;
            }
        },

        setupBroadcasting() {
            if (!this.selectedConversationId || !window.Echo) {
                console.log('Cannot setup broadcasting - missing conversationId or Echo');
                return;
            }

            // Disconnect previous listener if exists
            if (this.echoChannel) {
                try {
                    console.log('Disconnecting previous channel listeners');
                    this.echoChannel.stopListening('.message.sent');
                    this.echoChannel.stopListening('.typing');
                    this.echoChannel.stopListening('.stopped-typing');
                    window.Echo.leave(`private-chat.${this.selectedConversationId}`);
                } catch (e) {
                    console.log('Error stopping previous listener:', e);
                }
            }

            const channelName = `chat.${this.selectedConversationId}`;
            console.log('=== SETTING UP BROADCASTING ===');
            console.log('Channel name:', channelName);
            console.log('Current user ID:', this.currentUserId);
            
            this.echoChannel = window.Echo.private(channelName)
                .listen('.message.sent', (e) => {
                    console.log('Received broadcast message:', e);
                    const messageData = {
                        id: e.id,
                        conversation_id: e.conversation_id,
                        sender_id: e.sender_id,
                        message: e.message,
                        type: e.type || 'text',
                        created_at: e.created_at,
                        formatted_time: e.formatted_time || this.formatMessageTime(e.created_at),
                        sender_name: e.sender_name || 'Unknown',
                    };
                    
                    if (!this.messages.find(m => m.id === messageData.id)) {
                        this.messages.push(messageData);
                        // Scroll to bottom when new message arrives
                        setTimeout(() => {
                            this.scrollToBottom();
                        }, 50);
                    }
                    // Stop typing indicator when message is received
                    this.isTyping = false;
                    clearTimeout(this.typingTimeout);
                })
                .listen('.typing', (e) => {
                    console.log('=== RECEIVED TYPING EVENT ===', e);
                    console.log('Event user_id:', e.user_id, 'Type:', typeof e.user_id);
                    console.log('Current user_id:', this.currentUserId, 'Type:', typeof this.currentUserId);
                    console.log('Comparison:', e.user_id !== this.currentUserId);
                    
                    // Convert to numbers for comparison
                    const eventUserId = parseInt(e.user_id);
                    const currentUserId = parseInt(this.currentUserId);
                    
                    // Only show typing if it's from the other user
                    if (eventUserId && eventUserId !== currentUserId) {
                        console.log('✅ Showing typing indicator for user:', eventUserId);
                        this.isTyping = true;
                        this.lastTypingEvent = Date.now();
                        // Force UI update
                        this.$nextTick(() => {
                            console.log('isTyping state:', this.isTyping);
                        });
                        // Auto-hide typing after 3 seconds
                        clearTimeout(this.typingTimeout);
                        this.typingTimeout = setTimeout(() => {
                            // Only hide if no new typing event in the last 2.5 seconds
                            if (Date.now() - this.lastTypingEvent > 2500) {
                                console.log('Auto-hiding typing indicator');
                                this.isTyping = false;
                            }
                        }, 3000);
                    } else {
                        console.log('❌ Ignoring typing - same user or invalid user_id');
                    }
                })
                .listen('.stopped-typing', (e) => {
                    console.log('=== RECEIVED STOPPED-TYPING EVENT ===', e);
                    const eventUserId = parseInt(e.user_id);
                    const currentUserId = parseInt(this.currentUserId);
                    if (eventUserId && eventUserId !== currentUserId) {
                        console.log('✅ Hiding typing indicator');
                        this.isTyping = false;
                        clearTimeout(this.typingTimeout);
                    }
                })
                .error((error) => {
                    console.error('Echo subscription error:', error);
                })
                .subscribed(() => {
                    console.log('Successfully subscribed to channel:', channelName);
                });
        },

        handleTyping() {
            if (!this.selectedConversationId) {
                return;
            }

            // Clear previous timeout
            if (this.typingTimeout) {
                clearTimeout(this.typingTimeout);
            }

            // If input is empty, stop typing
            if (!this.newMessage.trim()) {
                fetch(`/chat/${this.selectedConversationId}/typing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        is_typing: false
                    })
                }).catch(err => console.log('Stop typing broadcast error:', err));
                return;
            }
            
            // Broadcast typing event only if there's text
            console.log('Sending typing event for conversation:', this.selectedConversationId);
            fetch(`/chat/${this.selectedConversationId}/typing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    is_typing: true
                })
            })
            .then(response => {
                if (response.ok) {
                    console.log('Typing event sent successfully');
                }
            })
            .catch(err => console.error('Typing broadcast error:', err));

            // Stop typing after 1.5 seconds of no input
            this.typingTimeout = setTimeout(() => {
                console.log('Auto-stopping typing after timeout');
                fetch(`/chat/${this.selectedConversationId}/typing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        is_typing: false
                    })
                }).catch(err => console.log('Stop typing broadcast error:', err));
            }, 1500);
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.messagesContainer) {
                    // Smooth scroll to bottom
                    this.messagesContainer.scrollTo({
                        top: this.messagesContainer.scrollHeight,
                        behavior: 'smooth'
                    });
                }
            });
        },

        formatMessageTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        },

        getOtherUserInitial() {
            if (!this.otherUserName || this.otherUserName === 'Unknown') return '?';
            return this.otherUserName.charAt(0).toUpperCase();
        }
    }
}
</script>
</x-app-layout>

