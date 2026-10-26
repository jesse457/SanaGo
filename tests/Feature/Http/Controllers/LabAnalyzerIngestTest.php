<?php

declare(strict_types=1);

use App\Enums\LabRequestStatus;
use App\Enums\LabResultStatus;
use App\Models\LabAnalyzerMessage;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\LabTestDefinition;
use App\Models\Patient;
use App\Models\User;
use App\Services\Laboratory\LabRequestService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Lab analyzer ingestion
|--------------------------------------------------------------------------
| Covers the two ways a hospital gets a run off a bench analyzer and into the
| record: the machine/bridge posting to the token-protected endpoint, and a
| tech previewing a frame from the result entry screen.
*/

function astmRun(string $accession, string $patientUid = 'CM-0098123'): string
{
    return implode("\r", [
        'H|\\^&|||Mindray^BC-5150^V2.1|||||||P|1|20261026101500',
        "P|1||{$patientUid}||MBALLA^Jean^Paul||19850312|M",
        "O|1|{$accession}||^^^CBC|R||||||||1",
        'R|1|^^^WBC^White blood cells|6.8|10^3/uL|4.0-11.0|N||F||||20261026101612',
        'R|2|^^^HGB^Haemoglobin|9.4|g/dL|11.5-16.5|LL||F||||20261026101612',
        'L|1|N',
    ])."\r";
}

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);

    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);

    config()->set('lab-analyzer.token', 'bridge-secret');
    config()->set('lab-analyzer.auto_complete_request', true);

    $this->patient = Patient::factory()->create(['patient_uid' => 'CM-0098123']);
    $this->test = LabTestDefinition::factory()->create([
        'test_name' => 'Full blood count',
        'code' => 'CBC',
        'analyzer_code' => 'CBC',
    ]);
    $this->request = LabRequest::factory()->pending()->create([
        'patient_id' => $this->patient->id,
        'test_definition_id' => $this->test->id,
        'accession_number' => 'ACC-20261026-0007',
    ]);
    $this->labTech = tap(User::factory()->create())->assignRole('lab_tech');
});

it('files a result when the analyzer posts a frame with its token', function () {
    $this->postJson(
        route('lab.analyzer.ingest'),
        ['payload' => astmRun('ACC-20261026-0007')],
        ['Authorization' => 'Bearer bridge-secret'],
    )
        ->assertCreated()
        ->assertJsonPath('status', 'imported')
        ->assertJsonPath('sample_id', 'ACC-20261026-0007')
        ->assertJsonPath('lab_request_id', $this->request->id)
        ->assertJsonPath('analytes', 2);

    $result = $this->request->fresh()->labResult;

    expect($result)->not->toBeNull()
        ->and($result->source)->toBe('analyzer')
        ->and($result->analyzer_name)->toBe('Mindray BC-5150')
        ->and($result->analyzer_sample_id)->toBe('ACC-20261026-0007')
        ->and($result->results_text)->toContain('White blood cells: 6.8 10^3/uL (4.0-11.0)')
        ->and($result->results_text)->toContain('Haemoglobin: 9.4 g/dL (11.5-16.5)')
        // A critical (LL) analyte must never file as a normal result.
        ->and($result->status)->toBe(LabResultStatus::CRITICAL)
        ->and($this->request->fresh()->status)->toBe(LabRequestStatus::COMPLETED);
});

it('sends the raw body of a bridge post as the frame', function () {
    $frame = astmRun('ACC-20261026-0007');

    $this->call(
        'POST',
        route('lab.analyzer.ingest'),
        [],
        [],
        [],
        ['CONTENT_TYPE' => 'text/plain', 'HTTP_AUTHORIZATION' => 'Bearer bridge-secret'],
        $frame,
    )->assertCreated();

    expect(LabAnalyzerMessage::query()->count())->toBe(1)
        ->and(LabAnalyzerMessage::query()->first()->raw_payload)->toBe($frame);
});

it('rejects a frame from an unauthorised caller', function () {
    $this->post(route('lab.analyzer.ingest'), ['payload' => astmRun('ACC-20261026-0007')])
        ->assertUnauthorized();

    expect(LabResult::query()->count())->toBe(0);
});

it('rejects the endpoint when no token is configured', function () {
    config()->set('lab-analyzer.token', null);

    $this->post(route('lab.analyzer.ingest'), ['payload' => astmRun('ACC-20261026-0007')], [
        'Authorization' => 'Bearer bridge-secret',
    ])->assertUnauthorized();
});

