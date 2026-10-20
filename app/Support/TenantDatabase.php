<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Tenant;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Throwable;

/**
 * Answers questions about a tenant's database without initialising tenancy.
 *
 * Initialising tenancy for a tenant whose database is missing throws and
 * leaves the connection manager in a purged state (stancl's bootstrappers
 * swap the default connection), which can take unrelated requests down. Every
 * cross-tenant operation checks this first.
 */
final class TenantDatabase
{
    public static function exists(Tenant $tenant): bool
    {
        try {
            /** @var TenantDatabaseManager $manager */
            $manager = app(TenantDatabaseManager::class);

            return (bool) $manager->databaseExists((string) $tenant->getTenantKey());
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Run a callback with tenancy initialised, when the database is present.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @param  TReturn|null  $default
     * @return TReturn|null
     */
    public static function run(Tenant $tenant, callable $callback, mixed $default = null)
    {
        if (! self::exists($tenant)) {
            return $default;
        }

        $alreadyInitialised = tenancy()->tenant !== null
            && (string) tenancy()->tenant->getTenantKey() === (string) $tenant->getTenantKey();

        if ($alreadyInitialised) {
            return $callback();
        }

        try {
            tenancy()->initialize($tenant);

            return $callback();
        } catch (Throwable $exception) {
            report($exception);

            return $default;
        } finally {
            try {
                if (tenancy()->initialized) {
                    tenancy()->end();
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}
