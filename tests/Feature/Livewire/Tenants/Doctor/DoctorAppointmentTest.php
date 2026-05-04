<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Doctor\DoctorAppointment;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class DoctorAppointmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        Livewire::test(DoctorAppointment::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.doctor.doctor-appointment');
    }

    /** @test */
    public function it_displays_appointment_list()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        // Create test appointments
        Appointment::factory()->count(3)->create(['doctor_id' => $user->id]);

        Livewire::test(DoctorAppointment::class)
            ->assertStatus(200)
            ->assertSee('Appointments')
            ->assertCount('@appointment-row', 3);
    }

    /** @test */
    public function it_searches_appointments()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        $patient1 = \App\Models\Patient::factory()->create(['name' => 'John Doe']);
        $patient2 = \App\Models\Patient::factory()->create(['name' => 'Jane Smith']);
        $patient3 = \App\Models\Patient::factory()->create(['name' => 'Mike Johnson']);

        Appointment::factory()->create(['doctor_id' => $user->id, 'patient_id' => $patient1->id]);
        Appointment::factory()->create(['doctor_id' => $user->id, 'patient_id' => $patient2->id]);
        Appointment::factory()->create(['doctor_id' => $user->id, 'patient_id' => $patient3->id]);

        Livewire::test(DoctorAppointment::class)
            ->set('search', 'john')
            ->assertStatus(200)
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith')
            ->assertDontSee('Mike Johnson');
    }

    /** @test */
    public function it_filters_appointments_by_status()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        Appointment::factory()->create(['doctor_id' => $user->id, 'status' => 'scheduled']);
        Appointment::factory()->create(['doctor_id' => $user->id, 'status' => 'completed']);
        Appointment::factory()->create(['doctor_id' => $user->id, 'status' => 'cancelled']);

        Livewire::test(DoctorAppointment::class)
            ->set('statusFilter', 'completed')
            ->assertStatus(200)
            ->assertSee('completed')
            ->assertDontSee('scheduled')
            ->assertDontSee('cancelled');
    }
}
