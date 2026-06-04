<?php

namespace Database\Seeders;

use App\Models\ProcedureKit;
use Illuminate\Database\Seeder;

class ProcedureKitSeeder extends Seeder
{
    public function run(): void
    {
        ProcedureKit::factory()->count(5)->create();
    }
}
