<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Tenancy;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Custom Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toBeInertiaComponent', function (string $_component) {
    return $this->toBeInstanceOf(Inertia::class);
});

/*
|--------------------------------------------------------------------------
| Pest Hooks
|--------------------------------------------------------------------------
*/

// Global afterEach: ensure tenancy + connection state doesn't leak between tests
afterEach(function () {
    app(Tenancy::class)->tenant = null;
    app(Tenancy::class)->initialized = false;

    Model::setConnectionResolver(app('db'));
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/**
 * Create a user with a specific permission for authorization tests.
 */
function userWithPermission(string $permission): User
{
    $user = User::factory()->create();

    $role = Role::firstOrCreate(['name' => 'test-role-'.$permission]);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    return $user;
}

/**
 * Create a user without any roles or permissions.
 */
function unauthorizedUser(): User
{
    return User::factory()->create();
}

/**
 * Run the test in a tenant context without switching database connections.
 *
 * Full tenancy initialisation is deliberately avoided: stancl/tenancy
 * bootstrappers swap the default database connection, which does not survive
 * the in-memory SQLite database used by the test suite. Setting the tenant on
 * the Tenancy container is enough for the plan context, feature gates and
 * subscription/tenant status middleware, which is what these tests exercise.
 */
function actAsTenant(Tenant $tenant): void
{
    app(Tenancy::class)->tenant = $tenant;
}

/**
 * Clear the tenant context again.
 */
function forgetTenant(): void
{
    app(Tenancy::class)->tenant = null;
}
