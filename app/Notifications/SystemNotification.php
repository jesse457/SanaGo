<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * The in-app notification used across the hospital application.
 *
 * Channels are resolved defensively: the database row is always written (it
 * is the source of truth for the bell), broadcasting and mail are only added
 * when the transports are configured and enabled. A missing broadcaster or
 * mailer therefore degrades to a plain in-app notification instead of
 * throwing.
 */
class SystemNotification extends Notification
{
    use Queueable;

    /**
     * When set, only these channels are used. Used by the fallback path when
     * a realtime or mail transport is unavailable.
     *
     * @var array<int, string>|null
     */
    public ?array $onlyChannels = null;

    public function __construct(
        private string $message,
        private string $category = 'general',
        private ?string $link = null,
        private string $typeLabel = 'info',
        private ?string $title = null,
        private array $extra = [],
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($this->onlyChannels !== null) {
            return $this->onlyChannels;
        }

        $channels = ['database'];

        if ($this->shouldBroadcast()) {
            $channels[] = 'broadcast';
        }

        if ($this->shouldSendMail($notifiable)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * A copy that only persists to the database — used as the fallback when a
     * realtime or mail transport fails.
     */
    public function databaseOnly(): static
    {
        $clone = clone $this;
        $clone->onlyChannels = ['database'];

        return $clone;
    }

    /**
     * A copy pointing somewhere else — used when the same event is delivered
     * to two audiences that need different destinations (a hospital admin
     * follows /billing, a platform operator follows the tenant record).
     */
    public function withLink(?string $link): static
    {
        $clone = clone $this;
        $clone->link = $link;

        return $clone;
    }

    /**
     * A copy that keeps database + mail but skips broadcasting.
     */
    public function withoutRealtime(): static
    {
        $clone = clone $this;
        $clone->onlyChannels = ['database', 'mail'];

        return $clone;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            ...$this->payload(),
            'notifiable_id' => $notifiable->getKey(),
        ]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title ?? $this->message)
            ->greeting('Hello '.($notifiable->name ?? 'there'))
            ->line($this->message);

        if ($this->link !== null) {
            $mail->action('Open SanaGo', url($this->link));
        }

        return $mail->salutation('— '.config('app.name'));
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => $this->category,
            'link' => $this->link,
            'type_label' => $this->typeLabel,
            ...$this->extra,
        ];
    }

    private function shouldBroadcast(): bool
    {
        if (! config('platform.notifications.realtime', true)) {
            return false;
        }

        $driver = config('broadcasting.default');

        if (in_array($driver, [null, 'null', 'log'], true)) {
            return false;
        }

        return config()->has('broadcasting.connections.'.$driver);
    }

    private function shouldSendMail(object $notifiable): bool
    {
        if (! config('platform.notifications.mail', true)) {
            return false;
        }

        return ! empty($notifiable->email ?? null);
    }
}
