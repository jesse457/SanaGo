<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Doctor\Patient as PatientComponent;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        Livewire::test(PatientComponent::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.doctor.patient');
    }

    /** @test */
    public function it_displays_patient_list()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        // Create test patients
        Patient::factory()->count(5)->create();

        Livewire::test(Patient::class)
            ->assertStatus(200)
            ->assertSee('Patients')
            ->assertCount('@patient-row', 5);
    }

    /** @test */
    public function it_searches_patients()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        Patient::factory()->create(['name' => 'John Doe']);
        Patient::factory()->create(['name' => 'Jane Smith']);
        Patient::factory()->create(['name' => 'Mike Johnson']);

        Livewire::test(Patient::class)
            ->set('search', 'john')
            ->assertStatus(200)
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith')
            ->assertDontSee('Mike Johnson');
    }

    /** @test */
    public function it_filters_patients_by_gender()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        Patient::factory()->create(['name' => 'John Doe', 'gender' => 'male']);
        Patient::factory()->create(['name' => 'Jane Smith', 'gender' => 'female']);

        Livewire::test(Patient::class)
            ->set('genderFilter', 'male')
            ->assertStatus(200)
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }
}
