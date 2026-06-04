<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;
use Stancl\Tenancy\Jobs\SeedDatabase;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            ['id' => 'city-hospital', 'name' => 'City General Hospital', 'domain' => 'city-hospital'],
            ['id' => 'green-valley', 'name' => 'Green Valley Clinic', 'domain' => 'green-valley'],
        ];

        foreach ($tenants as $data) {
            $exists = \Illuminate\Support\Facades\DB::table('tenants')
                ->where('id', $data['id'])
                ->exists();

            if (! $exists) {
                // Use DB facade to bypass Eloquent event system in CLI
                \Illuminate\Support\Facades\DB::table('tenants')->insert([
                    'id' => $data['id'],
                    'data' => json_encode(['name' => $data['name']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $tenant = Tenant::query()->where('id', $data['id'])->first();
                $tenant->domains()->create(['domain' => $data['domain']]);

                dispatch_sync(new CreateDatabase($tenant));
                dispatch_sync(new MigrateDatabase($tenant));
                dispatch_sync(new SeedDatabase($tenant));
            }
        }
    }
}
