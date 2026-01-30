<?php

namespace Tests\Feature\Livewire\Landlord;

use App\Livewire\LandLord\Dashboard;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.land-lord.dashboard');
    }

    /** @test */
    public function it_loads_dashboard_statistics()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        // Create test data
        Tenant::factory()->count(5)->create();
        Subscription::factory()->count(3)->create(['status' => 'active']);
        Subscription::factory()->count(2)->create(['status' => 'inactive']);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertSet('totalTenants', 5)
            ->assertSet('activeSubscriptions', 3);
    }

    /** @test */
    public function it_loads_recent_tenants()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        // Create test data
        Tenant::factory()->count(6)->create();

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertSet('recentTenants', function ($recentTenants) {
                return count($recentTenants) === 5;
            });
    }

    /** @test */
    public function it_loads_subscription_statistics()
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Auth::login($user);

        // Create test data
        Subscription::factory()->count(3)->create(['plan' => 'basic']);
        Subscription::factory()->count(2)->create(['plan' => 'premium']);
        Subscription::factory()->count(1)->create(['plan' => 'enterprise']);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertSet('subscriptionStats', function ($stats) {
                return $stats['basic'] === 3 && $stats['premium'] === 2 && $stats['enterprise'] === 1;
            });
    }
}
