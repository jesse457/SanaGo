<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Resolves the 'central' database connection to the default connection.
 *
 * Models like DemoRequest, Subscription, and Tenant use the 'central' connection.
 * With SQLite :memory: in tests, separate connections create isolated databases.
 * This resolver forces 'central' to share the default connection's PDO instance,
 * keeping all writes within RefreshDatabase's transaction boundary.
 */
function resolveCentralToDefault(): void
{
    $db = app('db');

    Model::setConnectionResolver(
        new class($db) implements ConnectionResolverInterface
        {
            private $db;

            public function __construct($db)
            {
                $this->db = $db;
            }

            public function connection($name = null)
            {
                if ($name === 'central') {
                    return $this->db->connection();
                }

                return $this->db->connection($name);
            }

            public function getDefaultConnection(): string
            {
                return $this->db->getDefaultConnection();
            }

            public function setDefaultConnection($name): void
            {
                $this->db->setDefaultConnection($name);
            }
        }
    );
}
