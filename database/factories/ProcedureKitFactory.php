<?php

namespace Database\Factories;

use App\Models\ProcedureKit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProcedureKit>
 */
class ProcedureKitFactory extends Factory
{
    protected $model = ProcedureKit::class;

    public function definition(): array
    {
        $kits = [
            [
                'name' => 'Minor Surgery Kit',
                'contents' => ['Scalpel', 'Forceps', 'Scissors', 'Gauze', 'Antiseptic wipes', 'Sutures'],
                'category' => 'Surgical',
            ],
            [
                'name' => 'Catheterization Kit',
                'contents' => ['Foley catheter', 'Lubricant gel', 'Syringe', 'Collection bag', 'Antiseptic', 'Gloves'],
                'category' => 'Nursing',
            ],
            [
                'name' => 'Wound Dressing Kit',
                'contents' => ['Sterile gauze', 'Saline solution', 'Adhesive tape', 'Scissors', 'Gloves', 'Cotton swabs'],
                'category' => 'Nursing',
            ],
            [
                'name' => 'Lumbar Puncture Kit',
                'contents' => ['Spinal needle', 'Manometer', 'Collection tubes', 'Antiseptic', 'Drapes'],
                'category' => 'Diagnostic',
            ],
            [
                'name' => 'IV Start Kit',
                'contents' => ['Tourniquet', 'Antiseptic wipes', 'Gauze pads', 'Adhesive tape', 'Cannula'],
                'category' => 'Nursing',
            ],
        ];

        $kit = fake()->randomElement($kits);

        return [
            'name' => $kit['name'],
            'description' => "Pre-assembled {$kit['name']} for clinical use.",
            'current_stock' => fake()->numberBetween(5, 50),
            'min_stock_level' => fake()->numberBetween(2, 10),
            'unit_cost' => fake()->randomFloat(2, 5.00, 100.00),
            'category' => $kit['category'],
            'contents' => $kit['contents'],
        ];
    }
}
