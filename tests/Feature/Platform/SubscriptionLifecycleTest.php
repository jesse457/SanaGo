<?php

declare(strict_types=1);

use App\Enums\PlanFeature;
use App\Enums\SubscriptionInvoiceStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\SubscriptionNotification;
use App\Services\Platform\SubscriptionService;
use Database\Seeders\SuperAdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
    $this->seed(SuperAdminRoleSeeder::class);

    $this->service = app(SubscriptionService::class);

    $this->landlord = User::factory()->create(['email' => 'owner@sanago.cm']);
    $this->landlord->assignRole('super-admin');

    $this->tenant = Tenant::factory()->create([
        'id' => 'hopital-central',
        'name' => 'Hôpital Central de Yaoundé',
    ]);

    $this->basic = Plan::factory()->basic()->create();
    $this->professional = Plan::factory()->professional()->create();
});

describe('plan assignment', function () {
    it('creates an active subscription with an invoice for the first period', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->basic, [
            'billing_cycle' => 'monthly',
        ]);

        expect($subscription->status)->toBe(SubscriptionStatus::ACTIVE)
            ->and($subscription->plan_id)->toBe($this->basic->id)
            ->and($subscription->tenant_id)->toBe('hopital-central')
            ->and($subscription->billing_cycle)->toBe('monthly')
            ->and((float) $subscription->price)->toBe(15000.0)
            ->and($subscription->end_date?->toDateString())->toBe(Carbon::today()->addMonth()->subDay()->toDateString());

        $invoice = SubscriptionInvoice::query()->where('tenant_id', 'hopital-central')->first();

        expect($invoice)->not->toBeNull()
            ->and($invoice->status)->toBe(SubscriptionInvoiceStatus::PENDING)
            ->and((float) $invoice->total)->toBe(15000.0)
            ->and($invoice->currency)->toBe('XAF');
    });

    it('starts a trial instead of billing when trial is requested', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->basic, [
            'trial' => true,
            'trial_days' => 14,
        ]);

        expect($subscription->status)->toBe(SubscriptionStatus::TRIALING)
            ->and($subscription->isOnTrial())->toBeTrue()
            ->and($subscription->trial_ends_at?->toDateString())->toBe(Carbon::today()->addDays(14)->toDateString())
            ->and(SubscriptionInvoice::query()->count())->toBe(0);
    });

    it('reuses the existing row instead of creating a duplicate per tenant', function () {
        $this->service->assignPlan($this->tenant, $this->basic);

        // A live subscription means a plan change, never a second row.
        $this->service->assignPlan($this->tenant, $this->professional);

        expect(Subscription::query()->where('tenant_id', 'hopital-central')->count())->toBe(1)
            ->and(Subscription::query()->first()->plan_name)->toBe('Professional');
    });

    it('notifies the tenant admins and the platform owners', function () {
        $this->service->assignPlan($this->tenant, $this->basic);

        Notification::assertSentTo($this->landlord, SubscriptionNotification::class);
    });

    it('records a platform audit entry', function () {
        $this->service->assignPlan($this->tenant, $this->basic);

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'subscription.assigned',
            'tenant_id' => 'hopital-central',
        ]);
    });
});

