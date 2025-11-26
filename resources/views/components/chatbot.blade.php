<!-- Chatbot Widget -->
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>

<div id="chatbot-widget" class="fixed bottom-6 right-6 z-50">
    <!-- Chat Toggle Button -->
    <button id="chatbot-toggle" 
            class="bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
        <svg id="chat-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.874A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
        </svg>
        <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" 
         class="hidden absolute bottom-16 right-0 w-80 h-[32rem] bg-white rounded-2xl shadow-2xl border border-pink-100 overflow-hidden transform transition-all duration-300 scale-95 opacity-0">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-pink-500 to-rose-500 text-white p-4 flex items-center space-x-3">
            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.874A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold">K Derma Assistant</h3>
                <p class="text-xs opacity-90">Your skin's best friend</p>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 p-4 h-80 overflow-y-auto bg-gray-50">
            <!-- Welcome message will be inserted here -->
        </div>

        <!-- Chat Input -->
        <div class="p-3 border-t border-pink-100 bg-white">
            <div id="chat-options" class="space-y-2">
                <!-- Dynamic options will be inserted here -->
            </div>
            <div id="text-input-area" class="hidden flex space-x-2">
                <input type="text" id="chat-input" 
                       class="flex-1 px-3 py-2 border border-pink-200 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-500 text-sm"
                       placeholder="Type your message...">
                <button id="send-btn" 
                        class="bg-pink-500 hover:bg-pink-600 text-white rounded-full p-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Chatbot functionality
class KDermaChatbot {
    constructor() {
        this.currentFlow = 'start';
        this.chatMessages = document.getElementById('chat-messages');
        this.chatOptions = document.getElementById('chat-options');
        this.textInputArea = document.getElementById('text-input-area');
        this.chatInput = document.getElementById('chat-input');
        
        this.init();
    }

    init() {
        // Toggle chatbot
        document.getElementById('chatbot-toggle').addEventListener('click', () => {
            this.toggleChatbot();
        });

        // Send message on Enter
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });

        // Send button
        document.getElementById('send-btn').addEventListener('click', () => {
            this.sendMessage();
        });

        // Start with welcome message
        this.showWelcomeMessage();
    }

    toggleChatbot() {
        const window = document.getElementById('chatbot-window');
        const chatIcon = document.getElementById('chat-icon');
        const closeIcon = document.getElementById('close-icon');
        
        if (window.classList.contains('hidden')) {
            // Show chatbot
            window.classList.remove('hidden');
            setTimeout(() => {
                window.classList.remove('scale-95', 'opacity-0');
                window.classList.add('scale-100', 'opacity-100');
            }, 10);
            chatIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        } else {
            // Hide chatbot
            window.classList.remove('scale-100', 'opacity-100');
            window.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                window.classList.add('hidden');
            }, 300);
            chatIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    }

    addMessage(message, isBot = true, delay = 0) {
        if (delay > 0 && isBot) {
            this.showTypingIndicator();
            setTimeout(() => {
                this.hideTypingIndicator();
                this.addMessageNow(message, isBot);
            }, delay);
        } else {
            this.addMessageNow(message, isBot);
        }
    }

    addMessageNow(message, isBot) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-3 ${isBot ? 'text-left' : 'text-right'} animate-fade-in`;
        
        messageDiv.innerHTML = `
            <div class="${isBot ? 'bg-white border border-pink-100' : 'bg-gradient-to-r from-pink-500 to-rose-500 text-white'} 
                        inline-block px-3 py-2 rounded-lg max-w-xs text-sm">
                ${message}
            </div>
        `;
        
        this.chatMessages.appendChild(messageDiv);
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
    }

    showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typing-indicator';
        typingDiv.className = 'mb-3 text-left';
        typingDiv.innerHTML = `
            <div class="bg-white border border-pink-100 inline-block px-3 py-2 rounded-lg">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-pink-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-pink-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-pink-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        `;
        this.chatMessages.appendChild(typingDiv);
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    getIconHtml(iconType) {
        const icons = {
            'consultation': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>`,
            'calendar': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>`,
            'services': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>`,
            'products': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>`,
            'user': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>`,
            'check': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>`,
            'shopping': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>`,
            'back': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>`,
            'help': `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`
        };
        
        return icons[iconType] || `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>`;
    }

    showOptions(options) {
        this.chatOptions.innerHTML = '';
        this.textInputArea.classList.add('hidden');
        
        
        options.forEach((option, index) => {
            const button = document.createElement('button');
            button.className = 'w-full text-left px-4 py-3 text-sm bg-white border border-pink-200 rounded-xl hover:bg-gradient-to-r hover:from-pink-50 hover:to-rose-50 hover:border-pink-300 transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-3 mb-1';
            
            const iconHtml = this.getIconHtml(option.icon);
            button.innerHTML = `
                <span class="text-pink-500 flex-shrink-0">${iconHtml}</span>
                <span class="font-medium text-gray-700">${option.text}</span>
            `;
            
            button.addEventListener('click', () => {
                // Add user message showing what they selected
                this.addMessage(option.text, false);
                setTimeout(() => option.action(), 300);
            });
            
            // Add button immediately without animation delays
            this.chatOptions.appendChild(button);
        });
    }

    showTextInput() {
        this.chatOptions.innerHTML = '';
        this.textInputArea.classList.remove('hidden');
        this.chatInput.focus();
    }

    sendMessage() {
        const message = this.chatInput.value.trim();
        if (message) {
            this.addMessage(message, false);
            this.chatInput.value = '';
            this.handleUserMessage(message);
        }
    }

    handleUserMessage(message) {
        // Handle text input based on current flow
        if (this.currentFlow === 'booking') {
            this.handleBookingStep(message);
        } else if (this.currentFlow === 'product-inquiry') {
            this.handleProductInquiry(message);
        }
    }

    handleBookingStep(message) {
        const trimmedMessage = message.trim();
        
        if (this.bookingStep === 'name') {
            this.bookingData.name = trimmedMessage;
            this.bookingStep = 'contact';
            setTimeout(() => this.askForContact(), 500);
            
        } else if (this.bookingStep === 'contact') {
            this.bookingData.contact = trimmedMessage;
            this.bookingStep = 'datetime';
            setTimeout(() => this.askForDateTime(), 500);
            
        } else if (this.bookingStep === 'datetime') {
            this.bookingData.datetime = trimmedMessage;
            this.bookingStep = 'service';
            setTimeout(() => this.askForService(), 500);
            
        } else if (this.bookingStep === 'service') {
            this.bookingData.service = trimmedMessage;
            this.bookingStep = 'complete';
            setTimeout(() => this.completeBooking(), 500);
        }
    }

    showWelcomeMessage() {
        this.addMessage(`Hi gorgeous! Welcome to K Derma Beauty Clinic, your skin's best friend!

