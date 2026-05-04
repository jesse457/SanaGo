<?php

namespace Tests\Feature\Api\Tenants;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_gets_notifications()
    {
        Notification::factory()->count(3)->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User'
        ]);

        $response = $this->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'notifiable_id',
                        'notifiable_type',
                        'type',
                        'data',
                        'is_read',
                        'read_at',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_gets_unread_notifications()
    {
        Notification::factory()->count(2)->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User',
            'read_at' => null
        ]);
        Notification::factory()->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User',
            'read_at' => now()
        ]);

        $response = $this->getJson('/api/notifications/unread');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'notifiable_id',
                        'notifiable_type',
                        'type',
                        'data',
                        'is_read',
                        'read_at',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_gets_unread_count()
    {
        Notification::factory()->count(3)->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User',
            'read_at' => null
        ]);
        Notification::factory()->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User',
            'read_at' => now()
        ]);

        $response = $this->getJson('/api/notifications/unread-count');

        $response->assertStatus(200)
            ->assertJson([
                'count' => 3
            ]);
    }

    /** @test */
    public function it_shows_single_notification()
    {
        $notification = Notification::factory()->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User'
        ]);

        $response = $this->getJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => (string)$notification->id,
                'notifiable_id' => $notification->notifiable_id,
                'notifiable_type' => $notification->notifiable_type,
                'type' => $notification->type,
                'is_read' => true, // It should be marked as read when viewed
            ]);
    }

    /** @test */
    public function it_marks_notification_as_read()
    {
        $notification = Notification::factory()->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User',
            'read_at' => null
        ]);

        $response = $this->postJson("/api/notifications/{$notification->id}/mark-read");

        $response->assertStatus(200)
            ->assertJson([
                'id' => (string)$notification->id,
                'is_read' => true
            ]);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function it_marks_all_notifications_as_read()
    {
        Notification::factory()->count(3)->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User',
            'read_at' => null
        ]);

        $response = $this->postJson('/api/notifications/mark-all-read');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'All notifications marked as read',
                'data' => []
            ]);

        $this->assertCount(0, Notification::where('notifiable_id', $this->user->id)
            ->where('notifiable_type', 'App\Models\User')
            ->whereNull('read_at')
            ->get());
    }

    /** @test */
    public function it_deletes_notification()
    {
        $notification = Notification::factory()->create([
            'notifiable_id' => $this->user->id,
            'notifiable_type' => 'App\Models\User'
        ]);

        $response = $this->deleteJson("/api/notifications/{$notification->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Notification deleted successfully',
                'data' => []
            ]);

        $this->assertNull(Notification::find($notification->id));
    }

    /** @test */
    public function it_returns_404_for_not_found_notification()
    {
        $response = $this->getJson('/api/notifications/999');

        $response->assertStatus(404);
    }
}
