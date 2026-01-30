<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Pharmacist\Medications;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class MedicationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        Auth::login($user);

        Livewire::test(Medications::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.pharmacist.medications');
    }

    /** @test */
    public function it_displays_medications_list()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        Auth::login($user);

        // Create test medications
        \App\Models\Medication::factory()->count(5)->create();

        Livewire::test(Medications::class)
            ->assertStatus(200)
            ->assertSee('Medications')
            ->assertCount('@medication-row', 5);
    }

    /** @test */
    public function it_searches_medications()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        Auth::login($user);

        \App\Models\Medication::factory()->create(['name' => 'Paracetamol']);
        \App\Models\Medication::factory()->create(['name' => 'Ibuprofen']);
        \App\Models\Medication::factory()->create(['name' => 'Amoxicillin']);

        Livewire::test(Medications::class)
            ->set('search', 'para')
            ->assertStatus(200)
            ->assertSee('Paracetamol')
            ->assertDontSee('Ibuprofen')
            ->assertDontSee('Amoxicillin');
    }

    /** @test */
    public function it_filters_medications_by_category()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        Auth::login($user);

        \App\Models\Medication::factory()->create(['name' => 'Paracetamol', 'category' => 'painkiller']);
        \App\Models\Medication::factory()->create(['name' => 'Ibuprofen', 'category' => 'painkiller']);
        \App\Models\Medication::factory()->create(['name' => 'Amoxicillin', 'category' => 'antibiotic']);

        Livewire::test(Medications::class)
            ->set('categoryFilter', 'painkiller')
            ->assertStatus(200)
            ->assertSee('Paracetamol')
            ->assertSee('Ibuprofen')
            ->assertDontSee('Amoxicillin');
    }
}
