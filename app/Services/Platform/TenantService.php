<?php

declare(strict_types=1);

namespace App\Services\Platform;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TenantLifecycleNotification;
use App\Services\System\NotificationService;
use App\Support\PlatformContext;
use App\Support\TenantDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Landlord-side tenant lifecycle: provision, suspend, reactivate, archive,
 * restore, purge and support-access impersonation.
 *
 * Every method is explicit about the central connection and never touches a
 * tenant database other than through `tenancy()->initialize()`, so hospital
 * data stays isolated per tenant.
 */
class TenantService
{
    public function __construct(
        private readonly SubscriptionService $subscriptions,
        private readonly NotificationService $notifications,
        private readonly PlatformAuditService $audit,
    ) {}

    /**
     * Create a tenant with its domain, first administrator and plan.
     *
     * @param  array{slug: string, name: string, domain: string, admin_name?: string|null, admin_email?: string|null, admin_password?: string|null, plan_id?: int|null, billing_cycle?: string|null, trial?: bool|null, demo_data?: bool|null}  $data
     */
    public function provision(array $data): Tenant
    {
        $this->assertDomainAvailable($data['domain']);

        $demoData = (bool) ($data['demo_data'] ?? false);

        // Choose the seeder that runs as part of the TenantCreated pipeline.
        config([
            'tenancy.seeder_parameters' => [
                '--class' => $demoData ? 'TenantDatabaseSeeder' : 'TenantProvisioningSeeder',
            ],
        ]);

        $tenant = DB::connection('central')->transaction(function () use ($data) {
            // stancl/tenancy's VirtualColumn stores non-column attributes in
            // the `data` JSON blob. Passing `data` verbatim here silently
            // dropped the hospital name, so the attribute is passed directly.
            $tenant = Tenant::create([
                'id' => $data['slug'],
                'name' => $data['name'],
            ]);

            $tenant->domains()->create(['domain' => $data['domain']]);

            return $tenant;
        });

        $adminEmail = $data['admin_email'] ?? 'admin@'.$data['domain'];

        try {
            $this->createAdminUser(
                $tenant,
                name: $data['admin_name'] ?? $data['name'].' Admin',
                email: $adminEmail,
                password: $data['admin_password'] ?? Str::password(12),
            );
        } catch (Throwable $exception) {
            report($exception);
        }

        $plan = isset($data['plan_id']) ? Plan::query()->find($data['plan_id']) : $this->defaultPlan();

        if ($plan !== null) {
            $this->subscriptions->assignPlan($tenant, $plan, [
                'billing_cycle' => $data['billing_cycle'] ?? 'monthly',
                'trial' => (bool) ($data['trial'] ?? true),
            ]);
        }

        $this->audit->log(
            action: 'tenant.provisioned',
            description: sprintf('Tenant %s provisioned at %s', $data['name'], $data['domain']),
            subject: $tenant,
            tenantId: (string) $tenant->getTenantKey(),
            properties: ['domain' => $data['domain'], 'plan_id' => $plan?->id, 'admin_email' => $adminEmail],
        );

        $this->notifications->notifyLandlordAdmins(new TenantLifecycleNotification(
            event: 'provisioned',
            message: sprintf('Tenant %s (%s) was created.', $data['name'], $data['domain']),
            link: '/tenants/'.urlencode((string) $tenant->getTenantKey()),
            title: 'Tenant created',
        ));

        PlatformContext::flush();

        return $tenant->refresh();
    }

    /**
     * Suspend a tenant: the flag is enforced by EnsureTenantIsActive.
     */
    public function suspend(Tenant $tenant, ?string $reason = null): Tenant
    {
        $tenant->forceFill([
            'is_active' => false,
            'suspended_at' => now(),
        ])->save();

        $this->audit->log(
            action: 'tenant.suspended',
            description: sprintf('%s suspended%s', $tenant->displayName(), $reason ? ': '.$reason : ''),
            subject: $tenant,
            tenantId: (string) $tenant->getTenantKey(),
            properties: ['reason' => $reason],
        );

        $this->notifications->notifyLandlordAdmins(new TenantLifecycleNotification(
            event: 'suspended',
            message: sprintf('Tenant %s was suspended.', $tenant->displayName()),
            link: '/tenants/'.urlencode((string) $tenant->getTenantKey()),
            title: 'Tenant suspended',
        ));

        $this->notifyTenantAdmins($tenant, new TenantLifecycleNotification(
            event: 'suspended',
            message: $reason !== null
                ? 'Your hospital account has been suspended: '.$reason
                : 'Your hospital account has been suspended. Contact support.',
            link: '/support',
            title: 'Account suspended',
        ));

        PlatformContext::flush();

        return $tenant->refresh();
    }

