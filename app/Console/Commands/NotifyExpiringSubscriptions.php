<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Platform\SubscriptionService;
use Illuminate\Console\Command;

class NotifyExpiringSubscriptions extends Command
{
    protected $signature = 'subscriptions:notify-expiring {--days= : Comma separated day marks, e.g. 14,7,1}';

    protected $description = 'Warn tenant admins whose subscription is about to end';

    public function handle(SubscriptionService $subscriptions): int
    {
        $days = $this->option('days');

        $marks = $days !== null
            ? array_map('intval', array_filter(explode(',', (string) $days), 'is_numeric'))
            : null;

        $count = $subscriptions->notifyExpiring($marks);

        $this->info("Sent {$count} expiry warning(s).");

        return self::SUCCESS;
    }
}