it('stores a frame that matches no order instead of dropping the values', function () {
    $this->post(route('lab.analyzer.ingest'), [
        // Neither the sample id nor the patient is known to this hospital.
        'payload' => astmRun('ACC-99999999-9999', 'CM-0000000'),
        'format' => 'astm',
    ], ['Authorization' => 'Bearer bridge-secret'])
        ->assertCreated()
        ->assertJsonPath('status', 'unmatched');

    expect(LabResult::query()->count())->toBe(0)
        ->and(LabAnalyzerMessage::query()->first()->status)->toBe('unmatched');
});

it('ignores a frame the bridge re-sends', function () {
    $payload = astmRun('ACC-20261026-0007');

    $first = $this->post(route('lab.analyzer.ingest'), ['payload' => $payload], [
        'Authorization' => 'Bearer bridge-secret',
    ])->assertCreated();

    $second = $this->post(route('lab.analyzer.ingest'), ['payload' => $payload], [
        'Authorization' => 'Bearer bridge-secret',
    ])->assertCreated();

    expect($second->json('message_id'))->toBe($first->json('message_id'))
        ->and(LabAnalyzerMessage::query()->count())->toBe(1)
        ->and(LabResult::query()->count())->toBe(1);
});

it('falls back to the patient uid when the sample id does not match', function () {
    $this->request->update(['accession_number' => 'ACC-DIFFERENT']);

    $this->post(route('lab.analyzer.ingest'), [
        'payload' => astmRun('ACC-20261026-0007'),
    ], ['Authorization' => 'Bearer bridge-secret'])
        ->assertCreated()
        ->assertJsonPath('status', 'imported');

    expect($this->request->fresh()->labResult)->not->toBeNull();
});

it('keeps the order open when the lab requires verification', function () {
    config()->set('lab-analyzer.auto_complete_request', false);

    $this->post(route('lab.analyzer.ingest'), [
        'payload' => astmRun('ACC-20261026-0007'),
    ], ['Authorization' => 'Bearer bridge-secret'])->assertCreated();

    expect($this->request->fresh()->status)->toBe(LabRequestStatus::PENDING)
        ->and($this->request->fresh()->labResult)->not->toBeNull();
});

it('replaces the previous values when the sample is re-run', function () {
    $this->post(route('lab.analyzer.ingest'), [
        'payload' => astmRun('ACC-20261026-0007'),
    ], ['Authorization' => 'Bearer bridge-secret'])->assertCreated();

    $rerun = str_replace('|9.4|', '|13.1|', astmRun('ACC-20261026-0007'));

    $this->post(route('lab.analyzer.ingest'), [
        'payload' => $rerun,
    ], ['Authorization' => 'Bearer bridge-secret'])->assertCreated();

    $result = $this->request->fresh()->labResult;

    expect(LabResult::query()->count())->toBe(1)
        ->and($result->results_text)->toContain('Haemoglobin: 13.1 g/dL');
});

it('previews a pasted frame for the result entry screen', function () {
    $response = $this->actingAs($this->labTech)->postJson(route('lab.analyzer.preview'), [
        'payload' => astmRun('ACC-20261026-0007'),
    ]);

    $response->assertOk()
        ->assertJsonPath('status', 'received')
        ->assertJsonPath('sample_id', 'ACC-20261026-0007')
        ->assertJsonPath('request.id', $this->request->id)
        ->assertJsonPath('request.patient.patient_uid', 'CM-0098123')
        ->assertJsonPath('analyzer_name', 'Mindray BC-5150')
        ->assertJsonCount(2, 'analytes');

    // A preview must not write anything.
    expect(LabResult::query()->count())->toBe(0)
        ->and(LabAnalyzerMessage::query()->count())->toBe(0);
});

it('refuses a preview to someone who cannot file results', function () {
    $receptionist = tap(User::factory()->create())->assignRole('reception');

    $this->actingAs($receptionist)
        ->postJson(route('lab.analyzer.preview'), [
            'payload' => astmRun('ACC-20261026-0007'),
        ])
        ->assertForbidden();
});

it('rejects a frame that carries no readable result', function () {
    $this->post(route('lab.analyzer.ingest'), [
        'payload' => 'this is a photo of a tube',
        'format' => 'astm',
    ], ['Authorization' => 'Bearer bridge-secret'])
        ->assertStatus(422)
        ->assertJsonPath('status', 'rejected');
});

it('assigns an accession number when a lab order is filed', function () {
    $this->actingAs($this->labTech);

    $request = app(LabRequestService::class)
        ->createRequest([
            'patient_id' => $this->patient->id,
            'test_definition_id' => $this->test->id,
            'doctor_id' => $this->labTech->id,
        ]);

    expect($request->accession_number)->toMatch('/^ACC-\d{8}-\d{4}$/');
});
