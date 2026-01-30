<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Admin\CreateNewUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class CreateNewUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(CreateNewUser::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.admin.create-new-user');
    }

    /** @test */
    public function validation_rules_are_enforced()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(CreateNewUser::class)
            ->set('name', '')
            ->set('email', '')
            ->set('password', '')
            ->set('role', '')
            ->call('createUser')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'password' => 'required', 'role' => 'required']);
    }

    /** @test */
    public function it_creates_new_admin_user()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(CreateNewUser::class)
            ->set('name', 'New Admin')
            ->set('email', 'newadmin@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'admin')
            ->call('createUser')
            ->assertHasNoErrors()
            ->assertSee('User created successfully');
    }

    /** @test */
    public function it_creates_new_doctor_user()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(CreateNewUser::class)
            ->set('name', 'Dr. John Smith')
            ->set('email', 'john.smith@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'doctor')
            ->call('createUser')
            ->assertHasNoErrors()
            ->assertSee('User created successfully');
    }

    /** @test */
    public function it_creates_new_nurse_user()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(CreateNewUser::class)
            ->set('name', 'Nurse Jane Doe')
            ->set('email', 'jane.doe@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'nurse')
            ->call('createUser')
            ->assertHasNoErrors()
            ->assertSee('User created successfully');
    }

    /** @test */
    public function it_creates_new_pharmacist_user()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(CreateNewUser::class)
            ->set('name', 'Pharmacist Mike Johnson')
            ->set('email', 'mike.johnson@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'pharmacist')
            ->call('createUser')
            ->assertHasNoErrors()
            ->assertSee('User created successfully');
    }

    /** @test */
    public function it_creates_new_lab_technician_user()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(CreateNewUser::class)
            ->set('name', 'Lab Tech Sarah Wilson')
            ->set('email', 'sarah.wilson@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'lab-technician')
            ->call('createUser')
            ->assertHasNoErrors()
            ->assertSee('User created successfully');
    }

    /** @test */
    public function it_creates_new_receptionist_user()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(CreateNewUser::class)
            ->set('name', 'Receptionist Emily Brown')
            ->set('email', 'emily.brown@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'receptionist')
            ->call('createUser')
            ->assertHasNoErrors()
            ->assertSee('User created successfully');
    }

    /** @test */
    public function it_checks_for_duplicate_email()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(CreateNewUser::class)
            ->set('name', 'Duplicate Email')
            ->set('email', $existingUser->email)
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('role', 'doctor')
            ->call('createUser')
            ->assertHasErrors(['email' => 'unique']);
    }
}
