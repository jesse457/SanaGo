<?php

declare(strict_types=1);

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use App\Services\Billing\BillingService;
use App\Services\PatientManagement\AdmissionService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

it('returns a redirect with a flash message when requesting an admission', function () {
    $patient = Patient::factory()->create(['is_admitted_approve' => false]);

    $this->actingAs($this->admin)
        ->post(route('request-admission', $patient))
        ->assertRedirect()
        ->assertSessionHas('success');

    // `is_admitted_approve` has no boolean cast, so SQLite returns the raw 1.
    expect((bool) $patient->fresh()->is_admitted_approve)->toBeTrue();

    $admission = Admission::where('patient_id', $patient->id)->firstOrFail();
    expect($admission->status)->toBe(AdmissionStatus::Pending);
});

it('bills the discharge exactly once when discharging through the service', function () {
    $admission = Admission::factory()->admitted()->create([
        'observation_fee' => 3000,
    ]);

    app(AdmissionService::class)->discharge($admission);

    // The service creates its own discharge invoice; the observer must not
    // add a second observation charge on the same status change.
    $invoices = Invoice::where('patient_id', $admission->patient_id)->get();

    expect($invoices)->toHaveCount(1)
        ->and($invoices->first()->invoice_number)->not->toBe('')
        ->and($invoices->first()->notes)->toContain('admission #'.$admission->id);

    $observationItems = $invoices->first()->items()
        ->where('item_type', 'observation')
        ->get();

    expect($observationItems)->toHaveCount(1);
});

it('keeps the observer fallback for a discharge that bypassed the service', function () {
    $admission = Admission::factory()->admitted()->create([
        'observation_fee' => 4500,
    ]);

    // Straight model update: no discharge invoice exists yet, so the observer
    // is the only billing path.
    $admission->update([
        'status' => AdmissionStatus::Discharged->value,
        'discharge_date' => now(),
    ]);

    $invoice = Invoice::where('patient_id', $admission->patient_id)->firstOrFail();

    $item = $invoice->items()->where('item_type', 'admission')->first();

    expect($item)->not->toBeNull()
        ->and((float) $item->unit_price)->toEqual(4500.00);
});

it('frees the bed on discharge', function () {
    $bed = Bed::factory()->create(['status' => 'occupied']);
    $admission = Admission::factory()->admitted()->create(['bed_id' => $bed->id]);

    app(AdmissionService::class)->discharge($admission);

    expect($bed->fresh()->status->value)->toBe('available')
        ->and($admission->fresh()->status)->toBe(AdmissionStatus::Discharged);
});

it('uses the shared admission invoice note helper', function () {
    $admission = Admission::factory()->admitted()->create();

    expect(BillingService::admissionInvoiceNote($admission))
        ->toBe('Invoice generated at discharge for admission #'.$admission->id);
});
