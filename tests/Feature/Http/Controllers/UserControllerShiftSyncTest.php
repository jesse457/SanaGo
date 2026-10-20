<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserShift;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

it('defaults missing shift times instead of raising an undefined key error', function () {
    $user = User::factory()->create(['phone_number' => '+237600000001']);

    $this->actingAs($this->admin)
        ->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => 'doctor',
            'shifts' => [
                ['shift_date' => '2026-02-10', 'shift_type' => 'morning'],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $shift = UserShift::where('user_id', $user->id)->firstOrFail();

    expect($shift->start_time)->toBe('08:00')
        ->and($shift->end_time)->toBe('17:00');
});

it('keeps submitted shift times', function () {
    $user = User::factory()->create(['phone_number' => '+237600000002']);

    $this->actingAs($this->admin)
        ->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => 'reception',
            'shifts' => [
                ['shift_date' => '2026-02-11', 'shift_type' => 'night', 'start_time' => '21:00', 'end_time' => '05:00'],
            ],
        ])
        ->assertRedirect();

    $shift = UserShift::where('user_id', $user->id)->firstOrFail();

    expect($shift->start_time)->toBe('21:00')
        ->and($shift->end_time)->toBe('05:00');
});

it('replaces the stored shifts idempotently when the same payload is sent twice', function () {
    $user = User::factory()->create(['phone_number' => '+237600000003']);

    $payload = [
        'name' => $user->name,
        'email' => $user->email,
        'phone_number' => $user->phone_number,
        'role' => 'doctor',
        'shifts' => [
            ['shift_date' => '2026-03-01', 'shift_type' => 'morning'],
            ['shift_date' => '2026-03-02', 'shift_type' => 'afternoon'],
        ],
    ];

    $this->actingAs($this->admin)->put(route('users.update', $user), $payload)->assertRedirect();
    $this->actingAs($this->admin)->put(route('users.update', $user), $payload)->assertRedirect();

    expect(UserShift::where('user_id', $user->id)->count())->toBe(2);
    expect(UserShift::where('user_id', $user->id)->pluck('shift_date')->unique()->count())->toBe(2);
});

it('removes all shifts when an empty set is submitted', function () {
    $user = User::factory()->create(['phone_number' => '+237600000004']);

    UserShift::create([
        'user_id' => $user->id,
        'shift_date' => '2026-04-01',
        'shift_type' => 'morning',
        'start_time' => '08:00',
        'end_time' => '17:00',
    ]);

    $this->actingAs($this->admin)
        ->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'role' => 'doctor',
            'shifts' => [],
        ])
        ->assertRedirect();

    expect(UserShift::where('user_id', $user->id)->count())->toBe(0);
});
