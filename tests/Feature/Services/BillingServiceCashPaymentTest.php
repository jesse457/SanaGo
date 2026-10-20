<?php

declare(strict_types=1);

use App\Enums\ClaimStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PayerType;
use App\Models\CashShift;
use App\Models\Claim;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientInsurance;
use App\Models\Payer;
use App\Models\User;
use App\Services\Billing\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();
});

/**
 * Build a pending invoice for a fresh patient.
 */
function invoiceFor(float $total): Invoice
{
    $patient = Patient::factory()->create();

    return Invoice::create([
        'invoice_number' => 'INV-TEST-'.uniqid(),
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => $total,
        'tax_amount' => 0,
        'total_amount' => $total,
        'status' => InvoiceStatus::PENDING->value,
    ]);
}

function shiftForBilling(): CashShift
{
    return CashShift::create([
        'user_id' => User::factory()->create()->id,
        'payment_point' => 'Main',
        'opening_balance' => 0,
        'status' => 'open',
        'opened_at' => now(),
    ]);
}

/**
 * Attach a settled insurance claim covering the given percentage.
 */
function attachPaidClaim(Invoice $invoice, float $claimAmount, int $coveragePercent): Claim
{
    $payer = Payer::create([
        'name' => 'Payer '.uniqid(),
        'type' => PayerType::Insurance,
        'coverage_percentage' => $coveragePercent,
    ]);

    $insurance = PatientInsurance::create([
        'patient_id' => $invoice->patient_id,
        'payer_id' => $payer->id,
        'policy_number' => 'POL-'.uniqid(),
        'valid_from' => now()->subYear(),
        'valid_until' => now()->addYear(),
        'is_primary' => true,
    ]);

    return Claim::create([
        'invoice_id' => $invoice->id,
        'payer_id' => $payer->id,
        'patient_insurance_id' => $insurance->id,
        'claim_amount' => $claimAmount,
        'status' => ClaimStatus::Paid->value,
    ]);
}

it('does not mark an invoice paid on a zero cash payment backed only by a claim', function () {
    $invoice = invoiceFor(100000);

    // A fully-settled insurance claim makes `is_fully_paid` true even though
    // the patient has handed over no cash at all.
    attachPaidClaim($invoice, 100000, 100);

    $invoice->refresh();
    expect($invoice->is_fully_paid)->toBeTrue();

    // The old code trusted `is_fully_paid` here and flipped the invoice to PAID
    // off the back of a 0.00 cash payment.
    $service = app(BillingService::class);
    $service->recordPayment($invoice, 0.0, shiftForBilling()->id);

    expect($invoice->fresh()->status)->toBe(InvoiceStatus::PENDING);
});

it('keeps an invoice pending while the patient still owes a cash share', function () {
    $invoice = invoiceFor(100000);
    attachPaidClaim($invoice, 80000, 80);

    $service = app(BillingService::class);
    $service->recordPayment($invoice, 20000, shiftForBilling()->id);

    // 20000 cash + 80000 claim now covers the bill.
    expect($invoice->fresh()->status)->toBe(InvoiceStatus::PAID);
});

it('marks an invoice paid once cash covers the full total', function () {
    $invoice = invoiceFor(50000);

    $service = app(BillingService::class);
    $service->recordPayment($invoice, 50000, shiftForBilling()->id);

    expect($invoice->fresh()->status)->toBe(InvoiceStatus::PAID);
});

it('accumulates cash payments towards the paid threshold', function () {
    $invoice = invoiceFor(30000);

    $shift = shiftForBilling();
    $service = app(BillingService::class);

    $service->recordPayment($invoice, 10000, $shift->id);
    expect($invoice->fresh()->status)->toBe(InvoiceStatus::PENDING);

    $service->recordPayment($invoice, 20000, $shift->id);
    expect($invoice->fresh()->status)->toBe(InvoiceStatus::PAID);
});

it('moves a draft invoice to pending on a partial cash payment', function () {
    $invoice = invoiceFor(40000);
    $invoice->update(['status' => InvoiceStatus::DRAFT->value]);

    $service = app(BillingService::class);
    $service->recordPayment($invoice, 10000, shiftForBilling()->id);

    expect($invoice->fresh()->status)->toBe(InvoiceStatus::PENDING);
});
