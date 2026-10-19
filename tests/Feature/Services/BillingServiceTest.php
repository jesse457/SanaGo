<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Patient;
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

it('generates invoice number with configured prefix', function () {
    config(['billing.invoice_prefix' => 'TEST']);

    $service = app(BillingService::class);
    $number = $service->generateInvoiceNumber();

    expect($number)->toStartWith('TEST-'.date('ym'));
});

it('uses default INV prefix when config is not set', function () {
    $service = app(BillingService::class);
    $number = $service->generateInvoiceNumber();

    expect($number)->toStartWith('INV-'.date('ym'));
});

it('findOrCreateDraftInvoice creates if none exists', function () {
    $patient = Patient::factory()->create();

    $service = app(BillingService::class);
    $invoice = $service->findOrCreateDraftInvoice($patient->id);

    expect($invoice)->toBeInstanceOf(Invoice::class)
        ->and($invoice->patient_id)->toBe($patient->id)
        ->and($invoice->status)->toBe(InvoiceStatus::DRAFT);
});

it('findOrCreateDraftInvoice returns existing draft for same patient', function () {
    $patient = Patient::factory()->create();

    $service = app(BillingService::class);
    $first = $service->findOrCreateDraftInvoice($patient->id);
    $second = $service->findOrCreateDraftInvoice($patient->id);

    expect($second->id)->toBe($first->id);
});
