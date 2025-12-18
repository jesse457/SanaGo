<?php

namespace Tests\Feature\Landlord;

use App\Livewire\LandLord\CreateTenant;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TenantManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_landlord_can_view_create_tenant_page()
    {
        $landlord = User::factory()->create(['role' => 'landlord']);

        $this->actingAs($landlord)
            ->get(route('landlord.create-tenants'))
            ->assertStatus(200)
            ->assertSeeLivewire(CreateTenant::class);
    }

    public function test_landlord_can_create_tenant()
    {
        $landlord = User::factory()->create(['role' => 'landlord']);

        // Mock the config to ensure consistent domain generation
        config(['tenancy.central_domains' => ['localhost']]);

        Livewire::actingAs($landlord)
            ->test(CreateTenant::class)
            ->set('tenantName', 'Test Hospital')
            ->set('adminName', 'Admin User')
            ->set('adminEmail', 'admin@testhospital.com')
            ->set('subscriptionTier', Subscription::PLAN_BASIC)
            ->set('billingCycle', Subscription::BILLING_YEARLY)
            ->call('createTenant')
            ->assertHasNoErrors();

        $expectedDomain = 'test-hospital.localhost';

        $this->assertDatabaseHas('tenants', [
            'id' => $expectedDomain,
            'name' => 'Test Hospital',
        ]);

        $this->assertDatabaseHas('domains', [
            'domain' => $expectedDomain,
            'tenant_id' => $expectedDomain,
        ]);

        // Verify tenant context data
        $tenant = Tenant::find($expectedDomain);
        
        $tenant->run(function () {
            $this->assertDatabaseHas('users', [
                'email' => 'admin@testhospital.com',
                'role' => 'admin',
            ]);

            $this->assertDatabaseHas('subscriptions', [
                'plan' => Subscription::PLAN_BASIC,
                'billing_cycle' => Subscription::BILLING_YEARLY,
                'status' => Subscription::STATUS_ACTIVE,
            ]);
        });
    }

    public function test_non_landlord_cannot_access_create_tenant_page()
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('landlord.create-tenants'))
            ->assertForbidden(); // Or 403
    }
}
