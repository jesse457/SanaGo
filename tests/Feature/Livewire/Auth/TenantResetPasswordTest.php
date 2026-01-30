<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Auth\TenantResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class TenantResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        Livewire::test(TenantResetPassword::class, [
            'token' => 'valid-token',
            'email' => 'test@example.com'
        ])
        ->assertStatus(200)
        ->assertViewIs('livewire.tenants.auth.tenant-reset-password');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        Livewire::test(TenantResetPassword::class, [
            'token' => 'valid-token',
            'email' => 'test@example.com'
        ])
        ->set('password', '')
        ->set('password_confirmation', '')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'required']);

        Livewire::test(TenantResetPassword::class, [
            'token' => 'valid-token',
            'email' => 'test@example.com'
        ])
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'min']);

        Livewire::test(TenantResetPassword::class, [
            'token' => 'valid-token',
            'email' => 'test@example.com'
        ])
        ->set('password', 'password1')
        ->set('password_confirmation', 'password2')
        ->call('resetPassword')
        ->assertHasErrors(['password' => 'same']);
    }

    /** @test */
    public function it_resets_tenant_user_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
            'tenant_id' => 'test-tenant-id',
        ]);

        Livewire::test(TenantResetPassword::class, [
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
