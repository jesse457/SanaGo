<?php

namespace Tests\Feature\Api\Tenants;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use Laravel\Sanctum\Sanctum;

class SyncControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_admin_pull_requires_authentication()
    {
        $response = $this->postJson('/api/sync/admin/pull', [
            'checkpoint' => [
                'updated_at' => '2024-01-01 00:00:00',
                'id' => 0
            ]
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_pull_returns_valid_response()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/sync/admin/pull', [
            'checkpoint' => [
                'updated_at' => '2024-01-01 00:00:00',
                'id' => 0
            ],
            'context' => 'dashboard'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'checkpoint',
                'documents',
                'hasMore'
            ]);

        $response->assertJsonPath('documents.dashboard_stats', fn($data) => is_array($data));
    }

    public function test_admin_pull_with_staff_context()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/sync/admin/pull', [
            'checkpoint' => [
                'updated_at' => '2024-01-01 00:00:00',
                'id' => 0
            ],
            'context' => 'staff'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'checkpoint',
                'documents' => [
                    'users',
                    'shifts',
                    'departments'
                ],
                'hasMore'
            ]);
    }

    public function test_admin_pull_with_settings_context()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/sync/admin/pull', [
            'checkpoint' => [
                'updated_at' => '2024-01-01 00:00:00',
                'id' => 0
            ],
            'context' => 'settings'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'checkpoint',
                'documents' => [
                    'departments',
                    'wards',
                    'bed_types',
                    'beds',
                    'supplies'
                ],
                'hasMore'
            ]);
    }

    public function test_admin_push_requires_authentication()
    {
        $response = $this->postJson('/api/sync/admin/push', [
            'collection' => 'users',
            'changes' => []
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_push_with_invalid_collection()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/sync/admin/push', [
            'collection' => 'invalid_collection',
            'changes' => []
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Push not allowed for \'invalid_collection\''
            ]);
    }

    public function test_doctor_pull_returns_readonly_for_push()
    {
        $tenant = Tenant::factory()->create();
        $doctor = User::factory()->create([
            'role' => 'doctor',
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]);
        Sanctum::actingAs($doctor);

        $response = $this->postJson('/api/sync/doctor/push', [
            'collection' => 'patients',
            'changes' => []
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Sync is read-only for this role. Use real-time API.'
            ]);
    }

    public function test_receptionist_push_valid_collection()
    {
        $tenant = Tenant::factory()->create();
        $receptionist = User::factory()->create([
            'role' => 'receptionist',
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]);
        Sanctum::actingAs($receptionist);

        $response = $this->postJson('/api/sync/receptionist/push', [
            'collection' => 'patients',
            'changes' => [
                [
                    'id' => '1',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'phone' => '1234567890',
                    'email' => 'john@example.com',
                    'gender' => 'male',
                    'dob' => '1990-01-01',
                    'updated_at' => now()->toISOString()
                ]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_pull_with_invalid_context()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'role' => 'admin',
            'tenant_id' => $tenant->id,
            'is_active' => true
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/sync/admin/pull', [
            'checkpoint' => [
                'updated_at' => '2024-01-01 00:00:00',
                'id' => 0
            ],
            'context' => 'invalid_context'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'checkpoint',
                'documents',
                'hasMore'
            ]);
    }
}
