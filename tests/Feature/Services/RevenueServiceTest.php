<?php

declare(strict_types=1);

use App\Enums\AdmissionStatus;
use App\Enums\EmergencyStatus;
use App\Enums\SurgeryStatus;
use App\Models\Admission;
use App\Models\EmergencyCase;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Surgery;
use App\Models\User;
use App\Services\Billing\RevenueService;
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

it('uses InvoiceStatus enum in getRevenueSummary', function () {
    Invoice::factory()->paid()->create([
        'invoice_date' => '2026-06-15',
        'total_amount' => 1000,
    ]);

    $service = app(RevenueService::class);
    $summary = $service->getRevenueSummary('2026-01-01', '2026-12-31');

    expect($summary['invoices'])->toBe(1000.0)
        ->and($summary['invoice_count'])->toBe(1);
});

it('excludes non-completed surgeries by status', function () {
    $patient = Patient::factory()->create();
    Surgery::create([
        'patient_id' => $patient->id,
        'surgeon_id' => $this->doctor->id,
        'surgery_name' => 'Test Surgery',
        'scheduled_at' => '2026-06-15 10:00:00',
        'status' => SurgeryStatus::SCHEDULED->value,
        'surgery_fee' => 5000,
        'urgency' => 'normal',
    ]);

    $service = app(RevenueService::class);
    $summary = $service->getRevenueSummary('2026-01-01', '2026-12-31');

    expect($summary['surgery_count'])->toBe(0);
});

it('uses EmergencyStatus enum values in getRevenueSummary', function () {
    $patient = Patient::factory()->create();
    EmergencyCase::create([
        'patient_id' => $patient->id,
        'arrived_at' => '2026-06-15 10:00:00',
        'chief_complaint' => 'Severe headache',
        'status' => EmergencyStatus::STABILIZED->value,
        'emergency_fee' => 2000,
    ]);
    EmergencyCase::create([
        'patient_id' => $patient->id,
        'arrived_at' => '2026-06-16 10:00:00',
        'chief_complaint' => 'Mild fever',
        'status' => EmergencyStatus::DISCHARGED->value,
        'emergency_fee' => 3000,
    ]);

    $service = app(RevenueService::class);
    $summary = $service->getRevenueSummary('2026-01-01', '2026-12-31');

    expect($summary['emergencies'])->toBe(5000.0)
        ->and($summary['emergency_count'])->toBe(2);
});

it('uses AdmissionStatus enum in getRevenueSummary', function () {
    $patient = Patient::factory()->create();
    Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => null,
        'admission_date' => '2026-06-15',
        'status' => AdmissionStatus::Discharged->value,
        'observation_fee' => 1500,
    ]);

    $service = app(RevenueService::class);
    $summary = $service->getRevenueSummary('2026-01-01', '2026-12-31');

    expect($summary['admissions'])->toBe(1500.0);
});

it('computes total revenue correctly across all sources', function () {
    $patient = Patient::factory()->create();

    Invoice::factory()->paid()->create([
        'invoice_date' => '2026-06-15',
        'total_amount' => 1000,
    ]);
    Surgery::create([
        'patient_id' => $patient->id,
        'surgeon_id' => $this->doctor->id,
        'surgery_name' => 'Test Surgery',
        'scheduled_at' => '2026-06-15 10:00:00',
        'status' => SurgeryStatus::COMPLETED->value,
        'surgery_fee' => 2000,
        'urgency' => 'normal',
    ]);
    EmergencyCase::create([
        'patient_id' => $patient->id,
        'arrived_at' => '2026-06-15 10:00:00',
        'chief_complaint' => 'Chest pain',
        'status' => EmergencyStatus::STABILIZED->value,
        'emergency_fee' => 500,
    ]);
    Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => null,
        'admission_date' => '2026-06-15',
        'status' => AdmissionStatus::Discharged->value,
        'observation_fee' => 1500,
    ]);

    $service = app(RevenueService::class);
    $summary = $service->getRevenueSummary('2026-01-01', '2026-12-31');

    expect($summary['total_revenue'])->toEqual(5000.0);
});

it('returns zero revenue when no records match', function () {
    $service = app(RevenueService::class);
    $summary = $service->getRevenueSummary('2026-01-01', '2026-01-31');

    expect($summary['total_revenue'])->toEqual(0.0)
        ->and($summary['invoice_count'])->toBe(0)
        ->and($summary['surgery_count'])->toBe(0);
});

it('returns 12 months in getMonthlyRevenue', function () {
    $service = app(RevenueService::class);
    $data = $service->getMonthlyRevenue(2026);

    expect($data)->toHaveCount(12)
        ->and($data[0])->toHaveKeys(['month', 'revenue', 'invoices', 'surgeries', 'emergencies', 'admissions', 'expenditure', 'profit']);
});
