<?php

namespace App\Notifications;

/**
 * Tenant lifecycle events raised by the platform (provisioned, suspended,
 * reactivated, archived, restored, support access).
 */
class TenantLifecycleNotification extends SystemNotification
{
    public function __construct(
        public string $event,
        string $message,
        ?string $link = null,
        string $title = 'Tenant update',
        array $extra = [],
    ) {
        parent::__construct(
            message: $message,
            category: 'platform',
            link: $link,
            typeLabel: 'tenant',
            title: $title,
            extra: ['event' => $event, ...$extra],
        );
    }
}
