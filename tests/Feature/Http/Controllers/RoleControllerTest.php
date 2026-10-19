<?php

declare(strict_types=1);

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
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the roles index', function () {
    $this->actingAs($this->admin)->get(route('roles.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Roles/index'));
});

it('creates a role with permissions', function () {
    $this->actingAs($this->admin)->post(route('roles.store'), [
        'name' => 'test-auxiliary-nurse', 'permissions' => ['view-patients'],
    ])->assertRedirect();
    $this->assertDatabaseHas('roles', ['name' => 'test-auxiliary-nurse']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('roles.index'))->assertForbidden();
});
