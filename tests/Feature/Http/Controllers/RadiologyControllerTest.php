<?php

declare(strict_types=1);

use App\Enums\RadiologyStatus;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
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
    $this->doctor = tap(User::factory()->create())->assignRole('doctor');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the imaging request queue with its counters', function () {
    RadiologyRequest::factory()->pending()->create();
    RadiologyRequest::factory()->inProgress()->create();
    RadiologyRequest::factory()->completed()->create();

    $this->actingAs($this->doctor)
        ->get(route('radiology.requests.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Radiology/Requests/Index')
            ->has('requests.data', 3)
            ->where('stats.total', 3)
            ->where('stats.pending', 1)
            ->where('stats.inProgress', 1)
            ->where('stats.completed', 1));
});

it('flattens the patient and exam onto each queue row', function () {
    $request = RadiologyRequest::factory()->pending()->create([
        'imaging_type' => 'ct_scan',
        'body_part' => 'head',
    ]);

    $this->actingAs($this->doctor)
        ->get(route('radiology.requests.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('requests.data.0.patient.first_name', $request->patient->first_name)
            ->where('requests.data.0.patient.patient_uid', $request->patient->patient_uid)
            ->where('requests.data.0.imaging_type', 'ct_scan')
            ->where('requests.data.0.body_part', 'head')
            ->where('requests.data.0.status', RadiologyStatus::Pending->value)
            ->where('requests.data.0.result', null));
});

it('filters the queue by status and search term', function () {
    $matching = RadiologyRequest::factory()->pending()->create([
        'body_part' => 'chest',
    ]);
    RadiologyRequest::factory()->completed()->create([
        'body_part' => 'pelvis',
    ]);

    $this->actingAs($this->doctor)
        ->get(route('radiology.requests.index', ['status' => 'pending']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('requests.data', 1)
            ->where('requests.data.0.id', $matching->id));

    $this->actingAs($this->doctor)
        ->get(route('radiology.requests.index', ['search' => 'chest']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('requests.data', 1)
            ->where('requests.data.0.id', $matching->id));
});

it('links a reported request to its report instead of a second form', function () {
    $request = RadiologyRequest::factory()->completed()->create();
    $result = RadiologyResult::factory()->for($request, 'request')->completed()->create();

    $this->actingAs($this->doctor)
        ->get(route('radiology.requests.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('requests.data.0.result.id', $result->id)
            ->where('requests.data.0.result.status', RadiologyStatus::Completed->value));
});

it('renders the report archive with the exam each report belongs to', function () {
    $request = RadiologyRequest::factory()->completed()->create([
        'imaging_type' => 'ultrasound',
        'body_part' => 'abdomen',
    ]);
    RadiologyResult::factory()->for($request, 'request')->validated()->create([
        'findings' => 'Gallbladder distended without stones.',
    ]);

    $this->actingAs($this->doctor)
        ->get(route('radiology.results.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Radiology/Results/Index')
            ->has('results.data', 1)
            ->where('results.data.0.imaging_type', 'ultrasound')
            ->where('results.data.0.body_part', 'abdomen')
            ->where('results.data.0.findings', 'Gallbladder distended without stones.')
            ->where('results.data.0.patient.first_name', $request->patient->first_name)
            ->where('results.data.0.status', RadiologyStatus::Validated->value)
            ->where('stats.total', 1)
            ->where('stats.validated', 1));
});

it('sends the operator back to the queue when no request is chosen', function () {
    $this->actingAs($this->doctor)
        ->get(route('radiology.results.create'))
        ->assertRedirect(route('radiology.requests.index'))
        ->assertSessionHas('error');
});

it('opens the report form for a request without a report', function () {
    $request = RadiologyRequest::factory()->pending()->create();

    $this->actingAs($this->doctor)
        ->get(route('radiology.results.create', ['request_id' => $request->id]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Radiology/Results/Enter')
            ->where('isEdit', false)
            ->where('result', null)
            ->where('request.id', $request->id)
            ->where('request.imaging_type', $request->imaging_type)
            ->where('request.patient.patient_uid', $request->patient->patient_uid));
});

it('opens the existing report instead of a duplicate form', function () {
    $request = RadiologyRequest::factory()->completed()->create();
    $result = RadiologyResult::factory()->for($request, 'request')->completed()->create();

    $this->actingAs($this->doctor)
        ->get(route('radiology.results.create', ['request_id' => $request->id]))
        ->assertRedirect(route('radiology.results.edit', $result));
});

it('records a report and moves the request forward', function () {
    $request = RadiologyRequest::factory()->pending()->create();

    $this->actingAs($this->doctor)
        ->post(route('radiology.results.store'), [
            'request_id' => $request->id,
            'status' => RadiologyStatus::Completed->value,
            'findings' => 'No acute osseous abnormality.',
            'conclusion' => 'Normal study.',
        ])
        ->assertRedirect(route('radiology.results.index'))
        ->assertSessionHas('success');

    $result = RadiologyResult::query()->where('request_id', $request->id)->firstOrFail();

    expect($result->findings)->toBe('No acute osseous abnormality.')
        ->and($result->conclusion)->toBe('Normal study.')
        ->and($result->status)->toBe(RadiologyStatus::Completed)
        ->and($result->performed_by)->toBe($this->doctor->id)
        ->and($request->fresh()->status)->toBe(RadiologyStatus::Completed);
});

it('requires findings before a report can be filed', function () {
    $request = RadiologyRequest::factory()->pending()->create();

    $this->actingAs($this->doctor)
        ->post(route('radiology.results.store'), [
            'request_id' => $request->id,
            'status' => RadiologyStatus::InProgress->value,
        ])
        ->assertSessionHasErrors('findings');

    expect(RadiologyResult::query()->count())->toBe(0);
});

it('keeps an in-progress report from closing the request', function () {
    $request = RadiologyRequest::factory()->pending()->create();

    $this->actingAs($this->doctor)
        ->post(route('radiology.results.store'), [
            'request_id' => $request->id,
            'status' => RadiologyStatus::InProgress->value,
            'findings' => 'Plate acquired, awaiting senior read.',
        ])
        ->assertRedirect(route('radiology.results.index'));

    expect($request->fresh()->status)->toBe(RadiologyStatus::InProgress);
});

it('updates a report and re-syncs the request status', function () {
    $request = RadiologyRequest::factory()->inProgress()->create();
    $result = RadiologyResult::factory()->for($request, 'request')->create();

    $this->actingAs($this->doctor)
        ->put(route('radiology.results.update', $result), [
            'status' => RadiologyStatus::Completed->value,
            'findings' => 'Small left basal effusion.',
            'conclusion' => 'Recommend follow-up film in six weeks.',
        ])
        ->assertRedirect(route('radiology.results.index'))
        ->assertSessionHas('success');

    expect($result->fresh())
        ->findings->toBe('Small left basal effusion.')
        ->status->toBe(RadiologyStatus::Completed)
        ->and($request->fresh()->status)->toBe(RadiologyStatus::Completed);
});

it('validates a signed report with the validating clinician stamped', function () {
    $request = RadiologyRequest::factory()->completed()->create();
    $result = RadiologyResult::factory()->for($request, 'request')->completed()->create();

    $this->actingAs($this->doctor)
        ->post(route('radiology.results.validate', $result))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($result->fresh())
        ->status->toBe(RadiologyStatus::Validated)
        ->validated_by->toBe($this->doctor->id)
        ->validated_at->not->toBeNull()
        ->and($request->fresh()->status)->toBe(RadiologyStatus::Validated);
});

it('shows the request detail with its report', function () {
    $request = RadiologyRequest::factory()->completed()->create();
    $result = RadiologyResult::factory()->for($request, 'request')->completed()->create([
        'findings' => 'Cardiomediastinal silhouette is normal.',
        'conclusion' => 'No acute cardiopulmonary process.',
    ]);

    $this->actingAs($this->doctor)
        ->get(route('radiology.requests.show', $request))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Radiology/Requests/Show')
            ->where('request.id', $request->id)
            ->where('request.result.findings', 'Cardiomediastinal silhouette is normal.')
            ->where('request.result.conclusion', 'No acute cardiopulmonary process.')
            ->where('request.result.status', RadiologyStatus::Completed->value)
            ->where('request.result.performed_by', $result->performedBy->name));
});
