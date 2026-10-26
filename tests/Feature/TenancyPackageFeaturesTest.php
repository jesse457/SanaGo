<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantDatabase;
use Database\Seeders\SuperAdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\CrossDomainRedirect;
use Stancl\Tenancy\Features\UniversalRoutes;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;

use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

it('boots the package features the application relies on', function () {
    expect(config('tenancy.features'))
        ->toContain(UserImpersonation::class)
        ->toContain(UniversalRoutes::class)
        ->toContain(CrossDomainRedirect::class);
});

it('registers the package cross-domain redirect macro', function () {
    tenancy();

    expect(RedirectResponse::hasMacro('domain'))->toBeTrue();
});

it('serves shared routes as universal through the package subdomain middleware', function () {
    $route = Route::getRoutes()->getByName('login');

    expect($route->gatherMiddleware())
        ->toContain('universal')
        ->toContain(InitializeTenancyBySubdomain::class);
});

it('keeps the support-access token ttl in sync with platform config', function () {
    expect(UserImpersonation::$ttl)->toBe((int) config('platform.impersonation.ttl'));
});

it('caches domain-to-tenant resolution with the package resolver', function () {
    resolveCentralToDefault();

    $tenant = Tenant::factory()->create();
    $tenant->domains()->create(['domain' => 'cached.localhost']);

    $resolver = app(DomainTenantResolver::class);
    $first = $resolver->resolve('cached.localhost');

    expect($first->getTenantKey())->toBe($tenant->getTenantKey());

    // Bypass model events so only the resolver cache can answer the lookup.
    DB::connection('central')->table('domains')->where('domain', 'cached.localhost')->delete();

    expect($resolver->resolve('cached.localhost')->getTenantKey())->toBe($tenant->getTenantKey());
});

it('detects tenant databases through the package database manager', function () {
    $tenant = Tenant::factory()->create(['id' => 'database-probe']);
    $path = database_path('tenants/database-probe/database.sqlite');

    expect(TenantDatabase::exists($tenant))->toBeFalse();

    mkdir(dirname($path), 0755, true);
    touch($path);

    try {
        expect(TenantDatabase::exists($tenant))->toBeTrue();
    } finally {
        unlink($path);
        rmdir(dirname($path));
    }

    expect(TenantDatabase::exists($tenant))->toBeFalse();
});

it('expands a bare tenant domain to a reachable host when impersonating', function () {
    $this->seed(SuperAdminRoleSeeder::class);

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');

    $tenant = Tenant::factory()->create(['id' => 'bare-domain']);
    $tenant->domains()->create(['domain' => 'bare-domain']);

    $response = $this->actingAs($superAdmin)
        ->post(route('tenants.impersonate', $tenant), ['user_id' => 12]);

    $response->assertRedirect();

    expect($response->headers->get('Location'))
        ->toContain('bare-domain.localhost')
        ->toContain('/impersonate/');

    $token = DB::connection('central')
        ->table('tenant_user_impersonation_tokens')
        ->where('tenant_id', 'bare-domain')
        ->first();

    expect($token)->not->toBeNull()
        ->and($token->redirect_url)->toStartWith('http://bare-domain.localhost')
        ->and($token->redirect_url)->toEndWith('/dashboard');
});
