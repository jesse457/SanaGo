<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();

    // Seed landlord permissions and roles
    $permissions = [
        'view-super-admin', 'manage-landlord-users', 'view-system-health',
        'manage-tenants', 'view-tenants', 'view-tenant-health',
        'manage-plans', 'manage-subscriptions', 'view-finance',
        'manage-tickets', 'view-tickets', 'manage-leads', 'view-leads',
        'manage-announcements',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web'])
        ->syncPermissions($permissions);
});

// ─── Login Page Rendering ───────────────────────────────────────

describe('Login Page', function () {
    it('renders the login page with isCentralDomain prop', function () {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Auth/Login')
            ->has('isCentralDomain')
        );
    });
});

// ─── Login Validation ────────────────────────────────────────────

describe('Login Validation', function () {
    it('requires email and password', function () {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    });

    it('ignores stale type and subdomain fields from older clients', function () {
        $user = User::factory()->create([
            'email' => 'admin@sanago.local',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('super-admin');

        // The form no longer sends these; an old cached page might. The host
        // context decides who is signing in, so the extras must be harmless.
        $this->post(route('login'), [
            'email' => 'admin@sanago.local',
            'password' => 'password',
            'type' => 'tenant',
            'subdomain' => 'nonexistent-hospital',
        ])->assertRedirect(route('dashboard.super-admin'));

        $this->assertAuthenticatedAs($user);
    });
});

// ─── Landlord Login ──────────────────────────────────────────────

describe('Landlord Login', function () {
    it('authenticates a super-admin and redirects to dashboard', function () {
        $user = User::factory()->create([
            'email' => 'admin@sanago.local',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('super-admin');

        $response = $this->post(route('login'), [
            'email' => 'admin@sanago.local',
            'password' => 'password',
            'type' => 'landlord',
        ]);

        $response->assertRedirect(route('dashboard.super-admin'));
        $this->assertAuthenticatedAs($user);
    });

    it('rejects invalid credentials', function () {
        User::factory()->create([
            'email' => 'admin@sanago.local',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@sanago.local',
            'password' => 'wrong-password',
            'type' => 'landlord',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    });
});

// ─── Hospital login (context comes from the subdomain) ──────────

describe('Hospital Login', function () {
    beforeEach(function () {
        $this->seed(\Database\Seeders\RoleSeeder::class);
    });

    it('signs a hospital user in on their own subdomain', function () {
        $tenant = \App\Models\Tenant::factory()->create(['id' => 'city-hospital', 'name' => 'City Hospital']);

        $user = User::factory()->create([
            'email' => 'admin@healthnet.test',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('admin');

        actAsTenant($tenant);

        $this->post(route('login'), [
            'email' => 'admin@healthnet.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard.admin'));

        $this->assertAuthenticatedAs($user);
    });

    it('refuses a hospital account on the platform domain', function () {
        // In tests both seeders share one database, so the tenant admin role
        // ends up holding landlord permissions. A permission-less staff
        // account is the honest stand-in for "hospital user".
        User::factory()->create([
            'email' => 'admin@healthnet.test',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@healthnet.test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    });

    it('refuses a deactivated account', function () {
        $tenant = \App\Models\Tenant::factory()->create(['id' => 'green-valley', 'name' => 'Green Valley']);

        User::factory()->create([
            'email' => 'former@healthnet.test',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        actAsTenant($tenant);

        $this->post(route('login'), [
            'email' => 'former@healthnet.test',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    });
});

// ─── Login Transfer ──────────────────────────────────────────────

describe('Login Transfer', function () {
    it('rejects transfer without a token', function () {
        $response = $this->get(route('login.transfer'));

        $response->assertForbidden();
    });

    it('rejects transfer with an invalid token', function () {
        $response = $this->get(route('login.transfer', ['token' => 'invalid-token']));

        $response->assertForbidden();
    });

    it('rejects transfer with an expired token', function () {
        $payload = [
            'user_id' => 1,
            'tenant_id' => 'test-tenant',
            'expires_at' => now()->subMinute()->timestamp,
        ];
        $token = Crypt::encrypt($payload);

        $response = $this->get(route('login.transfer', ['token' => $token]));

        $response->assertForbidden();
    });
});

// ─── Middleware: EnsureCentralDomain ─────────────────────────────

describe('EnsureCentralDomain Middleware', function () {
    it('blocks super-admin routes from non-central domains', function () {
        // Create a regular user (no view-super-admin permission)
        // and attempt to access super-admin routes from a tenant subdomain.
        // The auth middleware passes (user is authenticated),
        // but central.domain blocks because it's not a central domain.
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withServerVariables([
                'HTTP_HOST' => 'city-hospital.sanago.local',
            ])->get(route('tenants.index'));

        $response->assertForbidden();
    });

    it('allows super-admin routes from central domains', function () {
        $user = User::factory()->create([
            'email' => 'admin@sanago.local',
        ]);
        $user->assignRole('super-admin');

        $response = $this->actingAs($user)
            ->withServerVariables([
                'HTTP_HOST' => 'sanago.local',
            ])->get(route('tenants.index'));

        $response->assertOk();
    });
});

// ─── Forgot Password ─────────────────────────────────────────────

describe('Forgot Password', function () {
    it('renders the forgot password page', function () {
        $response = $this->get(route('password.request'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Auth/ForgotPassword')
        );
    });

    it('sends a password reset link for a valid email', function () {
        User::factory()->create([
            'email' => 'admin@sanago.local',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'admin@sanago.local',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    });

    it('returns an error for an unrecognized email', function () {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@sanago.local',
        ]);

        $response->assertSessionHasErrors('email');
    });
});
