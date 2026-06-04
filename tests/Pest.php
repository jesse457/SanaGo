<?php

declare(strict_types=1);

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
    return $this->toBeInstanceOf(\Inertia\Inertia::class);
});

/*
|--------------------------------------------------------------------------
| Pest Hooks
|--------------------------------------------------------------------------
*/

// Global afterEach: ensure model connection resolvers don't leak between tests
afterEach(function () {
    // Reset connection resolver to Laravel's default database manager
    \Illuminate\Database\Eloquent\Model::setConnectionResolver(app('db'));
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/**
 * Create a user with a specific permission for authorization tests.
 */
function userWithPermission(string $permission): \App\Models\User
{
    $user = \App\Models\User::factory()->create();

    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'test-role-'.$permission]);
    $role->givePermissionTo($permission);
    $user->assignRole($role);

    return $user;
}

/**
 * Create a user without any roles or permissions.
 */
function unauthorizedUser(): \App\Models\User
{
    return \App\Models\User::factory()->create();
}
