<?php

declare(strict_types=1);

use App\Models\LabRequest;
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
    $this->labTech = tap(User::factory()->create())->assignRole('lab_tech');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
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
