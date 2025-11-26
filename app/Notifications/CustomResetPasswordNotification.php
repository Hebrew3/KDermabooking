<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use App\Services\PHPMailerService;

class CustomResetPasswordNotification extends Notification
{
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['phpmailer'];
    }

    public function toPhpmailer($notifiable)
    {
        $phpMailerService = new PHPMailerService();
        
        $userName = $notifiable->first_name ? 
            $notifiable->first_name . ' ' . $notifiable->last_name : 
            null;
        
        $success = $phpMailerService->sendPasswordResetEmail(
            $notifiable->email,
            $this->token,
            $userName
        );

        if (!$success) {
            throw new \Exception('Failed to send password reset email');
        }

        return true;
    }
}
