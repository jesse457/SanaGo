<?php

namespace Database\Factories;

use App\Enums\BedStatus;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bed>
 */
class BedFactory extends Factory
{
    protected $model = Bed::class;

    private static int $counter = 0;

    public function definition(): array
    {
        $ward = Ward::factory();
        self::$counter++;

        return [
            'ward_id' => $ward,
            'code' => function (array $attributes) {
                $wardModel = Ward::find($attributes['ward_id']);

                return ($wardModel ? strtoupper(substr($wardModel->code, 0, 4)) : 'BED').'-'.str_pad((string) self::$counter, 3, '0', STR_PAD_LEFT);
            },
            'bed_type_id' => BedType::factory(),
            'status' => fake()->randomElement(BedStatus::cases()),
        ];
    }

    public function available(): static
    {
        return $this->state(fn () => ['status' => BedStatus::Available]);
    }

    public function occupied(): static
    {
        return $this->state(fn () => ['status' => BedStatus::Occupied]);
    }
}
