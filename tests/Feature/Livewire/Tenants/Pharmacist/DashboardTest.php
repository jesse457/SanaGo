<?php

namespace Tests\Feature\Livewire\Tenants\Pharmacist;

use App\Livewire\Tenants\Pharmacist\Dashboard;
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
        $user = User::factory()->create(['role' => 'pharmacist']);
        Auth::login($user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.pharmacist.dashboard');
    }

    /** @test */
    public function it_displays_pharmacist_dashboard()
    {
        $user = User::factory()->create(['role' => 'pharmacist']);
        Auth::login($user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertSee('Pharmacist Dashboard')
            ->assertSee('Welcome');
    }
}
