<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
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

it('renders the appointments index', function () {
    Appointment::factory()->count(3)->create();
    $this->actingAs($this->admin)->get(route('appointments.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Appointments/Index'));
});

it('creates an appointment', function () {
    $patient = Patient::factory()->create();
    $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $patient->id,
        'doctor_id' => $this->admin->id,
        'appointment_date' => now()->addDays(3)->toDateString(),
        'appointment_time' => '10:00',
        'status' => 'scheduled',
        'reason_for_visit' => 'Routine checkup',
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('appointments', ['patient_id' => $patient->id]);
});

it('validates required fields on store', function () {
    $this->actingAs($this->admin)->post(route('appointments.store'), [])
        ->assertSessionHasErrors(['patient_id', 'doctor_id', 'appointment_date']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('appointments.index'))->assertForbidden();
});

it('updates an appointment', function () {
    $appointment = Appointment::factory()->scheduled()->create();
    $this->actingAs($this->admin)->put(route('appointments.update', $appointment), [
        'patient_id' => $appointment->patient_id,
        'doctor_id' => $this->admin->id,
        'appointment_date' => now()->addDays(5)->toDateString(),
        'appointment_time' => '14:00',
        'status' => 'completed',
        'reason_for_visit' => 'Follow-up',
    ])->assertRedirect();
    expect(Appointment::find($appointment->id)->status)->toBe(AppointmentStatus::COMPLETED);
});

// Note: AppointmentController has no destroy method — deletions may be handled via status changes
