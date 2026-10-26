<?php

declare(strict_types=1);

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\PlatformAuditLog;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TenantLifecycleNotification;
use App\Services\Platform\SubscriptionService;
use App\Support\PlatformContext;
use Database\Seeders\SuperAdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    Notification::fake();
    $this->seed(SuperAdminRoleSeeder::class);

    $this->superAdmin = User::factory()->create(['email' => 'root@sanago.cm']);
    $this->superAdmin->assignRole('super-admin');

    $this->salesRep = User::factory()->create(['email' => 'sales@sanago.cm']);
    $this->salesRep->assignRole('sales-rep');

    $this->billingManager = User::factory()->create(['email' => 'billing@sanago.cm']);
    $this->billingManager->assignRole('billing-manager');

    $this->tenant = Tenant::factory()->create(['id' => 'hopital-berton', 'name' => 'Hôpital de Bertoua']);
    $this->otherTenant = Tenant::factory()->create(['id' => 'clinique-kribi', 'name' => 'Clinique de Kribi']);

    $this->plan = Plan::factory()->basic()->create();
    $this->professional = Plan::factory()->professional()->create();

    $this->subscriptions = app(SubscriptionService::class);
});

describe('panel access', function () {
    it('serves every platform screen to the super admin', function (string $routeName) {
        $response = $this->actingAs($this->superAdmin)->get(route($routeName));

        expect($response->status())->toBeLessThan(500);
    })->with([
        'dashboard.super-admin',
        'tenants.index',
        'plans.index',
        'subscriptions.index',
        'platform.settings',
        'platform.billing',
        'platform.audit',
        'platform.health',
    ]);

    it('denies tenant management to a sales representative', function () {
        // Before the Gate::before fix any landlord role could do this.
        $this->actingAs($this->salesRep)
            ->get(route('tenants.index'))
            ->assertForbidden();

        $this->actingAs($this->salesRep)
            ->post(route('tenants.suspend', $this->tenant))
            ->assertForbidden();
    });

    it('lets a billing manager read tenants but not suspend them', function () {
        $this->actingAs($this->billingManager)
            ->get(route('tenants.index'))
            ->assertOk();

        $this->actingAs($this->billingManager)
            ->post(route('tenants.suspend', $this->tenant))
            ->assertForbidden();
    });

    it('forbids unauthenticated access to the panel', function () {
        $this->get(route('tenants.index'))->assertRedirect(route('login'));
    });
});

describe('shared account pages', function () {
    it('renders the profile page inside the landlord context', function () {
        $this->actingAs($this->superAdmin)
            ->get(route('profile'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Profile/Show', false)
                ->where('isLandlord', true));
    });

    it('routes landlord settings to the platform settings screen', function () {
        $this->actingAs($this->superAdmin)
            ->get(route('settings'))
            ->assertRedirect(route('platform.settings'));
    });

    it('denies platform settings to landlord roles without the permission', function () {
        $this->actingAs($this->salesRep)
            ->get(route('settings'))
            ->assertForbidden();
    });

    it('shows platform notifications to landlord users without tenant finance permissions', function () {
        $this->salesRep->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => TenantLifecycleNotification::class,
            'data' => [
                'message' => 'New hospital provisioned',
                'category' => 'platform',
                'type_label' => 'tenant',
            ],
            'read_at' => null,
        ]);

        $this->actingAs($this->salesRep)
            ->get(route('landlord.notifications.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Notifications/Index', false)
                ->where('isLandlord', true)
                ->has('notifications.data', 1));
    });
});

