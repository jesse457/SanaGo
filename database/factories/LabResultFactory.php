<?php

namespace Database\Factories;

use App\Enums\LabResultStatus;
use App\Models\LabRequest;
use App\Models\LabResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabResult>
 */
class LabResultFactory extends Factory
{
    protected $model = LabResult::class;

    public function definition(): array
    {
        return [
            'lab_request_id' => LabRequest::factory(),
            'status' => fake()->randomElement(LabResultStatus::cases()),
            'result_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'results_text' => fake()->randomElement([
                "Hemoglobin: 12.5 g/dL\nWBC: 7.2 x10³/µL\nPlatelets: 245 x10³/µL\nRBC: 4.8 x10⁶/µL\nMCV: 88 fL\nAll parameters within normal limits.",
                "Test Result: POSITIVE\nParasite Count: ++ (moderate)\nRecommended: Antimalarial therapy",
                "Fasting Blood Glucose: 180 mg/dL (HIGH)\nReference Range: 70-110 mg/dL\nInterpretation: Hyperglycemia — consistent with Diabetes Mellitus",
                "pH: 6.0\nSpecific Gravity: 1.020\nProtein: Negative\nGlucose: Negative\nKetones: Negative\nWBC: 2-3/HPF\nRBC: 0-1/HPF\nInterpretation: Within normal limits",
            ]),
            'analysis_comments' => fake()->optional(0.5)->randomElement([
                'Results reviewed and verified by laboratory specialist.',
                'Recommend correlation with clinical findings.',
                'Repeat test suggested in 2 weeks for monitoring.',
                'No critical values. Results communicated to requesting physician.',
            ]),
        ];
    }

    public function normal(): static
    {
        return $this->state(fn () => ['status' => LabResultStatus::NORMAL]);
    }

    public function abnormal(): static
    {
        return $this->state(fn () => ['status' => LabResultStatus::ABNORMAL]);
    }

    public function critical(): static
    {
        return $this->state(fn () => ['status' => LabResultStatus::CRITICAL]);
    }
}