    public function reactivate(Tenant $tenant): Tenant
    {
        $tenant->forceFill([
            'is_active' => true,
            'suspended_at' => null,
        ])->save();

        $this->audit->log(
            action: 'tenant.reactivated',
            description: sprintf('%s reactivated', $tenant->displayName()),
            subject: $tenant,
            tenantId: (string) $tenant->getTenantKey(),
        );

        $this->notifyTenantAdmins($tenant, new TenantLifecycleNotification(
            event: 'reactivated',
            message: 'Your hospital account is active again.',
            link: '/dashboard',
            title: 'Account reactivated',
        ));

        PlatformContext::flush();

        return $tenant->refresh();
    }

    /**
     * Archive (soft-delete) a tenant. Data is kept and can be restored.
     */
    public function archive(Tenant $tenant, ?string $reason = null): Tenant
    {
        $tenant->forceFill([
            'archived_at' => now(),
            'archived_reason' => $reason,
        ])->save();

        $this->audit->log(
            action: 'tenant.archived',
            description: sprintf('%s archived%s', $tenant->displayName(), $reason ? ': '.$reason : ''),
            subject: $tenant,
            tenantId: (string) $tenant->getTenantKey(),
            properties: ['reason' => $reason],
        );

        PlatformContext::flush();

        return $tenant->refresh();
    }

    public function restore(Tenant $tenant): Tenant
    {
        $tenant->forceFill([
            'archived_at' => null,
            'archived_reason' => null,
        ])->save();

        $this->audit->log(
            action: 'tenant.restored',
            description: sprintf('%s restored from archive', $tenant->displayName()),
            subject: $tenant,
            tenantId: (string) $tenant->getTenantKey(),
        );

        PlatformContext::flush();

        return $tenant->refresh();
    }

    /**
     * Permanently delete a tenant and drop its database. Callers must confirm.
     */
    public function purge(Tenant $tenant): void
    {
        $tenantId = (string) $tenant->getTenantKey();
        $name = $tenant->displayName();

        $this->audit->log(
            action: 'tenant.purged',
            description: sprintf('%s (%s) permanently deleted', $name, $tenantId),
            subject: $tenant,
            tenantId: $tenantId,
        );

        $tenant->delete();

        PlatformContext::flush();
    }

    /**
     * Create (or update) the first administrator inside the tenant database.
     */
    public function createAdminUser(Tenant $tenant, string $name, string $email, string $password): ?User
    {
        return TenantDatabase::run($tenant, function () use ($name, $email, $password): ?User {
            $user = User::query()->firstOrNew(['email' => $email]);
            $user->fill([
                'name' => $name,
                'password' => Hash::make($password),
                'is_active' => true,
            ]);
            $user->email_verified_at = now();
            $user->save();

            if (! $user->hasRole('admin')) {
                $user->assignRole('admin');
            }

            return $user;
        });
    }

    /**
     * Reset a tenant user's password from the platform panel.
     */
    public function resetUserPassword(Tenant $tenant, string $email, string $password): bool
    {
        $reset = TenantDatabase::run($tenant, function () use ($email, $password): bool {
            $user = User::query()->where('email', $email)->first();
            $user?->update(['password' => Hash::make($password)]);

            return $user !== null;
        }, false);

        if ($reset === true) {
            $this->audit->log(
                action: 'tenant.user_password_reset',
                description: sprintf('Password reset for %s in %s', $email, $tenant->displayName()),
                subject: $tenant,
                tenantId: (string) $tenant->getTenantKey(),
                properties: ['email' => $email],
            );
        }

        return $reset === true;
    }

