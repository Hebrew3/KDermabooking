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

    /**
     * Send password change notification email.
     */
    public function sendPasswordChangeEmail($email, $userName = null, $userRole = 'client')
    {
        try {
            // Recipients
            $this->mail->addAddress($email, $userName);

            // Content
            $this->mail->Subject = 'Your K-Derma Password Has Been Changed';
            
            $this->mail->Body = $this->getPasswordChangeEmailTemplate($userName, $userRole);
            $this->mail->AltBody = $this->getPasswordChangeTextTemplate($userName, $userRole);

            $this->mail->send();
            
            Log::info('Password change notification email sent successfully to: ' . $email);
            return true;

        } catch (Exception $e) {
            Log::error('Failed to send password change notification email: ' . $e->getMessage());
            return false;
        } finally {
            // Clear addresses for next email
            $this->mail->clearAddresses();
        }
    }

    private function getPasswordChangeEmailTemplate($userName = null, $userRole = 'client')
    {
        $greeting = $userName ? "Hi {$userName}!" : "Hello!";
        $roleText = $userRole === 'staff' ? 'Staff' : 'Client';
        
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Changed - K-Derma</title>
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
                .success-icon {
                    font-size: 48px;
                    color: #10b981;
                    margin-bottom: 20px;
                }
                .security-notice {
                    background-color: #d1fae5;
                    border: 1px solid #10b981;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 30px 0;
                    color: #065f46;
                }
                .security-notice strong {
                    color: #064e3b;
                }
                .action-required {
                    background-color: #fef3c7;
                    border: 1px solid #fbbf24;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 30px 0;
                    color: #92400e;
                }
                .action-required strong {
                    color: #78350f;
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
                    <div class='success-icon'>✓</div>
                    <div class='greeting'>{$greeting}</div>
                    
                    <div class='message'>
                        Your password for your K-Derma {$roleText} account has been successfully changed.
                    </div>
                    
                    <div class='security-notice'>
                        <strong>✓ Password Updated:</strong><br>
                        Your account password was changed on " . date('F d, Y \a\t g:i A') . ".<br>
                        If you made this change, you can safely ignore this email.
                    </div>
                    
                    <div class='action-required'>
                        <strong>⚠️ Security Alert:</strong><br>
                        If you did NOT make this change, please contact us immediately and reset your password using the \"Forgot Password\" feature on the login page.
                    </div>
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

    private function getPasswordChangeTextTemplate($userName = null, $userRole = 'client')
    {
        $greeting = $userName ? "Hi {$userName}!" : "Hello!";
        $roleText = $userRole === 'staff' ? 'Staff' : 'Client';
        
        return "
K-DERMA BOOKING SYSTEM
Professional Dermatology Services

{$greeting}

Your password for your K-Derma {$roleText} account has been successfully changed.

✓ PASSWORD UPDATED:
Your account password was changed on " . date('F d, Y \a\t g:i A') . ".
If you made this change, you can safely ignore this email.

⚠️ SECURITY ALERT:
If you did NOT make this change, please contact us immediately and reset your password using the \"Forgot Password\" feature on the login page.

---
K-Derma Booking System
© " . date('Y') . " All rights reserved.
This is an automated message, please do not reply.
        ";
    }

    /**
     * Send leave request status notification email to staff.
     */
    public function sendLeaveRequestStatusEmail($email, $userName, $status, $leaveDate, $timeRange, $reason, $notes = null)
    {
        try {
            // Recipients
            $this->mail->addAddress($email, $userName);

            // Content
            $statusText = $status === 'approved' ? 'Approved' : 'Rejected';
            $this->mail->Subject = "Your Leave Request Has Been {$statusText} - K-Derma";
            
            $this->mail->Body = $this->getLeaveRequestStatusEmailTemplate($userName, $status, $leaveDate, $timeRange, $reason, $notes);
            $this->mail->AltBody = $this->getLeaveRequestStatusTextTemplate($userName, $status, $leaveDate, $timeRange, $reason, $notes);

            $this->mail->send();
            
            Log::info('Leave request status email sent successfully to: ' . $email);
            return true;

        } catch (Exception $e) {
            Log::error('Failed to send leave request status email: ' . $e->getMessage());
            return false;
        } finally {
            // Clear addresses for next email
            $this->mail->clearAddresses();
        }
    }

    private function getLeaveRequestStatusEmailTemplate($userName, $status, $leaveDate, $timeRange, $reason, $notes = null)
    {
        $greeting = $userName ? "Hi {$userName}!" : "Hello!";
        $isApproved = $status === 'approved';
        $statusColor = $isApproved ? '#10b981' : '#ef4444';
        $statusBg = $isApproved ? '#d1fae5' : '#fee2e2';
        $statusText = $isApproved ? 'Approved' : 'Rejected';
        $statusIcon = $isApproved ? '✓' : '✗';
        
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Leave Request {$statusText} - K-Derma</title>
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
                .status-badge {
                    display: inline-block;
                    background-color: {$statusBg};
                    color: {$statusColor};
                    padding: 12px 24px;
                    border-radius: 50px;
                    font-weight: bold;
                    font-size: 18px;
                    margin: 20px 0;
                    border: 2px solid {$statusColor};
                }
                .message {
                    font-size: 16px;
                    color: #666;
                    margin-bottom: 30px;
                    line-height: 1.5;
                }
                .details-box {
                    background-color: #f8f9fa;
                    border-radius: 8px;
                    padding: 20px;
                    margin: 30px 0;
                    text-align: left;
                }
                .detail-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #e5e7eb;
                }
                .detail-row:last-child {
                    border-bottom: none;
                }
                .detail-label {
                    font-weight: 600;
                    color: #666;
                }
                .detail-value {
                    color: #333;
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
                    
                    <div class='status-badge'>{$statusIcon} {$statusText}</div>
                    
                    <div class='message'>
                        Your leave request has been <strong>{$statusText}</strong> by the administrator.
                    </div>
                    
                    <div class='details-box'>
                        <div class='detail-row'>
                            <span class='detail-label'>Date:</span>
                            <span class='detail-value'>{$leaveDate}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Time:</span>
                            <span class='detail-value'>{$timeRange}</span>
                        </div>
                        <div class='detail-row'>
                            <span class='detail-label'>Reason:</span>
                            <span class='detail-value'>{$reason}</span>
                        </div>
                        " . ($notes ? "<div class='detail-row'>
                            <span class='detail-label'>Notes:</span>
                            <span class='detail-value'>{$notes}</span>
                        </div>" : "") . "
                    </div>
                    
                    " . ($isApproved ? "<div class='message' style='color: #10b981; font-weight: 600;'>
                        Your leave request has been approved. Please make sure to coordinate with your team for coverage.
                    </div>" : "<div class='message' style='color: #ef4444; font-weight: 600;'>
                        If you have any questions or concerns about this decision, please contact the administrator.
                    </div>") . "
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

    private function getLeaveRequestStatusTextTemplate($userName, $status, $leaveDate, $timeRange, $reason, $notes = null)
    {
        $greeting = $userName ? "Hi {$userName}!" : "Hello!";
        $isApproved = $status === 'approved';
        $statusText = $isApproved ? 'Approved' : 'Rejected';
        $statusIcon = $isApproved ? '✓' : '✗';
        
        return "
K-DERMA BOOKING SYSTEM
Professional Dermatology Services

{$greeting}

{$statusIcon} LEAVE REQUEST {$statusText}

Your leave request has been {$statusText} by the administrator.

DETAILS:
Date: {$leaveDate}
Time: {$timeRange}
Reason: {$reason}
" . ($notes ? "Notes: {$notes}\n" : "") . "

" . ($isApproved ? "Your leave request has been approved. Please make sure to coordinate with your team for coverage." : "If you have any questions or concerns about this decision, please contact the administrator.") . "

---
K-Derma Booking System
© " . date('Y') . " All rights reserved.
This is an automated message, please do not reply.
        ";
    }
}
