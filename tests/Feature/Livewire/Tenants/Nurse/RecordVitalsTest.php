<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Nurse\RecordVitals;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class RecordVitalsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'nurse']);
        Auth::login($user);
        $patient = Patient::factory()->create();

        Livewire::test(RecordVitals::class, ['patient' => $patient])
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.nurse.record-vitals');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        $user = User::factory()->create(['role' => 'nurse']);
        Auth::login($user);
        $patient = Patient::factory()->create();

        Livewire::test(RecordVitals::class, ['patient' => $patient])
            ->set('vitals.temperature', '')
            ->set('vitals.pulse', '')
            ->set('vitals.respiration', '')
            ->set('vitals.blood_pressure', '')
            ->call('saveVitals')
            ->assertHasErrors(['vitals.temperature' => 'required', 'vitals.pulse' => 'required', 'vitals.respiration' => 'required', 'vitals.blood_pressure' => 'required']);
    }

    /** @test */
    public function it_records_patient_vitals()
    {
        $user = User::factory()->create(['role' => 'nurse']);
        Auth::login($user);
        $patient = Patient::factory()->create();

        Livewire::test(RecordVitals::class, ['patient' => $patient])
            ->set('vitals.temperature', '37.5')
            ->set('vitals.pulse', '80')
            ->set('vitals.respiration', '16')
            ->set('vitals.blood_pressure', '120/80')
            ->set('vitals.oxygen_saturation', '98')
            ->call('saveVitals')
            ->assertHasNoErrors()
            ->assertSee('Vitals recorded successfully');
    }
}
