<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address; // 👈 Added this
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $resetUrl;

    public $tenantName;

    public $tenantDomain;

    // Receive the domain and name directly from the component
    public function __construct(User $user, string $token, string $domain, string $name)
    {
        $this->user = $user;
        $this->tenantDomain = $domain;
        $this->tenantName = $name;

        $relativePath = route('tenant.password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false);

        $protocol = app()->environment('production') ? 'https://' : 'http://';
        $this->resetUrl = $protocol.$this->tenantDomain.$relativePath;
    }

    public function envelope(): Envelope
    {
        // Using the passed-in domain string safely
        return new Envelope(
            from: new Address('noreply@sanago.site', $this->tenantName),
            subject: 'Welcome to '.$this->tenantName.' - Set your password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user-invitation',
        );
    }
}
