<?php

namespace App\Notifications;

/**
 * Appointment events delivered to the care team and the patient contact.
 */
class AppointmentNotification extends SystemNotification
{
    public function __construct(
        public string $event,
        string $message,
        ?string $link = null,
        string $title = 'Appointment update',
        array $extra = [],
    ) {
        parent::__construct(
            message: $message,
            category: 'clinical',
            link: $link,
            typeLabel: 'appointment',
            title: $title,
            extra: ['event' => $event, ...$extra],
        );
    }
}