describe('tenant lifecycle', function () {
    it('shows the hospital name instead of its slug', function () {
        $this->actingAs($this->superAdmin)
            ->get(route('tenants.show', $this->tenant))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Tenants/Show')
                ->where('tenant.name', 'Hôpital de Bertoua'));
    });

    it('suspends and reactivates a tenant', function () {
        $this->actingAs($this->superAdmin)
            ->post(route('tenants.suspend', $this->tenant), ['reason' => 'Impayé'])
            ->assertRedirect();

        $suspended = $this->tenant->fresh();

        expect($suspended->is_active)->toBeFalse()
            ->and($suspended->isSuspended())->toBeTrue()
            ->and($suspended->isActive())->toBeFalse();

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'tenant.suspended',
            'tenant_id' => 'hopital-berton',
        ]);

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.reactivate', $this->tenant))
            ->assertRedirect();

        expect($this->tenant->fresh()->isActive())->toBeTrue();
    });

    it('archives and restores a tenant without deleting its data', function () {
        $this->actingAs($this->superAdmin)
            ->post(route('tenants.archive', $this->tenant), ['reason' => 'Closed'])
            ->assertRedirect(route('tenants.index'));

        expect($this->tenant->fresh()->isArchived())->toBeTrue()
            ->and($this->tenant->fresh()->isActive())->toBeFalse();

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.restore', $this->tenant))
            ->assertRedirect();

        expect($this->tenant->fresh()->isArchived())->toBeFalse();
    });

    it('refuses a permanent delete without the confirmation slug', function () {
        $this->actingAs($this->superAdmin)
            ->delete(route('tenants.destroy', $this->tenant))
            ->assertRedirect();

        // The hospital is still there and no purge was logged.
        expect(Tenant::query()->where('id', 'hopital-berton')->exists())->toBeTrue()
            ->and(PlatformAuditLog::query()->where('action', 'tenant.purged')->exists())->toBeFalse();
    });

    it('stores internal notes', function () {
        $this->actingAs($this->superAdmin)
            ->post(route('tenants.add-note', $this->tenant), ['note' => 'Called the director'])
            ->assertRedirect();

        expect($this->tenant->fresh()->notes()->count())->toBe(1)
            ->and($this->tenant->fresh()->notes()->first()->note)->toBe('Called the director');
    });

    it('records a support-access audit entry when impersonating', function () {
        $this->tenant->domains()->create(['domain' => 'bertoua.localhost']);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('tenants.impersonate', $this->tenant), ['user_id' => 7]);

        $response->assertRedirect();
        expect($response->headers->get('Location'))
            ->toContain('bertoua.localhost')
            ->toContain('/impersonate/');

        // The token is created through stancl/tenancy's impersonation macro.
        $this->assertDatabaseHas('tenant_user_impersonation_tokens', [
            'tenant_id' => 'hopital-berton',
            'user_id' => '7',
        ]);

        $this->assertDatabaseHas('platform_audit_logs', [
            'action' => 'tenant.impersonation_started',
            'tenant_id' => 'hopital-berton',
        ]);
    });
});

