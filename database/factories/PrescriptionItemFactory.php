<?php

namespace Database\Factories;

use App\Models\Medication;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrescriptionItem>
 */
class PrescriptionItemFactory extends Factory
{
    protected $model = PrescriptionItem::class;

    private const DOSAGES = ['250mg', '500mg', '1g', '5mg', '10mg', '20mg', '100mcg', '2 puffs'];

    private const FREQUENCIES = ['OD', 'BD', 'TDS', 'QID', 'Q4H', 'Q6H', 'PRN', 'Stat'];

    private const DURATIONS = ['3 days', '5 days', '7 days', '10 days', '14 days', '1 month', '2 weeks'];

    public function definition(): array
    {
        $quantity = fake()->numberBetween(6, 60);
        $unitPrice = fake()->randomFloat(2, 0.50, 25.00);

        return [
            'prescription_id' => Prescription::factory(),
            'medication_id' => Medication::factory(),
            'drug_name' => null, // Will fall back to medication name in the model
            'dosage' => fake()->randomElement(self::DOSAGES),
            'frequency' => fake()->randomElement(self::FREQUENCIES),
            'duration' => fake()->randomElement(self::DURATIONS),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'instructions' => fake()->optional(0.6)->randomElement([
                'Take after meals',
                'Take on empty stomach',
                'Take with plenty of water',
                'Complete the full course',
                'Do not crush or chew',
                'Shake well before use',
            ]),
        ];
    }
}