🕐 We're open: Monday - Sunday, 9:00 AM to 7:00 PM

Would you like me to help you find the perfect treatment or product for your skin type?

Please choose below:`, true, 500);
        
        setTimeout(() => {
            this.showOptions([
                { text: 'Skin Consultation', icon: 'consultation', action: () => this.showSkinConsultation() },
                { text: 'Book an Appointment', icon: 'calendar', action: () => this.showBooking() },
                { text: 'View All Treatments', icon: 'services', action: () => this.showAllTreatments() },
                { text: 'View Aftercare Products', icon: 'products', action: () => this.showAllProducts() },
                { text: 'How to Use Chatbot', icon: 'help', action: () => this.showHelp() }
            ]);
        }, 800);
    }

    showHelp() {
        this.addMessage(`Here's how to use the K Derma Chatbot: 📖

1️⃣ **Finding the Chatbot**
   • Look for the chatbot icon in the bottom-right corner of the page
   • Click on it to open the chat window

2️⃣ **Getting Started**
   • The chatbot will greet you with a welcome message
   • You can select from the available options or type your question

3️⃣ **What I Can Help You With:**
   📅 **Booking Appointments** - Guide you through the booking process
   🌟 **Service Inquiries** - Provide information about available services
   💰 **Pricing Information** - Answer questions about service prices
   💆 **Skin Consultation** - Provide basic skin care advice
   🛍️ **Product Inquiries** - Information about products and treatments
   🕐 **Business Hours & Location** - Clinic operating hours and address

4️⃣ **Booking Through Chatbot:**
   • Select "Book an Appointment" option
   • Follow the chatbot's prompts step by step
   • Provide the requested information

5️⃣ **Closing the Chatbot:**
   • Click the close button (X) or icon to close the chat window
   • You can reopen it anytime by clicking the chatbot icon again

Need help with something specific? Just ask me! 😊`, true, 600);
        
        setTimeout(() => {
            this.showOptions([
                { text: 'Book an Appointment', icon: 'calendar', action: () => this.showBooking() },
                { text: 'View Services', icon: 'services', action: () => this.showServices() },
                { text: 'Back to Main Menu', icon: 'back', action: () => this.showMainMenu() }
            ]);
        }, 1200);
    }

    showSkinConsultation() {
        this.currentFlow = 'consultation';
        this.addMessage(`Let's find what's best for your skin!
