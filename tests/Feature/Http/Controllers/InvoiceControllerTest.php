<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Patient;
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
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the invoices index', function () {
    Invoice::factory()->count(2)->create();
    $this->actingAs($this->admin)->get(route('finance.invoices.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Finance/Invoices/Index'));
});

it('creates an invoice', function () {
    $patient = Patient::factory()->create();
    $this->actingAs($this->admin)->post(route('finance.invoices.store'), [
        'patient_id' => $patient->id,
        'invoice_date' => now()->toDateString(),
        'due_date' => now()->addDays(30)->toDateString(),
        'status' => 'pending',
        'items' => [['item_type' => 'consultation', 'description' => 'Consultation', 'quantity' => 1, 'unit_price' => 5000, 'total_price' => 5000]],
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('invoices', ['patient_id' => $patient->id]);
});

it('validates required fields on store', function () {
    $this->actingAs($this->admin)->post(route('finance.invoices.store'), [])
        ->assertSessionHasErrors(['patient_id', 'items']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('finance.invoices.index'))->assertForbidden();
});

it('renders the printable invoice detail', function () {
    $patient = Patient::factory()->create();
    $invoice = Invoice::factory()->create([
        'patient_id' => $patient->id,
        'status' => InvoiceStatus::OVERDUE->value,
        'subtotal' => 12000,
        'tax_amount' => 0,
        'total_amount' => 12000,
    ]);
    $invoice->items()->create([
        'item_type' => 'consultation',
        'item_id' => null,
        'description' => 'General consultation',
        'quantity' => 1,
        'unit_price' => 12000,
        'total_price' => 12000,
    ]);

    $this->actingAs($this->admin)->get(route('finance.invoices.show', $invoice->id))
        ->assertOk()
        ->assertInertia(fn ($p) => $p
            ->component('Finance/Invoices/Show')
            ->where('invoice.invoice_number', $invoice->invoice_number)
            ->where('invoice.status', 'overdue')
            ->where('invoice.total_amount', 12000)
            ->where('invoice.patient.patient_uid', $patient->patient_uid)
            ->has('invoice.items', 1)
            ->where('invoice.items.0.description', 'General consultation')
            ->has('invoice.payments', 0)
            ->where('invoice.claim', null)
        );
});

it('closes an overdue invoice', function () {
    $invoice = Invoice::factory()->create(['status' => InvoiceStatus::OVERDUE->value]);

    $this->actingAs($this->admin)
        ->patch(route('finance.invoices.status', $invoice->id), ['status' => 'cancelled'])
        ->assertRedirect();

    expect($invoice->fresh()->status)->toBe(InvoiceStatus::CANCELLED);
});
