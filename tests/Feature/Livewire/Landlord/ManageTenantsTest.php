<?php

namespace Tests\Feature\Livewire;

use App\Livewire\LandLord\ManageTenants;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class ManageTenantsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        Livewire::test(ManageTenants::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.land-lord.manage-tenants');
    }

    /** @test */
    public function it_displays_tenant_list()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        // Create test tenants
        Tenant::factory()->count(5)->create();

        Livewire::test(ManageTenants::class)
            ->assertStatus(200)
            ->assertSee('Tenants')
            ->assertCount('@tenant-row', 5);
    }

    /** @test */
    public function it_searches_tenants()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        // Create test tenants
        Tenant::factory()->create(['name' => 'Test Hospital']);
        Tenant::factory()->create(['name' => 'Demo Clinic']);
        Tenant::factory()->create(['name' => 'City Medical Center']);

        Livewire::test(ManageTenants::class)
            ->set('search', 'demo')
            ->assertStatus(200)
            ->assertSee('Demo Clinic')
            ->assertDontSee('Test Hospital')
            ->assertDontSee('City Medical Center');
    }

    /** @test */
    public function it_filters_tenants_by_status()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        // Create test tenants with different statuses
        Tenant::factory()->create(['status' => 'active']);
        Tenant::factory()->create(['status' => 'inactive']);
        Tenant::factory()->create(['status' => 'pending']);

        Livewire::test(ManageTenants::class)
            ->set('statusFilter', 'active')
            ->assertStatus(200)
            ->assertSee('active')
            ->assertDontSee('inactive')
            ->assertDontSee('pending');
    }
}
