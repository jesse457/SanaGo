<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $landlordAdmin = User::factory()->create([
            'email' => 'admin@healthnet.test', // Specific email for landlord admin
            'role' => 'landlord',
            'password' => bcrypt('password'), // Default password
            'is_active' => true,
        ]);
    }
}
