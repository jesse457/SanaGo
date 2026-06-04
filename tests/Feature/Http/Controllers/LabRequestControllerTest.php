<?php

declare(strict_types=1);

use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->labTech = tap(User::factory()->create())->assignRole('lab_tech');
    $this->withoutMiddleware([
        \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
        \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the lab requests index', function () {
    LabRequest::factory()->count(3)->create();
    $this->actingAs($this->labTech)->get(route('requests.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('LabTests/Requests/Index'));
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('requests.index'))->assertForbidden();
});
