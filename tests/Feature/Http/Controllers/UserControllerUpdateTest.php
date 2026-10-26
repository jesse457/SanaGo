<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserShift;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

it('ignores legacy shifts payloads when updating a user', function () {
    $user = User::factory()->create(['phone_number' => '+237600000005']);

    $this->actingAs($this->admin)
        ->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => 'doctor',
            'shifts' => [
                ['shift_date' => '2026-05-01', 'shift_type' => 'morning'],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect(UserShift::where('user_id', $user->id)->count())->toBe(0);
    expect(Schema::hasTable('user_shift_staff'))->toBeTrue();
});
