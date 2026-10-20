<?php

declare(strict_types=1);

use App\Enums\LabResultStatus;
use App\Models\LabRequest;
use App\Models\LabResult;
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

it('renders the results index', function () {
    LabResult::factory()->count(2)->create();
    $this->actingAs($this->labTech)->get(route('results.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('LabTests/Results/Index'));
});

it('flattens patient and test definition onto each result row', function () {
    $request = LabRequest::factory()->create();
    LabResult::factory()->for($request)->create();

    $this->actingAs($this->labTech)->get(route('results.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('LabTests/Results/Index')
            ->has('results.data', 1)
            ->where('results.data.0.patient.first_name', $request->patient->first_name)
            ->where('results.data.0.patient.last_name', $request->patient->last_name)
            ->where('results.data.0.patient.patient_uid', $request->patient->patient_uid)
            ->where('results.data.0.test_definition.test_name', $request->testDefinition->test_name)
            ->where('results.data.0.test_definition.code', $request->testDefinition->code)
            ->where('results.data.0.lab_request_id', $request->id)
            ->has('results.data.0.status'));
});

it('sends the result status as a plain string', function () {
    $result = LabResult::factory()->normal()->create();

    $this->actingAs($this->labTech)->get(route('results.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('results.data.0.status', LabResultStatus::NORMAL->value)
            ->where('results.data.0.id', $result->id));
});

it('creates a lab result', function () {
    $request = LabRequest::factory()->create();
    $this->actingAs($this->labTech)->post(route('results.store'), [
        'lab_request_id' => $request->id,
        'status' => LabResultStatus::NORMAL->value,
        'result_date' => now()->toDateString(),
        'results_text' => 'All parameters within normal limits.',
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('lab_results', ['lab_request_id' => $request->id]);
});
