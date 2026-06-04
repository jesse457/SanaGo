<?php

namespace Database\Seeders;

use App\Models\Vital;
use Illuminate\Database\Seeder;

class VitalSeeder extends Seeder
{
    public function run(): void
    {
        Vital::factory()->count(25)->create();
    }
}
