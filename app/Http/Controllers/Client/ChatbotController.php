<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\User;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    /**
     * Display the chatbot interface.
     */
    public function index()
    {
        $conversation = ChatbotConversation::getOrCreateForClient(Auth::id());
        $messages = $conversation->recentMessages(20);

        // If this is a new conversation, send welcome message
        if ($messages->isEmpty()) {
            $this->sendWelcomeMessage($conversation);
            $messages = $conversation->recentMessages(20);
        }

        return view('client.chatbot.index', compact('conversation', 'messages'));
    }

    /**
     * Send a message to the chatbot.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_id' => 'required|exists:chatbot_conversations,id',
        ]);

        $conversation = ChatbotConversation::findOrFail($request->conversation_id);
        
        // Ensure the conversation belongs to the authenticated user
        if ($conversation->client_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Create client message
        $clientMessage = ChatbotMessage::createClientMessage(
            $conversation->id,
            $request->message
        );

        // Update conversation activity
        $conversation->updateActivity();

        // Generate bot response
        $botResponse = $this->generateBotResponse($request->message, $conversation);

        return response()->json([
            'success' => true,
            'client_message' => [
                'id' => $clientMessage->id,
                'message' => $clientMessage->message,
                'sender_type' => $clientMessage->sender_type,
                'formatted_time' => $clientMessage->formatted_time,
            ],
            'bot_message' => [
                'id' => $botResponse->id,
                'message' => $botResponse->message,
                'sender_type' => $botResponse->sender_type,
                'message_type' => $botResponse->message_type,
                'message_data' => $botResponse->message_data,
                'formatted_time' => $botResponse->formatted_time,
            ],
        ]);
    }

    /**
     * Get conversation messages.
     */
    public function getMessages($conversationId)
    {
        $conversation = ChatbotConversation::findOrFail($conversationId);
        
        if ($conversation->client_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $conversation->recentMessages(50);

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'message_type' => $message->message_type,
                    'message_data' => $message->message_data,
                    'formatted_time' => $message->formatted_time,
                ];
            }),
        ]);
    }

    /**
     * Send welcome message to new conversation.
     */
    private function sendWelcomeMessage($conversation)
    {
        $welcomeMessage = "Hi gorgeous! 💖 Welcome to K Derma Beauty Clinic, your skin's best friend 💆‍♀️✨\n\n" .
            "🕐 **We're open:** Monday - Sunday, 9:00 AM to 7:00 PM\n\n" .
            "Would you like me to help you find the perfect treatment or product for your skin type?\n\n" .
            "Please choose below:\n\n" .
            "1️⃣ Skin Consultation\n\n" .
            "2️⃣ Book an Appointment\n\n" .
            "3️⃣ View Services\n\n" .
            "4️⃣ Product Inquiry";

        $quickReplies = [
            ['text' => '1️⃣ Skin Consultation', 'action' => 'skin_consultation'],
            ['text' => '2️⃣ Book an Appointment', 'action' => 'book_appointment'],
            ['text' => '3️⃣ View Services', 'action' => 'view_services'],
            ['text' => '4️⃣ Product Inquiry', 'action' => 'product_inquiry'],
        ];

        ChatbotMessage::createBotMessage(
            $conversation->id,
            $welcomeMessage,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Generate bot response based on user message.
     */
    private function generateBotResponse($userMessage, $conversation)
    {
        $message = strtolower(trim($userMessage));
        $client = $conversation->client;

        // Analyze intent and generate appropriate response
        // Check for main menu options first
        if ($this->containsKeywords($message, ['main_menu', 'main menu', 'go back', 'back to main', 'home', 'start over'])) {
            return $this->handleGreeting($conversation);
        } elseif ($this->containsKeywords($message, ['1', 'skin consultation', 'consultation', 'skin type', 'skin_consultation'])) {
            return $this->handleSkinConsultation($conversation);
        } elseif ($this->containsKeywords($message, ['2', 'book', 'appointment', 'schedule', 'reserve', 'book_appointment'])) {
            return $this->handleBookingFlow($conversation);
        } elseif ($this->containsKeywords($message, ['3', 'view services', 'services', 'treatments', 'all services', 'all treatments', 'view_services'])) {
            return $this->handleViewServices($conversation);
        } elseif ($this->containsKeywords($message, ['4', 'product', 'products', 'product inquiry', 'inquiry', 'product_inquiry'])) {
            return $this->handleProductInquiry($conversation);
        } elseif ($this->containsKeywords($message, ['whitening set', 'whitening', 'product_whitening'])) {
            return $this->handleProductDetails($conversation, 'whitening');
        } elseif ($this->containsKeywords($message, ['acne care set', 'acne care', 'product_acne'])) {
            return $this->handleProductDetails($conversation, 'acne');
        } elseif ($this->containsKeywords($message, ['hydration set', 'hydration', 'product_hydration'])) {
            return $this->handleProductDetails($conversation, 'hydration');
        } elseif ($this->containsKeywords($message, ['rejuvenating set', 'rejuvenating', 'product_rejuvenating'])) {
            return $this->handleProductDetails($conversation, 'rejuvenating');
        } elseif ($this->containsKeywords($message, ['oily skin', 'oily', 'oily_skin'])) {
            return $this->handleOilySkin($conversation);
        } elseif ($this->containsKeywords($message, ['dry skin', 'dry', 'dry_skin'])) {
            return $this->handleDrySkin($conversation);
        } elseif ($this->containsKeywords($message, ['combination skin', 'combination', 'combination_skin'])) {
            return $this->handleCombinationSkin($conversation);
        } elseif ($this->containsKeywords($message, ['sensitive skin', 'sensitive', 'sensitive_skin'])) {
            return $this->handleSensitiveSkin($conversation);
        } elseif ($this->containsKeywords($message, ['acne-prone', 'acne prone', 'acne', 'acne_prone_skin'])) {
            return $this->handleAcneProneSkin($conversation);
        } elseif ($this->containsKeywords($message, ['thank', 'thanks', 'thank you', 'bye', 'goodbye'])) {
            return $this->handleThankYou($conversation);
        } elseif ($this->containsKeywords($message, ['how to use', 'how do i use', 'help', 'how does this work', 'chatbot help', 'how to use chatbot'])) {
            return $this->handleChatbotHelp($conversation);
        } elseif ($this->containsKeywords($message, ['aftercare', 'aftercare products', 'all products', 'list products', 'show products'])) {
            return $this->handleAllProducts($conversation);
        } elseif ($this->containsKeywords($message, ['price', 'cost', 'how much', 'pricing'])) {
            return $this->handlePricingInquiry($conversation, $message);
        } elseif ($this->containsKeywords($message, ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'start', 'menu'])) {
            return $this->handleGreeting($conversation);
        } elseif ($this->containsKeywords($message, ['hours', 'open', 'time', 'when', 'closing', 'closing time', 'opening', 'opening time', 'what time', 'what days'])) {
            return $this->handleBusinessHours($conversation);
        } elseif ($this->containsKeywords($message, ['location', 'address', 'where'])) {
            return $this->handleLocationInquiry($conversation);
        } else {
            return $this->handleGeneralInquiry($conversation, $message);
        }
    }

    /**
     * Check if message contains specific keywords.
     */
    private function containsKeywords($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($message, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Handle chatbot help/how to use inquiries.
     */
    private function handleChatbotHelp($conversation)
    {
        $response = "Here's how to use the K Derma Chatbot: 📖\n\n";
        $response .= "1️⃣ **Finding the Chatbot**\n";
        $response .= "   • Look for the chatbot icon in the bottom-right corner of the page\n";
        $response .= "   • Click on it to open the chat window\n\n";
        $response .= "2️⃣ **Getting Started**\n";
        $response .= "   • The chatbot will greet you with a welcome message\n";
        $response .= "   • You can select from the available options or type your question\n\n";
        $response .= "3️⃣ **What I Can Help You With:**\n";
        $response .= "   📅 **Booking Appointments** - Guide you through the booking process\n";
        $response .= "   🌟 **Service Inquiries** - Provide information about available services\n";
        $response .= "   💰 **Pricing Information** - Answer questions about service prices\n";
        $response .= "   💆 **Skin Consultation** - Provide basic skin care advice\n";
        $response .= "   🛍️ **Product Inquiries** - Information about products and treatments\n";
        $response .= "   🕐 **Business Hours & Location** - Clinic operating hours and address\n\n";
        $response .= "4️⃣ **Booking an Appointment:**\n";
        $response .= "   • First, you need to login to your account\n";
        $response .= "   • Select \"Book an Appointment\" option from the chatbot or main menu\n";
        $response .= "   • You'll be redirected to our booking system\n";
        $response .= "   • Select your service, date, time, and staff\n";
        $response .= "   • Confirm your booking\n\n";
        $response .= "5️⃣ **Closing the Chatbot:**\n";
        $response .= "   • Click the close button (X) or icon to close the chat window\n";
        $response .= "   • You can reopen it anytime by clicking the chatbot icon again\n\n";
        $response .= "Need help with something specific? Just ask me! 😊";

        $quickReplies = [
            ['text' => 'Book an Appointment', 'action' => 'book_appointment'],
            ['text' => 'View Services', 'action' => 'view_services'],
            ['text' => 'How to Book', 'action' => 'how_to_book'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'text',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle "how to book" inquiries.
     */
    private function handleHowToBook($conversation)
    {
        $loginUrl = route('login');
        
        $response = "To book an appointment at K Derma Beauty Clinic, please follow these steps: 📅\n\n";
        $response .= "1️⃣ **Login to your account**\n";
        $response .= "   First, you need to login to access the appointment booking system.\n\n";
        $response .= "2️⃣ **Go to Book Appointment**\n";
        $response .= "   Once logged in, you can book your appointment through our booking system.\n\n";
        $response .= "3️⃣ **Select your service and preferred date/time**\n";
        $response .= "   Choose from our range of treatments and pick a convenient time slot.\n\n";
        $response .= "4️⃣ **Confirm your booking**\n";
        $response .= "   Review your details and confirm your appointment.\n\n";
        $response .= "**Don't have an account yet?** No problem! You can sign up for free and start booking right away. 🎉\n\n";
        $response .= "Would you like me to help you with anything else?";

        $quickReplies = [
            ['text' => 'Login Now', 'action' => 'login', 'url' => $loginUrl],
            ['text' => 'Sign Up', 'action' => 'signup', 'url' => route('register')],
            ['text' => 'View Services', 'action' => 'view_services'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle appointment-related inquiries.
     */
    private function handleAppointmentInquiry($conversation, $message)
    {
        $client = $conversation->client;
        $upcomingAppointments = $client->clientAppointments()
            ->upcoming()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $response = "I'd be happy to help you with appointment scheduling! 📅\n\n";
        
        if ($upcomingAppointments > 0) {
            $response .= "I see you have {$upcomingAppointments} upcoming appointment(s). ";
        }

        $response .= "You can:\n" .
            "• Book a new appointment\n" .
            "• View your existing appointments\n" .
            "• Reschedule or cancel appointments\n\n" .
            "Would you like me to show you our available services or help you book directly?";

        $quickReplies = [
            ['text' => 'View Services', 'action' => 'view_services'],
            ['text' => 'Book Now', 'action' => 'book_appointment'],
            ['text' => 'My Appointments', 'action' => 'view_appointments'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle treatment/service inquiries.
     */
    private function handleTreatmentInquiry($conversation, $message)
    {
        // Get popular services
        $popularServices = Service::active()
            ->withCount(['appointments' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('appointments_count', 'desc')
            ->limit(3)
            ->get();

        $response = "We offer a wide range of professional skincare treatments! ✨\n\n";
        $response .= "Here are our most popular services:\n\n";

        $serviceData = [];
        foreach ($popularServices as $service) {
            $response .= "🌟 **{$service->name}**\n";
            $response .= "   {$service->formatted_price} • {$service->formatted_duration}\n";
            $response .= "   {$service->short_description}\n\n";
            
            $serviceData[] = [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->formatted_price,
                'duration' => $service->formatted_duration,
                'description' => $service->short_description,
            ];
        }

        $response .= "Would you like to learn more about any of these treatments or see our full service menu?";

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'recommendation',
            [
                'services' => $serviceData,
                'quick_replies' => [
                    ['text' => 'All Services', 'action' => 'view_all_services'],
                    ['text' => 'Get Recommendation', 'action' => 'get_recommendation'],
                ]
            ]
        );
    }

    /**
     * Handle pricing inquiries.
     */
    private function handlePricingInquiry($conversation, $message)
    {
        $services = Service::active()->orderBy('price')->get();
        
        $response = "Here's our pricing information: 💰\n\n";
        
        $priceRanges = [
            'Basic Treatments' => $services->where('price', '<=', 2000),
            'Premium Treatments' => $services->where('price', '>', 2000)->where('price', '<=', 5000),
            'Luxury Treatments' => $services->where('price', '>', 5000),
        ];

        foreach ($priceRanges as $category => $categoryServices) {
            if ($categoryServices->count() > 0) {
                $response .= "**{$category}:**\n";
                foreach ($categoryServices->take(3) as $service) {
                    $response .= "• {$service->name}: {$service->formatted_price}\n";
                }
                $response .= "\n";
            }
        }

        $response .= "Prices may vary based on specific needs. Would you like to book a consultation for a personalized quote?";

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'text',
            [
                'quick_replies' => [
                    ['text' => 'Book Consultation', 'action' => 'book_consultation'],
                    ['text' => 'View All Prices', 'action' => 'view_services'],
                ]
            ]
        );
    }

    /**
     * Handle recommendation requests.
     */
    private function handleRecommendationRequest($conversation, $message)
    {
        $client = $conversation->client;
        
        // Get client's appointment history for personalized recommendations
        $pastAppointments = $client->clientAppointments()
            ->with('service')
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->limit(5)
            ->get();

        $response = "I'd love to help you find the perfect treatment! 💫\n\n";

        if ($pastAppointments->count() > 0) {
            $response .= "Based on your previous appointments, I can see you've enjoyed:\n";
            foreach ($pastAppointments->take(2) as $appointment) {
                $response .= "• {$appointment->service->name}\n";
            }
            $response .= "\n";
        }

        $response .= "To give you the best recommendations, could you tell me:\n\n";
        $response .= "🎯 What are your main skin concerns?\n";
        $response .= "⏰ How much time do you have for treatment?\n";
        $response .= "💰 What's your budget range?\n\n";
        $response .= "Or I can show you treatments based on popular choices for your age group!";

        $quickReplies = [
            ['text' => 'Acne Treatment', 'action' => 'recommend_acne'],
            ['text' => 'Anti-Aging', 'action' => 'recommend_antiaging'],
            ['text' => 'Skin Brightening', 'action' => 'recommend_brightening'],
            ['text' => 'General Maintenance', 'action' => 'recommend_maintenance'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'recommendation',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle greeting messages.
     */
    private function handleGreeting($conversation)
    {
        $response = "Hi gorgeous! 💖 Welcome to K Derma Beauty Clinic, your skin's best friend 💆‍♀️✨\n\n" .
            "🕐 **We're open:** Monday - Sunday, 9:00 AM to 7:00 PM\n\n" .
            "Would you like me to help you find the perfect treatment or product for your skin type?\n\n" .
            "Please choose below:\n\n" .
            "1️⃣ Skin Consultation\n\n" .
            "2️⃣ Book an Appointment\n\n" .
            "3️⃣ View Services\n\n" .
            "4️⃣ Product Inquiry";

        $quickReplies = [
            ['text' => '1️⃣ Skin Consultation', 'action' => 'skin_consultation'],
            ['text' => '2️⃣ Book an Appointment', 'action' => 'book_appointment'],
            ['text' => '3️⃣ View Services', 'action' => 'view_services'],
            ['text' => '4️⃣ Product Inquiry', 'action' => 'product_inquiry'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle business hours inquiry.
     */
    private function handleBusinessHours($conversation)
    {
        $response = "K-Derma Clinic is open: 🕐\n\n";
        $response .= "**Operating Hours:** 9:00 AM to 7:00 PM\n";
        $response .= "**Days Open:** Monday - Sunday (7 days a week)\n\n";
        $response .= "We're open every day from 9:00 AM to 7:00 PM to serve you better! Would you like to book an appointment?";

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'text',
            [
                'quick_replies' => [
                    ['text' => 'Book Appointment', 'action' => 'book_appointment'],
                    ['text' => 'View Services', 'action' => 'view_services'],
                ]
            ]
        );
    }

    /**
     * Handle name and contact inquiries.
     */
    private function handleNameContactInquiry($conversation, $message)
    {
        $response = "I'd be happy to help you! However, to book an appointment and provide your full name and contact number, you'll need to login to your account first. 📝\n\n";
        $response .= "**If you want to book your desired treatment, please login to your account.**\n\n";
        $response .= "Once you're logged in, you can:\n";
        $response .= "• Book appointments easily\n";
        $response .= "• Select your preferred service and time\n";
        $response .= "• Manage your appointments\n\n";
        $response .= "Don't have an account yet? Sign up for free! 🎉";

        $loginUrl = route('login');
        $registerUrl = route('register');

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'text',
            [
                'quick_replies' => [
                    ['text' => 'Login Now', 'action' => 'login', 'url' => $loginUrl],
                    ['text' => 'Sign Up', 'action' => 'signup', 'url' => $registerUrl],
                    ['text' => 'View Services', 'action' => 'view_services'],
                ]
            ]
        );
    }

    /**
     * Handle all treatments/services inquiry.
     */
    private function handleAllTreatments($conversation)
    {
        return $this->handleViewServices($conversation);
    }

    /**
     * Handle all aftercare products inquiry.
     */
    private function handleAllProducts($conversation)
    {
        $products = InventoryItem::where('category', 'Aftercare Products')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($products->isEmpty()) {
            $response = "I'm sorry, but we don't have any aftercare products available at the moment. Please check back later!";
        } else {
            $response = "Here are all our available aftercare products: 🛍️\n\n";
            
            foreach ($products as $product) {
                $response .= "**{$product->name}**\n";
                if ($product->selling_price) {
                    $response .= "💰 Price: ₱" . number_format($product->selling_price, 2) . "\n";
                } else {
                    $response .= "💰 Price: Contact us for pricing\n";
                }
                if ($product->description) {
                    $description = strlen($product->description) > 100 
                        ? substr($product->description, 0, 100) . '...' 
                        : $product->description;
                    $response .= "📝 {$description}\n";
                }
                $response .= "\n";
            }
            
            $response .= "These products are perfect for maintaining your skin after treatment!";
        }

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'text',
            [
                'quick_replies' => [
                    ['text' => 'Book Appointment', 'action' => 'book_appointment'],
                    ['text' => 'View Treatments', 'action' => 'view_treatments'],
                ]
            ]
        );
    }

    /**
     * Handle location inquiry.
     */
    private function handleLocationInquiry($conversation)
    {
        $response = "You can find us at: 📍\n\n";
        $response .= "**K-Derma Aesthetic Clinic**\n";
        $response .= "123 Beauty Street, Makati City\n";
        $response .= "Metro Manila, Philippines\n\n";
        $response .= "We're easily accessible by public transport and have parking available. Need directions or want to book an appointment?";

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'text',
            [
                'quick_replies' => [
                    ['text' => 'Get Directions', 'action' => 'get_directions'],
                    ['text' => 'Book Appointment', 'action' => 'book_appointment'],
                ]
            ]
        );
    }

    /**
     * Handle general inquiries.
     */
    private function handleGeneralInquiry($conversation, $message)
    {
        $response = "Hi gorgeous! 💖 Welcome to K Derma Beauty Clinic, your skin's best friend 💆‍♀️✨\n\n" .
            "🕐 **We're open:** Monday - Sunday, 9:00 AM to 7:00 PM\n\n" .
            "Would you like me to help you find the perfect treatment or product for your skin type?\n\n" .
            "Please choose below:\n\n" .
            "1️⃣ Skin Consultation\n\n" .
            "2️⃣ Book an Appointment\n\n" .
            "3️⃣ View Services\n\n" .
            "4️⃣ Product Inquiry";

        $quickReplies = [
            ['text' => '1️⃣ Skin Consultation', 'action' => 'skin_consultation'],
            ['text' => '2️⃣ Book an Appointment', 'action' => 'book_appointment'],
            ['text' => '3️⃣ View Services', 'action' => 'view_services'],
            ['text' => '4️⃣ Product Inquiry', 'action' => 'product_inquiry'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Skin Consultation flow.
     */
    private function handleSkinConsultation($conversation)
    {
        $response = "Let's find what's best for your skin! 💆‍♀️\n\n" .
            "Please tell me your skin type (or choose below):\n\n" .
            "👩‍🦰 1. Oily Skin\n\n" .
            "👩‍🦱 2. Dry Skin\n\n" .
            "👩‍🦳 3. Combination Skin\n\n" .
            "👩 4. Sensitive Skin\n\n" .
            "👨‍🦰 5. Acne-Prone Skin";

        $quickReplies = [
            ['text' => '👩‍🦰 Oily Skin', 'action' => 'oily_skin'],
            ['text' => '👩‍🦱 Dry Skin', 'action' => 'dry_skin'],
            ['text' => '👩‍🦳 Combination Skin', 'action' => 'combination_skin'],
            ['text' => '👩 Sensitive Skin', 'action' => 'sensitive_skin'],
            ['text' => '👨‍🦰 Acne-Prone Skin', 'action' => 'acne_prone_skin'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Oily Skin recommendations.
     */
    private function handleOilySkin($conversation)
    {
        $response = "For oily skin, we recommend:\n\n" .
            "💆‍♀️ **Korean Facial w/ D.P** – deeply cleanses pores and helps control excess oil.\n\n" .
            "💧 **Celebrity Facial** – gives an instant refreshed, matte glow.\n\n" .
            "🧴 **Product suggestion:** Oil Control Toner and Mattifying Moisturizer.\n\n" .
            "Would you like to:\n\n" .
            "✅ Book a session\n\n" .
            "🛍️ Inquire about products\n\n" .
            "🔙 Go back to main menu";

        $quickReplies = [
            ['text' => '✅ Book a session', 'action' => 'book_appointment'],
            ['text' => '🛍️ Inquire about products', 'action' => 'product_inquiry'],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Dry Skin recommendations.
     */
    private function handleDrySkin($conversation)
    {
        $response = "For dry skin, hydration is key 💧\n\n" .
            "We recommend:\n\n" .
            "💆‍♀️ **Neckcial w/ LED Light** – soothes and nourishes the skin on your neck area.\n\n" .
            "💫 **Immortal Facial** – promotes deep hydration and youthful glow.\n\n" .
            "🧴 **Product suggestion:** Hydrating Serum and Moisture Lock Cream.\n\n" .
            "Would you like to:\n\n" .
            "✅ Book a session\n\n" .
            "🛍️ Inquire about products\n\n" .
            "🔙 Go back to main menu";

        $quickReplies = [
            ['text' => '✅ Book a session', 'action' => 'book_appointment'],
            ['text' => '🛍️ Inquire about products', 'action' => 'product_inquiry'],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Combination Skin recommendations.
     */
    private function handleCombinationSkin($conversation)
    {
        $response = "Combination skin needs balance 🌸\n\n" .
            "Try:\n\n" .
            "💆‍♀️ **Hollywood Facial** – revitalizes dull areas while controlling oily zones.\n\n" .
            "💧 **Blockdoll Facial** – brightens and smoothens skin tone.\n\n" .
            "🧴 **Product suggestion:** Balancing Toner and Light Hydrating Gel Cream.\n\n" .
            "Would you like to:\n\n" .
            "✅ Book a session\n\n" .
            "🛍️ Inquire about products\n\n" .
            "🔙 Go back to main menu";

        $quickReplies = [
            ['text' => '✅ Book a session', 'action' => 'book_appointment'],
            ['text' => '🛍️ Inquire about products', 'action' => 'product_inquiry'],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Sensitive Skin recommendations.
     */
    private function handleSensitiveSkin($conversation)
    {
        $response = "For sensitive skin, gentle and soothing care is a must 💕\n\n" .
            "We suggest:\n\n" .
            "💆‍♀️ **Backcial w/ LED Light** – gentle deep cleanse and LED calming therapy for your back area.\n\n" .
            "💫 **BB Glow w/ Blush** – evens out skin tone while being safe for sensitive skin.\n\n" .
            "🧴 **Product suggestion:** Gentle Cleanser and Calming Serum.\n\n" .
            "Would you like to:\n\n" .
            "✅ Book a session\n\n" .
            "🛍️ Inquire about products\n\n" .
            "🔙 Go back to main menu";

        $quickReplies = [
            ['text' => '✅ Book a session', 'action' => 'book_appointment'],
            ['text' => '🛍️ Inquire about products', 'action' => 'product_inquiry'],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Acne-Prone Skin recommendations.
     */
    private function handleAcneProneSkin($conversation)
    {
        $response = "For acne-prone skin, we've got treatments that target breakouts effectively 💪\n\n" .
            "We recommend:\n\n" .
            "💆‍♀️ **Acne Facial w/ Acne Laser** – clears clogged pores and reduces active pimples.\n\n" .
            "💡 **Pico Glow Laser** – minimizes acne marks and improves skin texture.\n\n" .
            "🧴 **Product suggestion:** Acne Defense Toner and Clarifying Gel Cleanser.\n\n" .
            "Would you like to:\n\n" .
            "✅ Book a session\n\n" .
            "🛍️ Inquire about products\n\n" .
            "🔙 Go back to main menu";

        $quickReplies = [
            ['text' => '✅ Book a session', 'action' => 'book_appointment'],
            ['text' => '🛍️ Inquire about products', 'action' => 'product_inquiry'],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle View Services flow.
     */
    private function handleViewServices($conversation)
    {
        $response = "Our current Facial Services include:\n\n" .
            "✨ **Complete Facial Treatments:**\n\n" .
            "• Korean Facial w/ D.P\n" .
            "• Celebrity Facial\n" .
            "• Acne Facial w/ Acne Laser\n" .
            "• Hollywood Facial\n" .
            "• Neckcial w/ LED Light\n" .
            "• Backcial w/ LED Light\n\n" .
            "🌟 **Skin Glow w/ Facial & LED Light Treatments:**\n\n" .
            "• BB Glow w/ Blush\n" .
            "• Blockdoll Facial\n" .
            "• Pico Glow Laser\n" .
            "• Fractional CO2 Laser (Face)\n" .
            "• Fractional CO2 Laser (Back)\n" .
            "• Immortal Facial\n\n" .
            "Would you like to book a session or know more about a specific service?";

        $quickReplies = [
            ['text' => 'Book a session', 'action' => 'book_appointment'],
            ['text' => 'Know more about a service', 'action' => 'view_services'],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Product Inquiry flow.
     */
    private function handleProductInquiry($conversation)
    {
        $response = "Hi po! 🧴 Here are our top K Derma Skincare Products:\n\n" .
            "• Whitening Set\n" .
            "• Acne Care Set\n" .
            "• Hydration Set\n" .
            "• Rejuvenating Set\n\n" .
            "Please type the product name to check its availability or price 💕";

        $quickReplies = [
            ['text' => 'Whitening Set', 'action' => 'product_whitening'],
            ['text' => 'Acne Care Set', 'action' => 'product_acne'],
            ['text' => 'Hydration Set', 'action' => 'product_hydration'],
            ['text' => 'Rejuvenating Set', 'action' => 'product_rejuvenating'],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Booking flow.
     */
    private function handleBookingFlow($conversation)
    {
        $response = "To book your appointment at K Derma Beauty Clinic, please provide:\n\n" .
            "• Full name\n" .
            "• Contact number\n" .
            "• Preferred date and time\n" .
            "• Service/treatment you'd like to avail 💆‍♀️\n\n" .
            "We'll confirm your slot shortly!";

        $loginUrl = route('login');
        $registerUrl = route('register');

        $quickReplies = [
            ['text' => 'Login to Book', 'action' => 'login', 'url' => $loginUrl],
            ['text' => 'Sign Up', 'action' => 'signup', 'url' => $registerUrl],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Product Details.
     */
    private function handleProductDetails($conversation, $productType)
    {
        $productInfo = [
            'whitening' => [
                'name' => 'Whitening Set',
                'description' => 'Our Whitening Set includes premium ingredients for brightening and evening skin tone. Perfect for achieving that radiant glow! ✨',
            ],
            'acne' => [
                'name' => 'Acne Care Set',
                'description' => 'The Acne Care Set is specially formulated to target breakouts and prevent future blemishes. Great for acne-prone skin! 💪',
            ],
            'hydration' => [
                'name' => 'Hydration Set',
                'description' => 'Our Hydration Set provides deep moisture and nourishment for dry and dehydrated skin. Your skin will thank you! 💧',
            ],
            'rejuvenating' => [
                'name' => 'Rejuvenating Set',
                'description' => 'The Rejuvenating Set helps restore youthful appearance and reduces signs of aging. Turn back time! ⏰',
            ],
        ];

        $product = $productInfo[$productType] ?? null;
        
        if (!$product) {
            return $this->handleProductInquiry($conversation);
        }

        $response = "**{$product['name']}** 🧴\n\n" .
            "{$product['description']}\n\n" .
            "For pricing and availability, please contact our clinic or book a consultation!";

        $quickReplies = [
            ['text' => 'Book a Consultation', 'action' => 'book_appointment'],
            ['text' => 'View Other Products', 'action' => 'product_inquiry'],
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }

    /**
     * Handle Thank You / End message.
     */
    private function handleThankYou($conversation)
    {
        $response = "Thank you for chatting with K Derma Beauty Clinic 💖\n\n" .
            "We can't wait to make your skin glow and confident ✨\n\n" .
            "You can message us anytime for updates, promos, or follow-ups!";

        $quickReplies = [
            ['text' => '🔙 Go back to main menu', 'action' => 'main_menu'],
            ['text' => 'Book Appointment', 'action' => 'book_appointment'],
        ];

        return ChatbotMessage::createBotMessage(
            $conversation->id,
            $response,
            'quick_reply',
            ['quick_replies' => $quickReplies]
        );
    }
}
