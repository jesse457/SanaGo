<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
        \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the departments index', function () {
    Department::factory()->count(3)->create();
    $this->actingAs($this->admin)->get(route('departments.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Departments/index'));
});

it('creates a department', function () {
    $this->actingAs($this->admin)->post(route('departments.store'), [
        'name' => 'Cardiology', 'code' => 'CARD', 'type' => 'clinical', 'phone_extension' => '101',
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('departments', ['name' => 'Cardiology']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('departments.index'))->assertForbidden();
});
