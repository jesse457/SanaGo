<?php

declare(strict_types=1);

use App\Enums\AdmissionStatus;
use App\Enums\CashShiftStatus;
use App\Enums\PaymentMethod;
use App\Models\Admission;
use App\Models\AdmissionService as PerformedAdmissionService;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\CashPoint;
use App\Models\CashShift;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabTestDefinition;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Services\Billing\BillingService;
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
    $this->cashier = tap(User::factory()->create())->assignRole('reception');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

function openShift(User $user, ?CashPoint $point = null): CashShift
{
    $point ??= CashPoint::primary();

    return CashShift::create([
        'user_id' => $user->id,
        'cash_point_id' => $point->id,
        'payment_point' => $point->name,
        'opening_balance' => 0,
        'status' => CashShiftStatus::Open->value,
        'opened_at' => now(),
    ]);
}

/**
 * A patient in a bed since N days, with the bed type priced by the catalogue.
 *
 * @return array{0: Patient, 1: Admission, 2: Service}
 */
function admittedPatient(int $nights = 3, float $dailyRate = 5000): array
{
    $service = Service::factory()->bed()->create([
        'name' => 'Lit/jour — Chambre Privée',
        'default_price' => $dailyRate,
    ]);

    $bedType = BedType::factory()->create([
        'name' => 'Private Room',
        'daily_rate' => 4000,
        'service_id' => $service->id,
    ]);

    $bed = Bed::factory()->create(['bed_type_id' => $bedType->id]);

    $patient = Patient::factory()->create();

    $admission = Admission::factory()->create([
        'patient_id' => $patient->id,
        'bed_id' => $bed->id,
        'status' => AdmissionStatus::Admitted,
        'admission_date' => today()->subDays($nights)->toDateString(),
        'discharge_date' => null,
    ]);

    return [$patient, $admission, $service];
}

it('sells drugs from the pharmacy stock at the catalogue price', function () {
    $drug = Medication::factory()->create([
        'name' => 'Ceftriaxone',
        'strength' => '1g',
        'unit_cost' => 500,
        'total_stock' => 40,
    ]);

    // The catalogue price wins over the stock cost: one price for everybody.
    $service = Service::factory()->create([
        'name' => 'Ceftriaxone 1g',
        'category' => 'medication',
        'default_price' => 2500,
    ]);
    $drug->update(['service_id' => $service->id]);

    $row = collect(app(CounterCatalogue::class)->picks())
        ->firstWhere('key', "medication:{$drug->id}");

    expect($row)->not->toBeNull()
        ->and($row['module'])->toBe('pharmacy')
        ->and($row['name'])->toBe('Ceftriaxone 1g')
        ->and($row['price'])->toEqual(2500.0)
        ->and($row['stock'])->toBe(40)
        ->and($row['item_type'])->toBe('pharmacy');
});

it('sells the laboratory catalogue, not a hand-typed test name', function () {
    $test = LabTestDefinition::factory()->create([
        'test_name' => 'Malaria Rapid Test',
        'code' => 'MRDT',
        'price' => 2500,
    ]);

    $row = collect(app(CounterCatalogue::class)->picks())
        ->firstWhere('key', "lab_test:{$test->id}");

    expect($row)->not->toBeNull()
        ->and($row['module'])->toBe('laboratory')
        ->and($row['item_type'])->toBe('lab_test')
        ->and($row['price'])->toEqual(2500.0);
});

it('keeps a module till to the module it belongs to', function () {
    Medication::factory()->create(['name' => 'Ibuprofen']);
    LabTestDefinition::factory()->create(['test_name' => 'Urinalysis']);

    $catalogue = app(CounterCatalogue::class);
    $pharmacy = CashPoint::create([
        'name' => 'Pharmacie',
        'code' => 'pharmacy-till',
        'module' => 'pharmacy',
        'receipt_prefix' => 'PHM',
    ]);

    $modules = array_column($catalogue->picks($pharmacy), 'module');

    expect(array_unique($modules))->toBe(['pharmacy'])
        ->and($catalogue->picks())->not->toBeEmpty();
});

it('detects the stay the patient is in and prices it from the catalogue', function () {
    [$patient, $admission, $service] = admittedPatient(nights: 3, dailyRate: 5000);

    $this->actingAs($this->cashier)
        ->getJson(route('caisse.outstanding', $patient->id))
        ->assertOk()
        ->assertJsonPath('admission.id', $admission->id)
        ->assertJsonPath('admission.nights', 3)
        ->assertJsonPath('admission.bed_type', 'Private Room')
        ->assertJsonPath('admission.suggestions.0.source', 'admission_bed')
        ->assertJsonPath('admission.suggestions.0.name', $service->name)
        ->assertJsonPath('admission.suggestions.0.price', 5000)
        ->assertJsonPath('admission.suggestions.0.quantity', 3);
});

