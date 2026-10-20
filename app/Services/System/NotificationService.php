<?php

namespace App\Services\System;

use App\Models\Tenant;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Support\TenantDatabase;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

/**
 * Central place for every notification the application sends.
 *
 * Delivery is defensive by design: the database row (the bell) is the
 * guaranteed channel, broadcasting and mail are best effort. If Reverb, the
 * queue or the mailer is down the caller still succeeds and the user still
 * sees the notification in-app.
 */
class NotificationService
{
    /**
     * Send a notification to a single user.
     */
    public function notifyUser(User $user, string $message, string $category = 'general', ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->deliver($user, new SystemNotification($message, $category, $link, $typeLabel));
    }

    /**
     * Send a notification to all users holding a permission.
     */
    public function notifyByPermission(string $permission, string $message, string $category = 'general', ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->deliver(
            User::permission($permission)->get(),
            new SystemNotification($message, $category, $link, $typeLabel),
        );
    }

    public function notifyDoctors(string $message, string $category = 'clinical', ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-consultations', $message, $category, $link, $typeLabel);
    }

    public function notifyLabTechs(string $message, ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-lab-results', $message, 'lab', $link, $typeLabel);
    }

    public function notifyPharmacy(string $message, ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-drugs', $message, 'pharmacy', $link, $typeLabel);
    }

    public function notifyReception(string $message, string $category = 'clinical', ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-appointments', $message, $category, $link, $typeLabel);
    }

    public function notifyFinance(string $message, ?string $link = null, string $typeLabel = 'info'): void
    {
        $this->notifyByPermission('view-finance', $message, 'general', $link, $typeLabel);
    }

    /**
     * Notify the administrators of one tenant (inside that tenant's context).
     *
     * When called from the landlord side the tenant is initialised for the
     * duration of the send, so plan changes and suspensions reach hospital
     * admins even though they live in a different database.
     */
    public function notifyTenantAdmins(?Tenant $tenant, Notification $notification): int
    {
        if ($tenant === null) {
            return 0;
        }

        return $this->withinTenant($tenant, function () use ($notification) {
            $admins = User::role('admin')->get();

            if ($admins->isEmpty()) {
                $admins = User::permission('manage-users')->get();
            }

            return $this->deliver($admins, $notification);
        }) ?? 0;
    }

    /**
     * Notify every landlord (super admin) account.
     */
    public function notifyLandlordAdmins(Notification $notification): int
    {
        $recipients = User::permission('view-super-admin')->get();

        return $this->deliver($recipients, $notification);
    }

    /**
     * Deliver a notification to one or many notifiables without ever throwing.
     *
     * A transport failure (Reverb down, mailer misconfigured, queue driver
     * missing) falls back to a database-only send so the in-app bell is never
     * starved of an event.
     *
     * @param  User|Collection<int, User>|array<int, User>  $notifiables
     */
    public function deliver(User|Collection|array $notifiables, Notification $notification): int
    {
        $recipients = $notifiables instanceof User
            ? collect([$notifiables])
            : collect($notifiables)->filter();

        if ($recipients->isEmpty()) {
            return 0;
        }

        // Remember who already has a database row so a partial transport
        // failure never duplicates the in-app notification.
        $before = $recipients->mapWithKeys(fn (User $user) => [
            $user->getKey() => $this->notificationCount($user),
        ]);

        try {
            NotificationFacade::send($recipients, $notification);

            return $recipients->count();
        } catch (Throwable $exception) {
            report($exception);
        }

        $fallback = $notification instanceof SystemNotification
            ? $notification->databaseOnly()
            : $notification;

        $missing = $recipients
            ->filter(fn (User $user) => $this->notificationCount($user) === ($before[$user->getKey()] ?? 0))
            ->values();

        if ($missing->isEmpty()) {
            return $recipients->count();
        }

        try {
            NotificationFacade::send($missing, $fallback);
        } catch (Throwable $exception) {
            report($exception);

            return $recipients->count() - $missing->count();
        }

        return $recipients->count();
    }

    /**
     * Number of stored notifications, used to detect partial deliveries.
     */
    private function notificationCount(User $user): int
    {
        try {
            return $user->notifications()->count();
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * Run a callback inside a tenant's database context.
     *
     * Skipped entirely when the tenant database is missing or unreachable, so
     * a broken hospital database can never take the platform down.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn|null
     */
    private function withinTenant(Tenant $tenant, callable $callback)
    {
        return TenantDatabase::run($tenant, $callback);
    }
}