Please tell me your skin type (or choose below):`, true, 600);
        
        setTimeout(() => {
            this.showOptions([
                { text: '1. Oily Skin', icon: 'user', action: () => this.showSkinAdvice('oily') },
                { text: '2. Dry Skin', icon: 'user', action: () => this.showSkinAdvice('dry') },
                { text: '3. Combination Skin', icon: 'user', action: () => this.showSkinAdvice('combination') },
                { text: '4. Sensitive Skin', icon: 'user', action: () => this.showSkinAdvice('sensitive') },
                { text: '5. Acne-Prone Skin', icon: 'user', action: () => this.showSkinAdvice('acne') }
            ]);
        }, 1000);
    }

    showSkinAdvice(skinType) {
        const advice = {
            oily: `For oily skin, we recommend:
• Korean Facial w/ D.P – deeply cleanses pores and helps control excess oil.
• Celebrity Facial – gives an instant refreshed, matte glow.
• Product suggestion: Oil Control Toner and Mattifying Moisturizer.`,
            
            dry: `For dry skin, hydration is key.
We recommend:
• Neckcial w/ LED Light – soothes and nourishes the skin on your neck area.
• Immortal Facial – promotes deep hydration and youthful glow.
• Product suggestion: Hydrating Serum and Moisture Lock Cream.`,
            
            combination: `Combination skin needs balance.
Try:
• Hollywood Facial – revitalizes dull areas while controlling oily zones.
• Blockdoll Facial – brightens and smoothens skin tone.
• Product suggestion: Balancing Toner and Light Hydrating Gel Cream.`,
            
            sensitive: `For sensitive skin, gentle and soothing care is a must.
We suggest:
• Backcial w/ LED Light – gentle deep cleanse and LED calming therapy for your back area.
• BB Glow w/ Blush – evens out skin tone while being safe for sensitive skin.
• Product suggestion: Gentle Cleanser and Calming Serum.`,
            
            acne: `For acne-prone skin, we've got treatments that target breakouts effectively.
We recommend:
• Acne Facial w/ Acne Laser – clears clogged pores and reduces active pimples.
• Pico Glow Laser – minimizes acne marks and improves skin texture.
• Product suggestion: Acne Defense Toner and Clarifying Gel Cleanser.`
        };

        this.addMessage(advice[skinType], true, 1000);
        
        setTimeout(() => {
            this.addMessage("Would you like to:", true, 300);
            setTimeout(() => {
                this.showOptions([
                    { text: 'Book a session', icon: 'check', action: () => this.showBooking() },
                    { text: 'Inquire about products', icon: 'shopping', action: () => this.showProductInquiry() },
                    { text: 'Go back to main menu', icon: 'back', action: () => this.showMainMenu() }
                ]);
            }, 600);
        }, 1500);
    }

    showServices() {
        this.addMessage(`Our current Facial Services include:

Complete Facial Treatments:
• Korean Facial w/ D.P
• Celebrity Facial
• Acne Facial w/ Acne Laser
• Hollywood Facial
• Neckcial w/ LED Light
• Backcial w/ LED Light`, true, 800);
        
        setTimeout(() => {
            this.addMessage(`Skin Glow w/ Facial & LED Light Treatments:
• BB Glow w/ Blush
• Blockdoll Facial
• Pico Glow Laser
• Fractional CO2 Laser (Face)
• Fractional CO2 Laser (Back)
• Immortal Facial`, true, 600);
            
            setTimeout(() => {
                this.addMessage("Would you like to book a session or know more about a specific service?", true, 400);
                setTimeout(() => {
                    this.showOptions([
                        { text: 'Book an Appointment', icon: 'calendar', action: () => this.showBooking() },
                        { text: 'Go back to main menu', icon: 'back', action: () => this.showMainMenu() }
                    ]);
                }, 700);
            }, 1000);
        }, 1200);
    }

    showProductInquiry() {
        this.currentFlow = 'product-inquiry';
        this.addMessage(`Hi! Here are our top K Derma Skincare Products:
