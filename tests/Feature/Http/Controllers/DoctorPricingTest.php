<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Models\Appointment;
use App\Models\CashPoint;
use App\Models\DoctorServicePrice;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\Billing\PriceListService;
use App\Services\Billing\SettlementService;
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
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);

    $this->consultation = Service::factory()->create([
        'name' => 'Consultation Générale',
        'code' => 'CONS-GEN',
        'default_price' => 3000,
    ]);
});

it('resolves a doctor price over the catalogue price', function () {
    $doctor = User::factory()->create();
    $prices = app(PriceListService::class);

    expect($prices->priceFor($doctor, $this->consultation))->toEqual(3000.0)
        ->and($prices->consultationPrice($doctor))->toEqual(3000.0);

    DoctorServicePrice::create([
        'user_id' => $doctor->id,
        'service_id' => $this->consultation->id,
        'price' => 7000,
    ]);

    expect($prices->priceFor($doctor, $this->consultation))->toEqual(7000.0)
        ->and($prices->consultationPrice($doctor))->toEqual(7000.0);
});

it('saves doctor overrides and removes them when set back to the catalogue price', function () {
    $admin = tap(User::factory()->create())->assignRole('admin');
    $doctor = tap(User::factory()->create())->assignRole('doctor');

    $this->actingAs($admin)
        ->put(route('users.pricing.update', $doctor->id), [
            'prices' => [$this->consultation->id => 6500],
        ])
        ->assertRedirect(route('users.pricing', $doctor->id));

    expect((float) DoctorServicePrice::where('user_id', $doctor->id)->value('price'))
        ->toEqual(6500.0);

    // Setting the catalogue price back removes the override.
    $this->actingAs($admin)
        ->put(route('users.pricing.update', $doctor->id), [
            'prices' => [$this->consultation->id => 3000],
        ]);

    expect(DoctorServicePrice::where('user_id', $doctor->id)->count())->toBe(0);
});

it('renders the pricing page for a doctor', function () {
    $admin = tap(User::factory()->create())->assignRole('admin');
    $doctor = tap(User::factory()->create())->assignRole('doctor');

    $this->actingAs($admin)
        ->get(route('users.pricing', $doctor->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Users/Pricing')
            ->where('doctor.id', $doctor->id)
            ->has('prices')
        );
});

it('bills an appointment as a consultation at the doctor price', function () {
    $doctor = tap(User::factory()->create())->assignRole('doctor');

    DoctorServicePrice::create([
        'user_id' => $doctor->id,
        'service_id' => $this->consultation->id,
        'price' => 7500,
    ]);

    $patient = Patient::factory()->create();

    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'consultation_fee' => 0,
    ]);

    $item = InvoiceItem::where('item_type', 'consultation')->firstOrFail();

    expect((float) $item->total_price)->toEqual(7500.0)
        ->and($item->description)->toContain($doctor->name)
        ->and($item->settled_at)->toBeNull();
});

it('honours a negotiated fee already stored on the appointment', function () {
    $doctor = User::factory()->create();
    $patient = Patient::factory()->create();

    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'consultation_fee' => 4250,
    ]);

    expect((float) InvoiceItem::where('item_type', 'consultation')->value('total_price'))
        ->toEqual(4250.0);
});

it('never bills the same visit twice when the consultation is recorded', function () {
    $doctor = tap(User::factory()->create())->assignRole('doctor');
    $patient = Patient::factory()->create();

    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    MedicalRecord::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    expect(InvoiceItem::where('item_type', 'consultation')->count())->toBe(1);
});

it('scopes the counter list to the cashier till', function () {
    $patient = Patient::factory()->create();
    $invoice = Invoice::create([
        'invoice_number' => 'INV-TILL-1',
        'patient_id' => $patient->id,
        'invoice_date' => now()->toDateString(),
        'subtotal' => 9000,
        'tax_amount' => 0,
        'total_amount' => 9000,
        'status' => InvoiceStatus::DRAFT->value,
    ]);

    $lab = $invoice->items()->create([
        'item_type' => 'lab_test',
        'description' => 'Lab Test: NFS',
        'quantity' => 1,
        'unit_price' => 5000,
        'total_price' => 5000,
    ]);

    $drugs = $invoice->items()->create([
        'item_type' => 'pharmacy',
        'description' => 'Prescription #12',
        'quantity' => 1,
        'unit_price' => 4000,
        'total_price' => 4000,
    ]);

    $settlement = app(SettlementService::class);

    $main = CashPoint::create(['name' => 'Caisse principale', 'code' => 'main-till', 'receipt_prefix' => 'REC']);
    $pharmacy = CashPoint::create(['name' => 'Pharmacie', 'code' => 'pharmacy-till', 'module' => 'pharmacy', 'receipt_prefix' => 'PHM']);

    $mainIds = array_column($settlement->outstandingCharges($patient, $main), 'id');
    $pharmacyIds = array_column($settlement->outstandingCharges($patient, $pharmacy), 'id');

    expect($mainIds)->toBe([$lab->id])
        ->and($pharmacyIds)->toBe([$drugs->id]);
});

it('refuses to take pharmacy money at the main caisse', function () {
    $point = CashPoint::create(['name' => 'Caisse principale', 'code' => 'main-till-2', 'receipt_prefix' => 'REC']);

    expect($point->canCollect('pharmacy'))->toBeFalse()
        ->and($point->canCollect('lab_test'))->toBeTrue()
        ->and($point->canCollect('consultation'))->toBeTrue();

    $pharmacy = CashPoint::create(['name' => 'Pharmacie', 'code' => 'pharmacy-till-2', 'module' => 'pharmacy', 'receipt_prefix' => 'PHM']);

    expect($pharmacy->canCollect('pharmacy'))->toBeTrue()
        ->and($pharmacy->canCollect('lab_test'))->toBeFalse();
});
