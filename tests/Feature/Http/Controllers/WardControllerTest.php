<?php

declare(strict_types=1);

use App\Models\Department;
use App\Models\User;
use App\Models\Ward;
use Database\Seeders\RoleSeeder;
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

it('renders the wards index', function () {
    Ward::factory()->count(3)->create();
    $this->actingAs($this->admin)->get(route('wards.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Wards/Index'));
});

it('creates a ward', function () {
    $dept = Department::factory()->create();
    $this->actingAs($this->admin)->post(route('wards.store'), [
        'name' => 'General Ward', 'code' => 'GW-001', 'department_id' => $dept->id, 'floor' => '1st', 'capacity' => 20,
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('wards', ['name' => 'General Ward']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('wards.index'))->assertForbidden();
});
