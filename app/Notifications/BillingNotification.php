<?php

namespace App\Notifications;

/**
 * Billing events: invoice issued, payment recorded, payment overdue.
 */
class BillingNotification extends SystemNotification
{
    public function __construct(
        public string $event,
        string $message,
        ?string $link = null,
        string $title = 'Billing update',
        array $extra = [],
    ) {
        parent::__construct(
            message: $message,
            category: 'billing',
            link: $link,
            typeLabel: 'billing',
            title: $title,
            extra: ['event' => $event, ...$extra],
        );
    }
}
