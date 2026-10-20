<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Platform roles
    |--------------------------------------------------------------------------
    |
    | The role that grants unrestricted landlord access. Used by the central
    | Gate::before hook so only the true platform super admin bypasses the
    | permission matrix.
    |
    */

    'super_admin_role' => env('PLATFORM_SUPER_ADMIN_ROLE', 'super-admin'),

    /*
    |--------------------------------------------------------------------------
    | Notification transports
    |--------------------------------------------------------------------------
    |
    | Each transport is optional. When `realtime` is false (or the Reverb
    | server is unreachable) notifications are still written to the database
    | and shown in the bell; mail is skipped when the mailer is not
    | configured. Neither failure mode raises to the caller.
    |
    */

    'notifications' => [
        'realtime' => env('PLATFORM_REALTIME_NOTIFICATIONS', true),
        'mail' => env('PLATFORM_MAIL_NOTIFICATIONS', true),
        'expiry_warning_days' => [14, 7, 3, 1],
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing
    |--------------------------------------------------------------------------
    */

    'billing' => [
        'currency' => 'XAF',
        'invoice_due_days' => 7,
        'default_grace_days' => 3,
        'proration' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Support access (impersonation)
    |--------------------------------------------------------------------------
    |
    | `ttl` is how long (seconds) a one-time impersonation token stays valid.
    |
    */

    'impersonation' => [
        'enabled' => env('PLATFORM_IMPERSONATION_ENABLED', true),
        'ttl' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Health thresholds
    |--------------------------------------------------------------------------
    */

    'health' => [
        'expiring_soon_days' => 14,
        'stale_activity_days' => 30,
    ],

];
