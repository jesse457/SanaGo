<?php

declare(strict_types=1);

use App\Enums\CashShiftStatus;
use App\Enums\InvoiceStatus;
use App\Enums\LabRequestStatus;
use App\Enums\PaymentMethod;
use App\Models\CashPayment;
use App\Models\CashPoint;
use App\Models\CashShift;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
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
    $this->cashier = tap(User::factory()->create())->assignRole('reception');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

function shiftOn(CashPoint $point, User $user, float $float = 10000): CashShift
{
    return CashShift::create([
        'user_id' => $user->id,
        'cash_point_id' => $point->id,
        'payment_point' => $point->name,
        'opening_balance' => $float,
        'status' => CashShiftStatus::Open->value,
        'opened_at' => now(),
    ]);
}

/** A patient with one unpaid lab charge, as the doctor's order leaves it. */
function patientWithLabCharge(float $price = 5000): array
{
    $patient = Patient::factory()->create();
    $definition = LabTestDefinition::factory()->create(['price' => $price]);

    $request = LabRequest::factory()->create([
        'patient_id' => $patient->id,
        'test_definition_id' => $definition->id,
        'status' => LabRequestStatus::PENDING,
    ]);

    return [$patient, $request];
}

it('bills a lab test when it is ordered, not when the result is ready', function () {
    [$patient, $request] = patientWithLabCharge(5000);

    $item = InvoiceItem::where('item_type', 'lab_test')
        ->where('item_id', $request->id)
        ->first();

    expect($item)->not->toBeNull()
        ->and((float) $item->total_price)->toEqual(5000.0)
        ->and($item->settled_at)->toBeNull()
        ->and($item->invoice->patient_id)->toBe($patient->id);
});

it('does not bill the same lab test twice when it completes', function () {
    [$patient, $request] = patientWithLabCharge(5000);

    $request->update(['status' => LabRequestStatus::COMPLETED]);

    expect(InvoiceItem::where('item_type', 'lab_test')->where('item_id', $request->id)->count())->toBe(1);
});

it('lists the outstanding services for a patient', function () {
    [$patient] = patientWithLabCharge(5000);
    $shift = shiftOn(CashPoint::primary(), $this->cashier);

    $response = $this->actingAs($this->cashier)
        ->getJson(route('caisse.outstanding', $patient->id));

    $response->assertOk()
        ->assertJsonCount(1, 'charges')
        ->assertJsonPath('charges.0.module', 'laboratory')
        ->assertJsonPath('charges.0.total_price', 5000);

    expect($shift->payments()->count())->toBe(0);
});

it('never offers lines from an invoice that is already settled', function () {
    [$patient] = patientWithLabCharge(5000);
    shiftOn(CashPoint::primary(), $this->cashier);

    $item = InvoiceItem::where('item_type', 'lab_test')->firstOrFail();
    $item->invoice->update(['status' => InvoiceStatus::PAID->value]);

    $this->actingAs($this->cashier)
        ->getJson(route('caisse.outstanding', $patient->id))
        ->assertOk()
        ->assertJsonCount(0, 'charges');

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $patient->id,
            'item_ids' => [$item->id],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 5000,
        ])
        ->assertSessionHasErrors(['item_ids']);
});

it('settles the selected services and marks them paid', function () {
    [$patient] = patientWithLabCharge(5000);
    $shift = shiftOn(CashPoint::primary(), $this->cashier);
    $item = InvoiceItem::where('item_type', 'lab_test')->firstOrFail();

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $patient->id,
            'item_ids' => [$item->id],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 5000,
        ])
        ->assertRedirect();

    $item->refresh();
    $payment = CashPayment::where('cash_shift_id', $shift->id)->firstOrFail();

    expect($item->settled_at)->not->toBeNull()
        ->and((float) $payment->amount_paid)->toEqual(5000.0)
        ->and($payment->patient_id)->toBe($patient->id)
        ->and($payment->cash_point_id)->toBe($shift->cash_point_id)
        ->and($payment->invoice_id)->toBe($item->invoice_id)
        ->and($item->invoice->fresh()->status)->toBe(InvoiceStatus::PAID);
});

it('refuses to take less money than the selected services cost', function () {
    [$patient] = patientWithLabCharge(5000);
    $shift = shiftOn(CashPoint::primary(), $this->cashier);
    $item = InvoiceItem::where('item_type', 'lab_test')->firstOrFail();

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $patient->id,
            'item_ids' => [$item->id],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 2000,
        ])
        ->assertSessionHasErrors(['amount_tendered']);

    expect($item->fresh()->settled_at)->toBeNull()
        ->and($shift->payments()->count())->toBe(0);
});

