<?php

declare(strict_types=1);

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->doctor = tap(User::factory()->create())->assignRole('doctor');
    $this->withoutMiddleware([
        \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
        \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    ]);
});

it('renders patient consultations', function () {
    $patient = Patient::factory()->create();
    $this->actingAs($this->doctor)->get(route('patients.consultations.index', $patient))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Consultations/Index'));
});

it('creates a consultation', function () {
    $patient = Patient::factory()->create();
    $this->actingAs($this->doctor)->post(route('consultations.store'), [
        'patient_id' => $patient->id,
        'doctor_id' => $this->doctor->id,
        'record_date' => now()->toDateString(),
        'chief_complaint' => 'Fever and headache for 3 days',
        'diagnosis' => 'Malaria',
        'treatment_plan' => 'Antimalarial therapy and follow-up',
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('medical_records', ['patient_id' => $patient->id]);
});

it('validates required fields on store', function () {
    $this->actingAs($this->doctor)->post(route('consultations.store'), [])
        ->assertSessionHasErrors(['patient_id', 'chief_complaint']);
});
