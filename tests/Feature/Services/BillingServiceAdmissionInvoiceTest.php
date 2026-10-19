<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\PayerType;
use App\Enums\ServiceCategory;
use App\Models\Admission;
use App\Models\AdmissionService;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientInsurance;
use App\Models\Payer;
use App\Models\Service;
use App\Models\User;
use App\Models\Ward;
use App\Services\Billing\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('generates invoice from admission with services', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create();
    $ward = Ward::create(['name' => 'Test Ward', 'code' => 'TW-001', 'capacity' => 10]);
    $bedType = BedType::create(['name' => 'Private', 'daily_rate' => 5000]);
    $bed = Bed::create([
        'ward_id' => $ward->id,
        'code' => 'B-001',
        'bed_type_id' => $bedType->id,
        'status' => 'occupied',
    ]);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => $bed->id,
        'attending_doctor_id' => $doctor->id,
        'admission_date' => now()->subDays(3),
        'status' => 'admitted',
        'observation_fee' => 2000,
    ]);

    // Create services for this admission
    $service1 = Service::create([
        'name' => 'Consultation Générale',
        'code' => 'CONS-GEN',
        'category' => ServiceCategory::Consultation,
        'default_price' => 3000,
    ]);
    $service2 = Service::create([
        'name' => 'Hémogramme',
        'code' => 'LAB-NFS',
        'category' => ServiceCategory::Lab,
        'default_price' => 5000,
    ]);

    AdmissionService::create([
        'admission_id' => $admission->id,
        'service_id' => $service1->id,
        'quantity' => 1,
        'unit_price' => 3000,
        'total_price' => 3000,
    ]);
    AdmissionService::create([
        'admission_id' => $admission->id,
        'service_id' => $service2->id,
        'quantity' => 1,
        'unit_price' => 5000,
        'total_price' => 5000,
    ]);

    $billingService = app(BillingService::class);
    $invoice = $billingService->generateInvoiceFromAdmission($admission);

    expect($invoice)->toBeInstanceOf(Invoice::class)
        ->and($invoice->patient_id)->toBe($patient->id)
        ->and($invoice->status)->toBe(InvoiceStatus::PENDING)
        ->and($invoice->items()->count())->toBe(4); // 2 services + bed + observation
});

it('generates invoice with bed charges calculated by nights', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create();
    $ward = Ward::create(['name' => 'Test Ward', 'code' => 'TW-002', 'capacity' => 10]);
    $bedType = BedType::create(['name' => 'VIP', 'daily_rate' => 10000]);
    $bed = Bed::create([
        'ward_id' => $ward->id,
        'code' => 'B-002',
        'bed_type_id' => $bedType->id,
        'status' => 'occupied',
    ]);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => $bed->id,
        'attending_doctor_id' => $doctor->id,
        'admission_date' => now()->subDays(5),
        'status' => 'admitted',
    ]);

    $billingService = app(BillingService::class);
    $invoice = $billingService->generateInvoiceFromAdmission($admission);

    $bedItem = $invoice->items()->where('item_type', 'bed')->first();
    expect($bedItem)->not->toBeNull()
        ->and((int) $bedItem->quantity)->toBe(5)
        ->and((float) $bedItem->unit_price)->toEqual(10000.00)
        ->and((float) $bedItem->total_price)->toEqual(50000.00);
});

it('generates invoice with observation fee', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create();

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'attending_doctor_id' => $doctor->id,
        'admission_date' => now(),
        'status' => 'admitted',
        'observation_fee' => 2500,
    ]);

    $billingService = app(BillingService::class);
    $invoice = $billingService->generateInvoiceFromAdmission($admission);

    $obsItem = $invoice->items()->where('item_type', 'observation')->first();
    expect($obsItem)->not->toBeNull()
        ->and((float) $obsItem->total_price)->toEqual(2500.00);
});

it('creates claim when patient has insurance', function () {
    $patient = Patient::factory()->create();
    $doctor = User::factory()->create();

    // Create payer with 80% coverage
    $payer = Payer::create([
        'name' => 'CNPS',
        'type' => PayerType::Government,
        'coverage_percentage' => 80,
    ]);

    // Link patient to insurance
    PatientInsurance::create([
        'patient_id' => $patient->id,
        'payer_id' => $payer->id,
        'policy_number' => 'POL-001',
        'valid_from' => now()->subYear(),
        'valid_until' => now()->addYear(),
        'is_primary' => true,
    ]);

    // Create a service
    $service = Service::create([
        'name' => 'Césarienne',
        'code' => 'PROC-CES',
        'category' => ServiceCategory::Procedure,
        'default_price' => 80000,
    ]);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'attending_doctor_id' => $doctor->id,
        'admission_date' => now(),
        'status' => 'admitted',
    ]);

    AdmissionService::create([
        'admission_id' => $admission->id,
        'service_id' => $service->id,
        'quantity' => 1,
        'unit_price' => 80000,
        'total_price' => 80000,
    ]);

    $billingService = app(BillingService::class);
    $invoice = $billingService->generateInvoiceFromAdmission($admission);
    $invoice->refresh();

    $claim = $invoice->claim;
    expect($claim)->not->toBeNull()
        ->and((float) $claim->claim_amount)->toEqual(64000.00); // 80% of 80000
});
