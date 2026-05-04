<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tenants\Admin\UserManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_component_can_render()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        Livewire::test(UserManagement::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.tenants.admin.user-management');
    }

    /** @test */
    public function it_displays_user_list()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        // Create test users
        User::factory()->count(5)->create(['tenant_id' => $user->tenant_id]);

        Livewire::test(UserManagement::class)
            ->assertStatus(200)
            ->assertSee('User Management')
            ->assertCount('@user-row', 6); // 5 + current admin
    }

    /** @test */
    public function it_searches_users()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        // Create test users
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com', 'tenant_id' => $user->tenant_id]);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com', 'tenant_id' => $user->tenant_id]);
        User::factory()->create(['name' => 'Mike Johnson', 'email' => 'mike@example.com', 'tenant_id' => $user->tenant_id]);

        Livewire::test(UserManagement::class)
            ->set('search', 'john')
            ->assertStatus(200)
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith')
            ->assertDontSee('Mike Johnson');
    }

    /** @test */
    public function it_filters_users_by_role()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        // Create test users
        User::factory()->create(['role' => 'doctor', 'tenant_id' => $user->tenant_id]);
        User::factory()->create(['role' => 'nurse', 'tenant_id' => $user->tenant_id]);
        User::factory()->create(['role' => 'pharmacist', 'tenant_id' => $user->tenant_id]);

        Livewire::test(UserManagement::class)
            ->set('roleFilter', 'doctor')
            ->assertStatus(200)
            ->assertSee('doctor')
            ->assertDontSee('nurse')
            ->assertDontSee('pharmacist');
    }

    /** @test */
    public function it_filters_users_by_status()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);

        // Create test users
        User::factory()->create(['is_active' => true, 'tenant_id' => $user->tenant_id]);
        User::factory()->create(['is_active' => false, 'tenant_id' => $user->tenant_id]);

        Livewire::test(UserManagement::class)
            ->set('statusFilter', 'active')
            ->assertStatus(200)
            ->assertSee('Active')
            ->assertDontSee('Inactive');
    }
}
