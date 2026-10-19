<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Models\CashPayment;
use App\Models\CashShift;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
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

it('records a partial payment against an invoice', function () {
    $patient = Patient::factory()->create();
    $user = User::factory()->create();

    $shift = CashShift::create([
        'user_id' => $user->id,
        'payment_point' => 'Main',
        'opening_balance' => 50000,
        'status' => 'open',
        'opened_at' => now(),
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-2607-0001',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 50000,
        'tax_amount' => 0,
        'total_amount' => 50000,
        'status' => InvoiceStatus::PENDING->value,
    ]);

    $billingService = app(BillingService::class);
    $payment = $billingService->recordPayment($invoice, 20000, $shift->id);

    expect($payment)->toBeInstanceOf(CashPayment::class)
        ->and((float) $payment->amount_paid)->toEqual(20000.00)
        ->and($payment->invoice_id)->toBe($invoice->id);
});

it('auto-marks invoice as PAID when fully paid', function () {
    $patient = Patient::factory()->create();
    $user = User::factory()->create();

    $shift = CashShift::create([
        'user_id' => $user->id,
        'payment_point' => 'Main',
        'opening_balance' => 50000,
        'status' => 'open',
        'opened_at' => now(),
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-2607-0002',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 30000,
        'tax_amount' => 0,
        'total_amount' => 30000,
        'status' => InvoiceStatus::PENDING->value,
    ]);

    $billingService = app(BillingService::class);
    $billingService->recordPayment($invoice, 30000, $shift->id);

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::PAID);
});

it('calculates remaining balance correctly after partial payment', function () {
    $patient = Patient::factory()->create();
    $user = User::factory()->create();

    $shift = CashShift::create([
        'user_id' => $user->id,
        'payment_point' => 'Main',
        'opening_balance' => 50000,
        'status' => 'open',
        'opened_at' => now(),
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-2607-0003',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 100000,
        'tax_amount' => 0,
        'total_amount' => 100000,
        'status' => InvoiceStatus::PENDING->value,
    ]);

    $billingService = app(BillingService::class);
    $billingService->recordPayment($invoice, 30000, $shift->id);
    $billingService->recordPayment($invoice, 20000, $shift->id);

    $invoice->refresh();
    expect($invoice->total_paid)->toEqual(50000.00)
        ->and($invoice->remaining_balance)->toEqual(50000.00)
        ->and($invoice->is_fully_paid)->toBeFalse();
});

it('is_fully_paid returns true when total_paid meets total_amount', function () {
    $patient = Patient::factory()->create();
    $user = User::factory()->create();

    $shift = CashShift::create([
        'user_id' => $user->id,
        'payment_point' => 'Main',
        'opening_balance' => 50000,
        'status' => 'open',
        'opened_at' => now(),
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-2607-0004',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 15000,
        'tax_amount' => 0,
        'total_amount' => 15000,
        'status' => InvoiceStatus::PENDING->value,
    ]);

    $billingService = app(BillingService::class);
    $billingService->recordPayment($invoice, 15000, $shift->id);

    $invoice->refresh();
    expect($invoice->is_fully_paid)->toBeTrue();
});

it('multiple payments accumulate correctly', function () {
    $patient = Patient::factory()->create();
    $user = User::factory()->create();

    $shift = CashShift::create([
        'user_id' => $user->id,
        'payment_point' => 'Main',
        'opening_balance' => 50000,
        'status' => 'open',
        'opened_at' => now(),
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-2607-0005',
        'patient_id' => $patient->id,
        'invoice_date' => now()->format('Y-m-d'),
        'subtotal' => 75000,
        'tax_amount' => 0,
        'total_amount' => 75000,
        'status' => InvoiceStatus::PENDING->value,
    ]);

    $billingService = app(BillingService::class);
    $billingService->recordPayment($invoice, 25000, $shift->id);
    $billingService->recordPayment($invoice, 25000, $shift->id);
    $billingService->recordPayment($invoice, 25000, $shift->id);

    expect($invoice->cashPayments()->count())->toBe(3)
        ->and((float) $invoice->cashPayments()->sum('amount_paid'))->toEqual(75000.00);
});
