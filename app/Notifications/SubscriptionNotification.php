<?php

namespace App\Notifications;

/**
 * Real-time + email notification for subscription lifecycle changes.
 *
 * Events: assigned, trial_started, upgraded, downgraded, renewed, extended,
 * cancelled, resumed, expiring, expired, limits_changed.
 */
class SubscriptionNotification extends SystemNotification
{
    public function __construct(
        public string $event,
        string $message,
        ?string $link = null,
        string $title = 'Subscription update',
        array $extra = [],
    ) {
        parent::__construct(
            message: $message,
            category: 'billing',
            link: $link,
            typeLabel: 'subscription',
            title: $title,
            extra: ['event' => $event, ...$extra],
        );
    }
}
