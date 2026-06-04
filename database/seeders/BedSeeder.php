<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\BedType;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    public function run(): void
    {
        $wards = Ward::all();
        $bedTypes = BedType::all();

        if ($wards->isEmpty() || $bedTypes->isEmpty()) {
            return;
        }

        $counter = 1;
        foreach ($wards as $ward) {
            $bedCount = (int) min($ward->capacity, 5);
            for ($i = 0; $i < $bedCount; $i++) {
                Bed::create([
                    'ward_id' => $ward->id,
                    'code' => strtoupper(substr($ward->code, 0, 3)).'-'.str_pad((string) $counter, 3, '0', STR_PAD_LEFT),
                    'bed_type_id' => $bedTypes->random()->id,
                    'status' => fake()->randomElement(['available', 'available', 'available', 'occupied']),
                ]);
                $counter++;
            }
        }
    }
}