describe('per-tenant subscription control', function () {
    it('assigns a plan to one tenant only', function () {
        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.assign', $this->tenant), [
                'plan_id' => $this->plan->id,
                'billing_cycle' => 'monthly',
                'trial' => false,
            ])
            ->assertRedirect();

        expect(Subscription::query()->where('tenant_id', 'hopital-berton')->value('plan_name'))->toBe('Basic')
            ->and(Subscription::query()->where('tenant_id', 'clinique-kribi')->exists())->toBeFalse();
    });

    it('upgrades and downgrades through the scoped endpoint', function () {
        $this->subscriptions->assignPlan($this->tenant, $this->plan);

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.change-plan', $this->tenant), [
                'plan_id' => $this->professional->id,
                'immediate' => true,
            ])
            ->assertRedirect();

        expect($this->subscriptions->forTenant($this->tenant)->plan_name)->toBe('Professional');

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.change-plan', $this->tenant), [
                'plan_id' => $this->plan->id,
                'immediate' => true,
            ])
            ->assertRedirect();

        expect($this->subscriptions->forTenant($this->tenant)->plan_name)->toBe('Basic');
    });

    it('renews, extends, cancels and resumes', function () {
        $this->subscriptions->assignPlan($this->tenant, $this->plan);
        $end = $this->subscriptions->forTenant($this->tenant)->end_date;

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.extend', $this->tenant), ['days' => 5])
            ->assertRedirect();

        expect($this->subscriptions->forTenant($this->tenant)->end_date?->toDateString())
            ->toBe($end->copy()->addDays(5)->toDateString());

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.cancel', $this->tenant), ['immediately' => false])
            ->assertRedirect();

        expect($this->subscriptions->forTenant($this->tenant)->cancel_at_period_end)->toBeTrue();

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.resume', $this->tenant))
            ->assertRedirect();

        expect($this->subscriptions->forTenant($this->tenant)->cancel_at_period_end)->toBeFalse();

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.renew', $this->tenant))
            ->assertRedirect();

        expect($this->subscriptions->forTenant($this->tenant)->last_renewed_at)->not->toBeNull();
    });

    it('applies limit overrides for a single tenant', function () {
        $this->subscriptions->assignPlan($this->tenant, $this->plan);
        $this->subscriptions->assignPlan($this->otherTenant, $this->plan);

        $this->actingAs($this->superAdmin)
            ->put(route('tenants.subscription.overrides', $this->tenant), [
                'limits' => ['users' => 25],
            ])
            ->assertRedirect();

        expect($this->subscriptions->forTenant($this->tenant)->limit('users'))->toBe(25)
            ->and($this->subscriptions->forTenant($this->otherTenant)->limit('users'))->toBe(10);
    });

    it('rejects a plan change for an unknown plan', function () {
        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.change-plan', $this->tenant), [
                'plan_id' => 9999,
                'immediate' => true,
            ])
            ->assertSessionHasErrors('plan_id');
    });

    it('marks invoices paid for the tenant that owns them', function () {
        $this->subscriptions->assignPlan($this->tenant, $this->plan);
        $invoice = $this->tenant->invoices()->firstOrFail();

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.pay-invoice', [$this->tenant, $invoice]), [
                'payment_method' => 'mobile_money',
                'reference' => 'OM-4412',
            ])
            ->assertRedirect();

        expect($invoice->fresh()->paid_at)->not->toBeNull();
    });

    it('refuses to pay another tenant invoice', function () {
        $this->subscriptions->assignPlan($this->tenant, $this->plan);
        $invoice = $this->tenant->invoices()->firstOrFail();

        $this->actingAs($this->superAdmin)
            ->post(route('tenants.subscription.pay-invoice', [$this->otherTenant, $invoice]), [
                'payment_method' => 'cash',
            ])
            ->assertNotFound();
    });

    it('shows the proration preview as JSON', function () {
        $this->subscriptions->assignPlan($this->tenant, $this->plan);

        $this->actingAs($this->superAdmin)
            ->getJson(route('tenants.subscription.preview', [
                'tenant' => $this->tenant,
                'plan_id' => $this->professional->id,
            ]))
            ->assertOk()
            ->assertJsonStructure(['amount_due', 'direction', 'days_remaining']);
    });
});

describe('platform tools', function () {
    it('saves platform settings', function () {
        $this->actingAs($this->superAdmin)
            ->put(route('platform.settings.update'), [
                'platform_name' => 'SanaGo Cameroun',
                'currency' => 'XAF',
                'currency_symbol' => 'FCFA',
                'default_locale' => 'fr',
                'supported_locales' => ['en', 'fr'],
                'trial_days' => 14,
                'grace_days' => 3,
                'support_email' => 'support@sanago.cm',
                'realtime_enabled' => true,
                'email_notifications_enabled' => true,
                'maintenance_mode' => false,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('platform_settings', ['key' => 'platform_name']);
        expect(app(PlatformContext::class)->setting('platform_name'))->toBe('SanaGo Cameroun');
    });

    it('exposes the audit trail with the actions performed', function () {
        $this->actingAs($this->superAdmin)
            ->post(route('tenants.suspend', $this->tenant), ['reason' => 'test'])
            ->assertRedirect();

        $this->actingAs($this->superAdmin)
            ->get(route('platform.audit'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Platform/AuditLog', false)
                ->has('logs.data', 1)
                ->where('logs.data.0.action', 'tenant.suspended'));
    });

    it('summarises billing across tenants', function () {
        $this->subscriptions->assignPlan($this->tenant, $this->plan);

        $this->actingAs($this->superAdmin)
            ->get(route('platform.billing'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Platform/Billing', false)
                ->has('rows', 1)
                ->where('rows.0.tenant_id', 'hopital-berton'));
    });

    it('reports zero subscription debt for a healthy tenant', function () {
        $this->subscriptions->assignPlan($this->tenant, $this->plan);

        expect($this->subscriptions->forTenant($this->tenant)->effectiveStatus())
            ->toBe(SubscriptionStatus::ACTIVE)
            ->and(PlatformAuditLog::query()->where('action', 'subscription.assigned')->exists())->toBeTrue();
    });
});