• Whitening Set
• Acne Care Set
• Hydration Set
• Rejuvenating Set`, true, 800);
        
        setTimeout(() => {
            this.addMessage("Please type the product name to check its availability or price", true, 400);
            setTimeout(() => {
                this.showTextInput();
            }, 700);
        }, 1200);
    }

    handleProductInquiry(product) {
        const productInfo = {
            'whitening': 'Our Whitening Set includes premium ingredients for brightening and evening skin tone. Perfect for achieving that radiant glow!',
            'acne': 'The Acne Care Set is specially formulated to target breakouts and prevent future blemishes. Great for acne-prone skin!',
            'hydration': 'Our Hydration Set provides deep moisture and nourishment for dry and dehydrated skin. Your skin will thank you!',
            'rejuvenating': 'The Rejuvenating Set helps restore youthful appearance and reduces signs of aging. Turn back time!'
        };

        const lowerProduct = product.toLowerCase();
        let response = "I'd be happy to help you with that product! For specific pricing and availability, please contact our clinic directly or book a consultation.";
        
        for (let key in productInfo) {
            if (lowerProduct.includes(key)) {
                response = productInfo[key] + "\n\nFor pricing and availability, please contact our clinic or book a consultation!";
                break;
            }
        }
        
        this.addMessage(response);
        
        setTimeout(() => {
            this.showOptions([
                { text: 'Book a Consultation', icon: 'calendar', action: () => this.showBooking() },
                { text: 'Go back to main menu', icon: 'back', action: () => this.showMainMenu() }
            ]);
        }, 1000);
    }

    showBooking() {
        this.currentFlow = 'booking';
        
        // Check if user is logged in
        @auth('web')
        const isLoggedIn = true;
        @else
        const isLoggedIn = false;
        @endauth
        
        if (!isLoggedIn) {
            this.addMessage(`To book an appointment at K Derma Beauty Clinic, you need to login to your account first! 📅`, true, 500);
            
            setTimeout(() => {
                this.addMessage(`Here's how to book your appointment:

1️⃣ Login to your account
   First, you need to login to access our appointment booking system.

2️⃣ Go to Book Appointment
   Once logged in, navigate to the "Book Appointment" page.

3️⃣ Select your service and preferred date/time
   Choose from our range of treatments and pick a convenient time slot.

4️⃣ Confirm your booking
   Review your details and confirm your appointment.

Don't have an account yet? No problem! You can sign up for free and start booking right away. 🎉`, true, 600);
                
                setTimeout(() => {
                    this.showOptions([
                        { text: 'Login Now', icon: 'calendar', action: () => {
                            window.location.href = '{{ route("login") }}';
                        }},
                        { text: 'Sign Up', icon: 'user', action: () => {
                            window.location.href = '{{ route("register") }}';
                        }},
                        { text: 'View Services', icon: 'services', action: () => this.showServices() },
                        { text: 'Back to Main Menu', icon: 'back', action: () => this.showMainMenu() }
                    ]);
                }, 1200);
            }, 800);
        } else {
            // User is logged in, redirect to booking page
            this.addMessage(`Great! Since you're already logged in, let me take you to our booking system! 📅`, true, 500);
            
            setTimeout(() => {
                window.location.href = '{{ route("appointments.book") }}';
            }, 1500);
        }
    }

    showMainMenu() {
        this.currentFlow = 'start';
        this.addMessage("How else can I help you today?", true, 500);
        
        setTimeout(() => {
            this.showOptions([
                { text: 'Skin Consultation', icon: 'consultation', action: () => this.showSkinConsultation() },
                { text: 'Book an Appointment', icon: 'calendar', action: () => this.showBooking() },
                { text: 'View All Treatments', icon: 'services', action: () => this.showAllTreatments() },
                { text: 'View Aftercare Products', icon: 'products', action: () => this.showAllProducts() },
                { text: 'How to Use Chatbot', icon: 'help', action: () => this.showHelp() }
            ]);
        }, 800);
    }

    showAllTreatments() {
        this.addMessage("To see all our available treatments with prices and descriptions, please type: 'Show all treatments' or 'List all treatments'", true, 500);
        
        setTimeout(() => {
            this.showOptions([
                { text: 'Book an Appointment', icon: 'calendar', action: () => this.showBooking() },
                { text: 'Back to Main Menu', icon: 'back', action: () => this.showMainMenu() }
            ]);
        }, 1000);
    }

    showAllProducts() {
        this.addMessage("To see all our available aftercare products with prices and descriptions, please type: 'Show all aftercare products' or 'List products'", true, 500);
        
        setTimeout(() => {
            this.showOptions([
                { text: 'Book an Appointment', icon: 'calendar', action: () => this.showBooking() },
                { text: 'Back to Main Menu', icon: 'back', action: () => this.showMainMenu() }
            ]);
        }, 1000);
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new KDermaChatbot();
});
</script>
