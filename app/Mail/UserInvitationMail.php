<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $resetUrl;
    public $tenantName;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;

        // 1. Get Tenant Details
        // We use the tenant() helper which is available because you are calling
        // this mailable from inside the tenant scope ($tenant->run() or active request).
        $this->tenantName = tenant('name') ?? config('app.name');

        // 2. Get the Correct Domain
        // We prioritize the 'domains' relationship (standard practice),
        // but fallback to tenant('id') since your CreateTenant code uses the domain as the ID.
        $tenantDomain = tenant()->domains->first()->domain ?? tenant('id');

        // 3. Generate the Relative Path
        // We pass 'false' as the 3rd argument to route().
        // This gives us just "/reset-password?token=..." without the "http://localhost" part.
        // This prevents the system from accidentally using the Landlord domain.
        $relativePath = route('tenant.password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false);

        // 4. Construct the Full URL
        // We manually stitch the protocol + tenant domain + relative path.
        // This guarantees the link opens the specific hospital's portal.
        $protocol = 'http://';

        $this->resetUrl = $protocol . $tenantDomain . ':8000' . $relativePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . $this->tenantName . ' - Set your password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-invitation',
        );
    }
}
