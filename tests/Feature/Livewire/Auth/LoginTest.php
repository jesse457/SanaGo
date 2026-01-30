<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        Livewire::test(Login::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.auth.login');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', '')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'required', 'password' => 'required']);

        Livewire::test(Login::class)
            ->set('email', 'not-an-email')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function it_displays_error_for_invalid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'nonexistent@example.com')
            ->set('password', 'wrongpassword')
            ->call('authenticate')
            ->assertHasErrors('email');
    }

    /** @test */
    public function it_displays_error_for_inactive_account()
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasErrors('email');
    }

    /** @test */
    public function it_successful_landlord_login()
    {
        $user = User::factory()->create([
            'email' => 'landlord@example.com',
            'password' => bcrypt('password'),
            'role' => 'landlord',
            'is_active' => true,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('landlord.dashboard'));
    }

    /** @test */
    public function it_successful_admin_login()
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'tenant_id' => $tenant->id,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function it_successful_doctor_login()
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => bcrypt('password'),
            'role' => 'doctor',
            'is_active' => true,
            'tenant_id' => $tenant->id,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('doctor.dashboard'));
    }

    /** @test */
    public function it_successful_nurse_login()
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $user = User::factory()->create([
            'email' => 'nurse@example.com',
            'password' => bcrypt('password'),
            'role' => 'nurse',
            'is_active' => true,
            'tenant_id' => $tenant->id,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('nurse.dashboard'));
    }

    /** @test */
    public function it_successful_pharmacist_login()
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $user = User::factory()->create([
            'email' => 'pharmacist@example.com',
            'password' => bcrypt('password'),
            'role' => 'pharmacist',
            'is_active' => true,
            'tenant_id' => $tenant->id,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('pharmacist.dashboard'));
    }

    /** @test */
    public function it_successful_lab_technician_login()
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $user = User::factory()->create([
            'email' => 'labtech@example.com',
            'password' => bcrypt('password'),
            'role' => 'lab-technician',
            'is_active' => true,
            'tenant_id' => $tenant->id,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('lab-technician.dashboard'));
    }

    /** @test */
    public function it_successful_receptionist_login()
    {
        $tenant = \App\Models\Tenant::factory()->create();
        $user = User::factory()->create([
            'email' => 'receptionist@example.com',
            'password' => bcrypt('password'),
            'role' => 'receptionist',
            'is_active' => true,
            'tenant_id' => $tenant->id,
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('receptionist.dashboard'));
    }
}
