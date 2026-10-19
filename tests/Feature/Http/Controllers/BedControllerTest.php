<?php

declare(strict_types=1);

use App\Models\Bed;
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

it('transfers a bed to a different ward', function () {
    $ward1 = Ward::factory()->create();
    $ward2 = Ward::factory()->create();
    $bed = Bed::factory()->create(['ward_id' => $ward1->id]);

    $this->actingAs($this->admin)
        ->post(route('beds.transfer', $bed), ['ward_id' => $ward2->id])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('beds', ['id' => $bed->id, 'ward_id' => $ward2->id]);
});

it('validates ward_id is required for transfer', function () {
    $ward = Ward::factory()->create();
    $bed = Bed::factory()->create(['ward_id' => $ward->id]);

    $this->actingAs($this->admin)
        ->post(route('beds.transfer', $bed), [])
        ->assertInvalid(['ward_id']);
});

it('validates ward_id must exist for transfer', function () {
    $ward = Ward::factory()->create();
    $bed = Bed::factory()->create(['ward_id' => $ward->id]);

    $this->actingAs($this->admin)
        ->post(route('beds.transfer', $bed), ['ward_id' => 9999])
        ->assertInvalid(['ward_id']);
});
