<?php

declare(strict_types=1);

use App\Enums\LabRequestStatus;
use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\Patient;
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
    $this->doctor = tap(User::factory()->create())->assignRole('doctor');
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

it('renders the stand-alone lab request form', function () {
    $patient = Patient::factory()->create();
    LabTestDefinition::factory()->create();

    $this->actingAs($this->doctor)->get(route('requests.create'))
        ->assertOk()
        ->assertInertia(fn ($p) => $p
            ->component('LabTests/Requests/Create')
            ->has('tests', 1)
            ->has('patients', 1)
            ->where('urgencies.0.value', 'normal')
        );
});

it('files a lab order straight from the form, with no consultation behind it', function () {
    $patient = Patient::factory()->create();
    $test = LabTestDefinition::factory()->create();

    $this->actingAs($this->doctor)
        ->post(route('requests.store'), [
            'patient_id' => $patient->id,
            'test_definition_id' => $test->id,
            'urgency' => 'urgent',
            'reason_for_test' => 'Follow-up anaemia panel',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $request = LabRequest::firstOrFail();

    expect($request->patient_id)->toBe($patient->id)
        ->and($request->test_definition_id)->toBe($test->id)
        ->and($request->urgency)->toBe('urgent')
        ->and($request->status)->toBe(LabRequestStatus::PENDING)
        ->and($request->consultation_id)->toBeNull()
        // The ordering doctor falls back to the staff member filing the form.
        ->and($request->doctor_id)->toBe($this->doctor->id)
        ->and($request->request_date)->not->toBeNull();
});

it('keeps an explicit ordering doctor when one is chosen', function () {
    $patient = Patient::factory()->create();
    $test = LabTestDefinition::factory()->create();
    $orderingDoctor = User::factory()->create();

    $this->actingAs($this->doctor)
        ->post(route('requests.store'), [
            'patient_id' => $patient->id,
            'test_definition_id' => $test->id,
            'doctor_id' => $orderingDoctor->id,
            'urgency' => 'normal',
        ])
        ->assertRedirect();

    expect(LabRequest::firstOrFail()->doctor_id)->toBe($orderingDoctor->id);
});

it('validates the lab order form', function () {
    $this->actingAs($this->doctor)
        ->post(route('requests.store'), [])
        ->assertSessionHasErrors(['patient_id', 'test_definition_id', 'urgency']);
});

it('rejects an order filed by someone who may not request lab tests', function () {
    $this->actingAs($this->labTech)
        ->post(route('requests.store'), [])
        ->assertForbidden();
});
