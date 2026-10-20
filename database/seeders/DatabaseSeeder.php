<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminRoleSeeder::class,
            SuperAdminUserSeeder::class,
            PlatformSettingSeeder::class,
            TenantSeeder::class,
            SubscriptionSeeder::class,
        ]);
    }
}
