<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Platform\TenantService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class PlatformHealthCheck extends Command
{
    protected $signature = 'platform:health {--json : Output machine readable JSON}';

    protected $description = 'Check every tenant database, subscription state and the platform transports';

    public function handle(TenantService $tenants): int
    {
        $rows = [];
        $failures = 0;

        Tenant::query()->with('subscription')->orderBy('id')->each(function (Tenant $tenant) use ($tenants, &$rows, &$failures) {
            $health = $tenants->health($tenant);
            $subscription = $tenant->subscription;

            $problem = null;

            if (! $health['reachable']) {
                $problem = 'database unreachable';
            } elseif (! $health['migrations_ok']) {
                $problem = 'tenant migrations missing';
            } elseif ($subscription !== null && ! $subscription->grantsAccess()) {
                $problem = 'subscription '.$subscription->effectiveStatus()->value;
            }

            if ($problem !== null) {
                $failures++;
            }

            $rows[] = [
                'tenant' => $tenant->getTenantKey(),
                'name' => $tenant->displayName(),
                'active' => $tenant->isActive(),
                'reachable' => $health['reachable'],
                'migrations_ok' => $health['migrations_ok'],
                'users' => $health['users'],
                'subscription' => $subscription?->effectiveStatus()->value,
                'end_date' => $subscription?->end_date?->toDateString(),
                'problem' => $problem,
            ];
        });

        try {
            DB::connection('central')->getPdo();
            $centralOk = true;
        } catch (Throwable) {
            $centralOk = false;
            $failures++;
        }

        $summary = [
            'central_database' => $centralOk,
            'tenants' => count($rows),
            'failures' => $failures,
            'queue' => (string) config('queue.default'),
            'broadcast' => (string) config('broadcasting.default'),
            'mail' => (string) config('mail.default'),
        ];

        if ($this->option('json')) {
            $this->line((string) json_encode(['summary' => $summary, 'tenants' => $rows], JSON_PRETTY_PRINT));

            return $failures === 0 ? self::SUCCESS : self::FAILURE;
        }

        $this->table(
            ['Tenant', 'Active', 'DB', 'Migrations', 'Users', 'Subscription', 'Ends', 'Problem'],
            array_map(fn (array $row) => [
                $row['tenant'],
                $row['active'] ? 'yes' : 'no',
                $row['reachable'] ? 'ok' : 'down',
                $row['migrations_ok'] ? 'ok' : 'missing',
                $row['users'],
                $row['subscription'] ?? '—',
                $row['end_date'] ?? '—',
                $row['problem'] ?? '',
            ], $rows),
        );

        $this->newLine();
        $this->info(sprintf(
            '%d tenant(s), %d problem(s). central=%s queue=%s broadcast=%s mail=%s',
            $summary['tenants'],
            $failures,
            $centralOk ? 'ok' : 'down',
            $summary['queue'],
            $summary['broadcast'],
            $summary['mail'],
        ));

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }
}
