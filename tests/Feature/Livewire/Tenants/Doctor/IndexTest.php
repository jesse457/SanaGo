<?php

namespace Tests\Feature\Livewire\Tenants\Doctor;

use App\Livewire\Tenants\Doctor\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.doctor.index');
    }

    /** @test */
    public function it_displays_doctor_dashboard()
    {
        $user = User::factory()->create(['role' => 'doctor']);
        Auth::login($user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Doctor Dashboard')
            ->assertSee('Welcome');
    }
}