it('offers the services already performed during the stay', function () {
    [$patient, $admission] = admittedPatient();

    $dressing = Service::factory()->create([
        'name' => 'Pansement',
        'category' => 'nursing',
        'default_price' => 2000,
    ]);

    $performed = PerformedAdmissionService::create([
        'admission_id' => $admission->id,
        'service_id' => $dressing->id,
        'quantity' => 2,
        'unit_price' => 2000,
        'total_price' => 4000,
    ]);

    $admissionRow = app(CounterCatalogue::class)->ongoingAdmission($patient);
    $keys = array_column($admissionRow['suggestions'], 'key');

    expect($keys)->toContain("admission_service:{$performed->id}");
});

it('stops treating a discharged stay as ongoing', function () {
    [$patient, $admission] = admittedPatient();

    $admission->update([
        'status' => AdmissionStatus::Discharged,
        'discharge_date' => today(),
    ]);

    expect(app(CounterCatalogue::class)->ongoingAdmission($patient))->toBeNull();

    $this->actingAs($this->cashier)
        ->getJson(route('caisse.outstanding', $patient->id))
        ->assertOk()
        ->assertJsonPath('admission', null);
});

it('bills the nights of the stay and settles them in one step', function () {
    [$patient, $admission, $service] = admittedPatient(nights: 4, dailyRate: 5000);
    $shift = openShift($this->cashier);

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $patient->id,
            'item_ids' => [],
            'picks' => [[
                'source' => 'admission_bed',
                'id' => $admission->id,
                'quantity' => 4,
            ]],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 20000,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $item = InvoiceItem::where('item_type', 'bed')->where('item_id', $admission->id)->firstOrFail();

    expect((float) $item->unit_price)->toEqual(5000.0)
        ->and($item->quantity)->toBe(4)
        ->and((float) $item->total_price)->toEqual(20000.0)
        ->and($item->settled_at)->not->toBeNull()
        ->and($item->description)->toBe($service->name)
        ->and((float) $shift->payments()->sum('amount_paid'))->toEqual(20000.0);
});

it('tops up unpaid nights instead of writing a second line', function () {
    [$patient, $admission] = admittedPatient(nights: 2);
    openShift($this->cashier);
    $settlement = app(SettlementService::class);

    $first = $settlement->billPicks($patient, [[
        'source' => 'admission_bed',
        'id' => $admission->id,
        'quantity' => 2,
    ]]);

    $second = $settlement->billPicks($patient, [[
        'source' => 'admission_bed',
        'id' => $admission->id,
        'quantity' => 5,
    ]]);

    expect($second)->toBe($first)
        ->and(InvoiceItem::where('item_type', 'bed')->count())->toBe(1)
        ->and(InvoiceItem::find($first[0])->quantity)->toBe(5)
        ->and((float) Invoice::firstOrFail()->total_amount)->toEqual(25000.0);
});

it('never bills another patient\'s stay', function () {
    [, $admission] = admittedPatient();
    $other = Patient::factory()->create();
    openShift($this->cashier);

    $this->actingAs($this->cashier)
        ->post(route('caisse.settle'), [
            'patient_id' => $other->id,
            'item_ids' => [],
            'picks' => [['source' => 'admission_bed', 'id' => $admission->id]],
            'payment_method' => PaymentMethod::Cash->value,
            'amount_tendered' => 5000,
        ])
        ->assertSessionHasErrors(['picks']);

    expect(InvoiceItem::where('item_type', 'bed')->count())->toBe(0);
});

it('does not bill the stay twice at discharge', function () {
    [$patient, $admission] = admittedPatient(nights: 3);
    $settlement = app(SettlementService::class);

    // The counter already took the three nights that had run up.
    $settlement->billPicks($patient, [[
        'source' => 'admission_bed',
        'id' => $admission->id,
        'quantity' => 3,
    ]]);

    $admission->update([
        'status' => AdmissionStatus::Discharged,
        'discharge_date' => today(),
    ]);

    $invoice = app(BillingService::class)->generateInvoiceFromAdmission($admission->fresh());

    expect($invoice->items()->where('item_type', 'bed')->count())->toBe(0)
        ->and(InvoiceItem::where('item_type', 'bed')->sum('quantity'))->toBe(3);
});

it('lists the drug and test catalogue for the desk in one payload', function () {
    Medication::factory()->create(['name' => 'Paracetamol']);
    LabTestDefinition::factory()->create(['test_name' => 'Blood Glucose']);
    Service::factory()->create(['name' => 'Consultation Générale', 'category' => 'consultation']);
    $patient = Patient::factory()->create();
    openShift($this->cashier);

    $this->actingAs($this->cashier)
        ->getJson(route('caisse.outstanding', $patient->id))
        ->assertOk()
        ->assertJsonStructure([
            'catalogue' => [['key', 'source', 'source_id', 'item_type', 'module', 'name', 'price']],
            'modules' => [['value', 'label']],
            'categories',
        ])
        ->assertJsonPath('admission', null);
});
