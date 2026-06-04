<?php

namespace Database\Seeders;

use App\Models\BedType;
use Illuminate\Database\Seeder;

class BedTypeSeeder extends Seeder
{
    public function run(): void
    {
        BedType::factory()->count(8)->create();
    }
}
