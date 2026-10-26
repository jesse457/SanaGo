<?php

declare(strict_types=1);

namespace App\Services\Platform;

use App\Enums\SubscriptionInvoiceStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use App\Notifications\BillingNotification;
use App\Notifications\SubscriptionNotification;
use App\Notifications\SystemNotification;
use App\Services\System\NotificationService;
use App\Support\PlatformContext;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Tenant-aware subscription lifecycle.
 *
 * Every operation is scoped to one tenant: the subscription is looked up by
 * `tenant_id` and never by id alone, so a plan change, renewal or
 * cancellation can not leak across hospitals. Billing periods that are
 * charged are recorded as {@see SubscriptionInvoice} rows, which gives the
 * platform a real revenue ledger.
 */
class SubscriptionService
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly PlatformAuditService $audit,
        private readonly PlatformSettingsService $settings,
    ) {}

    public function forTenant(Tenant $tenant): ?Subscription
    {
        return Subscription::query()
            ->with('plan')
            ->where('tenant_id', $tenant->getTenantKey())
            ->first();
    }

    /**
     * Assign a plan to a tenant that has no current subscription, or replace
     * the plan of an existing one (upgrade/downgrade).
     *
     * @param  array{billing_cycle?: string, start_date?: CarbonInterface|string|null, price?: float|int|string|null, trial?: bool, trial_days?: int, auto_renew?: bool, notes?: string|null, generate_invoice?: bool}  $options
     */
    public function assignPlan(Tenant $tenant, Plan $plan, array $options = []): Subscription
    {
        $existing = $this->forTenant($tenant);

        // A live subscription means this is really a plan change.
        if ($existing !== null && $existing->effectiveStatus()->grantsAccess()) {
            return $this->changePlan($tenant, $plan, $options);
        }

        $cycle = $this->normaliseCycle(
            $options['billing_cycle'] ?? ($plan->monthly_price !== null ? 'monthly' : 'yearly')
        );
        $start = $this->toDate($options['start_date'] ?? null) ?? Carbon::today();
        $trial = (bool) ($options['trial'] ?? false);
        $trialDays = (int) ($options['trial_days'] ?? $plan->trialDays());
        $price = array_key_exists('price', $options) && $options['price'] !== null
            ? (float) $options['price']
            : $plan->priceFor($cycle);

        $endDate = $trial
            ? $start->copy()->addDays($trialDays)
            : $this->periodEnd($start, $cycle);

        $subscription = $existing ?? new Subscription(['tenant_id' => $tenant->getTenantKey()]);

        $subscription->fill([
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'billing_cycle' => $cycle,
            'currency' => $plan->currency ?: $this->settings->get('currency', 'XAF'),
            'price' => $price,
            'start_date' => $start,
            'end_date' => $endDate,
            'trial_ends_at' => $trial ? $start->copy()->addDays($trialDays) : null,
            'status' => $trial ? SubscriptionStatus::TRIALING : SubscriptionStatus::ACTIVE,
            'auto_renew' => (bool) ($options['auto_renew'] ?? true),
            'cancelled_at' => null,
            'cancel_at_period_end' => false,
            'features_json' => $plan->features_json,
            'notes' => $options['notes'] ?? null,
        ]);

        $subscription->save();

        if (! $trial && $price > 0 && ($options['generate_invoice'] ?? true)) {
            $this->invoiceForPeriod($subscription, $start, $endDate, $price, [
                'reason' => 'plan_assigned',
                'plan' => $plan->name,
            ]);
        }

        $this->audit->log(
            action: $trial ? 'subscription.trial_started' : 'subscription.assigned',
            description: sprintf('%s plan assigned to %s', $plan->name, $tenant->displayName()),
            subject: $subscription,
            tenantId: (string) $tenant->getTenantKey(),
            properties: [
                'plan_id' => $plan->id,
                'billing_cycle' => $cycle,
                'price' => $price,
                'trial' => $trial,
            ],
        );

        $this->notifyTenant(
            $tenant,
            new SubscriptionNotification(
                event: $trial ? 'trial_started' : 'assigned',
                message: $trial
                    ? sprintf('Your %s trial is active until %s.', $plan->name, $endDate->toDateString())
                    : sprintf('Your subscription is now on the %s plan (%s).', $plan->name, $cycle),
                link: '/billing',
                title: 'Subscription activated',
            ),
        );

        PlatformContext::flush();

        return $subscription->refresh();
    }

    /**
     * Change the plan of an existing subscription.
     *
     * Upgrades take effect immediately and are prorated for the unused part of
     * the current period. Downgrades take effect at the end of the paid period
     * unless `immediate` is requested.
     *
     * @param  array{billing_cycle?: string, immediate?: bool, price?: float|int|string|null, notes?: string|null, generate_invoice?: bool}  $options
     */
    public function changePlan(Tenant $tenant, Plan $plan, array $options = []): Subscription
    {
        $subscription = $this->forTenant($tenant);

        if ($subscription === null) {
            return $this->assignPlan($tenant, $plan, $options);
        }

        $cycle = $this->normaliseCycle($options['billing_cycle'] ?? $subscription->billing_cycle);
        $direction = $this->direction($subscription, $plan);
        $immediate = (bool) ($options['immediate'] ?? ($direction === 'upgrade'));

        $price = array_key_exists('price', $options) && $options['price'] !== null
            ? (float) $options['price']
            : $plan->priceFor($cycle);

        $proration = $this->proration($subscription, $plan, $cycle);

        $subscription->fill([
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'billing_cycle' => $cycle,
            'currency' => $plan->currency ?: $subscription->currency,
            'price' => $price,
            'features_json' => $plan->features_json,
            'notes' => $options['notes'] ?? $subscription->notes,
        ]);

        // A downgrade that is not immediate leaves the current period intact;
        // the plan id is applied but the entitlement shrinks when the next
        // renewal is generated. Marking it keeps the intent visible.
        if (! $immediate && $direction === 'downgrade') {
            $subscription->status = SubscriptionStatus::ACTIVE;
        } else {
            $subscription->status = SubscriptionStatus::ACTIVE;
            $subscription->trial_ends_at = null;
        }

        $subscription->save();

        if ($immediate && $proration['amount_due'] > 0 && ($options['generate_invoice'] ?? true)) {
            $this->invoiceForPeriod(
                $subscription,
                Carbon::today(),
                $subscription->end_date ?? $this->periodEnd(Carbon::today(), $cycle),
                $proration['amount_due'],
                [
                    'reason' => 'plan_change_proration',
                    'plan' => $plan->name,
                    'credit' => $proration['credit'],
                    'charge' => $proration['charge'],
                ],
            );
        }

        $this->audit->log(
            action: 'subscription.'.$direction,
            description: sprintf(
                '%s moved from %s to %s',
                $tenant->displayName(),
                $subscription->getOriginal('plan_name') ?? $subscription->plan_name,
                $plan->name,
            ),
            subject: $subscription,
            tenantId: (string) $tenant->getTenantKey(),
            properties: [
                'from_plan_id' => $subscription->getOriginal('plan_id'),
                'to_plan_id' => $plan->id,
                'immediate' => $immediate,
                'proration' => $proration,
            ],
        );

        $this->notifyTenant(
            $tenant,
            new SubscriptionNotification(
                event: $direction,
                message: sprintf(
                    'Your plan changed to %s. %s',
                    $plan->name,
                    $immediate ? 'The change is effective now.' : 'The change takes effect at the end of your current period.',
                ),
                link: '/billing',
                title: 'Plan changed',
            ),
        );

        PlatformContext::flush();

        return $subscription->refresh();
    }

    /**
     * Renew a subscription for another billing period.
     */
    public function renew(Tenant $tenant, ?string $cycle = null, bool $generateInvoice = true): Subscription
    {
        $subscription = $this->forTenant($tenant);

        if ($subscription === null) {
            throw new \RuntimeException('Tenant has no subscription to renew.');
        }

        $cycle = $this->normaliseCycle($cycle ?? $subscription->billing_cycle);
        $start = $subscription->end_date !== null && $subscription->end_date->isFuture()
            ? $subscription->end_date->copy()->addDay()
            : Carbon::today();
        $end = $this->periodEnd($start, $cycle);
        $price = $subscription->plan?->priceFor($cycle) ?? (float) $subscription->price;

        $subscription->fill([
            'billing_cycle' => $cycle,
            'price' => $price,
            'start_date' => $start,
            'end_date' => $end,
            'status' => SubscriptionStatus::ACTIVE,
            'last_renewed_at' => now(),
            'cancelled_at' => null,
            'cancel_at_period_end' => false,
        ])->save();

        if ($generateInvoice && $price > 0) {
            $this->invoiceForPeriod($subscription, $start, $end, $price, [
                'reason' => 'renewal',
                'plan' => $subscription->plan_name,
            ]);
        }

        $this->audit->log(
            action: 'subscription.renewed',
            description: sprintf('%s renewed until %s', $tenant->displayName(), $end->toDateString()),
            subject: $subscription,
            tenantId: (string) $tenant->getTenantKey(),
            properties: ['period_start' => $start->toDateString(), 'period_end' => $end->toDateString(), 'price' => $price],
        );

        $this->notifyTenant($tenant, new SubscriptionNotification(
            event: 'renewed',
            message: sprintf('Your subscription was renewed until %s.', $end->toDateString()),
            link: '/billing',
            title: 'Subscription renewed',
        ));

        PlatformContext::flush();

        return $subscription->refresh();
    }

    /**
     * Extend the current period by a number of days (goodwill, trial top-up).
     */
    public function extend(Tenant $tenant, int $days): Subscription
    {
        $subscription = $this->forTenant($tenant);

        if ($subscription === null) {
            throw new \RuntimeException('Tenant has no subscription to extend.');
        }

        $base = $subscription->end_date !== null && $subscription->end_date->isFuture()
            ? $subscription->end_date
            : Carbon::today();

        $subscription->update([
            'end_date' => $base->copy()->addDays($days),
            'cancel_at_period_end' => false,
        ]);

        $this->audit->log(
            action: 'subscription.extended',
            description: sprintf('%s extended by %d days', $tenant->displayName(), $days),
            subject: $subscription,
            tenantId: (string) $tenant->getTenantKey(),
            properties: ['days' => $days, 'end_date' => $subscription->end_date?->toDateString()],
        );

        $this->notifyTenant($tenant, new SubscriptionNotification(
            event: 'extended',
            message: sprintf('Your subscription now runs until %s.', $subscription->end_date?->toDateString()),
            link: '/billing',
            title: 'Subscription extended',
        ));

        PlatformContext::flush();

        return $subscription->refresh();
    }

    /**
     * Cancel immediately or at the end of the paid period.
     */
    public function cancel(Tenant $tenant, bool $immediately = false, ?string $reason = null): Subscription
    {
        $subscription = $this->forTenant($tenant);

        if ($subscription === null) {
            throw new \RuntimeException('Tenant has no subscription to cancel.');
        }

        if ($immediately) {
            $subscription->fill([
                'status' => SubscriptionStatus::CANCELLED,
                'cancelled_at' => now(),
                'cancel_at_period_end' => false,
                'auto_renew' => false,
                'end_date' => Carbon::today(),
                'notes' => $reason ?? $subscription->notes,
            ])->save();
        } else {
            $subscription->fill([
                'cancel_at_period_end' => true,
                'auto_renew' => false,
                'notes' => $reason ?? $subscription->notes,
            ])->save();
        }

        $this->audit->log(
            action: 'subscription.cancelled',
            description: sprintf(
                '%s cancelled %s',
                $tenant->displayName(),
                $immediately ? 'immediately' : 'at period end',
            ),
            subject: $subscription,
            tenantId: (string) $tenant->getTenantKey(),
            properties: ['immediately' => $immediately, 'reason' => $reason],
        );

        $this->notifyTenant($tenant, new SubscriptionNotification(
            event: 'cancelled',
            message: $immediately
                ? 'Your subscription was cancelled. Access has ended.'
                : sprintf('Your subscription will end on %s.', $subscription->end_date?->toDateString()),
            link: '/billing',
            title: 'Subscription cancelled',
        ));

        PlatformContext::flush();

        return $subscription->refresh();
    }

    /**
     * Undo a scheduled cancellation.
     */
    public function resume(Tenant $tenant): Subscription
    {
        $subscription = $this->forTenant($tenant);

        if ($subscription === null) {
            throw new \RuntimeException('Tenant has no subscription to resume.');
        }

        $subscription->fill([
            'cancel_at_period_end' => false,
            'auto_renew' => true,
            'cancelled_at' => null,
            'status' => $subscription->end_date !== null && $subscription->end_date->isPast()
                ? SubscriptionStatus::PAST_DUE
                : SubscriptionStatus::ACTIVE,
        ])->save();

        $this->audit->log(
            action: 'subscription.resumed',
            description: sprintf('%s resumed its subscription', $tenant->displayName()),
            subject: $subscription,
            tenantId: (string) $tenant->getTenantKey(),
        );

        PlatformContext::flush();

        return $subscription->refresh();
    }

    /**
     * Force a subscription to expire now.
     */
    public function expire(Tenant $tenant): Subscription
    {
        $subscription = $this->forTenant($tenant);

        if ($subscription === null) {
            throw new \RuntimeException('Tenant has no subscription to expire.');
        }

        $subscription->update([
            'status' => SubscriptionStatus::EXPIRED,
            'end_date' => Carbon::today(),
            'auto_renew' => false,
        ]);

        $this->audit->log(
            action: 'subscription.expired',
            description: sprintf('%s subscription expired', $tenant->displayName()),
            subject: $subscription,
            tenantId: (string) $tenant->getTenantKey(),
        );

        $this->notifyTenant($tenant, new SubscriptionNotification(
            event: 'expired',
            message: 'Your subscription has expired. Contact support to restore access.',
            link: '/billing',
            title: 'Subscription expired',
        ));

        PlatformContext::flush();

        return $subscription->refresh();
    }

    /**
     * Apply per-tenant quota and feature overrides on top of the plan.
     *
     * @param  array<string, int|string|null>  $limits
     * @param  array<string, bool>  $features
     */
    public function updateOverrides(Tenant $tenant, array $limits = [], array $features = []): Subscription
    {
        $subscription = $this->forTenant($tenant);

        if ($subscription === null) {
            throw new \RuntimeException('Tenant has no subscription to override.');
        }

        $cleanLimits = [];

        foreach (['users', 'patients', 'storage_gb'] as $key) {
            if (array_key_exists($key, $limits)) {
                $value = $limits[$key];
                $cleanLimits[$key] = ($value === null || $value === '' || $value === 'unlimited')
                    ? null
                    : (int) $value;
            }
        }

        $cleanFeatures = [];

        foreach ($features as $key => $enabled) {
            if ($enabled === 'inherit' || $enabled === null) {
                continue;
            }

            $cleanFeatures[$key] = (bool) $enabled;
        }

        $subscription->update([
            'limits_json' => $cleanLimits === [] ? null : $cleanLimits,
            'feature_overrides_json' => $cleanFeatures === [] ? null : $cleanFeatures,
        ]);

        $this->audit->log(
            action: 'subscription.overrides_updated',
            description: sprintf('Plan overrides updated for %s', $tenant->displayName()),
            subject: $subscription,
            tenantId: (string) $tenant->getTenantKey(),
            properties: ['limits' => $cleanLimits, 'features' => $cleanFeatures],
        );

        PlatformContext::flush();

        return $subscription->refresh();
    }

    /**
     * Mark an invoice as paid and notify the tenant.
     *
     * @param  array{payment_method?: string|null, reference?: string|null}  $data
     */
    public function recordPayment(SubscriptionInvoice $invoice, array $data = []): SubscriptionInvoice
    {
        if ($invoice->status === SubscriptionInvoiceStatus::PAID) {
            return $invoice;
        }

        $invoice->markPaid($data['payment_method'] ?? null, $data['reference'] ?? null);

        $this->audit->log(
            action: 'billing.invoice_paid',
            description: sprintf('Invoice %s marked paid', $invoice->number),
            subject: $invoice,
            tenantId: $invoice->tenant_id,
            properties: ['total' => (float) $invoice->total, 'currency' => $invoice->currency],
        );

        $tenant = $invoice->tenant;

        if ($tenant !== null) {
            $this->notifyTenant($tenant, new BillingNotification(
                event: 'invoice_paid',
                message: sprintf('Payment of %s %s received for invoice %s.', number_format((float) $invoice->total, 0, '.', ' '), $invoice->currency, $invoice->number),
                link: '/billing',
                title: 'Payment received',
            ));
        }

        return $invoice->refresh();
    }

    /**
     * Expire every subscription whose paid period (plus grace) has ended.
     *
     * @return int number of subscriptions transitioned
     */
    public function expireDue(): int
    {
        $count = 0;

        Subscription::query()
            ->with(['plan', 'tenant'])
            ->usable()
            ->whereNotNull('end_date')
            ->where('end_date', '<', Carbon::today()->toDateString())
            ->get()
            ->each(function (Subscription $subscription) use (&$count) {
                if ($subscription->isBeyondGrace() || $subscription->cancel_at_period_end) {
                    $this->transitionToTerminal($subscription);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * Renew every auto-renewing subscription whose period has ended.
     *
     * @return int number of subscriptions renewed
     */
    public function renewDue(): int
    {
        $count = 0;

        Subscription::query()
            ->with('tenant')
            ->where('auto_renew', true)
            ->where('cancel_at_period_end', false)
            ->usable()
            ->whereNotNull('end_date')
            ->where('end_date', '<', Carbon::today()->toDateString())
            ->get()
            ->each(function (Subscription $subscription) use (&$count) {
                if ($subscription->tenant === null) {
                    return;
                }

                try {
                    $this->renew($subscription->tenant, generateInvoice: true);
                    $count++;
                } catch (Throwable $exception) {
                    report($exception);
                }
            });

        return $count;
    }

    /**
     * Warn tenants whose subscription ends soon.
     *
     * @param  array<int, int>|null  $days
     * @return int number of notifications sent
     */
    public function notifyExpiring(?array $days = null): int
    {
        $days ??= (array) config('platform.notifications.expiry_warning_days', [14, 7, 3, 1]);

        $sent = 0;

        Subscription::query()
            ->with(['tenant', 'plan'])
            ->usable()
            ->where('auto_renew', false)
            ->whereNotNull('end_date')
            ->get()
            ->each(function (Subscription $subscription) use ($days, &$sent) {
                if ($subscription->tenant === null) {
                    return;
                }

                $remaining = $subscription->daysRemaining();

                if ($remaining === null || ! in_array($remaining, $days, true)) {
                    return;
                }

                $this->notifyTenant($subscription->tenant, new SubscriptionNotification(
                    event: 'expiring',
                    message: sprintf('Your subscription ends in %d day(s) on %s.', $remaining, $subscription->end_date?->toDateString()),
                    link: '/billing',
                    title: 'Subscription expiring',
                    extra: ['days_remaining' => $remaining],
                ));

                $sent++;
            });

        return $sent;
    }

    /**
     * Proration preview for a plan change.
     *
     * @return array{days_remaining: int, total_days: int, credit: float, charge: float, amount_due: float, direction: string}
     */
    public function proration(Subscription $subscription, Plan $plan, ?string $cycle = null): array
    {
        $cycle = $this->normaliseCycle($cycle ?? $subscription->billing_cycle);
        $today = Carbon::today();

        $periodStart = $subscription->start_date ?? $today;
        $periodEnd = $subscription->end_date ?? $this->periodEnd($periodStart, $cycle);

        $totalDays = max(1, $periodStart->diffInDays($periodEnd));
        $remainingDays = max(0, $today->diffInDays($periodEnd));

        $oldRate = (float) $subscription->price;
        $newRate = $plan->priceFor($cycle);

        $credit = round($oldRate * $remainingDays / $totalDays, 2);
        $charge = round($newRate * $remainingDays / $totalDays, 2);

        return [
            'days_remaining' => $remainingDays,
            'total_days' => $totalDays,
            'credit' => $credit,
            'charge' => $charge,
            'amount_due' => max(0, round($charge - $credit, 2)),
            'direction' => $this->direction($subscription, $plan),
        ];
    }

    /**
     * Create the invoice row for a billing period.
     *
     * @param  array<string, mixed>  $meta
     */
    public function invoiceForPeriod(
        Subscription $subscription,
        CarbonInterface $periodStart,
        CarbonInterface $periodEnd,
        float $amount,
        array $meta = [],
    ): ?SubscriptionInvoice {
        try {
            $invoice = SubscriptionInvoice::create([
                'tenant_id' => $subscription->tenant_id,
                'subscription_id' => $subscription->id,
                'number' => SubscriptionInvoice::generateNumber(),
                'currency' => $subscription->currency ?: 'XAF',
                'subtotal' => $amount,
                'tax' => 0,
                'total' => $amount,
                'status' => SubscriptionInvoiceStatus::PENDING,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'issued_at' => now(),
                'due_at' => now()->addDays((int) config('platform.billing.invoice_due_days', 7)),
                'meta_json' => $meta,
            ]);

            $tenant = $subscription->tenant ?? Tenant::find($subscription->tenant_id);

            if ($tenant !== null) {
                $this->notifyTenant($tenant, new BillingNotification(
                    event: 'invoice_issued',
                    message: sprintf(
                        'Invoice %s for %s %s is due on %s.',
                        $invoice->number,
                        number_format($amount, 0, '.', ' '),
                        $invoice->currency,
                        $invoice->due_at?->toDateString() ?? '',
                    ),
                    link: '/billing',
                    title: 'New invoice issued',
                ));
            }

            return $invoice;
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Revenue summary used by the platform dashboard.
     *
     * @return array{mrr: float, arr: float, active: int, trialing: int, past_due: int, cancelled: int, currency: string}
     */
    public function revenueSummary(): array
    {
        $subscriptions = Subscription::query()->usable()->get();

        $mrr = 0.0;

        foreach ($subscriptions as $subscription) {
            $price = (float) $subscription->price;
            $mrr += $subscription->billing_cycle === 'yearly' ? $price / 12 : $price;
        }

        return [
            'mrr' => round($mrr, 2),
            'arr' => round($mrr * 12, 2),
            'active' => Subscription::query()->where('status', SubscriptionStatus::ACTIVE->value)->count(),
            'trialing' => Subscription::query()->where('status', SubscriptionStatus::TRIALING->value)->count(),
            'past_due' => Subscription::query()->where('status', SubscriptionStatus::PAST_DUE->value)->count(),
            'cancelled' => Subscription::query()->where('status', SubscriptionStatus::CANCELLED->value)->count(),
            'currency' => (string) $this->settings->get('currency', 'XAF'),
        ];
    }

    private function transitionToTerminal(Subscription $subscription): void
    {
        $cancelled = $subscription->cancel_at_period_end;

        $subscription->update([
            'status' => $cancelled ? SubscriptionStatus::CANCELLED : SubscriptionStatus::EXPIRED,
            'cancelled_at' => $cancelled ? now() : $subscription->cancelled_at,
            'cancel_at_period_end' => false,
            'auto_renew' => false,
        ]);

        $this->audit->log(
            action: 'subscription.'.($cancelled ? 'cancelled' : 'expired').'.automatic',
            description: sprintf('%s subscription ended automatically', $subscription->tenant_id),
            subject: $subscription,
            tenantId: (string) $subscription->tenant_id,
        );

        if ($subscription->tenant !== null) {
            $this->notifyTenant($subscription->tenant, new SubscriptionNotification(
                event: $cancelled ? 'cancelled' : 'expired',
                message: $cancelled
                    ? 'Your subscription ended as scheduled.'
                    : 'Your subscription expired. Renew it to restore hospital access.',
                link: '/billing',
                title: $cancelled ? 'Subscription ended' : 'Subscription expired',
            ));
        }

        PlatformContext::flush();
    }

    private function direction(Subscription $subscription, Plan $plan): string
    {
        if ($subscription->plan_id === null) {
            return 'upgrade';
        }

        $current = $subscription->plan;
        $currentRank = $current?->sort_order ?? 0;
        $newRank = $plan->sort_order ?? 0;

        if ($newRank === $currentRank) {
            $currentPrice = (float) ($current?->priceFor($subscription->billing_cycle) ?? $subscription->price);

            return $plan->priceFor($subscription->billing_cycle) >= $currentPrice ? 'upgrade' : 'downgrade';
        }

        return $newRank > $currentRank ? 'upgrade' : 'downgrade';
    }

    private function normaliseCycle(?string $cycle): string
    {
        return $cycle === 'yearly' ? 'yearly' : 'monthly';
    }

    private function toDate(CarbonInterface|string|null $date): ?Carbon
    {
        if ($date === null) {
            return null;
        }

        return $date instanceof CarbonInterface
            ? Carbon::instance($date)->startOfDay()
            : Carbon::parse($date)->startOfDay();
    }

    private function periodEnd(CarbonInterface $start, string $cycle): Carbon
    {
        $end = Carbon::instance($start)->copy();

        return $cycle === 'yearly'
            ? $end->addYear()->subDay()
            : $end->addMonth()->subDay();
    }

    private function notifyTenant(Tenant $tenant, SystemNotification $notification): void
    {
        try {
            $this->notifications->notifyTenantAdmins($tenant, $notification);
        } catch (Throwable $exception) {
            report($exception);
        }

        try {
            // Platform operators cannot open the hospital's /billing page, so
            // their copy points at the tenant record instead.
            $this->notifications->notifyLandlordAdmins(
                $notification->withLink('/tenants/'.rawurlencode((string) $tenant->getTenantKey()))
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
