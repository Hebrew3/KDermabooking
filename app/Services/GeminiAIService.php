<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeminiAIService
{
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Generate a response using Gemini AI
     *
     * @param string $userMessage
     * @param array $context Additional context (conversation history, user info, etc.)
     * @return string|null
     */
    public function generateResponse(string $userMessage, array $context = []): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('Gemini API key is not configured');
            return null;
        }

        try {
            // Build the system prompt with context about K-Derma
            $systemPrompt = $this->buildSystemPrompt($context);
            
            // Build conversation history
            $conversationHistory = $this->buildConversationHistory($context);
            
            // Combine system prompt, history, and current message
            $fullPrompt = $systemPrompt . "\n\n" . $conversationHistory . "\n\nUser: " . $userMessage . "\n\nAssistant:";

            $response = Http::timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $fullPrompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ],
                'safetySettings' => [
                    [
                        'category' => 'HARM_CATEGORY_HARASSMENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_HATE_SPEECH',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                    [
                        'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];
                    return trim($aiResponse);
                }
            }

            Log::error('Gemini API error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Gemini AI service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Build system prompt with K-Derma context
     */
    private function buildSystemPrompt(array $context): string
    {
        $prompt = "You are K Derma Assistant, a friendly and helpful AI assistant for K Derma Beauty Clinic. ";
        $prompt .= "Your role is to help clients with:\n";
        $prompt .= "1. Booking appointments\n";
        $prompt .= "2. Providing information about services and treatments\n";
        $prompt .= "3. Skin consultation and recommendations\n";
        $prompt .= "4. Product inquiries\n";
        $prompt .= "5. General questions about the clinic\n\n";
        
        $prompt .= "Clinic Information:\n";
        $prompt .= "- Operating Hours: Monday - Sunday, 9:00 AM to 7:00 PM\n";
        $prompt .= "- Location: K-Derma Aesthetic Clinic, 123 Beauty Street, Makati City, Metro Manila, Philippines\n\n";
        
        $prompt .= "Services Available:\n";
        $prompt .= "- Complete Facial Treatments: Korean Facial w/ D.P, Celebrity Facial, Acne Facial w/ Acne Laser, Hollywood Facial, Neckcial w/ LED Light, Backcial w/ LED Light\n";
        $prompt .= "- Skin Glow Treatments: BB Glow w/ Blush, Blockdoll Facial, Pico Glow Laser, Fractional CO2 Laser (Face/Back), Immortal Facial\n\n";
        
        $prompt .= "Products Available:\n";
        $prompt .= "- Whitening Set\n";
        $prompt .= "- Acne Care Set\n";
        $prompt .= "- Hydration Set\n";
        $prompt .= "- Rejuvenating Set\n\n";
        
        $prompt .= "Guidelines:\n";
        $prompt .= "- Be friendly, warm, and professional\n";
        $prompt .= "- Use emojis appropriately to make conversations engaging\n";
        $prompt .= "- For booking appointments, guide users to login to their account\n";
        $prompt .= "- Provide helpful skincare advice based on skin types\n";
        $prompt .= "- Keep responses concise but informative\n";
        $prompt .= "- If you don't know something, politely redirect to contacting the clinic\n";
        $prompt .= "- Always maintain a positive and helpful tone\n";
        $prompt .= "- Respond in the same language the user uses (English or Filipino/Tagalog)\n\n";
        
        // Add user context if available
        if (isset($context['user_name'])) {
            $prompt .= "The user's name is: " . $context['user_name'] . "\n";
        }
        
        if (isset($context['user_appointments_count'])) {
            $prompt .= "The user has " . $context['user_appointments_count'] . " appointment(s).\n";
        }

        return $prompt;
    }

    /**
     * Build conversation history for context
     */
    private function buildConversationHistory(array $context): string
    {
        if (empty($context['conversation_history'])) {
            return "";
        }

        $history = "";
        foreach ($context['conversation_history'] as $message) {
            $role = $message['sender_type'] === 'client' ? 'User' : 'Assistant';
            $history .= $role . ": " . $message['message'] . "\n";
        }

        return $history;
    }

    /**
     * Check if Gemini AI is enabled and configured
     */
    public function isEnabled(): bool
    {
        return !empty($this->apiKey) && config('services.gemini.enabled', false);
    }
}

