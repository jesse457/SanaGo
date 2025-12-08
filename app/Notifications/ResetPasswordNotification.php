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
        $url = $this->generateResetUrl($notifiable);

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->view('emails.reset-password-notification', [
                'url' => $url,
                'user' => $notifiable,
                // Pass the tenant name if available, otherwise app name
                'appName' => tenant('name') ?? config('app.name'),
            ]);
    }

    /**
     * Context-aware URL generation
     */
    protected function generateResetUrl($notifiable)
    {
        // 1. Check if we are running inside a Tenant Context
        if (function_exists('tenant') && tenant()) {

            // Get the current tenant's domain
            // Fallback to ID if domain relation isn't loaded (rare but possible)
            $domain = tenant()->domains->first()->domain ?? tenant('id');

            // Construct the Tenant-Specific Route
            // 'tenant.password.reset' must exist in routes/tenant.php
            $relativePath = route('tenant.password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false);

          
            return 'http://' . $domain . ':8000' . $relativePath;
        }

        // 2. Default: Landlord / Central App Context
        // 'password.reset' must exist in routes/web.php
        return route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