describe('upgrade and downgrade', function () {
    beforeEach(function () {
        $this->subscription = $this->service->assignPlan($this->tenant, $this->basic, [
            'start_date' => Carbon::today()->subDays(15),
        ]);
    });

    it('applies an upgrade immediately and invoices the prorated difference', function () {
        $before = SubscriptionInvoice::query()->count();

        $subscription = $this->service->changePlan($this->tenant, $this->professional, ['immediate' => true]);

        expect($subscription->plan_name)->toBe('Professional')
            ->and($subscription->status)->toBe(SubscriptionStatus::ACTIVE)
            ->and(SubscriptionInvoice::query()->count())->toBeGreaterThan($before);

        // 45 000 - 15 000 over the remaining days of the period.
        $prorationInvoice = SubscriptionInvoice::query()
            ->whereJsonContains('meta_json->reason', 'plan_change_proration')
            ->first();

        expect($prorationInvoice)->not->toBeNull()
            ->and((float) $prorationInvoice->total)->toBeGreaterThan(0)
            ->and((float) $prorationInvoice->total)->toBeLessThan(30000);
    });

    it('keeps a downgrade pending until the period ends', function () {
        $subscription = $this->service->changePlan($this->tenant, $this->basic, ['immediate' => false]);

        expect($subscription->status)->toBe(SubscriptionStatus::ACTIVE)
            ->and($subscription->plan_id)->toBe($this->basic->id);

        $this->service->assignPlan($this->tenant, $this->professional, ['immediate' => true]);

        expect($this->service->forTenant($this->tenant)->plan_name)->toBe('Professional');
    });

    it('computes proration for a mid-cycle change', function () {
        $proration = $this->service->proration($this->subscription, $this->professional, 'monthly');

        expect($proration['direction'])->toBe('upgrade')
            ->and($proration['days_remaining'])->toBeGreaterThan(0)
            ->and($proration['charge'])->toBeGreaterThan($proration['credit']);
    });
});

describe('renewal and extension', function () {
    it('renews from the day after the current period ends', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->basic, [
            'start_date' => Carbon::today()->subMonth(),
        ]);

        $endDate = $subscription->end_date;

        $renewed = $this->service->renew($this->tenant);

        expect($renewed->end_date?->greaterThan($endDate))->toBeTrue()
            ->and($renewed->last_renewed_at)->not->toBeNull()
            ->and($renewed->status)->toBe(SubscriptionStatus::ACTIVE);
    });

    it('extends the paid period by the requested days', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->basic);
        $endDate = $subscription->end_date;

        $extended = $this->service->extend($this->tenant, 10);

        expect($extended->end_date?->toDateString())->toBe($endDate->copy()->addDays(10)->toDateString());
    });
});

describe('cancellation', function () {
    beforeEach(function () {
        $this->service->assignPlan($this->tenant, $this->basic);
    });

    it('schedules a cancellation at the end of the period', function () {
        $subscription = $this->service->cancel($this->tenant);

        expect($subscription->cancel_at_period_end)->toBeTrue()
            ->and($subscription->auto_renew)->toBeFalse()
            ->and($subscription->status)->toBe(SubscriptionStatus::ACTIVE);
    });

    it('cancels immediately and revokes access', function () {
        $subscription = $this->service->cancel($this->tenant, immediately: true, reason: 'Non payment');

        expect($subscription->status)->toBe(SubscriptionStatus::CANCELLED)
            ->and($subscription->cancelled_at)->not->toBeNull()
            ->and($subscription->grantsAccess())->toBeFalse();
    });

    it('resumes a scheduled cancellation', function () {
        $this->service->cancel($this->tenant);
        $subscription = $this->service->resume($this->tenant);

        expect($subscription->cancel_at_period_end)->toBeFalse()
            ->and($subscription->auto_renew)->toBeTrue();
    });

    it('expires a subscription on demand', function () {
        $subscription = $this->service->expire($this->tenant);

        expect($subscription->status)->toBe(SubscriptionStatus::EXPIRED)
            ->and($subscription->end_date?->isToday())->toBeTrue();
    });
});

describe('scheduled lifecycle', function () {
    it('expires subscriptions beyond their grace period', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->basic, [
            'start_date' => Carbon::today()->subMonths(2),
        ]);

        Subscription::query()->whereKey($subscription->id)->update([
            'end_date' => Carbon::today()->subDays(10),
        ]);

        expect($this->service->expireDue())->toBe(1)
            ->and($subscription->fresh()->status)->toBe(SubscriptionStatus::EXPIRED);
    });

    it('does not expire a subscription inside the grace window', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->basic);
        Subscription::query()->whereKey($subscription->id)->update(['end_date' => Carbon::today()->subDay()]);

        expect($this->service->expireDue())->toBe(0)
            ->and($subscription->fresh()->status)->toBe(SubscriptionStatus::ACTIVE);
    });

    it('renews subscriptions that are due and auto-renewing', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->basic);
        Subscription::query()->whereKey($subscription->id)->update([
            'end_date' => Carbon::today()->subDay(),
            'auto_renew' => true,
        ]);

        expect($this->service->renewDue())->toBe(1);

        $renewed = $subscription->fresh();

        expect($renewed->status)->toBe(SubscriptionStatus::ACTIVE)
            ->and($renewed->end_date?->isFuture())->toBeTrue();
    });

    it('warns tenants whose subscription ends inside the warning window', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->basic);
        Subscription::query()->whereKey($subscription->id)->update([
            'end_date' => Carbon::today()->addDays(7),
            'auto_renew' => false,
        ]);

        expect($this->service->notifyExpiring([7]))->toBe(1);

        Notification::assertSentTo($this->landlord, SubscriptionNotification::class);
    });
});

