<?php

declare(strict_types=1);

use App\Enums\ClaimStatus;
use App\Enums\PayerType;
use App\Models\Claim;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientInsurance;
use App\Models\Payer;
use App\Services\Billing\ClaimService;
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

it('submits a claim with coverage percentage calculation', function () {
    $patient = Patient::factory()->create();
    $payer = Payer::create([
        'name' => 'MUPAC',
        'type' => PayerType::Mutual,
        'coverage_percentage' => 75,
    ]);
    $insurance = PatientInsurance::create([
        'patient_id' => $patient->id,
        'payer_id' => $payer->id,
        'policy_number' => 'POL-MUPAC-001',
        'valid_from' => now()->subYear(),
        'valid_until' => now()->addYear(),
        'is_primary' => true,
    ]);
    $invoice = Invoice::create([
        'invoice_number' => 'INV-CLM-0001',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 100000,
        'tax_amount' => 0,
        'total_amount' => 100000,
        'status' => 'pending',
    ]);

    $claimService = app(ClaimService::class);
    $claim = $claimService->submitClaim($invoice, $payer, $insurance->id);

    expect($claim)->toBeInstanceOf(Claim::class)
        ->and($claim->status)->toBe(ClaimStatus::Submitted)
        ->and((float) $claim->claim_amount)->toEqual(75000.00) // 75% of 100000
        ->and($claim->invoice_id)->toBe($invoice->id)
        ->and($claim->payer_id)->toBe($payer->id)
        ->and($claim->submitted_at)->not->toBeNull();
});

it('approves a claim', function () {
    $patient = Patient::factory()->create();
    $invoice = Invoice::create([
        'invoice_number' => 'INV-APP-001',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 50000,
        'tax_amount' => 0,
        'total_amount' => 50000,
        'status' => 'pending',
    ]);
    $payer = Payer::create([
        'name' => 'Test Payer',
        'type' => PayerType::Mutual,
        'coverage_percentage' => 80,
    ]);
    $claim = Claim::create([
        'invoice_id' => $invoice->id,
        'payer_id' => $payer->id,
        'claim_amount' => 50000,
        'status' => ClaimStatus::Submitted,
    ]);

    $claimService = app(ClaimService::class);
    $claimService->approve($claim);

    expect($claim->fresh()->status)->toBe(ClaimStatus::Approved);
});

it('rejects a claim with reason', function () {
    $patient = Patient::factory()->create();
    $invoice = Invoice::create([
        'invoice_number' => 'INV-REJ-001',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 50000,
        'tax_amount' => 0,
        'total_amount' => 50000,
        'status' => 'pending',
    ]);
    $payer = Payer::create([
        'name' => 'Test Payer 2',
        'type' => PayerType::Insurance,
        'coverage_percentage' => 70,
    ]);
    $claim = Claim::create([
        'invoice_id' => $invoice->id,
        'payer_id' => $payer->id,
        'claim_amount' => 50000,
        'status' => ClaimStatus::Submitted,
    ]);

    $claimService = app(ClaimService::class);
    $claimService->reject($claim, 'Missing documentation');

    $claim->refresh();
    expect($claim->status)->toBe(ClaimStatus::Rejected)
        ->and($claim->rejection_reason)->toBe('Missing documentation');
});

it('marks claim as paid with remittance reference', function () {
    $patient = Patient::factory()->create();
    $invoice = Invoice::create([
        'invoice_number' => 'INV-PAID-001',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 50000,
        'tax_amount' => 0,
        'total_amount' => 50000,
        'status' => 'pending',
    ]);
    $payer = Payer::create([
        'name' => 'Test Payer 3',
        'type' => PayerType::Government,
        'coverage_percentage' => 100,
    ]);
    $claim = Claim::create([
        'invoice_id' => $invoice->id,
        'payer_id' => $payer->id,
        'claim_amount' => 50000,
        'status' => ClaimStatus::Approved,
    ]);

    $claimService = app(ClaimService::class);
    $claimService->markPaid($claim, 'REM-2026-001');

    $claim->refresh();
    expect($claim->status)->toBe(ClaimStatus::Paid)
        ->and($claim->paid_at)->not->toBeNull()
        ->and($claim->remittance_reference)->toBe('REM-2026-001');
});

it('calculates claim amount from coverage percentage', function () {
    $patient = Patient::factory()->create();
    $payer = Payer::create([
        'name' => 'CNPS 100%',
        'type' => PayerType::Government,
        'coverage_percentage' => 100,
    ]);
    $insurance = PatientInsurance::create([
        'patient_id' => $patient->id,
        'payer_id' => $payer->id,
        'policy_number' => 'POL-FULL',
        'valid_from' => now()->subYear(),
        'valid_until' => now()->addYear(),
        'is_primary' => true,
    ]);
    $invoice = Invoice::create([
        'invoice_number' => 'INV-FULL',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 50000,
        'tax_amount' => 0,
        'total_amount' => 50000,
        'status' => 'pending',
    ]);

    $claimService = app(ClaimService::class);
    $claim = $claimService->submitClaim($invoice, $payer, $insurance->id);

    expect((float) $claim->claim_amount)->toEqual(50000.00); // 100% coverage
});

it('handles zero coverage percentage', function () {
    $patient = Patient::factory()->create();
    $payer = Payer::create([
        'name' => 'Self-Pay',
        'type' => PayerType::Government,
        'coverage_percentage' => 0,
    ]);
    $insurance = PatientInsurance::create([
        'patient_id' => $patient->id,
        'payer_id' => $payer->id,
        'policy_number' => 'POL-ZERO',
        'valid_from' => now()->subYear(),
        'valid_until' => now()->addYear(),
        'is_primary' => true,
    ]);
    $invoice = Invoice::create([
        'invoice_number' => 'INV-ZERO',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 30000,
        'tax_amount' => 0,
        'total_amount' => 30000,
        'status' => 'pending',
    ]);

    $claimService = app(ClaimService::class);
    $claim = $claimService->submitClaim($invoice, $payer, $insurance->id);

    expect((float) $claim->claim_amount)->toEqual(0.00);
});
