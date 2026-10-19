<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $message,
        private string $category = 'general',
        private ?string $link = null,
        private string $typeLabel = 'info',
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'category' => $this->category,
            'link' => $this->link,
            'type_label' => $this->typeLabel,
        ];
    }

    /**
     * Get the broadcast representation (for future real-time support).
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'category' => $this->category,
            'link' => $this->link,
            'type_label' => $this->typeLabel,
        ];
    }
}
