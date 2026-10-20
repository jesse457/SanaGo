<?php

namespace App\Notifications;

/**
 * Raised when a tenant approaches or reaches a plan quota.
 */
class PlanLimitNotification extends SystemNotification
{
    public function __construct(
        public string $limitKey,
        string $message,
        ?string $link = null,
        string $title = 'Plan limit reached',
        array $extra = [],
    ) {
        parent::__construct(
            message: $message,
            category: 'billing',
            link: $link,
            typeLabel: 'limit',
            title: $title,
            extra: ['limit' => $limitKey, ...$extra],
        );
    }
}
