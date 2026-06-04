<?php

declare(strict_types=1);

use App\Models\LabRequest;
use App\Models\LabResult;
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

it('renders the results index', function () {
    LabResult::factory()->count(2)->create();
    $this->actingAs($this->labTech)->get(route('results.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('LabTests/Results/Index'));
});

it('creates a lab result', function () {
    $request = LabRequest::factory()->create();
    $this->actingAs($this->labTech)->post(route('results.store'), [
        'lab_request_id' => $request->id,
        'status' => \App\Enums\LabResultStatus::NORMAL->value,
        'result_date' => now()->toDateString(),
        'results_text' => 'All parameters within normal limits.',
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('lab_results', ['lab_request_id' => $request->id]);
});
