<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);

    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);

    $this->admin = tap(User::factory()->create())->assignRole('admin');
});

describe('index', function () {
    it('lists the hospital staff', function () {
        User::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Users/Index', false)
                ->has('users'));
    });

    it('forbids users without the manage-users permission', function () {
        $nurse = tap(User::factory()->create())->assignRole('reception');

        $this->actingAs($nurse)
            ->get(route('users.index'))
            ->assertForbidden();
    });
});

describe('store', function () {
    it('creates a staff account with a role', function () {
        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name' => 'Dr Aïcha Njoya',
                'email' => 'aicha@hopital.cm',
                'phone_number' => '+237690000111',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'doctor',
            ])
            ->assertRedirect();

        $user = User::query()->where('email', 'aicha@hopital.cm')->first();

        expect($user)->not->toBeNull()
            ->and($user->hasRole('doctor'))->toBeTrue();
    });

    it('rejects a duplicate email', function () {
        User::factory()->create(['email' => 'taken@hopital.cm']);

        $this->actingAs($this->admin)
            ->post(route('users.store'), [
                'name' => 'Duplicate',
                'email' => 'taken@hopital.cm',
                'phone_number' => '+237690000222',
                'phone_number' => '+237690000111',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'doctor',
            ])
            ->assertSessionHasErrors('email');
    });
});

describe('update and destroy', function () {
    it('updates a staff member', function () {
        $user = User::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'name' => 'New Name',
                'email' => $user->email,
                'phone_number' => '+237690000333',
                'role' => 'pharma',
            ])
            ->assertRedirect();

        expect($user->fresh()->name)->toBe('New Name')
            ->and($user->fresh()->hasRole('pharma'))->toBeTrue();
    });

    it('deletes a staff member', function () {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('users.destroy', $user))
            ->assertRedirect();

        expect(User::query()->whereKey($user->id)->exists())->toBeFalse();
    });
});

describe('departments', function () {
    it('assigns a department to a staff member', function () {
        $department = Department::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => '+237690000444',
                'role' => 'doctor',
                'department_id' => $department->id,
            ])
            ->assertRedirect();

        expect($user->fresh()->department_id)->toBe($department->id);
    });
});
