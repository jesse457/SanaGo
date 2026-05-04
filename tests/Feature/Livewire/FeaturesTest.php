<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Features;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeaturesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        Livewire::test(Features::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.features');
    }

    /** @test */
    public function it_displays_features_content()
    {
        Livewire::test(Features::class)
            ->assertStatus(200)
            ->assertSee('Features')
            ->assertSee('Healthcare Management')
            ->assertSee('Features');
    }
}