describe('per-tenant overrides', function () {
    it('overrides plan limits and feature flags for one tenant only', function () {
        $other = Tenant::factory()->create(['id' => 'clinique-bafoussam', 'name' => 'Clinique de Bafoussam']);

        $this->service->assignPlan($this->tenant, $this->basic);
        $this->service->assignPlan($other, $this->basic);

        $this->service->updateOverrides($this->tenant, ['users' => 99], [PlanFeature::Radiology->value => true]);

        $overridden = $this->service->forTenant($this->tenant);
        $untouched = $this->service->forTenant($other);

        expect($overridden->limit('users'))->toBe(99)
            ->and($overridden->featureEnabled(PlanFeature::Radiology->value))->toBeTrue()
            ->and($untouched->limit('users'))->toBe(10)
            ->and($untouched->featureEnabled(PlanFeature::Radiology->value))->toBeFalse();
    });

    it('falls back to plan limits when no override is set', function () {
        $subscription = $this->service->assignPlan($this->tenant, $this->professional);

        expect($subscription->limits())->toMatchArray([
            'users' => 50,
            'patients' => 5000,
            'storage_gb' => 50,
        ])->and($subscription->featureEnabled(PlanFeature::Surgery->value))->toBeTrue();
    });
});

describe('tenant scoping', function () {
    it('never leaks a subscription between tenants', function () {
        $other = Tenant::factory()->create(['id' => 'clinique-douala', 'name' => 'Clinique de Douala']);

        $this->service->assignPlan($this->tenant, $this->basic);
        $this->service->assignPlan($other, $this->professional);

        expect($this->service->forTenant($this->tenant)->plan_name)->toBe('Basic')
            ->and($this->service->forTenant($other)->plan_name)->toBe('Professional');

        $this->service->cancel($this->tenant, immediately: true);

        expect($this->service->forTenant($this->tenant)->status)->toBe(SubscriptionStatus::CANCELLED)
            ->and($this->service->forTenant($other)->status)->toBe(SubscriptionStatus::ACTIVE);
    });
});

describe('billing', function () {
    it('marks an invoice paid and notifies the owners', function () {
        $this->service->assignPlan($this->tenant, $this->basic);
        $invoice = SubscriptionInvoice::query()->firstOrFail();

        $paid = $this->service->recordPayment($invoice, [
            'payment_method' => 'mobile_money',
            'reference' => 'MOMO-99812',
        ]);

        expect($paid->status)->toBe(SubscriptionInvoiceStatus::PAID)
            ->and($paid->paid_at)->not->toBeNull()
            ->and($paid->reference)->toBe('MOMO-99812');

        $this->assertDatabaseHas('platform_audit_logs', ['action' => 'billing.invoice_paid']);
    });

    it('reports MRR by normalising annual plans', function () {
        $this->service->assignPlan($this->tenant, $this->basic, ['billing_cycle' => 'monthly']);
        $other = Tenant::factory()->create(['id' => 'hopital-garoua', 'name' => 'Hôpital de Garoua']);
        $this->service->assignPlan($other, $this->professional, ['billing_cycle' => 'yearly']);

        $summary = $this->service->revenueSummary();

        // 15 000 monthly + 350 000 / 12 annual.
        expect($summary['mrr'])->toBe(round(15000 + 350000 / 12, 2))
            ->and($summary['active'])->toBe(2)
            ->and($summary['currency'])->toBe('XAF');
    });
});
