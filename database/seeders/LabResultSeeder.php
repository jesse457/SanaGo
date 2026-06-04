<?php

namespace Database\Seeders;

use App\Models\LabRequest;
use App\Models\LabResult;
use Illuminate\Database\Seeder;

class LabResultSeeder extends Seeder
{
    public function run(): void
    {
        $requests = LabRequest::where('status', 'completed')->get();

        if ($requests->isEmpty()) {
            // Use all requests if none are completed
            $requests = LabRequest::all();
        }

        if ($requests->isEmpty()) {
            return;
        }

        $sampleResults = [
            "Hemoglobin: 12.5 g/dL\nWBC: 7.2 x10³/µL\nPlatelets: 245 x10³/µL\nAll parameters within normal limits.",
            "Test Result: POSITIVE\nParasite Count: ++ (moderate)\nRecommended: Antimalarial therapy",
            "Fasting Blood Glucose: 180 mg/dL (HIGH)\nReference Range: 70-110 mg/dL",
            "pH: 6.0\nSpecific Gravity: 1.020\nProtein: Negative\nGlucose: Negative",
        ];

        foreach ($requests->take(10) as $request) {
            LabResult::create([
                'lab_request_id' => $request->id,
                'status' => fake()->randomElement(['normal', 'abnormal', 'critical']),
                'result_date' => now()->subDays(rand(1, 7))->format('Y-m-d'),
                'results_text' => $sampleResults[array_rand($sampleResults)],
                'analysis_comments' => rand(0, 1) ? 'Results reviewed and verified.' : null,
            ]);
        }
    }
}
