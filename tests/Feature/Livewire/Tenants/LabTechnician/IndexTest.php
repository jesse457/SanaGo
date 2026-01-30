<?php

namespace Tests\Feature\Livewire\Tenants\LabTechnician;

use App\Livewire\Tenants\LabTechnician\Index;
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
        $user = User::factory()->create(['role' => 'lab-technician']);
        Auth::login($user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.lab-technician.index');
    }

    /** @test */
    public function it_displays_lab_technician_dashboard()
    {
        $user = User::factory()->create(['role' => 'lab-technician']);
        Auth::login($user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Lab Technician Dashboard')
            ->assertSee('Welcome');
    }
}
