<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleChatService
{
    private $webhookUrl;
    private $enabled;

    public function __construct()
    {
        $this->webhookUrl = config('services.google_chat.webhook_url');
        $this->enabled = config('services.google_chat.enabled', false);
    }

    /**
     * Send a notification to Google Chat when a new message is received.
     */
    public function notifyNewMessage($message, $conversation, $recipient)
    {
        if (!$this->enabled || !$this->webhookUrl) {
            return false;
        }

        try {
            $sender = $message->sender;
            $appointment = $conversation->appointment;

            $card = [
                'cards' => [
                    [
                        'header' => [
                            'title' => 'New Chat Message',
                            'subtitle' => 'K-Derma Booking System',
                            'imageUrl' => asset('images/logo1.jpg'),
                        ],
                        'sections' => [
                            [
                                'widgets' => [
                                    [
                                        'keyValue' => [
                                            'topLabel' => 'From',
                                            'content' => $sender->name,
                                        ],
                                    ],
                                    [
                                        'keyValue' => [
                                            'topLabel' => 'Appointment',
                                            'content' => $appointment->appointment_number,
                                        ],
                                    ],
                                    [
                                        'keyValue' => [
                                            'topLabel' => 'Service',
                                            'content' => $appointment->service->name,
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'widgets' => [
                                    [
                                        'textParagraph' => [
                                            'text' => '<b>Message:</b><br/>' . htmlspecialchars($message->message),
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'widgets' => [
                                    [
                                        'buttons' => [
                                            [
                                                'textButton' => [
                                                    'text' => 'View Chat',
                                                    'onClick' => [
                                                        'openLink' => [
                                                            'url' => route('admin.appointments.show', $appointment->id),
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::post($this->webhookUrl, $card);

            if ($response->successful()) {
                Log::info('Google Chat notification sent successfully', [
                    'message_id' => $message->id,
                    'conversation_id' => $conversation->id,
                ]);
                return true;
            } else {
                Log::warning('Failed to send Google Chat notification', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error sending Google Chat notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Send a notification when an appointment is confirmed.
     */
    public function notifyAppointmentConfirmed($appointment)
    {
        if (!$this->enabled || !$this->webhookUrl) {
            return false;
        }

        try {
            $card = [
                'cards' => [
                    [
                        'header' => [
                            'title' => 'Appointment Confirmed',
                            'subtitle' => 'K-Derma Booking System',
                            'imageUrl' => asset('images/logo1.jpg'),
                        ],
                        'sections' => [
                            [
                                'widgets' => [
                                    [
                                        'keyValue' => [
                                            'topLabel' => 'Appointment Number',
                                            'content' => $appointment->appointment_number,
                                        ],
                                    ],
                                    [
                                        'keyValue' => [
                                            'topLabel' => 'Client',
                                            'content' => $appointment->client->name,
                                        ],
                                    ],
                                    [
                                        'keyValue' => [
                                            'topLabel' => 'Service',
                                            'content' => $appointment->service->name,
                                        ],
                                    ],
                                    [
                                        'keyValue' => [
                                            'topLabel' => 'Date & Time',
                                            'content' => $appointment->formatted_date_time,
                                        ],
                                    ],
                                ],
                            ],
                            [
                                'widgets' => [
                                    [
                                        'buttons' => [
                                            [
                                                'textButton' => [
                                                    'text' => 'View Appointment',
                                                    'onClick' => [
                                                        'openLink' => [
                                                            'url' => route('admin.appointments.show', $appointment->id),
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::post($this->webhookUrl, $card);

            if ($response->successful()) {
                Log::info('Google Chat appointment confirmation notification sent');
                return true;
            } else {
                Log::warning('Failed to send Google Chat appointment confirmation', [
                    'status' => $response->status(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error sending Google Chat appointment confirmation', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

