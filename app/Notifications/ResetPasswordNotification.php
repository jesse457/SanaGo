<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // 1. Generate the URL
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // 2. Return the CUSTOM VIEW
        // We delete 'line' and 'action' and replace them with 'view'
        return (new MailMessage)
            ->subject('Reset Your Password Now!')
            ->view('emails.reset-password-notification', [
                'url' => $url,
                'user' => $notifiable
            ]);
    }
}
