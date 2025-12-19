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

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;

        // 1. Get Tenant Details
        $this->tenantName = tenant('name') ?? config('app.name');

        // 2. Get the Correct Domain
        // Priority: Verified domain record > tenant ID
        $this->tenantDomain = tenant()->domains->first()->domain ?? tenant('id');

        // 3. Generate the Relative Path
        $relativePath = route('tenant.password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false);

        // 4. Construct the Full URL for Production
        // We use https and remove :8000 because Nginx handles the proxy.
        $protocol = app()->environment('production') ? 'https://' : 'http://';

        // Final URL: https://tenant.sanago.site/reset-password...
        $this->resetUrl = $protocol . $this->tenantDomain . $relativePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        /**
         * IMPORTANT FOR RESEND:
         * If you verified 'sanago.site' in Resend, you can usually send from
         * any subdomain like 'noreply@tenant.sanago.site'.
         *
         * If the tenant uses a custom domain (tenant.com) that isn't verified yet,
         * Resend will fail. In that case, use: noreply@sanago.site
         */
        $senderEmail = "noreply@" . $this->tenantDomain;

        // Fallback check: If the domain doesn't contain your master domain and isn't verified
        // You might want to force the master domain to ensure delivery:
        // $senderEmail = "onboarding@sanago.site";

        return new Envelope(
            from: new Address($senderEmail, $this->tenantName),
            subject: 'Welcome to ' . $this->tenantName . ' - Set your password',
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
