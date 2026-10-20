<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Platform\SubscriptionService;
use Illuminate\Console\Command;

class RenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:renew';

    protected $description = 'Renew auto-renewing subscriptions whose period has ended and issue their invoices';

    public function handle(SubscriptionService $subscriptions): int
    {
        $count = $subscriptions->renewDue();

        $this->info("Renewed {$count} subscription(s).");

        return self::SUCCESS;
    }
}
