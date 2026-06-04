<?php

namespace Database\Factories;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medication>
 */
class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    private const DRUGS = [
        ['Paracetamol', '500mg', 'Tablet'],
        ['Ibuprofen', '400mg', 'Tablet'],
        ['Amoxicillin', '500mg', 'Capsule'],
        ['Ciprofloxacin', '500mg', 'Tablet'],
        ['Metformin', '500mg', 'Tablet'],
        ['Omeprazole', '20mg', 'Capsule'],
        ['Salbutamol', '100mcg', 'Inhaler'],
        ['Normal Saline', '0.9%', 'IV Bag'],
        ['Artemether/Lumefantrine', '20/120mg', 'Tablet'],
        ['Ceftriaxone', '1g', 'Injection'],
        ['Diclofenac', '50mg', 'Tablet'],
        ['Lisinopril', '10mg', 'Tablet'],
        ['Amlodipine', '5mg', 'Tablet'],
        ['Metronidazole', '400mg', 'Tablet'],
        ['Dextrose', '5%', 'IV Bag'],
    ];

    public function definition(): array
    {
        $drug = fake()->randomElement(self::DRUGS);
        $unitCost = fake()->randomFloat(2, 0.10, 15.00);

        return [
            'name' => $drug[0],
            'strength' => $drug[1],
            'dosage_units' => $drug[2],
            'min_stock_level' => fake()->numberBetween(10, 50),
            'total_stock' => fake()->numberBetween(100, 2000),
            'description' => "{$drug[0]} {$drug[1]} — {$drug[2]}. Stored at room temperature.",
            'unit_cost' => $unitCost,
            'is_active' => true,
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn () => [
            'total_stock' => fake()->numberBetween(0, 10),
            'min_stock_level' => 50,
        ]);
    }
}
