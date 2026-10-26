<?php

declare(strict_types=1);

namespace App\Tenancy;

use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class DatabaseManager implements TenantDatabaseManager
{
    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $path = $this->databasePath($tenant);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        if (! file_exists($path)) {
            touch($path);
        }

        return true;
    }

    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        $path = $this->databasePath($tenant);

        if (file_exists($path)) {
            return unlink($path);
        }

        return true;
    }

    public function databaseExists(string $name): bool
    {
        return file_exists(database_path('tenants/'.$name.'/database.sqlite'));
    }

    public function makeConnectionConfig(array $baseConfig, string $databaseName): array
    {
        return array_merge($baseConfig, [
            'database' => database_path('tenants/'.$databaseName.'/database.sqlite'),
        ]);
    }

    protected function databasePath(TenantWithDatabase $tenant): string
    {
        return database_path('tenants/'.$tenant->getTenantKey().'/database.sqlite');
    }

    public function setConnection(string $connection): void
    {
        // No-op for SQLite
    }
}
