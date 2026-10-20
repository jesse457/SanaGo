<?php

namespace Tests;

use App\Support\PlatformContext;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    /**
     * Point the `central` connection at the default connection before any
     * test runs.
     *
     * Landlord models (Tenant, Plan, Subscription, SubscriptionInvoice,
     * PlatformSetting, PlatformAuditLog, Announcement, DemoRequest,
     * SupportTicket…) declare `$connection = 'central'`. In tests both
     * connections would be separate `:memory:` databases, so RefreshDatabase's
     * transaction would not cover writes made through `central` and every
     * central query would hit an unmigrated database.
     *
     * The resolver below is lazy — it resolves the database manager on first
     * use, after the container is booted — and maps `central` onto the default
     * connection. `Tests\Helpers\resolveCentralToDefault()` remains available
     * for tests that want to be explicit.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Static caches survive between tests in the same PHP process.
        PlatformContext::flush();

        /*
         * Raw queries (`DB::connection('central')`, `Schema::connection('central')`)
         * bypass the Eloquent connection resolver used above. Registering the
         * name as an extension that returns the default connection keeps the
         * central reads/writes inside the same in-memory database (and inside
         * RefreshDatabase's transaction).
         */
        DB::extend('central', fn () => DB::connection());

        Model::setConnectionResolver(new class implements ConnectionResolverInterface
        {
            public function connection($name = null)
            {
                $manager = app('db');

                if ($name === 'central') {
                    return $manager->connection();
                }

                return $manager->connection($name);
            }

            public function getDefaultConnection(): string
            {
                return app('db')->getDefaultConnection();
            }

            public function setDefaultConnection($name): void
            {
                app('db')->setDefaultConnection($name);
            }
        });
    }
}
