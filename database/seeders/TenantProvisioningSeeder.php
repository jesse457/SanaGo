<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder for tenants provisioned through the super-admin panel.
 *
 * Unlike {@see TenantDatabaseSeeder} (the local demo dataset) this creates
 * only what a real hospital needs to start: roles/permissions, the service
 * catalogue and reference data. No fake patients, appointments or invoices
 * are written into a hospital's production database.
 */
class TenantProvisioningSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(ReferenceDataSeeder::class);
    }
}