it('treats a re-sent payment as the same payment', function () {
    [$patient] = patientWithLabCharge(5000);
    $shift = shiftOn(CashPoint::primary(), $this->cashier);
    $item = InvoiceItem::where('item_type', 'lab_test')->firstOrFail();

    $payload = [
        'patient_id' => $patient->id,
        'item_ids' => [$item->id],
        'payment_method' => PaymentMethod::Cash->value,
        'amount_tendered' => 5000,
        'client_uuid' => 'counter-attempt-1',
    ];

    $this->actingAs($this->cashier)->post(route('caisse.settle'), $payload);
    $this->actingAs($this->cashier)->post(route('caisse.settle'), $payload);

    expect($shift->payments()->count())->toBe(1);
});

it('refuses to settle a service that belongs to another patient', function () {
    [$patient] = patientWithLabCharge(5000);
    $other = Patient::factory()->create();
    shiftOn(CashPoint::primary(), $this->cashier);
    $item = InvoiceItem::where('item_type', 'lab_test')->firstOrFail();

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $other->id,
            'item_ids' => [$item->id],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 5000,
        ])
        ->assertSessionHasErrors(['item_ids']);

    expect($item->fresh()->settled_at)->toBeNull();
});

it('bills a catalogue service on the spot and settles it in one step', function () {
    $patient = Patient::factory()->create();
    $shift = shiftOn(CashPoint::primary(), $this->cashier);
    $service = Service::factory()->create([
        'name' => 'Consultation Générale',
        'default_price' => 3000,
    ]);

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $patient->id,
            'item_ids' => [],
            'service_ids' => [$service->id],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 5000,
        ])
        ->assertRedirect();

    $item = InvoiceItem::where('item_id', $service->id)->firstOrFail();

    expect((float) $item->total_price)->toEqual(3000.0)
        ->and($item->settled_at)->not->toBeNull()
        ->and((float) $shift->payments()->sum('amount_paid'))->toEqual(3000.0);
});

it('refuses a pharmacy charge at the main till', function () {
    $patient = Patient::factory()->create();
    $mainPoint = CashPoint::primary();
    shiftOn($mainPoint, $this->cashier);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-PHARM-1',
        'patient_id' => $patient->id,
        'invoice_date' => now()->toDateString(),
        'subtotal' => 4000,
        'tax_amount' => 0,
        'total_amount' => 4000,
        'status' => InvoiceStatus::DRAFT->value,
    ]);

    $drugs = $invoice->items()->create([
        'item_type' => 'pharmacy',
        'description' => 'Prescription #7',
        'quantity' => 1,
        'unit_price' => 4000,
        'total_price' => 4000,
    ]);

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $patient->id,
            'item_ids' => [$drugs->id],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 4000,
        ])
        ->assertSessionHasErrors(['item_ids']);

    expect($drugs->fresh()->settled_at)->toBeNull();
});

it('requires an open shift before taking money', function () {
    [$patient] = patientWithLabCharge(5000);
    $item = InvoiceItem::where('item_type', 'lab_test')->firstOrFail();

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $patient->id,
            'item_ids' => [$item->id],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 5000,
        ])
        ->assertSessionHas('error');

    expect($item->fresh()->settled_at)->toBeNull();
});

it('puts an invoice payment on the acting cashier shift, not someone else', function () {
    $financeUser = tap(User::factory()->create())->assignRole('admin');
    $shift = shiftOn(CashPoint::primary(), $financeUser);
    $otherCashier = tap(User::factory()->create())->assignRole('reception');
    $otherShift = shiftOn(CashPoint::primary(), $otherCashier);

    $patient = Patient::factory()->create();
    $invoice = Invoice::create([
        'invoice_number' => 'INV-TEST-1',
        'patient_id' => $patient->id,
        'invoice_date' => now()->toDateString(),
        'subtotal' => 4000,
        'tax_amount' => 0,
        'total_amount' => 4000,
        'status' => InvoiceStatus::PENDING->value,
    ]);

    $this->actingAs($financeUser)
        ->post(route('invoices.record-payment', $invoice), [
            'amount' => 4000,
            'payment_method' => PaymentMethod::Cash->value,
        ]);

    expect(CashPayment::where('cash_shift_id', $shift->id)->count())->toBe(1)
        ->and(CashPayment::where('cash_shift_id', $otherShift->id)->count())->toBe(0);
});
