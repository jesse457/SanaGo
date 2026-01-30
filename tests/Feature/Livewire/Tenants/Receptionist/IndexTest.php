<?php

namespace Tests\Feature\Livewire\Tenants\Receptionist;

use App\Livewire\Tenants\Receptionist\Index;
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
        $user = User::factory()->create(['role' => 'receptionist']);
        Auth::login($user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.receptionist.index');
    }

    /** @test */
    public function it_displays_receptionist_dashboard()
    {
        $user = User::factory()->create(['role' => 'receptionist']);
        Auth::login($user);

        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Receptionist Dashboard')
            ->assertSee('Welcome');
    }
}
