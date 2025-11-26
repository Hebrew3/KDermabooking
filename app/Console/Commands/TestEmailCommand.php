<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PHPMailerService;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email : The email address to send test email to}';
    protected $description = 'Test email configuration by sending a test email';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Testing email configuration...');
        
        try {
            $phpMailerService = new PHPMailerService();
            
            $success = $phpMailerService->sendCustomEmail(
                $email,
                'K-Derma Email Test',
                $this->getTestEmailTemplate(),
                $this->getTestEmailTextTemplate()
            );
            
            if ($success) {
                $this->info("✅ Test email sent successfully to: {$email}");
                $this->info('Please check your inbox (and spam folder) for the test email.');
            } else {
                $this->error("❌ Failed to send test email to: {$email}");
                $this->error('Please check your email configuration and logs.');
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error('Please check your email configuration in .env file.');
        }
    }
    
    private function getTestEmailTemplate()
    {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Email Test</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f8f9fa;
                }
                .container {
                    background-color: #ffffff;
                    border-radius: 12px;
                    padding: 40px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    text-align: center;
                }
                .logo {
                    width: 60px;
                    height: 60px;
                    background-color: #ec4899;
                    border-radius: 50%;
                    margin: 0 auto 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 24px;
                    font-weight: bold;
                }
                h1 {
                    color: #1f2937;
                    margin: 0 0 20px 0;
                }
                .success {
                    background-color: #d1fae5;
                    border: 1px solid #10b981;
                    border-radius: 6px;
                    padding: 20px;
                    margin: 20px 0;
                    color: #065f46;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='logo'>K</div>
                <h1>Email Configuration Test</h1>
                
                <div class='success'>
                    <h3>✅ Success!</h3>
                    <p>Your email configuration is working correctly. The K-Derma Booking System can now send emails using PHPMailer.</p>
                </div>
                
                <p>This test email confirms that:</p>
                <ul style='text-align: left; display: inline-block;'>
                    <li>SMTP connection is established</li>
                    <li>Authentication is successful</li>
                    <li>Email delivery is working</li>
                    <li>HTML templates are rendering correctly</li>
                </ul>
                
                <p><strong>Your forgot password feature is now ready to use!</strong></p>
                
                <hr style='margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;'>
                <p style='color: #6b7280; font-size: 14px;'>
                    © " . date('Y') . " K-Derma Booking System<br>
                    This is an automated test message.
                </p>
            </div>
        </body>
        </html>
        ";
    }
    
    private function getTestEmailTextTemplate()
    {
        return "
K-Derma Booking System - Email Configuration Test

✅ Success!

Your email configuration is working correctly. The K-Derma Booking System can now send emails using PHPMailer.

This test email confirms that:
- SMTP connection is established
- Authentication is successful  
- Email delivery is working
- Email templates are working correctly

Your forgot password feature is now ready to use!

© " . date('Y') . " K-Derma Booking System
This is an automated test message.
        ";
    }
}
