<?php

namespace Tests\Feature\Livewire\Tenants\Nurse;

use App\Livewire\Tenants\Nurse\Dashboard;
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
        $user = User::factory()->create(['role' => 'nurse']);
        Auth::login($user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.nurse.dashboard');
    }

    /** @test */
    public function it_displays_nurse_dashboard()
    {
        $user = User::factory()->create(['role' => 'nurse']);
        Auth::login($user);

        Livewire::test(Dashboard::class)
            ->assertStatus(200)
            ->assertSee('Nurse Dashboard')
            ->assertSee('Welcome');
    }
}
