<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\ForgotPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        Livewire::test(ForgotPassword::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.auth.forgot-password');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', '')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'required']);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'not-an-email')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function it_sends_reset_link_email()
    {
        Mail::fake();

        Livewire::test(ForgotPassword::class)
            ->set('email', 'test@example.com')
            ->call('sendResetLink')
            ->assertHasNoErrors()
            ->assertSet('emailSent', true);
    }

    /** @test */
    public function it_handles_email_not_found()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'nonexistent@example.com')
            ->call('sendResetLink')
            ->assertHasNoErrors()
            ->assertSet('emailSent', true);
    }
}
