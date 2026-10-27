# Testing

The project uses Pest 4 on top of PHPUnit. Most tests are feature tests that exercise HTTP routes, tenancy gates, Inertia responses, and services.

## Commands

```bash
php artisan test --compact                 # all tests
php artisan test --compact --filter=Name   # one test/filter
php artisan test --compact tests/Feature/Platform/PlanLimitEnforcementTest.php
composer test                              # Pint check + all tests
composer ci:check                          # frontend checks + Pint + tests
```

Pest's CLI also works:

```bash
./vendor/bin/pest
./vendor/bin/pest --filter="tenant"
```

## Test environment

`phpunit.xml` forces the test environment:

- `APP_ENV=testing`
- `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`
- `SESSION_DRIVER=array`, `QUEUE_CONNECTION=sync`, `CACHE_STORE=array`
- `BROADCAST_CONNECTION=null`, `MAIL_MAILER=array`
- Platform notification transports disabled

Tests run against a single in-memory database. There is no external database setup.

## Test case setup

`tests/Pest.php` binds the Laravel `TestCase` to `tests/Feature` only and provides:

- `userWithPermission(string $permission)` — user with a role granting one permission
- `unauthorizedUser()` — user with no roles/permissions
- `actAsTenant(Tenant $tenant)` — sets the tenant in the tenancy container without switching databases (for middleware/plan tests)
- `forgetTenant()` — clears tenant context
- A global `afterEach` that resets the tenancy container and the Eloquent connection resolver

`RefreshDatabase` is **not** global. Add it per test file:

```php
uses(RefreshDatabase::class);
```

## Tenant-aware tests

The in-memory database cannot survive the package's connection swap, so tenant tests use a lighter setup:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);

    $this->admin = tap(User::factory()->create())->assignRole('admin');

    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the patients index', function () {
    $this->actingAs($this->admin)
        ->get(route('patients.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Patients/Index'));
});
```

What the helpers do:

- `migrateTenantTables()` runs `database/migrations/test`. It is a no-op if `patients` already exists.
- `actAsTenant($tenant)` sets the tenant so plan/subscription/tenant middleware see it.
- Disabling the two tenancy middleware classes lets tenant routes resolve without a real subdomain or connection swap.

For central models in tests (Tenant, Plan, Subscription, SupportTicket, etc.), call `resolveCentralToDefault()` so the `central` connection shares the in-memory PDO:

```php
use function Tests\Helpers\resolveCentralToDefault;

resolveCentralToDefault();
```

## Central/platform tests

Platform tests often create tenants and subscriptions directly:

```php
use App\Models\Plan;
use App\Models\Tenant;
use App\Services\Platform\SubscriptionService;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->seed(SuperAdminRoleSeeder::class);

    $this->tenant = Tenant::factory()->create(['id' => 'hopital-limites']);
    $this->plan = Plan::factory()->basic()->create(['max_users_per_tenant' => 3]);

    app(SubscriptionService::class)->assignPlan($this->tenant, $this->plan);
});
```

Use `Plan::factory()->basic()`, `->professional()`, `->hospital()`, and `->enterprise()` to get consistent capability sets. `hospital()` grants every capability except `api_access` and `sms_notifications`.

## Testing plan and subscription gates

Set the tenant context, then hit tenant routes:

```php
actAsTenant($this->tenant);

$this->actingAs($admin)
    ->get(route('radiology.requests.index'))
    ->assertForbidden();
```

`EnsurePlanFeature` returns 403 and `EnsureSubscriptionIsActive` returns 402 for page requests. `EnforcePlanLimit` redirects back with `error` and validation errors on writes.

## Inertia assertions

```php
$response->assertInertia(fn ($page) => $page
    ->component('Patients/Index')
    ->has('patients.data', 3)
    ->where('filters.search', 'Awa')
);
```

Use `assertInertia` for component names and props. Use ordinary status/session assertions for redirects and flash messages.

## Service tests

Services can be resolved from the container and tested directly:

```php
use App\Services\Billing\BillingService;

it('creates a draft invoice', function () {
    $patient = Patient::factory()->create();
    $invoice = app(BillingService::class)->findOrCreateDraftInvoice($patient->id);

    expect($invoice->status)->toBe(InvoiceStatus::DRAFT);
});
```

Tenant service tests still need `migrateTenantTables()` and usually `withoutMiddleware()` only when hitting routes.

## Factories

Factories live in `database/factories`. Prefer named states where they exist:

- `TenantFactory`
- `PlanFactory` with `basic()`, `professional()`, `hospital()`, `enterprise()`
- `SubscriptionFactory`
- Domain factories: `PatientFactory`, `AppointmentFactory`, `AdmissionFactory`, `InvoiceFactory`, `LabRequestFactory`, `RadiologyRequestFactory`, etc.

Do not create models with raw SQL. Use factories and seeders.

## Conventions

- Put most tests in `tests/Feature`; use `tests/Unit` only for pure logic.
- Name files `SomethingTest.php` and tests with `it('...')` / `test('...')`.
- Seed only the roles needed by the test (`RoleSeeder`, `SuperAdminRoleSeeder`).
- Assert the user-visible outcome (status, redirect, Inertia component, flash, database state).
- Reset tenancy via the global hook; avoid leaving `tenancy()->tenant` set.

## CI

`.github/workflows/tests.yml` runs on PHP 8.3, 8.4, and 8.5. It installs Composer/Bun dependencies, builds assets, and runs `./vendor/bin/pest`. Match that locally with `composer ci:check` before opening a PR.
