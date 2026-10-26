<?php

declare(strict_types=1);

use App\Enums\PlanFeature;
use App\Enums\SubscriptionStatus;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Platform\PlanLimitService;
use App\Services\Platform\SubscriptionService;
use App\Services\Platform\TenantService;
use App\Support\PlatformContext;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SuperAdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->seed(SuperAdminRoleSeeder::class);

    $this->tenant = Tenant::factory()->create(['id' => 'hopital-limites', 'name' => 'Hôpital des Limites']);
    $this->plan = Plan::factory()->basic()->create([
        'max_users_per_tenant' => 3,
        'max_patients_per_tenant' => 2,
    ]);

    $this->service = app(SubscriptionService::class);
    $this->service->assignPlan($this->tenant, $this->plan);
});

describe('quota enforcement', function () {
    it('blocks creating a user past the plan seat limit', function () {
        User::factory()->count(3)->create();

        actAsTenant($this->tenant);

        $limits = app(PlanLimitService::class);

        expect($limits->usage('users'))->toBe(3)
            ->and($limits->remaining('users'))->toBe(0)
            ->and($limits->exceeded('users'))->toBeTrue();

        expect(fn () => $limits->ensureCan('users'))
            ->toThrow(ValidationException::class);
    });

    it('allows creating a user while seats remain', function () {
        User::factory()->count(2)->create();

        actAsTenant($this->tenant);

        $limits = app(PlanLimitService::class);

        expect($limits->remaining('users'))->toBe(1);

        $limits->ensureCan('users');

        expect(true)->toBeTrue();
    });

    it('treats a null limit as unlimited', function () {
        Subscription::query()->update(['plan_id' => Plan::factory()->enterprise()->create()->id]);

        User::factory()->count(25)->create();

        actAsTenant($this->tenant);

        $limits = app(PlanLimitService::class);

        expect($limits->limit('users'))->toBe(500)
            ->and($limits->exceeded('users'))->toBeFalse();

        // An explicit `null` override means "unlimited" for this tenant.
        $this->service->updateOverrides($this->tenant, ['users' => null]);

        expect(app(PlanLimitService::class)->limit('users'))->toBeNull()
            ->and(app(PlanLimitService::class)->exceeded('users'))->toBeFalse();
    });

    it('applies a per-tenant override instead of the plan limit', function () {
        actAsTenant($this->tenant);

        User::factory()->count(3)->create();

        $this->service->updateOverrides($this->tenant, ['users' => 10]);

        expect(app(PlanLimitService::class)->exceeded('users'))->toBeFalse();
    });

    it('tells the hospital clearly when the patient quota is reached', function () {
        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $admin = tap(User::factory()->create())->assignRole('admin');
        Patient::factory()->count(2)->create();

        actAsTenant($this->tenant);

        $response = $this->actingAs($admin)
            ->from(route('patients.create'))
            ->post(route('patients.store'), [
                'first_name' => 'Awa',
                'last_name' => 'Nkeng',
            ]);

        $response->assertRedirect(route('patients.create'));
        $response->assertSessionHas('error', fn (string $message) => str_contains($message, 'Plan limit reached')
            && str_contains($message, '2 patients'));

        $response->assertSessionHasErrors(['patients']);

        expect(Patient::query()->count())->toBe(2);
    });

    it('reports usage against every quota', function () {
        actAsTenant($this->tenant);

        $summary = app(PlanLimitService::class)->summary();

        expect($summary)->toHaveKeys(['users', 'patients', 'storage_gb'])
            ->and($summary['users']['limit'])->toBe(3)
            ->and($summary['users']['used'])->toBe(0)
            ->and($summary['users']['remaining'])->toBe(3);
    });
});

describe('feature gating', function () {
    it('denies a module the plan does not include', function () {
        // Basic does not include radiology.
        actAsTenant($this->tenant);

        expect(app(PlanLimitService::class)->featureEnabled(PlanFeature::Radiology))->toBeFalse()
            ->and(app(PlanLimitService::class)->featureEnabled(PlanFeature::Pharmacy))->toBeTrue();
    });

    it('serves the radiology module once the feature is granted', function () {
        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $admin = tap(User::factory()->create())->assignRole('admin');
        actAsTenant($this->tenant);

        $this->actingAs($admin)
            ->get(route('radiology.requests.index'))
            ->assertStatus(403);

        $this->service->updateOverrides($this->tenant, [], [PlanFeature::Radiology->value => true]);

        $this->actingAs($admin)
            ->get(route('radiology.requests.index'))
            ->assertOk();
    });
});

describe('subscription status gate', function () {
    it('lets an active subscription through', function () {
        actAsTenant($this->tenant);

        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $admin = tap(User::factory()->create())->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('patients.index'))
            ->assertOk();
    });

    it('blocks the hospital app when the subscription expired', function () {
        $this->service->expire($this->tenant);

        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $admin = tap(User::factory()->create())->assignRole('admin');
        actAsTenant($this->tenant);

        $this->actingAs($admin)
            ->get(route('patients.index'))
            ->assertStatus(402);

        // The billing portal stays reachable so the hospital can renew.
        $this->actingAs($admin)
            ->get(route('tenant.billing'))
            ->assertOk();
    });

    it('blocks a suspended tenant but keeps support reachable', function () {
        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $admin = tap(User::factory()->create())->assignRole('admin');

        app(TenantService::class)->suspend($this->tenant, 'Unpaid balance');
        actAsTenant($this->tenant->fresh());

        $this->actingAs($admin)
            ->get(route('patients.index'))
            ->assertStatus(403);

        $this->actingAs($admin)
            ->get(route('tenant.billing'))
            ->assertOk();
    });

    it('blocks shared account pages while a tenant is suspended', function () {
        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $admin = tap(User::factory()->create())->assignRole('admin');

        app(TenantService::class)->suspend($this->tenant, 'Unpaid balance');
        actAsTenant($this->tenant->fresh());

        $this->actingAs($admin)
            ->get(route('settings'))
            ->assertStatus(403);

        // Profile stays in the middleware's allowlist for suspended tenants.
        $this->actingAs($admin)
            ->get(route('profile'))
            ->assertOk();
    });

    it('keeps working when no subscription exists at all', function () {
        Subscription::query()->delete();

        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $admin = tap(User::factory()->create())->assignRole('admin');
        actAsTenant($this->tenant->fresh());

        $this->actingAs($admin)
            ->get(route('patients.index'))
            ->assertOk();
    });

    it('exposes the plan context to the frontend', function () {
        actAsTenant($this->tenant);

        $context = app(PlatformContext::class)->forTenant($this->tenant);

        expect($context['subscription']['status'])->toBe(SubscriptionStatus::ACTIVE->value)
            ->and($context['limits']['users'])->toBe(3)
            ->and($context['features'])->toHaveKey(PlanFeature::Patients->value);
    });
});
