<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        Livewire::test(ResetPassword::class, [
            'token' => 'valid-token',
            'email' => 'test@example.com'
        ])
        ->assertStatus(200)
        ->assertViewIs('livewire.auth.reset-password');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        Livewire::test(ResetPassword::class, [
            'token' => 'valid-token',
            'email' => 'test@example.com'
        ])
        ->set('password', '')
        ->set('password_confirmation', '')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'required']);

        Livewire::test(ResetPassword::class, [
            'token' => 'valid-token',
            'email' => 'test@example.com'
        ])
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'min']);

        Livewire::test(ResetPassword::class, [
            'token' => 'valid-token',
            'email' => 'test@example.com'
        ])
        ->set('password', 'password1')
        ->set('password_confirmation', 'password2')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'same']);
    }

    /** @test */
    public function it_resets_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        Livewire::test(ResetPassword::class, [
            'token' => 'valid-token',
            'email' => $user->email
        ])
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));
    }
}
