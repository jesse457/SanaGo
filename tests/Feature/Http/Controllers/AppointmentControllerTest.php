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

it('redirects the legacy calendar route to the index calendar tab', function () {
    $this->actingAs($this->admin)->get(route('appointments.calendar'))
        ->assertRedirect(route('appointments.index', ['view' => 'calendar']));
});

it('lets reception see every appointment', function () {
    $reception = tap(User::factory()->create())->assignRole('reception');
    Appointment::factory()->count(2)->create();

    $this->actingAs($reception)->get(route('appointments.index'))
        ->assertOk()
        ->assertInertia(fn ($p) => $p->has('appointments.data', 2)->has('calendarAppointments', 2));
});

it('scopes appointments to the signed-in doctor', function () {
    $doctor = tap(User::factory()->create())->assignRole('doctor');
    Appointment::factory()->create(['doctor_id' => $doctor->id]);
    Appointment::factory()->count(2)->create();

    $this->actingAs($doctor)->get(route('appointments.index'))
        ->assertOk()
        ->assertInertia(fn ($p) => $p->has('appointments.data', 1)->has('calendarAppointments', 1));
});

it('lets a doctor update their own appointment status', function () {
    $doctor = tap(User::factory()->create())->assignRole('doctor');
    $appointment = Appointment::factory()->scheduled()->create(['doctor_id' => $doctor->id]);

    $this->actingAs($doctor)
        ->patch(route('appointments.update', $appointment), ['status' => 'waiting'])
        ->assertRedirect();

    expect(Appointment::find($appointment->id)->status)->toBe(AppointmentStatus::WAITING);
});

it('prevents a doctor from updating another doctor appointment', function () {
    $doctor = tap(User::factory()->create())->assignRole('doctor');
    $appointment = Appointment::factory()->scheduled()->create();

    $this->actingAs($doctor)
        ->patch(route('appointments.update', $appointment), ['status' => 'waiting'])
        ->assertForbidden();
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
