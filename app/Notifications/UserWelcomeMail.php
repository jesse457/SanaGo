<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $loginUrl;
    public $tenantName;

    /**
     * Create a new message instance.
     *
     * @param User $user The user model
     * @param string $password The un-hashed password to show them
     * @param string $loginUrl The link to the login page
     */
    public function __construct(User $user, string $password, string $loginUrl)
    {
        $this->user = $user;
        $this->password = $password;
        $this->loginUrl = $loginUrl;
        // Dynamically get the current tenant's name
        $this->tenantName = tenant('name') ?? config('app.name');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . $this->tenantName . ' - Account Verification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-modern', // We will create this view next
        );
    }
}
