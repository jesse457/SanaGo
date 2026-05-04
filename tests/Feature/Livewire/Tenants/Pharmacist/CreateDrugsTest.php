<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Pharmacist\CreateDrugs;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateDrugsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);

        $this->actingAs($user);

        Livewire::test(CreateDrugs::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.pharmacist.create-drugs');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        $this->actingAs($user);

        Livewire::test(CreateDrugs::class)
            // We set nothing (empty fields) to trigger "required" rules
            ->set('name', '')
            ->set('unit_price_purchase', '')
            ->set('stock_quantity', '')
            ->set('min_stock_level', '')
            ->set('dosage_unit', '')
            ->call('saveDrug')
            ->assertHasErrors([
                'name' => 'required',
                'unit_price_purchase' => 'required',
                'stock_quantity' => 'required',
                'min_stock_level' => 'required',
                'dosage_unit' => 'required'
            ]);
    }

    /** @test */
    public function it_creates_new_drug_successfully()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        $this->actingAs($user);

        // Assuming your 'pharmacist.manage-drugs' route exists,
        // otherwise this test will fail on the redirect assertion.

        Livewire::test(CreateDrugs::class)
            ->set('name', 'Paracetamol')
            ->set('description', 'Pain relief')
            ->set('unit_price_purchase', 500)
            ->set('stock_quantity', 100)
            ->set('min_stock_level', 10)
            ->set('dosage_unit', '500mg')
            ->call('saveDrug')
            ->assertHasNoErrors()
            ->assertRedirect(route('pharmacist.manage-drugs'));

        // Optional: Verify it actually saved to the database
        // Adjust table name 'medications' or 'drugs' based on your PharmacyService logic
        /*
        $this->assertDatabaseHas('medications', [
            'name' => 'Paracetamol',
            'stock_quantity' => 100,
        ]);
        */
    }
}
