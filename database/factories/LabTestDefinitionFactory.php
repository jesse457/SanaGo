<?php

namespace Database\Factories;

use App\Models\LabTestDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LabTestDefinition>
 */
class LabTestDefinitionFactory extends Factory
{
    protected $model = LabTestDefinition::class;

    private const TESTS = [
        'Complete Blood Count' => ['CBC', 25.00, 'cells/mcL'],
        'Malaria Rapid Test' => ['MRDT', 10.00, 'Qualitative'],
        'Blood Glucose (Fasting)' => ['BGF', 8.00, 'mmol/L'],
        'Urinalysis' => ['URIN', 12.00, 'Qualitative'],
        'Liver Function Test' => ['LFT', 35.00, 'IU/L'],
        'Kidney Function Test' => ['KFT', 35.00, 'mg/dL'],
        'HIV Rapid Test' => ['HIV', 15.00, 'Qualitative'],
        'Chest X-Ray' => ['CXR', 40.00, 'N/A'],
        'Lipid Profile' => ['LP', 30.00, 'mg/dL'],
        'Thyroid Function Test' => ['TFT', 45.00, 'mIU/L'],
        'Hepatitis B Surface Antigen' => ['HBSAG', 20.00, 'Qualitative'],
        'Stool Microscopy' => ['STMC', 15.00, 'Qualitative'],
        'Widal Test' => ['WIDAL', 18.00, 'Titer'],
        'Blood Culture' => ['BCLT', 50.00, 'Qualitative'],
        'Electrocardiogram' => ['ECG', 35.00, 'N/A'],
    ];

    public function definition(): array
    {
        $test = fake()->randomElement(array_keys(self::TESTS));
        [$code, $price, $units] = self::TESTS[$test];

        return [
            'test_name' => $test,
            'code' => $code . '-' . fake()->unique()->lexify('???'),
            'price' => $price,
            'units' => $units,
            'description' => "Laboratory test: {$test}. Performed in-house with results available within 24 hours.",
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
