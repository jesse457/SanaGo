<?php

declare(strict_types=1);

use App\Enums\PrescriptionStatus;
use App\Models\Invoice;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
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
    $this->doctor = tap(User::factory()->create())->assignRole('doctor');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

/**
 * @return array<string, mixed>
 */
function prescriptionPayload(Patient $patient, Medication $medication): array
{
    return [
        'patient_id' => $patient->id,
        'items' => [
            [
                'medication_id' => $medication->id,
                'dosage' => '500mg',
                'frequency' => 'TID',
                'duration' => '5 days',
                'quantity' => 15,
            ],
        ],
    ];
}

/**
 * Build a prescription directly (bypassing the factory).
 */
function makePrescription(Patient $patient, User $doctor, PrescriptionStatus $status): Prescription
{
    return Prescription::create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'prescription_date' => now()->toDateString(),
        'status' => $status->value,
        'total_amount' => 0,
    ]);
}

it('stores a new prescription with the PARTIAL status value', function () {
    $patient = Patient::factory()->create();
    $medication = Medication::factory()->create(['unit_cost' => 100]);

    $this->actingAs($this->doctor)
        ->post(route('prescriptions.store'), prescriptionPayload($patient, $medication))
        ->assertRedirect(route('prescriptions.index'))
        ->assertSessionHas('success');

    $prescription = Prescription::where('patient_id', $patient->id)->firstOrFail();

    // Reading the attribute back through the PrescriptionStatus cast used to
    // throw a ValueError for the literal 'active'.
    expect($prescription->status)->toBe(PrescriptionStatus::PARTIAL);
});

it('stores the prescription items with a computed total', function () {
    $patient = Patient::factory()->create();
    $medication = Medication::factory()->create(['name' => 'Amoxicillin', 'unit_cost' => 150]);

    $this->actingAs($this->doctor)
        ->post(route('prescriptions.store'), prescriptionPayload($patient, $medication));

    $prescription = Prescription::where('patient_id', $patient->id)->firstOrFail();

    expect($prescription->items()->count())->toBe(1)
        ->and((float) $prescription->total_amount)->toEqual(2250.00)
        ->and($prescription->items()->first()->drug_name)->toBe('Amoxicillin');
});

it('moves a dispensed prescription to the COMPLETED status value', function () {
    $patient = Patient::factory()->create();
    $medication = Medication::factory()->create(['unit_cost' => 100]);

    $this->actingAs($this->doctor)
        ->post(route('prescriptions.store'), prescriptionPayload($patient, $medication));

    $prescription = Prescription::where('patient_id', $patient->id)->firstOrFail();

    $this->actingAs($this->doctor)
        ->post(route('prescriptions.dispense', $prescription))
        ->assertRedirect(route('prescriptions.index'))
        ->assertSessionHas('success');

    // PrescriptionObserver keys on COMPLETED, so this is also what makes the
    // pharmacy billing hook fire.
    expect($prescription->fresh()->status)->toBe(PrescriptionStatus::COMPLETED)
        ->and($prescription->dispensations()->count())->toBe(1);
});

it('refuses to dispense an already dispensed prescription', function () {
    $patient = Patient::factory()->create();
    $prescription = makePrescription($patient, $this->doctor, PrescriptionStatus::COMPLETED);

    $this->actingAs($this->doctor)
        ->post(route('prescriptions.dispense', $prescription))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($prescription->fresh()->status)->toBe(PrescriptionStatus::COMPLETED)
        ->and($prescription->dispensations()->count())->toBe(0);
});

it('bills the pharmacy invoice when a prescription completes', function () {
    $patient = Patient::factory()->create();
    $medication = Medication::factory()->create(['unit_cost' => 200]);

    $this->actingAs($this->doctor)
        ->post(route('prescriptions.store'), prescriptionPayload($patient, $medication));

    $prescription = Prescription::where('patient_id', $patient->id)->firstOrFail();

    $this->actingAs($this->doctor)
        ->post(route('prescriptions.dispense', $prescription))
        ->assertRedirect();

    $invoice = Invoice::where('patient_id', $patient->id)->first();

    expect($invoice)->not->toBeNull()
        ->and($invoice->items()->where('item_type', 'pharmacy')->count())->toBe(1);
});
