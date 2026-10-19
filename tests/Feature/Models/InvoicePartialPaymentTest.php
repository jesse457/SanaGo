<?php

declare(strict_types=1);

use App\Enums\ClaimStatus;
use App\Enums\PayerType;
use App\Models\CashPayment;
use App\Models\CashShift;
use App\Models\Claim;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payer;
use App\Models\User;
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

it('total_paid sums cash payments', function () {
    $patient = Patient::factory()->create();
    $user = User::factory()->create();
    $shift = CashShift::create([
        'user_id' => $user->id,
        'payment_point' => 'Main',
        'opening_balance' => 100000,
        'status' => 'open',
        'opened_at' => now(),
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-ACC-001',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 100000,
        'tax_amount' => 0,
        'total_amount' => 100000,
        'status' => 'pending',
    ]);

    CashPayment::create([
        'cash_shift_id' => $shift->id,
        'invoice_id' => $invoice->id,
        'patient_id' => $patient->id,
        'amount_paid' => 30000,
        'payment_method' => 'cash',
    ]);
    CashPayment::create([
        'cash_shift_id' => $shift->id,
        'invoice_id' => $invoice->id,
        'patient_id' => $patient->id,
        'amount_paid' => 20000,
        'payment_method' => 'mobile_money',
    ]);

    $invoice->refresh();
    expect($invoice->total_paid)->toEqual(50000.00);
});

it('remaining_balance is total minus paid', function () {
    $patient = Patient::factory()->create();

    $invoice = Invoice::create([
        'invoice_number' => 'INV-BAL-001',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 50000,
        'tax_amount' => 0,
        'total_amount' => 50000,
        'status' => 'pending',
    ]);

    expect($invoice->remaining_balance)->toEqual(50000.00);
});

it('is_fully_paid returns false for new invoice', function () {
    $patient = Patient::factory()->create();

    $invoice = Invoice::create([
        'invoice_number' => 'INV-NEW-001',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 30000,
        'tax_amount' => 0,
        'total_amount' => 30000,
        'status' => 'pending',
    ]);

    expect($invoice->is_fully_paid)->toBeFalse();
});

it('total_paid includes paid claim amounts', function () {
    $patient = Patient::factory()->create();
    $user = User::factory()->create();
    $shift = CashShift::create([
        'user_id' => $user->id,
        'payment_point' => 'Main',
        'opening_balance' => 100000,
        'status' => 'open',
        'opened_at' => now(),
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-CLM-ACC',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 100000,
        'tax_amount' => 0,
        'total_amount' => 100000,
        'status' => 'pending',
    ]);

    $payer = Payer::create([
        'name' => 'Test Insurance',
        'type' => PayerType::Insurance,
        'coverage_percentage' => 80,
    ]);

    // Patient pays 20k
    CashPayment::create([
        'cash_shift_id' => $shift->id,
        'invoice_id' => $invoice->id,
        'patient_id' => $patient->id,
        'amount_paid' => 20000,
        'payment_method' => 'cash',
    ]);

    // Insurance pays 80k claim
    Claim::create([
        'invoice_id' => $invoice->id,
        'payer_id' => $payer->id,
        'claim_amount' => 80000,
        'status' => ClaimStatus::Paid,
    ]);

    $invoice->refresh();
    expect($invoice->total_paid)->toEqual(100000.00)
        ->and($invoice->is_fully_paid)->toBeTrue();
});
