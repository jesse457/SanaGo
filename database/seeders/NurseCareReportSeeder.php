<?php

namespace Database\Seeders;

use App\Models\NurseCareReport;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class NurseCareReportSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $nurses = User::all();

        if ($patients->isEmpty()) {
            return;
        }

        $careDetails = [
            'Vital signs monitored Q4H. Patient stable. Medications administered as prescribed.',
            'Wound dressing changed. Site clean and dry with no signs of infection.',
            'IV fluids running at 80ml/hr. Site checked, no infiltration.',
            'Patient ambulated with assistance x2. Tolerated activity well.',
            'Pain medication given at 0600. Patient reports pain reducing to 1/10.',
        ];

        for ($i = 0; $i < 15; $i++) {
            NurseCareReport::create([
                'patient_id' => $patients->random()->id,
                'nurse_id' => $nurses->isNotEmpty() ? $nurses->random()->id : User::factory(),
                'report_date' => now()->subDays(rand(1, 20)),
                'shift_type' => fake()->randomElement(['morning', 'afternoon', 'night']),
                'care_details' => $careDetails[array_rand($careDetails)],
                'observations' => rand(0, 1) ? fake()->sentence() : null,
            ]);
        }
    }
}
