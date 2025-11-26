<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class PHPMailerChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toPhpmailer')) {
            return $notification->toPhpmailer($notifiable);
        }
    }
}
