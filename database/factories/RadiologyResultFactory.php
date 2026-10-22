<?php

namespace Database\Factories;

use App\Enums\RadiologyStatus;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RadiologyResult>
 */
class RadiologyResultFactory extends Factory
{
    protected $model = RadiologyResult::class;

    public function definition(): array
    {
        return [
            'request_id' => RadiologyRequest::factory(),
            'performed_by' => User::factory(),
            'findings' => fake()->randomElement([
                'Bilateral lung fields are clear. No focal consolidation, pleural effusion or pneumothorax. Cardiomediastinal silhouette is normal.',
                'No free gas under the diaphragm. Bowel gas pattern is non-obstructive. No calcific gallstones identified.',
                'Alignment of the distal tibia and fibula is anatomical. No cortical step or visible fracture line.',
                'Unenhanced study shows no acute intracranial haemorrhage, mass effect or midline shift.',
                'Mild degenerative changes of the lumbar spine with preserved disc heights.',
            ]),
            'conclusion' => fake()->optional(0.85)->randomElement([
                'Normal study.',
                'No acute osseous abnormality.',
                'Findings consistent with early osteoarthritis; clinical correlation advised.',
                'No acute cardiopulmonary process.',
            ]),
            'validated_by' => null,
            'validated_at' => null,
            'status' => RadiologyStatus::InProgress,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => ['status' => RadiologyStatus::Completed]);
    }

    public function validated(): static
    {
        return $this->state(fn () => [
            'status' => RadiologyStatus::Validated,
            'validated_by' => User::factory(),
            'validated_at' => now(),
        ]);
    }
}
