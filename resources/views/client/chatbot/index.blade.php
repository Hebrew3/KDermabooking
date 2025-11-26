<x-app-layout>
<x-mobile-header />
<x-client-sidebar />

<div class="lg:ml-64 min-h-screen bg-gray-50">
    <div class="flex flex-col h-screen lg:h-auto lg:min-h-screen">
        <!-- Chatbot Container -->
        <div class="flex-1 flex flex-col w-full max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm border-b px-4 lg:px-6 py-3 lg:py-4 sticky top-0 z-30">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 lg:space-x-3 flex-1 min-w-0">
                        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-600 hover:text-pink-600 transition-colors mr-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-r from-pink-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-lg lg:text-xl font-semibold text-gray-900 truncate">K-Derma Assistant</h1>
                            <p class="text-xs lg:text-sm text-gray-500 truncate">Your personal skincare advisor</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 flex-shrink-0">
                        <span class="inline-flex items-center px-2 lg:px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 lg:w-2 lg:h-2 bg-green-400 rounded-full mr-1 lg:mr-1.5"></span>
                            <span class="hidden sm:inline">Online</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="flex-1 overflow-hidden flex flex-col" style="min-height: 0;">
                <div id="messages-container" class="flex-1 overflow-y-auto p-4 lg:p-6 space-y-3 lg:space-y-4" style="max-height: calc(100vh - 200px);">
                    @foreach($messages as $message)
                        @if($message->isFromClient())
                            <!-- Client Message -->
                            <div class="flex justify-end">
                                <div class="max-w-[85%] sm:max-w-xs lg:max-w-md px-3 lg:px-4 py-2 rounded-lg bg-pink-500 text-white">
                                    <p class="text-sm break-words">{{ $message->message }}</p>
                                    <p class="text-xs text-pink-100 mt-1">{{ $message->formatted_time }}</p>
                                </div>
                            </div>
                        @else
                            <!-- Bot Message -->
                            <div class="flex justify-start">
                                <div class="flex items-start space-x-2 max-w-[85%] sm:max-w-xs lg:max-w-md">
                                    <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </div>
                                    <div class="bg-white rounded-lg shadow-sm border p-3 lg:p-4">
                                        <div class="prose prose-sm max-w-none break-words">
                                            {!! nl2br(e($message->message)) !!}
                                        </div>
                                        
                                        @if($message->message_type === 'recommendation' && $message->message_data)
                                            <!-- Service Recommendations -->
                                            @if(isset($message->message_data['services']))
                                                <div class="mt-3 space-y-2">
                                                    @foreach($message->message_data['services'] as $service)
                                                        <div class="border rounded-lg p-3 bg-gray-50">
                                                            <div class="flex justify-between items-start">
                                                                <div>
                                                                    <h4 class="font-medium text-gray-900">{{ $service['name'] }}</h4>
                                                                    <p class="text-sm text-gray-600">{{ $service['description'] }}</p>
                                                                    <p class="text-sm font-medium text-pink-600 mt-1">{{ $service['price'] }} • {{ $service['duration'] }}</p>
                                                                </div>
                                                                <a href="{{ route('client.services.show', $service['id']) }}" 
                                                                   class="text-pink-600 hover:text-pink-800 text-sm font-medium">
                                                                    View Details
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif

                                        @if(isset($message->message_data['quick_replies']))
                                            <!-- Quick Reply Buttons -->
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($message->message_data['quick_replies'] as $reply)
                                                    <button class="quick-reply-btn px-3 py-1 text-sm bg-pink-100 text-pink-700 rounded-full hover:bg-pink-200 transition-colors"
                                                            data-action="{{ $reply['action'] }}"
                                                            data-text="{{ $reply['text'] }}">
                                                        {{ $reply['text'] }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif

                                        <p class="text-xs text-gray-400 mt-2">{{ $message->formatted_time }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Typing Indicator (hidden by default) -->
                <div id="typing-indicator" class="px-6 pb-2 hidden">
                    <div class="flex justify-start">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div class="bg-white rounded-lg shadow-sm border p-3">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="bg-white border-t px-4 lg:px-6 py-3 lg:py-4 sticky bottom-0">
                    <form id="message-form" class="flex space-x-2 lg:space-x-3">
                        @csrf
                        <input type="hidden" id="conversation-id" value="{{ $conversation->id }}">
                        <div class="flex-1 min-w-0">
                            <input type="text" 
                                   id="message-input" 
                                   name="message"
                                   placeholder="Type your message..." 
                                   class="w-full px-3 lg:px-4 py-2 text-sm lg:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                   maxlength="1000"
                                   autocomplete="off">
                        </div>
                        <button type="submit" 
                                id="send-button"
                                class="px-4 lg:px-6 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Mobile optimizations */
    @media (max-width: 1023px) {
        .lg\:ml-64 {
            margin-left: 0 !important;
        }
        
        #messages-container {
            -webkit-overflow-scrolling: touch;
            padding-bottom: 1rem;
        }
        
        /* Ensure input stays visible on mobile */
        #message-form {
            position: sticky;
            bottom: 0;
            background: white;
            z-index: 10;
        }
    }
    
    /* Prevent zoom on input focus (iOS) */
    @media screen and (max-width: 768px) {
        input[type="text"],
        input[type="email"],
        textarea {
            font-size: 16px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const messagesContainer = document.getElementById('messages-container');
    const typingIndicator = document.getElementById('typing-indicator');
    const conversationId = document.getElementById('conversation-id').value;

    // Auto-scroll to bottom
    function scrollToBottom() {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    }
    
    // Close sidebar on mobile when page loads
    if (window.innerWidth < 1024) {
        const sidebar = document.getElementById('client-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (sidebar && overlay) {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }

    // Show typing indicator
    function showTypingIndicator() {
        typingIndicator.classList.remove('hidden');
        scrollToBottom();
    }

    // Hide typing indicator
    function hideTypingIndicator() {
        typingIndicator.classList.add('hidden');
    }

    // Add message to chat
    function addMessage(message, isClient = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = isClient ? 'flex justify-end' : 'flex justify-start';
        
        if (isClient) {
            messageDiv.innerHTML = `
                <div class="max-w-[85%] sm:max-w-xs lg:max-w-md px-3 lg:px-4 py-2 rounded-lg bg-pink-500 text-white">
                    <p class="text-sm break-words">${message.message}</p>
                    <p class="text-xs text-pink-100 mt-1">${message.formatted_time}</p>
                </div>
            `;
        } else {
            let quickRepliesHtml = '';
            if (message.message_data && message.message_data.quick_replies) {
                quickRepliesHtml = '<div class="mt-3 flex flex-wrap gap-2">';
                message.message_data.quick_replies.forEach(reply => {
                    quickRepliesHtml += `
                        <button class="quick-reply-btn px-3 py-1 text-sm bg-pink-100 text-pink-700 rounded-full hover:bg-pink-200 transition-colors"
                                data-action="${reply.action}" data-text="${reply.text}">
                            ${reply.text}
                        </button>
                    `;
                });
                quickRepliesHtml += '</div>';
            }

            let servicesHtml = '';
            if (message.message_data && message.message_data.services) {
                servicesHtml = '<div class="mt-3 space-y-2">';
                message.message_data.services.forEach(service => {
                    servicesHtml += `
                        <div class="border rounded-lg p-3 bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900">${service.name}</h4>
                                    <p class="text-sm text-gray-600">${service.description}</p>
                                    <p class="text-sm font-medium text-pink-600 mt-1">${service.price} • ${service.duration}</p>
                                </div>
                                <a href="/client/services/${service.id}" class="text-pink-600 hover:text-pink-800 text-sm font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    `;
                });
                servicesHtml += '</div>';
            }

            messageDiv.innerHTML = `
                <div class="flex items-start space-x-2 max-w-[85%] sm:max-w-xs lg:max-w-md">
                    <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border p-3 lg:p-4">
                        <div class="prose prose-sm max-w-none break-words">
                            ${message.message.replace(/\n/g, '<br>')}
                        </div>
                        ${servicesHtml}
                        ${quickRepliesHtml}
                        <p class="text-xs text-gray-400 mt-2">${message.formatted_time}</p>
                    </div>
                </div>
            `;
        }

        messagesContainer.appendChild(messageDiv);
        scrollToBottom();

        // Add event listeners to quick reply buttons
        if (!isClient) {
            messageDiv.querySelectorAll('.quick-reply-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const text = this.getAttribute('data-text');
                    const action = this.getAttribute('data-action');
                    
                    // Handle quick reply actions
                    handleQuickReply(text, action);
                });
            });
        }
    }

    // Handle quick reply actions
    function handleQuickReply(text, action) {
        // Send the quick reply text as a message
        messageInput.value = text;
        messageForm.dispatchEvent(new Event('submit'));
        
        // Handle specific actions
        switch(action) {
            case 'view_services':
                setTimeout(() => {
                    window.location.href = '{{ route("client.services") }}';
                }, 1000);
                break;
            case 'book_appointment':
                setTimeout(() => {
                    window.location.href = '{{ route("client.appointments.create") }}';
                }, 1000);
                break;
            case 'view_appointments':
                setTimeout(() => {
                    window.location.href = '{{ route("client.appointments.index") }}';
                }, 1000);
                break;
        }
    }

    // Handle form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Disable form
        sendButton.disabled = true;
        messageInput.disabled = true;

        // Add client message immediately
        addMessage({
            message: message,
            formatted_time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
        }, true);

        // Clear input
        messageInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        // Send message to server
        console.log('Sending message:', message, 'to conversation:', conversationId);
        fetch('{{ route("client.chatbot.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message,
                conversation_id: conversationId
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            hideTypingIndicator();
            
            if (data.success) {
                // Add bot response
                addMessage(data.bot_message);
            } else {
                console.error('Server error:', data.error || data);
                // Add error message
                addMessage({
                    message: 'Sorry, I encountered an error. Please try again.',
                    formatted_time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
                });
            }
        })
        .catch(error => {
            hideTypingIndicator();
            console.error('Fetch error:', error);
            addMessage({
                message: 'Sorry, I encountered a connection error. Please check your internet connection and try again.',
                formatted_time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})
            });
        })
        .finally(() => {
            // Re-enable form
            sendButton.disabled = false;
            messageInput.disabled = false;
            messageInput.focus();
        });
    });

    // Focus on input when page loads (only on desktop to avoid mobile keyboard issues)
    if (window.innerWidth >= 768) {
        messageInput.focus();
    }

    // Initial scroll to bottom
    scrollToBottom();
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            scrollToBottom();
        }, 250);
    });
    
    // Prevent form submission on mobile when keyboard is open
    if (window.innerWidth < 768) {
        messageForm.addEventListener('submit', function(e) {
            // Small delay to ensure keyboard doesn't interfere
            setTimeout(() => {
                messageInput.blur();
            }, 100);
        });
    }

    // Handle Enter key
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });
});
</script>
@endpush
</x-app-layout>
