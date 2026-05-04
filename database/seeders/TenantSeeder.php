<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'General Hospital',
                'email' => 'admin@general.test',
                'sub' => Subscription::PLAN_BASIC,
                'domain' => 'general'
            ],
            [
                'name' => 'City Clinic',
                'email' => 'admin@cityclinic.test',
                'sub' => Subscription::PLAN_ENTERPRISE,
                'domain' => 'city'
            ],
        ];

        foreach ($tenants as $data) {
            $domainName = $data['domain'] . '.' . config('tenancy.central_domains.1');

            // 1. Create the Tenant (Central DB)
            $tenant = Tenant::create([
                'name' => $data['name'],
                'contact_email' => 'contact@' . $domainName,
                'phone_number' => '123456789',
                'address' => '123 Medical Drive',
                'subscription_tier' => $data['sub'],
            ]);

            // 2. Create the Domain (Central DB)
            $tenant->domains()->create([
                'domain' => $domainName,
            ]);

            // 3. Run Logic Inside Tenant Database
            $tenant->run(function () use ($data, $tenant) {
                // Create Admin User for this tenant
                User::create([
                    'name' => $data['name'] . ' Admin',
                    'email' => $data['email'],
                    'password' => Hash::make('password'), // Default password for local dev
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]);

                // Create Subscription record
                $sub = new Subscription();
                $sub->plan = $data['sub'];
                $sub->billing_cycle = Subscription::BILLING_YEARLY;
                $sub->status = Subscription::STATUS_ACTIVE;
                $sub->starts_at = now();
                $sub->ends_at = now()->addYear();

                $sub->amount = $sub->getPlanAmount();
                $features = $sub->getDefaultFeatures();
                $sub->features = $features;
                $sub->max_users = $features['max_users'] ?? 0;
                $sub->max_storage = $features['max_storage'] ?? 0;

                $sub->tenant_id = $tenant->id;
                $sub->save();
            });

            $this->command->info("Tenant '{$data['name']}' created at {$domainName}");
        }
    }
}
