<?php

namespace Database\Seeders;

use App\Models\UserShift;
use Illuminate\Database\Seeder;

class UserShiftSeeder extends Seeder
{
    public function run(): void
    {
        UserShift::factory()->count(15)->create();
    }
}
