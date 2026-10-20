<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Platform schedule
|--------------------------------------------------------------------------
|
| Subscription lifecycle runs on the scheduler so expiry, renewal and
| warnings happen without an operator. `schedule:run` (or `schedule:work`)
| must be part of the deployment's cron entry:
|
|   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
|
*/

Schedule::command('subscriptions:renew')->dailyAt('01:10')->withoutOverlapping();
Schedule::command('subscriptions:expire')->dailyAt('01:30')->withoutOverlapping();
Schedule::command('subscriptions:notify-expiring')->dailyAt('08:00')->withoutOverlapping();
Schedule::command('platform:health')->everySixHours()->withoutOverlapping();
