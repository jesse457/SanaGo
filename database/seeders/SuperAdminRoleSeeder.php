<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Landlord Permissions ───
        $permissions = [
            // Super Admin
            'view-super-admin',
            'manage-landlord-users',
            'view-system-health',

            // Tenant Management
            'manage-tenants',
            'view-tenants',
            'view-tenant-health',
            'impersonate-tenants',

            // Plan & Subscription Management
            'manage-plans',
            'manage-subscriptions',
            'manage-feature-flags',
            'view-billing',
            'view-finance',

            // Platform operations
            'manage-platform-settings',
            'view-audit-logs',

            // Support
            'manage-tickets',
            'view-tickets',

            // Sales
            'manage-leads',
            'view-leads',

            // Announcements
            'manage-announcements',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ─── Super Admin ───
        $superAdmin = Role::findOrCreate('super-admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        // ─── Tenant Manager ───
        $tenantManager = Role::findOrCreate('tenant-manager', 'web');
        $tenantManager->syncPermissions([
            'view-super-admin',
            'manage-tenants',
            'view-tenants',
            'view-tenant-health',
            'impersonate-tenants',
            'view-audit-logs',
        ]);

        // ─── Billing Manager ───
        $billingManager = Role::findOrCreate('billing-manager', 'web');
        $billingManager->syncPermissions([
            'view-super-admin',
            'manage-plans',
            'manage-subscriptions',
            'manage-feature-flags',
            'view-billing',
            'view-finance',
            'view-tenants',
            'view-audit-logs',
        ]);

        // ─── Support Agent ───
        $supportAgent = Role::findOrCreate('support-agent', 'web');
        $supportAgent->syncPermissions([
            'view-super-admin',
            'view-tenants',
            'view-tenant-health',
            'manage-tickets',
            'view-tickets',
        ]);

        // ─── Sales Representative ───
        $salesRep = Role::findOrCreate('sales-rep', 'web');
        $salesRep->syncPermissions([
            'view-super-admin',
            'manage-leads',
            'view-leads',
        ]);
    }
}
