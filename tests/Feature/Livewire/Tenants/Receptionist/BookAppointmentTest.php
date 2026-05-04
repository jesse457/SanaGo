<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Receptionist\BookAppointment;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class BookAppointmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'receptionist']);
        Auth::login($user);

        Livewire::test(BookAppointment::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.receptionist.book-appointment');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        $user = User::factory()->create(['role' => 'receptionist']);
        Auth::login($user);

        Livewire::test(BookAppointment::class)
            ->set('appointment.patient_id', '')
            ->set('appointment.doctor_id', '')
            ->set('appointment.appointment_date', '')
            ->set('appointment.appointment_time', '')
            ->call('bookAppointment')
            ->assertHasErrors(['appointment.patient_id' => 'required', 'appointment.doctor_id' => 'required', 'appointment.appointment_date' => 'required', 'appointment.appointment_time' => 'required']);
    }

    /** @test */
    public function it_books_an_appointment()
    {
        $user = User::factory()->create(['role' => 'receptionist']);
        Auth::login($user);

        $patient = Patient::factory()->create();
        $doctor = User::factory()->create(['role' => 'doctor']);

        Livewire::test(BookAppointment::class)
            ->set('appointment.patient_id', $patient->id)
            ->set('appointment.doctor_id', $doctor->id)
            ->set('appointment.appointment_date', '2024-01-15')
            ->set('appointment.appointment_time', '10:00')
            ->call('bookAppointment')
            ->assertHasNoErrors()
            ->assertSee('Appointment booked successfully');
    }
}
