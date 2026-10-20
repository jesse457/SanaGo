<?php

declare(strict_types=1);

use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Services\Clinical\MedicalRecordService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
});

/**
 * The tenant test schema is created by database/migrations/test, which predates
 * the nullable `medication_id` migration. Apply the same relaxation here so the
 * service behaviour under test matches a real tenant database.
 */
/**
 * The shared test schema now declares `medication_id` nullable exactly like
 * the shipped tenant migration, so nothing has to be relaxed. The helper is
 * kept so the intent stays visible in these tests.
 */
function relaxPrescriptionItemMedicationId(): void
{
    $columns = collect(Schema::getColumns('prescription_items'))->keyBy('name');

    if (($columns['medication_id']['nullable'] ?? false) === true) {
        return;
    }

    DB::statement('PRAGMA foreign_keys = OFF');
    Schema::table('prescription_items', function ($table): void {
        $table->foreignId('medication_id')->nullable()->change();
    });
}

it('allows a free-text external drug to be prescribed without a catalogue entry', function () {
    relaxPrescriptionItemMedicationId();

    $doctor = User::factory()->create();
    $patient = Patient::factory()->create();

    $this->actingAs($doctor);

    $record = app(MedicalRecordService::class)->createConsultation([
        'patient_id' => $patient->id,
        'chief_complaint' => 'Persistent cough',
        'diagnosis' => 'Acute bronchitis',
        'treatment_plan' => 'Antitussive therapy',
        'prescription_items' => [
            [
                'drug_name' => 'Hospital-compounded cough syrup',
                'dosage' => '5ml',
                'frequency' => 'TID',
                'duration' => '5 days',
                'quantity' => 1,
            ],
        ],
    ]);

    $prescription = Prescription::where('patient_id', $patient->id)->firstOrFail();
    $item = $prescription->items()->firstOrFail();

    expect($record->patient_id)->toBe($patient->id)
        ->and($item->drug_name)->toBe('Hospital-compounded cough syrup')
        ->and($item->medication_id)->toBeNull();
});

it('still links a catalogue medication when one is supplied', function () {
    relaxPrescriptionItemMedicationId();

    $doctor = User::factory()->create();
    $patient = Patient::factory()->create();
    $medication = Medication::factory()->create(['name' => 'Paracetamol']);

    $this->actingAs($doctor);

    app(MedicalRecordService::class)->createConsultation([
        'patient_id' => $patient->id,
        'chief_complaint' => 'Fever',
        'diagnosis' => 'Viral syndrome',
        'treatment_plan' => 'Antipyretic therapy',
        'prescription_items' => [
            [
                'medication_id' => $medication->id,
                'drug_name' => 'Paracetamol',
                'dosage' => '500mg',
                'frequency' => 'TID',
                'duration' => '3 days',
                'quantity' => 9,
            ],
        ],
    ]);

    $item = Prescription::where('patient_id', $patient->id)->firstOrFail()->items()->firstOrFail();

    expect($item->medication_id)->toBe($medication->id);
});
