<?php

declare(strict_types=1);

use App\Enums\AdmissionStatus;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Patient;
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
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

it('does not leak non matching admissions when a status filter is applied', function () {
    $matched = Patient::factory()->create(['first_name' => 'Zainab', 'last_name' => 'Yusuf']);
    Admission::factory()->create([
        'patient_id' => $matched->id,
        'status' => AdmissionStatus::Admitted->value,
    ]);

    // This admission does NOT match the search term, and its status must also
    // keep it out once the status filter is applied.
    $other = Patient::factory()->create(['first_name' => 'Chidi', 'last_name' => 'Okonkwo']);
    Admission::factory()->create([
        'patient_id' => $other->id,
        'status' => AdmissionStatus::Pending->value,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admissions.index', [
            'search' => 'Zainab',
            'status' => AdmissionStatus::Admitted->value,
        ]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Patients/Checkin')
            ->has('admissions.data', 1)
            ->where('admissions.data.0.patient_id', $matched->id)
        );
});

it('still returns the matching admission when no status filter is set', function () {
    $matched = Patient::factory()->create(['first_name' => 'Zainab']);
    Admission::factory()->create(['patient_id' => $matched->id]);

    Patient::factory()->create(['first_name' => 'Chidi']);

    $this->actingAs($this->admin)
        ->get(route('admissions.index', ['search' => 'Zainab']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('admissions.data', 1));
});

it('does not leak non matching appointments when a status filter is applied', function () {
    $matched = Patient::factory()->create(['first_name' => 'Amara', 'last_name' => 'Nwosu']);
    Appointment::factory()->create([
        'patient_id' => $matched->id,
        'doctor_id' => $this->admin->id,
        'status' => 'scheduled',
    ]);

    $other = Patient::factory()->create(['first_name' => 'Bello', 'last_name' => 'Adamu']);
    Appointment::factory()->create([
        'patient_id' => $other->id,
        'doctor_id' => $this->admin->id,
        'status' => 'completed',
    ]);

    $this->actingAs($this->admin)
        ->get(route('appointments.index', ['search' => 'Amara', 'status' => 'scheduled']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Appointments/Index')
            ->has('appointments.data', 1)
            ->where('appointments.data.0.patient_id', $matched->id)
        );
});

it('does not leak appointments across a date filter', function () {
    $matched = Patient::factory()->create(['first_name' => 'Sade']);
    Appointment::factory()->create([
        'patient_id' => $matched->id,
        'doctor_id' => $this->admin->id,
        'appointment_date' => now()->toDateString(),
    ]);

    $other = Patient::factory()->create(['first_name' => 'Tunde']);
    Appointment::factory()->create([
        'patient_id' => $other->id,
        'doctor_id' => $this->admin->id,
        'appointment_date' => now()->addDays(5)->toDateString(),
    ]);

    $this->actingAs($this->admin)
        ->get(route('appointments.index', [
            'search' => 'Sade',
            'date' => now()->toDateString(),
        ]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('appointments.data', 1));
});
