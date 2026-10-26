<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserShift;
use App\Models\UserShiftStaff;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
    $this->admin = tap(User::factory()->create())->assignRole('admin');

    $this->staffA = User::factory()->create(['phone_number' => '+237600000011']);
    $this->staffB = User::factory()->create(['phone_number' => '+237600000012']);
});

it('assigns several staff members to a single shift', function () {
    $this->actingAs($this->admin)
        ->post(route('shifts.store'), [
            'shift_date' => '2026-06-01',
            'shift_type' => 'morning',
            'start_time' => '07:00',
            'end_time' => '15:00',
            'staff' => [
                ['user_id' => $this->staffA->id],
                ['user_id' => $this->staffB->id],
                ['name' => 'External locum'],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $shift = UserShift::query()->firstOrFail();

    expect($shift->user_id)->toBeNull()
        ->and($shift->staffAssignments()->count())->toBe(3)
        ->and($shift->staffAssignments()->whereNotNull('user_id')->count())->toBe(2)
        ->and($shift->staffAssignments()->whereNotNull('name')->first()->name)->toBe('External locum');
});

it('records a shift for staff who are not in the system', function () {
    $this->actingAs($this->admin)
        ->post(route('shifts.store'), [
            'shift_date' => '2026-06-02',
            'shift_type' => 'night',
            'start_time' => '21:00',
            'end_time' => '05:00',
            'staff' => [
                ['name' => 'Melingo Mukete'],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $assignment = UserShiftStaff::query()->firstOrFail();

    expect($assignment->user_id)->toBeNull()
        ->and($assignment->name)->toBe('Melingo Mukete');
});

it('replaces the staffing roster when a shift is updated', function () {
    $shift = UserShift::factory()->create();

    UserShiftStaff::create(['user_shift_id' => $shift->id, 'user_id' => $this->staffA->id]);
    UserShiftStaff::create(['user_shift_id' => $shift->id, 'name' => 'Old locum']);

    $this->actingAs($this->admin)
        ->put(route('shifts.update', $shift), [
            'shift_date' => '2026-06-03',
            'shift_type' => 'afternoon',
            'start_time' => '14:00',
            'end_time' => '22:00',
            'staff' => [
                ['user_id' => $this->staffB->id],
            ],
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $shift->refresh();

    expect($shift->staffAssignments()->count())->toBe(1)
        ->and($shift->staffAssignments()->first()->user_id)->toBe($this->staffB->id);
});

it('renders the roster with its staffing and the assignable users', function () {
    $shift = UserShift::factory()->create([
        'shift_date' => '2026-06-04',
        'shift_type' => 'morning',
    ]);

    UserShiftStaff::create(['user_shift_id' => $shift->id, 'user_id' => $this->staffA->id]);
    UserShiftStaff::create(['user_shift_id' => $shift->id, 'name' => 'Roster locum']);

    $this->actingAs($this->admin)
        ->get(route('shifts.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Shifts/Index')
            ->has('shifts.data', 1)
            ->has('shifts.data.0.staff_assignments', 2)
            ->where('shifts.data.0.staff_assignments.0.user.name', $this->staffA->name)
            ->where('shifts.data.0.staff_assignments.1.name', 'Roster locum')
            ->has('users', 3)
        );
});
