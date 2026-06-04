<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Illuminate\Support\Facades\Artisan;

/**
 * Runs tenant-only table migrations for testing.
 *
 * Central tables (users, permissions, roles, etc.) are created by
 * RefreshDatabase. Tenant-specific tables (patients, appointments,
 * admissions, etc.) need a separate migration that won't conflict.
 */
function migrateTenantTables(): void
{
    if (\Illuminate\Support\Facades\Schema::hasTable('patients')) {
        return;
    }

    Artisan::call('migrate', [
        '--path' => 'database/migrations/test',
        '--force' => true,
    ]);
}
