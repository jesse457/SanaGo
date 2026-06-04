<?php

namespace Database\Seeders;

use App\Models\Medication;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Seeder;

class PrescriptionItemSeeder extends Seeder
{
    public function run(): void
    {
        $prescriptions = Prescription::all();
        $medications = Medication::all();

        if ($prescriptions->isEmpty() || $medications->isEmpty()) {
            return;
        }

        $itemsByPrescription = [];
        foreach ($prescriptions as $rx) {
            $itemsByPrescription[$rx->id] = rand(1, 3);
        }

        $frequencies = ['OD', 'BD', 'TDS', 'QID', 'PRN'];
        $durations = ['3 days', '5 days', '7 days', '10 days', '14 days'];

        foreach ($itemsByPrescription as $rxId => $itemCount) {
            for ($i = 0; $i < $itemCount; $i++) {
                $med = $medications->random();
                $qty = rand(6, 30);
                $price = (float) ($med->unit_cost ?? 2.00);

                PrescriptionItem::create([
                    'prescription_id' => $rxId,
                    'medication_id' => $med->id,
                    'drug_name' => $med->name,
                    'dosage' => $med->strength ?? '500mg',
                    'frequency' => $frequencies[array_rand($frequencies)],
                    'duration' => $durations[array_rand($durations)],
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'instructions' => rand(0, 1) ? fake()->randomElement(['Take after meals', 'Take on empty stomach', 'Complete the full course']) : null,
                ]);
            }
        }
    }
}