    /**
     * Directory of users inside a tenant's database (read-only).
     *
     * @return array<int, array{id: int, name: string, email: string, roles: array<int, string>, is_active: bool, created_at: string|null}>
     */
    public function users(Tenant $tenant): array
    {
        return TenantDatabase::run($tenant, fn (): array => User::query()
            ->with('roles')
            ->orderBy('name')
            ->limit(200)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->all(),
                'is_active' => (bool) $user->is_active,
                'created_at' => $user->created_at?->toDateTimeString(),
            ])
            ->all(), []);
    }

    /**
     * Live usage counters for a tenant, read inside its own database.
     *
     * @return array{users: int, patients: int, appointments: int, invoices: int}
     */
    public function usage(Tenant $tenant): array
    {
        return TenantDatabase::run($tenant, fn (): array => [
            'users' => $this->countInTenant('users'),
            'patients' => $this->countInTenant('patients'),
            'appointments' => $this->countInTenant('appointments'),
            'invoices' => $this->countInTenant('invoices'),
        ], ['users' => 0, 'patients' => 0, 'appointments' => 0, 'invoices' => 0]);
    }

    /**
     * Health snapshot for one tenant: database reachability and freshness.
     *
     * @return array{reachable: bool, users: int, last_activity: string|null, migrations_ok: bool}
     */
    public function health(Tenant $tenant): array
    {
        $default = ['reachable' => false, 'users' => 0, 'last_activity' => null, 'migrations_ok' => false];

        if (! TenantDatabase::exists($tenant)) {
            return $default;
        }

        return TenantDatabase::run($tenant, function () use ($tenant): array {
            $reachable = true;
            $users = 0;

            try {
                DB::connection()->getPdo();
                $users = $this->countInTenant('users');
            } catch (Throwable) {
                $reachable = false;
            }

            return [
                'reachable' => $reachable,
                'users' => $users,
                'last_activity' => $tenant->last_seen_at?->toDateTimeString(),
                'migrations_ok' => $reachable && DB::connection()->getSchemaBuilder()->hasTable('patients'),
            ];
        }, $default);
    }

    /**
     * Usage, health and user directory in ONE tenancy initialisation.
     *
     * The tenant page needs all three; entering/leaving the tenant context
     * three times per page load is both slow and fragile.
     *
     * @return array{usage: array<string, int>, health: array<string, mixed>, users: array<int, array<string, mixed>>}
     */
    public function snapshot(Tenant $tenant): array
    {
        $default = [
            'usage' => ['users' => 0, 'patients' => 0, 'appointments' => 0, 'invoices' => 0],
            'health' => ['reachable' => false, 'users' => 0, 'last_activity' => null, 'migrations_ok' => false],
            'users' => [],
        ];

        if (! TenantDatabase::exists($tenant)) {
            return $default;
        }

        return TenantDatabase::run($tenant, function () use ($tenant): array {
            $reachable = true;

            try {
                DB::connection()->getPdo();
            } catch (Throwable) {
                $reachable = false;
            }

            $users = [];

            if ($reachable) {
                $users = User::query()
                    ->with('roles')
                    ->orderBy('name')
                    ->limit(200)
                    ->get()
                    ->map(fn (User $user) => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('name')->all(),
                        'is_active' => (bool) $user->is_active,
                        'created_at' => $user->created_at?->toDateTimeString(),
                    ])
                    ->all();
            }

            return [
                'usage' => [
                    'users' => $this->countInTenant('users'),
                    'patients' => $this->countInTenant('patients'),
                    'appointments' => $this->countInTenant('appointments'),
                    'invoices' => $this->countInTenant('invoices'),
                ],
                'health' => [
                    'reachable' => $reachable,
                    'users' => $this->countInTenant('users'),
                    'last_activity' => $tenant->last_seen_at?->toDateTimeString(),
                    'migrations_ok' => $reachable && DB::connection()->getSchemaBuilder()->hasTable('patients'),
                ],
                'users' => $users,
            ];
        }, $default);
    }

    /**
     * Subscriptions that expire inside the warning window.
     *
     * @return Collection<int, Subscription>
     */
    public function expiringSoon(int $days = 14)
    {
        return Subscription::query()
            ->with(['tenant', 'plan'])
            ->usable()
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', now()->addDays($days))
            ->whereDate('end_date', '>=', today())
            ->orderBy('end_date')
            ->get();
    }

    private function countInTenant(string $table): int
    {
        try {
            if (! DB::connection()->getSchemaBuilder()->hasTable($table)) {
                return 0;
            }

            return (int) DB::connection()->table($table)->count();
        } catch (Throwable) {
            return 0;
        }
    }

    private function defaultPlan(): ?Plan
    {
        return Plan::query()->where('is_active', true)->orderBy('sort_order')->first();
    }

    private function notifyTenantAdmins(Tenant $tenant, TenantLifecycleNotification $notification): void
    {
        try {
            $this->notifications->notifyTenantAdmins($tenant, $notification);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function assertDomainAvailable(string $domain): void
    {
        $exists = DB::connection('central')->table('domains')->where('domain', $domain)->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'domain' => 'That domain is already used by another hospital.',
            ]);
        }
    }
}
