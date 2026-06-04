<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@sanago.local',
                'name' => 'Super Admin',
                'role' => 'super-admin',
            ],
            [
                'email' => 'tenant@sanago.local',
                'name' => 'Tenant Manager',
                'role' => 'tenant-manager',
            ],
            [
                'email' => 'billing@sanago.local',
                'name' => 'Billing Manager',
                'role' => 'billing-manager',
            ],
            [
                'email' => 'support@sanago.local',
                'name' => 'Support Agent',
                'role' => 'support-agent',
            ],
            [
                'email' => 'sales@sanago.local',
                'name' => 'Sales Representative',
                'role' => 'sales-rep',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (! $user->hasRole($userData['role'])) {
                $user->syncRoles([$userData['role']]);
            }
        }
    }
}
