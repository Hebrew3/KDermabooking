<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class PHPMailerService
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configureSMTP();
    }

    private function configureSMTP()
    {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = config('mail.mailers.smtp.host', 'smtp.gmail.com');
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = config('mail.mailers.smtp.username');
            $this->mail->Password   = config('mail.mailers.smtp.password');
            $this->mail->SMTPSecure = config('mail.mailers.smtp.encryption', PHPMailer::ENCRYPTION_STARTTLS);
            $this->mail->Port       = config('mail.mailers.smtp.port', 587);

            // Content settings
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
            
            // From address
            $this->mail->setFrom(
                config('mail.from.address', 'noreply@kdermabooking.com'),
                config('mail.from.name', 'K-Derma Booking System')
            );

        } catch (Exception $e) {
            Log::error('PHPMailer configuration error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendPasswordResetEmail($email, $token, $userName = null)
    {
        try {
            // Recipients
            $this->mail->addAddress($email, $userName);

            // Content
            $this->mail->Subject = 'Reset Your K-Derma Account Password';
            
            $resetUrl = url('reset-password/' . $token . '?email=' . urlencode($email));
            
            $this->mail->Body = $this->getPasswordResetEmailTemplate($resetUrl, $userName);
            $this->mail->AltBody = $this->getPasswordResetTextTemplate($resetUrl, $userName);

            $this->mail->send();
            
            Log::info('Password reset email sent successfully to: ' . $email);
            return true;

        } catch (Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
            return false;
        } finally {
            // Clear addresses for next email
            $this->mail->clearAddresses();
        }
    }

    public function sendCustomEmail($to, $subject, $htmlBody, $textBody = null, $toName = null)
    {
        try {
            // Recipients
            $this->mail->addAddress($to, $toName);

            // Content
            $this->mail->Subject = $subject;
            $this->mail->Body = $htmlBody;
            
            if ($textBody) {
                $this->mail->AltBody = $textBody;
            }

            $this->mail->send();
            
            Log::info('Email sent successfully to: ' . $to);
            return true;

        } catch (Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            return false;
        } finally {
            // Clear addresses for next email
            $this->mail->clearAddresses();
        }
    }

    private function getPasswordResetEmailTemplate($resetUrl, $userName = null)
    {
        $greeting = $userName ? "Hi {$userName}!" : "Hello!";
        
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Reset Your Password - K-Derma</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 20px;
                    background-color: #f5f5f5;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .header {
                    background-color: #ec4899;
                    padding: 25px 20px;
                    text-align: center;
                    color: white;
                }
                .logo-text {
                    font-size: 28px;
                    font-weight: bold;
                    margin: 0;
                }
                .tagline {
                    font-size: 14px;
                    margin: 5px 0 0 0;
                    opacity: 0.9;
                }
                .content {
                    padding: 40px 30px;
                    text-align: center;
                }
                .greeting {
                    font-size: 20px;
                    color: #333;
                    margin-bottom: 20px;
                    font-weight: 600;
                }
                .message {
                    font-size: 16px;
                    color: #666;
                    margin-bottom: 30px;
                    line-height: 1.5;
                }
                .button {
                    display: inline-block;
                    background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);
                    color: white !important;
                    text-decoration: none;
                    padding: 15px 40px;
                    border-radius: 50px;
                    font-weight: bold;
                    font-size: 16px;
                    margin: 20px 0;
                    box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
                    transition: all 0.3s ease;
                }
                .button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
                }
                .security-notice {
                    background-color: #fff3cd;
                    border: 1px solid #ffeaa7;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 30px 0;
                    color: #856404;
                }
                .security-notice strong {
                    color: #533f03;
                }
                .link-text {
                    font-size: 12px;
                    color: #999;
                    margin-top: 20px;
                    word-break: break-all;
                    padding: 15px;
                    background-color: #f8f9fa;
                    border-radius: 5px;
                }
                .footer {
                    background-color: #f8f9fa;
                    padding: 20px;
                    text-align: center;
                    color: #666;
                    font-size: 12px;
                    border-top: 1px solid #eee;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo-text'>K-DERMA</div>
                    <div class='tagline'>Professional Dermatology Services</div>
                </div>
                
                <div class='content'>
                    <div class='greeting'>{$greeting}</div>
                    
                    <div class='message'>
                        You requested to reset your password for your K-Derma account.<br>
                        Click the button below to create a new password:
                    </div>
                    
                    <a href='{$resetUrl}' class='button'>RESET MY PASSWORD</a>
                    
                    <div class='security-notice'>
                        <strong>⚠️ Important:</strong> This link expires in 60 minutes for your security.<br>
                        If you didn't request this, please ignore this email.
                    </div>
                    
                    <div style='color: #999; font-size: 14px; margin-top: 20px;'>
                        Having trouble with the button? Copy this link:
                    </div>
                    <div class='link-text'>{$resetUrl}</div>
                </div>
                
                <div class='footer'>
                    <strong>K-Derma Booking System</strong><br>
                    © " . date('Y') . " All rights reserved.<br>
                    This is an automated message, please do not reply.
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function getPasswordResetTextTemplate($resetUrl, $userName = null)
    {
        $greeting = $userName ? "Hi {$userName}!" : "Hello!";
        
        return "
K-DERMA BOOKING SYSTEM
Professional Dermatology Services

{$greeting}

You requested to reset your password for your K-Derma account.

RESET YOUR PASSWORD:
{$resetUrl}

⚠️ IMPORTANT: This link expires in 60 minutes for your security.
If you didn't request this, please ignore this email.

---
K-Derma Booking System
© " . date('Y') . " All rights reserved.
This is an automated message, please do not reply.
        ";
    }
}
