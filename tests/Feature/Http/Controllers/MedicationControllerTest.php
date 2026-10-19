<?php

declare(strict_types=1);

use App\Models\Medication;
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
    $this->pharma = tap(User::factory()->create())->assignRole('pharma');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the drugs index', function () {
    Medication::factory()->count(3)->create();
    $this->actingAs($this->pharma)->get(route('drugs.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Drugs/Index'));
});

it('creates a medication', function () {
    $this->actingAs($this->pharma)->post(route('drugs.store'), [
        'name' => 'Paracetamol', 'strength' => '500mg', 'dosage_units' => 'Tablet',
        'total_stock' => 500, 'min_stock_level' => 20, 'unit_cost' => 1.50,
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('medications', ['name' => 'Paracetamol']);
});

it('validates required fields', function () {
    $this->actingAs($this->pharma)->post(route('drugs.store'), [])
        ->assertSessionHasErrors(['name']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('drugs.index'))->assertForbidden();
});
