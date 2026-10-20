<?php

declare(strict_types=1);

use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SuperAdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->seed(SuperAdminRoleSeeder::class);

    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);

    $this->tenantA = Tenant::factory()->create(['id' => 'hopital-a', 'name' => 'Hôpital A']);
    $this->tenantB = Tenant::factory()->create(['id' => 'hopital-b', 'name' => 'Hôpital B']);

    $this->userA = tap(User::factory()->create())->assignRole('admin');
    $this->userB = tap(User::factory()->create())->assignRole('admin');
});

describe('support tickets', function () {
    it('lets a hospital read its own ticket', function () {
        $ticket = SupportTicket::create([
            'tenant_id' => 'hopital-a',
            'user_id' => $this->userA->id,
            'subject' => 'Printer offline',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        actAsTenant($this->tenantA);

        $this->actingAs($this->userA)
            ->get(route('tenant.tickets.show', $ticket))
            ->assertOk();
    });

    it('hides another hospital ticket from the tenant portal', function () {
        $ticket = SupportTicket::create([
            'tenant_id' => 'hopital-b',
            'user_id' => $this->userB->id,
            'subject' => 'Confidential billing dispute',
            'priority' => 'high',
            'status' => 'open',
        ]);

        actAsTenant($this->tenantA);

        // Same id, different tenant: must not resolve.
        $this->actingAs($this->userA)
            ->get(route('tenant.tickets.show', $ticket))
            ->assertNotFound();

        $this->actingAs($this->userA)
            ->post(route('tenant.tickets.reply', $ticket), ['message' => 'injected'])
            ->assertNotFound();

        expect($ticket->messages()->count())->toBe(0);
    });

    it('does not leak internal notes to the hospital', function () {
        $ticket = SupportTicket::create([
            'tenant_id' => 'hopital-a',
            'user_id' => $this->userA->id,
            'subject' => 'Refund request',
            'priority' => 'low',
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'user_id' => $this->userA->id,
            'message' => 'Public reply',
            'is_internal' => false,
        ]);
        $ticket->messages()->create([
            'user_id' => $this->userA->id,
            'message' => 'Internal: do not refund yet',
            'is_internal' => true,
        ]);

        actAsTenant($this->tenantA);

        $this->actingAs($this->userA)
            ->get(route('tenant.tickets.show', $ticket))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Support/Show', false)
                ->has('ticket.messages', 1)
                ->where('ticket.messages.0.message', 'Public reply'));
    });

    it('stamps a new ticket with the acting tenant', function () {
        actAsTenant($this->tenantA);

        $this->actingAs($this->userA)
            ->post(route('tenant.tickets.store'), [
                'subject' => 'Cannot print receipts',
                'priority' => 'low',
                'message' => 'The Caisse receipt does not print.',
            ])
            ->assertRedirect();

        expect(SupportTicket::query()->where('tenant_id', 'hopital-a')->count())->toBe(1)
            ->and(SupportTicket::query()->where('tenant_id', 'hopital-b')->count())->toBe(0);
    });
});

describe('landlord visibility', function () {
    it('shows every ticket to the platform team', function () {
        $landlord = User::factory()->create();
        $landlord->assignRole('super-admin');

        SupportTicket::create([
            'tenant_id' => 'hopital-a',
            'user_id' => $this->userA->id,
            'subject' => 'A ticket',
            'priority' => 'low',
            'status' => 'open',
        ]);
        SupportTicket::create([
            'tenant_id' => 'hopital-b',
            'user_id' => $this->userB->id,
            'subject' => 'B ticket',
            'priority' => 'low',
            'status' => 'open',
        ]);

        $this->actingAs($landlord)
            ->get(route('support-tickets.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('SupportTickets/Index', false)
                ->has('tickets', 2));
    });
});
