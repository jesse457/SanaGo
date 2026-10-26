<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\CashPoint;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabTestDefinition;
use App\Models\MedicalRecord;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\Billing\CounterCatalogue;
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
        'category' => 'consultation',
        'default_price' => 3000,
    ]);
});

it('records a consultation without billing anything', function () {
    $doctor = tap(User::factory()->create())->assignRole('doctor');
    $patient = Patient::factory()->create();

    Appointment::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => today()->toDateString(),
        'consultation_fee' => 0,
    ]);

    MedicalRecord::factory()->create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'record_date' => today()->toDateString(),
    ]);

    // The clinical workspace no longer touches money at all: the visit is
    // never charged when it is recorded, the counter owns billing.
    expect(InvoiceItem::where('item_type', 'consultation')->count())->toBe(0)
        ->and(Invoice::count())->toBe(0);
});

it('prices the cash desk catalogue by module', function () {
    $consultation = $this->consultation;
    $procedure = Service::factory()->create([
        'category' => 'procedure',
        'default_price' => 45000,
    ]);
    $drug = Medication::factory()->create([
        'name' => 'Artemether',
        'strength' => '20/120mg',
        'unit_cost' => 1500,
    ]);
    $test = LabTestDefinition::factory()->create([
        'test_name' => 'Malaria Rapid Test',
        'price' => 2500,
    ]);
    $bedType = BedType::factory()->create(['name' => 'ICU', 'daily_rate' => 25000]);
    $patient = Patient::factory()->create();
    Admission::factory()->create([
        'patient_id' => $patient->id,
        'bed_id' => Bed::factory()->create(['bed_type_id' => $bedType->id])->id,
        'admission_date' => today()->subDays(2)->toDateString(),
    ]);

    $catalogue = app(CounterCatalogue::class);
    $rows = collect($catalogue->picks())->keyBy('key');

    expect($rows["service:{$consultation->id}"]['module'])->toBe('consultation')
        ->and($rows["service:{$procedure->id}"]['module'])->toBe('surgery')
        // Drugs are sold from the pharmacy stock, not from a flat list…
        ->and($rows["medication:{$drug->id}"]['module'])->toBe('pharmacy')
        ->and($rows["medication:{$drug->id}"]['price'])->toEqual(1500.0)
        ->and($rows["lab_test:{$test->id}"]['module'])->toBe('laboratory')
        // …and a bed day comes from the stay that is running, never from the
        // flat price list the cashier searches.
        ->and(collect($catalogue->picks())->where('module', 'admission')->isEmpty())->toBeTrue()
        ->and($catalogue->ongoingAdmission($patient)['suggestions'][0]['module'])->toBe('admission');
});

it('filters the price list by module', function () {
    $admin = tap(User::factory()->create())->assignRole('admin');

    Service::factory()->lab()->create(['name' => 'Complete blood count']);
    Service::factory()->create([
        'name' => 'Paracetamol 500mg',
        'category' => 'medication',
    ]);

    $this->actingAs($admin)
        ->get(route('finance.services.index', ['module' => 'pharmacy']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Finance/Services/Index')
            ->has('services.data', 1)
            ->where('services.data.0.module', 'pharmacy')
            ->where('modules.3.label', 'Drugs')
        );
});

it('changes the catalogue price from the price list itself', function () {
    $admin = tap(User::factory()->create())->assignRole('admin');

    $this->actingAs($admin)
        ->put(route('finance.services.price', $this->consultation->id), [
            'default_price' => 6500,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect((float) $this->consultation->fresh()->default_price)->toBe(6500.0);
});

it('rejects a negative catalogue price', function () {
    $admin = tap(User::factory()->create())->assignRole('admin');

    $this->actingAs($admin)
        ->put(route('finance.services.price', $this->consultation->id), [
            'default_price' => -500,
        ])
        ->assertSessionHasErrors(['default_price']);

    expect((float) $this->consultation->fresh()->default_price)->toBe(3000.0);
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

    // The main desk clears the whole bill, drugs included: a patient must
    // never have to queue twice for one statement.
    expect($mainIds)->toBe([$lab->id, $drugs->id])
        ->and($pharmacyIds)->toBe([$drugs->id]);
});

it('lets the catch-all desk take any money but keeps module tills to their module', function () {
    $point = CashPoint::create(['name' => 'Caisse principale', 'code' => 'main-till-2', 'receipt_prefix' => 'REC']);

    expect($point->canCollect('pharmacy'))->toBeTrue()
        ->and($point->canCollect('lab_test'))->toBeTrue()
        ->and($point->canCollect('consultation'))->toBeTrue();

    $pharmacy = CashPoint::create(['name' => 'Pharmacie', 'code' => 'pharmacy-till-2', 'module' => 'pharmacy', 'receipt_prefix' => 'PHM']);

    expect($pharmacy->canCollect('pharmacy'))->toBeTrue()
        ->and($pharmacy->canCollect('lab_test'))->toBeFalse();

    $lab = CashPoint::create(['name' => 'Laboratoire', 'code' => 'lab-till-2', 'module' => 'laboratory', 'receipt_prefix' => 'LAB']);

    expect($lab->canCollect('lab_test'))->toBeTrue()
        ->and($lab->canCollect('pharmacy'))->toBeFalse();
});
