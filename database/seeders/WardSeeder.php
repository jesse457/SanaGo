<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        if ($departments->isEmpty()) {
            return;
        }

        $wardNames = [
            ['General Ward', 'GW', 'Ground', 25],
            ['ICU', 'ICU', '1st', 8],
            ['Maternity Ward', 'MAT', '2nd', 15],
            ['Pediatric Ward', 'PED', '1st', 12],
            ['Surgical Ward', 'SURG', '3rd', 20],
        ];

        foreach ($wardNames as $i => [$name, $code, $floor, $capacity]) {
            Ward::create([
                'name' => $name,
                'code' => $code.'-'.fake()->unique()->numerify('###'),
                'description' => "{$name} — providing quality inpatient care.",
                'department_id' => $departments->random()->id,
                'floor' => $floor,
                'capacity' => $capacity,
                'is_active' => true,
            ]);
        }
    }
}
