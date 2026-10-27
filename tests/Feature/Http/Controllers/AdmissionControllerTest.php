<?php

declare(strict_types=1);

use App\Enums\AdmissionStatus;
use App\Enums\BedStatus;
use App\Models\Admission;
use App\Models\Bed;
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

it('renders the admissions index', function () {
    Admission::factory()->count(2)->create();
    $this->actingAs($this->admin)->get(route('admissions.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Patients/Checkin'));
});

it('requests an admission for a patient', function () {
    $patient = Patient::factory()->create();

    $this->actingAs($this->admin)
        ->from(route('admissions.index'))
        ->post(route('request-admission', $patient))
        ->assertRedirect(route('admissions.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('admissions', [
        'patient_id' => $patient->id,
        'status' => AdmissionStatus::Pending->value,
    ]);

    // The column is a plain integer flag, not a cast boolean.
    expect($patient->fresh()->is_admitted_approve)->toBe(1);
});

it('renders the admit form with the beds that are still free', function () {
    $admission = Admission::factory()->pending()->create();
    Bed::factory()->create(['status' => BedStatus::Available->value]);
    Bed::factory()->create(['status' => BedStatus::Occupied->value]);

    $this->actingAs($this->admin)
        ->get(route('admissions.create', $admission))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Patients/Admission')
            ->has('availableBeds', 1)
            ->where('admission_id', $admission->id));
});

it('admits a pending patient into a bed', function () {
    $patient = Patient::factory()->create();
    $bed = Bed::factory()->create(['status' => BedStatus::Available->value]);
    $admission = Admission::factory()->pending()->create(['patient_id' => $patient->id]);

    $this->actingAs($this->admin)
        ->put(route('admissions.update', $admission), [
            'patient_id' => $patient->id,
            'bed_id' => $bed->id,
            'attending_doctor_id' => $this->admin->id,
            'admission_date' => now()->toDateString(),
            'reason_for_admission' => 'Severe malaria requiring IV therapy',
        ])
        ->assertRedirect(route('patients.show', $patient->id))
        ->assertSessionHasNoErrors();

    expect($admission->fresh()->status)->toBe(AdmissionStatus::Admitted)
        ->and($bed->fresh()->status)->toBe(BedStatus::Occupied);
});

it('validates the admit form', function () {
    $admission = Admission::factory()->pending()->create();

    $this->actingAs($this->admin)
        ->put(route('admissions.update', $admission), [])
        ->assertSessionHasErrors([
            'patient_id',
            'bed_id',
            'attending_doctor_id',
            'admission_date',
            'reason_for_admission',
        ]);

    expect($admission->fresh()->status)->toBe(AdmissionStatus::Pending);
});

it('discharges an admitted patient and frees the bed', function () {
    $bed = Bed::factory()->create(['status' => BedStatus::Occupied->value]);
    $admission = Admission::factory()->admitted()->create(['bed_id' => $bed->id]);

    $this->actingAs($this->admin)->post(route('admissions.discharge', $admission))
        ->assertRedirect();

    expect(Admission::find($admission->id)->status)->toBe(AdmissionStatus::Discharged)
        ->and($bed->fresh()->status)->toBe(BedStatus::Available);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('admissions.index'))->assertForbidden();
});
