<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Platform\SubscriptionService;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark subscriptions whose paid period and grace window have ended as expired/cancelled';

    public function handle(SubscriptionService $subscriptions): int
    {
        $count = $subscriptions->expireDue();

        $this->info("Expired {$count} subscription(s).");

        return self::SUCCESS;
    }
}
