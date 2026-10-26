<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserShift;
use Illuminate\Database\Seeder;

class UserShiftSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::query()->pluck('id')->all();

        UserShift::factory()
            ->times(15)
            ->withStaff($userIds, ['Paul Mbarga', 'Estelle Ngo', 'Bouba Fedoul', 'Rose Ekane'])
            ->create();
    }
}
