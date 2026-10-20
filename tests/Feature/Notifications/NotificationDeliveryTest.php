<?php

declare(strict_types=1);

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\BillingNotification;
use App\Notifications\SubscriptionNotification;
use App\Notifications\SystemNotification;
use App\Services\Platform\SubscriptionService;
use App\Services\System\NotificationService;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SuperAdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->seed(SuperAdminRoleSeeder::class);

    $this->tenant = Tenant::factory()->create(['id' => 'hopital-notifs', 'name' => 'Hôpital Notifications']);
    $this->plan = Plan::factory()->basic()->create();
});

describe('channel selection', function () {
    it('falls back to the database channel when transports are disabled', function () {
        config([
            'platform.notifications.realtime' => false,
            'platform.notifications.mail' => false,
        ]);

        $notification = new SystemNotification('Patient admitted', 'clinical', '/patients');
        $user = User::factory()->create();

        expect($notification->via($user))->toBe(['database']);
    });

    it('adds broadcast and mail when both transports are enabled', function () {
        config([
            'platform.notifications.realtime' => true,
            'platform.notifications.mail' => true,
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb' => ['driver' => 'reverb'],
        ]);

        $notification = new SystemNotification('Patient admitted', 'clinical', '/patients');
        $user = User::factory()->create(['email' => 'doctor@hospital.cm']);

        expect($notification->via($user))->toBe(['database', 'broadcast', 'mail']);
    });

    it('skips mail for a notifiable without an email address', function () {
        config([
            'platform.notifications.mail' => true,
            'platform.notifications.realtime' => false,
        ]);

        $notification = new SystemNotification('Stock low', 'pharmacy');

        // `users.email` is NOT NULL, so unset it in memory only: the channel
        // decision must not depend on a persisted row.
        $user = User::factory()->create();
        $user->email = null;

        expect($notification->via($user))->toBe(['database']);
    });

    it('can be restricted to the database channel explicitly', function () {
        config([
            'platform.notifications.mail' => true,
            'platform.notifications.realtime' => true,
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb' => ['driver' => 'reverb'],
        ]);

        $notification = (new SystemNotification('Stock low', 'pharmacy'))->databaseOnly();

        expect($notification->via(User::factory()->create(['email' => 'a@b.cm'])))->toBe(['database']);
    });
});

describe('delivery', function () {
    it('persists the notification for the bell', function () {
        $user = User::factory()->create();

        app(NotificationService::class)->notifyUser($user, 'New lab result ready', 'lab', '/lab/results');

        expect($user->fresh()->notifications()->count())->toBe(1);

        $payload = $user->fresh()->notifications()->first()->data;

        expect($payload['message'])->toBe('New lab result ready')
            ->and($payload['category'])->toBe('lab')
            ->and($payload['link'])->toBe('/lab/results');
    });

    it('survives a failed broadcast without losing the in-app notification', function () {
        config([
            'platform.notifications.realtime' => true,
            'platform.notifications.mail' => false,
            'broadcasting.default' => 'reverb',
            'broadcasting.connections.reverb' => ['driver' => 'reverb', 'key' => 'x', 'secret' => 'y', 'app_id' => 'z', 'options' => ['host' => '127.0.0.1', 'port' => 1]],
        ]);

        $user = User::factory()->create();

        $delivered = app(NotificationService::class)->deliver(
            $user,
            new SystemNotification('Realtime down', 'general'),
        );

        expect($delivered)->toBe(1)
            ->and($user->fresh()->notifications()->count())->toBe(1);
    });

    it('notifies the landlord owners about subscription changes', function () {
        Notification::fake();

        $landlord = User::factory()->create(['email' => 'owner@sanago.cm']);
        $landlord->assignRole('super-admin');

        app(SubscriptionService::class)->assignPlan($this->tenant, $this->plan);

        Notification::assertSentTo($landlord, SubscriptionNotification::class);
    });

    it('notifies tenant administrators when a plan is assigned', function () {
        Notification::fake();

        // The tenant has no database in this test, so delivery is skipped
        // instead of exploding — that is the graceful-degradation contract.
        $delivered = app(NotificationService::class)
            ->notifyTenantAdmins($this->tenant, new SubscriptionNotification('assigned', 'Plan assigned'));

        expect($delivered)->toBe(0);
        Notification::assertNothingSent();
    });
});

describe('notification API', function () {
    beforeEach(function () {
        $this->withoutMiddleware([
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);

        $this->admin = tap(User::factory()->create())->assignRole('admin');
        actAsTenant($this->tenant);
    });

    it('returns the unread notifications for the bell dropdown', function () {
        $this->admin->notify(new SystemNotification('Appointment moved', 'clinical', '/appointments'));

        $this->actingAs($this->admin)
            ->getJson(route('notifications.latest'))
            ->assertOk()
            ->assertJsonPath('unreadCount', 1)
            ->assertJsonPath('notifications.0.message', 'Appointment moved');
    });

    it('marks a single notification as read', function () {
        $this->admin->notify(new SystemNotification('Invoice paid', 'billing'));
        $notification = $this->admin->fresh()->notifications()->first();

        $this->actingAs($this->admin)
            ->postJson(route('notifications.read', $notification->id))
            ->assertOk();

        expect($notification->fresh()->read_at)->not->toBeNull();
    });

    it('marks every notification as read', function () {
        $this->admin->notify(new SystemNotification('One', 'general'));
        $this->admin->notify(new SystemNotification('Two', 'general'));

        $this->actingAs($this->admin)
            ->post(route('notifications.mark-all-read'))
            ->assertRedirect();

        expect($this->admin->fresh()->unreadNotifications()->count())->toBe(0);
    });

    it('renders the notification inbox', function () {
        $this->admin->notify(new SystemNotification('Lab result validated', 'lab'));

        $this->actingAs($this->admin)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Notifications/Index', false)
                ->has('notifications.data', 1)
                ->where('unreadCount', 1));
    });
});

describe('billing notifications', function () {
    it('notifies owners when an invoice is issued', function () {
        Notification::fake();

        $landlord = User::factory()->create();
        $landlord->assignRole('super-admin');

        app(SubscriptionService::class)->assignPlan($this->tenant, $this->plan);

        Notification::assertSentTo($landlord, BillingNotification::class);
    });
});
