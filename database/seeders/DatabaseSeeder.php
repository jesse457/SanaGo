<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the Landlord Super Admin
        $password = config('app.passwords.admin', 'password');
        User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@healthnet.test',
            'role' => 'landlord',
            'password' => bcrypt($password),
            'is_active' => true,
        ]);

        // 2. Call the Tenant Seeder
        $this->call([
            TenantSeeder::class,
        ]);
    }
}
