<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Home;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        Livewire::test(Home::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.home');
    }

    /** @test */
    public function it_displays_home_page_content()
    {
        Livewire::test(Home::class)
            ->assertStatus(200)
            ->assertSee('Comprehensive')
            ->assertSee('Integrated Healthcare Management')
            ->assertSee('Role-Based Access System');
    }
}
