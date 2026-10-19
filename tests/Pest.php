<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
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

// Global afterEach: ensure model connection resolvers don't leak between tests
afterEach(function () {
    // Reset connection resolver to Laravel's default database manager
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
