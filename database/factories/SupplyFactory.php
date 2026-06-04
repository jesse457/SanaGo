<?php

namespace Database\Factories;

use App\Models\Supply;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supply>
 */
class SupplyFactory extends Factory
{
    protected $model = Supply::class;

    private const SUPPLIES = [
        ['Surgical Gloves', 'Box', 'Medical Consumables'],
        ['Disposable Syringes (5ml)', 'Box', 'Medical Consumables'],
        ['Cotton Wool', 'Roll', 'Medical Consumables'],
        ['Face Masks (Surgical)', 'Box', 'PPE'],
        ['IV Cannula (G18)', 'Piece', 'Medical Devices'],
        ['Blood Collection Tubes', 'Box', 'Laboratory'],
        ['Gauze Bandages', 'Roll', 'Medical Consumables'],
        ['Hand Sanitizer', 'Liter', 'Hygiene'],
        ['Examination Gloves', 'Box', 'Medical Consumables'],
        ['Surgical Blades', 'Box', 'Surgical Supplies'],
        ['Urine Collection Cups', 'Box', 'Laboratory'],
        ['Disposable Aprons', 'Piece', 'PPE'],
    ];

    public function definition(): array
    {
        $supply = fake()->randomElement(self::SUPPLIES);

        return [
            'name' => $supply[0],
            'unit_of_measure' => $supply[1],
            'current_stock' => fake()->numberBetween(20, 500),
            'min_stock_level' => fake()->numberBetween(10, 30),
            'cost_per_unit' => fake()->randomFloat(2, 0.05, 25.00),
            'category' => $supply[2],
            'last_restocked' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function lowStock(): static
    {
        return $this->state(fn () => [
            'current_stock' => fake()->numberBetween(0, 10),
            'min_stock_level' => 30,
        ]);
    }
}
