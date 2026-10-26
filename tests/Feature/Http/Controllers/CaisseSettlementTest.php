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
use App\Models\Medication;
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

/**
 * A patient with one unpaid laboratory charge — what the counter writes by
 * hand now that lab orders no longer bill themselves.
 */
function patientWithLabCharge(float $price = 5000): array
{
    $patient = Patient::factory()->create();
    $definition = LabTestDefinition::factory()->create(['price' => $price]);

    LabRequest::factory()->create([
        'patient_id' => $patient->id,
        'test_definition_id' => $definition->id,
        'status' => LabRequestStatus::PENDING,
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-LAB-1',
        'patient_id' => $patient->id,
        'invoice_date' => now()->toDateString(),
        'subtotal' => $price,
        'tax_amount' => 0,
        'total_amount' => $price,
        'status' => InvoiceStatus::DRAFT->value,
    ]);

    $item = $invoice->items()->create([
        'item_type' => 'lab_test',
        'item_id' => null,
        'description' => 'Lab Test: '.$definition->test_name,
        'quantity' => 1,
        'unit_price' => $price,
        'total_price' => $price,
    ]);

    return [$patient, $item];
}

it('records a lab order without billing anything', function () {
    $patient = Patient::factory()->create();
    $definition = LabTestDefinition::factory()->create(['price' => 5000]);

    $request = LabRequest::factory()->create([
        'patient_id' => $patient->id,
        'test_definition_id' => $definition->id,
        'status' => LabRequestStatus::PENDING,
    ]);

    // Order and completion are clinical events: a charge exists only once the
    // counter writes one.
    $request->update(['status' => LabRequestStatus::COMPLETED]);

    expect(InvoiceItem::where('item_type', 'lab_test')->count())->toBe(0)
        ->and(Invoice::count())->toBe(0);
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

it('records a partial payment and leaves the charge partially paid', function () {
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
        ->assertRedirect();

    $item->refresh();

    expect($item->settled_at)->toBeNull()
        ->and($item->payment_status->value)->toBe('partially_paid')
        ->and((float) $item->paid_amount)->toEqual(2000.0)
        ->and((float) $shift->payments()->sum('amount_paid'))->toEqual(2000.0)
        // Money moved but the bill is not covered: it shows up as overdue
        // instead of hiding in the draft bucket while the patient owes the rest.
        ->and($item->invoice->fresh()->status)->toBe(InvoiceStatus::OVERDUE);
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
            'picks' => [['source' => 'service', 'id' => $service->id]],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 5000,
        ])
        ->assertRedirect();

    $item = InvoiceItem::where('item_id', $service->id)->firstOrFail();

    expect((float) $item->total_price)->toEqual(3000.0)
        ->and($item->settled_at)->not->toBeNull()
        ->and((float) $shift->payments()->sum('amount_paid'))->toEqual(3000.0);
});

it('refuses a pharmacy charge at a laboratory till', function () {
    $patient = Patient::factory()->create();
    $labPoint = CashPoint::create([
        'name' => 'Laboratoire',
        'code' => 'lab-till',
        'module' => 'laboratory',
        'receipt_prefix' => 'LAB',
    ]);
    shiftOn($labPoint, $this->cashier);

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

it('takes the money for a drug charge at the main till', function () {
    $patient = Patient::factory()->create();
    $shift = shiftOn(CashPoint::primary(), $this->cashier);
    $medication = Medication::factory()->create([
        'name' => 'Amoxicillin',
        'strength' => '250mg',
        'unit_cost' => 1200,
    ]);

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $patient->id,
            'item_ids' => [],
            'picks' => [['source' => 'medication', 'id' => $medication->id, 'quantity' => 2]],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 2400,
        ])
        ->assertRedirect();

    $item = InvoiceItem::where('item_type', 'pharmacy')
        ->where('item_id', $medication->id)
        ->firstOrFail();

    expect((float) $item->total_price)->toEqual(2400.0)
        ->and($item->settled_at)->not->toBeNull()
        ->and((float) $shift->payments()->sum('amount_paid'))->toEqual(2400.0);
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
