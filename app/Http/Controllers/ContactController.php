<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\PHPMailerService;

class ContactController extends Controller
{
    protected $phpMailerService;

    public function __construct(PHPMailerService $phpMailerService)
    {
        $this->phpMailerService = $phpMailerService;
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill in all required fields correctly.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'subject' => 'New Contact Form Submission - K-Derma',
        ];

        // Create HTML email content
        $htmlBody = $this->getContactEmailTemplate($data);
        $textBody = $this->getContactTextTemplate($data);

        try {
            // Use PHPMailer service to send email
            $emailSent = $this->phpMailerService->sendCustomEmail(
                'deasisd82@gmail.com',
                $data['subject'],
                $htmlBody,
                $textBody,
                'K-Derma Contact'
            );

            // Log the contact form submission for backup
            Log::info('Contact Form Submission', [
                'name' => $data['name'],
                'email' => $data['email'],
                'message' => $data['message'],
                'timestamp' => now(),
                'email_sent' => $emailSent
            ]);

            if ($emailSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your message! We will get back to you soon.'
                ]);
            } else {
                // Email failed but still log the message
                Log::info('Contact Form Submission (Email Failed)', [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'message' => $data['message'],
                    'timestamp' => now(),
                    'note' => 'Email sending failed, but message was logged'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your message! We have received your inquiry and will get back to you soon.'
                ]);
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Contact Form Error: ' . $e->getMessage(), [
                'name' => $data['name'],
                'email' => $data['email'],
                'message' => $data['message'],
                'error' => $e->getMessage()
            ]);

            // Still log the contact form submission even if email fails
            Log::info('Contact Form Submission (Exception)', [
                'name' => $data['name'],
                'email' => $data['email'],
                'message' => $data['message'],
                'timestamp' => now(),
                'note' => 'Exception occurred, but message was logged'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We have received your inquiry and will get back to you soon.'
            ]);
        }
    }

    private function getContactEmailTemplate($data)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <title>New Contact Form Submission - K-Derma</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background: linear-gradient(135deg, #ec4899, #f43f5e);
                    color: white;
                    padding: 20px;
                    border-radius: 10px 10px 0 0;
                    text-align: center;
                }
                .content {
                    background: #f8f9fa;
                    padding: 30px;
                    border-radius: 0 0 10px 10px;
                }
                .field {
                    margin-bottom: 20px;
                }
                .field-label {
                    font-weight: bold;
                    color: #ec4899;
                    margin-bottom: 5px;
                }
                .field-value {
                    background: white;
                    padding: 15px;
                    border-radius: 5px;
                    border-left: 4px solid #ec4899;
                }
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    color: #666;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>K-Derma Contact Form</h1>
                <p>New message received from website</p>
            </div>
            
            <div class='content'>
                <div class='field'>
                    <div class='field-label'>Name:</div>
                    <div class='field-value'>{$data['name']}</div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>Email:</div>
                    <div class='field-value'>{$data['email']}</div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>Message:</div>
                    <div class='field-value'>{$data['message']}</div>
                </div>
            </div>
            
            <div class='footer'>
                <p>This message was sent from the K-Derma website contact form.</p>
                <p>Please respond directly to: {$data['email']}</p>
            </div>
        </body>
        </html>
        ";
    }

    private function getContactTextTemplate($data)
    {
        return "
K-DERMA CONTACT FORM
New message received from website

Name: {$data['name']}
Email: {$data['email']}
Message: {$data['message']}

---
This message was sent from the K-Derma website contact form.
Please respond directly to: {$data['email']}
        ";
    }
}
