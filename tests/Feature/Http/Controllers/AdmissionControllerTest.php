<?php

declare(strict_types=1);

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
        \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the admissions index', function () {
    Admission::factory()->count(2)->create();
    $this->actingAs($this->admin)->get(route('admissions.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Patients/Checkin'));
});

it('creates an admission', function () {
    $patient = Patient::factory()->create();
    $bed = Bed::factory()->create();
    $this->actingAs($this->admin)->post(route('admissions.store'), [
        'patient_id' => $patient->id,
        'bed_id' => $bed->id,
        'attending_doctor_id' => $this->admin->id,
        'admission_date' => now()->toDateString(),
        'status' => 'pending',
        'reason_for_admission' => 'Test admission',
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('admissions', ['patient_id' => $patient->id]);
});

it('discharges an admitted patient', function () {
    $admission = Admission::factory()->admitted()->create();
    $this->actingAs($this->admin)->post(route('admissions.discharge', $admission))
        ->assertRedirect();
    expect(Admission::find($admission->id)->status)->toBe(\App\Enums\AdmissionStatus::Discharged);
});

it('validates required fields on store', function () {
    $this->actingAs($this->admin)->post(route('admissions.store'), [])
        ->assertSessionHasErrors(['patient_id']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('admissions.index'))->assertForbidden();
});
