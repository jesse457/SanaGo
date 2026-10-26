<?php

namespace Database\Factories;

use App\Enums\ShiftType;
use App\Models\UserShift;
use App\Models\UserShiftStaff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserShift>
 */
class UserShiftFactory extends Factory
{
    protected $model = UserShift::class;

    public function definition(): array
    {
        $shiftType = fake()->randomElement(ShiftType::cases());
        $startHour = match ($shiftType) {
            ShiftType::MORNING => 7,
            ShiftType::AFTERNOON => 14,
            ShiftType::NIGHT => 21,
            default => 8,
        };

        return [
            'shift_date' => fake()->dateTimeBetween('-2 weeks', '+2 weeks')->format('Y-m-d'),
            'shift_type' => $shiftType,
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', ($startHour + 8) % 24),
        ];
    }

    /**
     * Attach staff to the shift: a mix of system users and, optionally,
     * people recorded by name only (not yet in the system).
     *
     * @param  list<int>  $userIds
     * @param  list<string>  $externalNames
     */
    public function withStaff(array $userIds = [], array $externalNames = []): static
    {
        return $this->afterCreating(function (UserShift $shift) use ($userIds, $externalNames) {
            if ($userIds !== []) {
                $picks = collect($userIds)->random(min(fake()->numberBetween(1, 3), count($userIds)));

                foreach ($picks as $id) {
                    UserShiftStaff::create(['user_shift_id' => $shift->id, 'user_id' => $id]);
                }
            }

            foreach (fake()->randomElements($externalNames, fake()->numberBetween(0, count($externalNames)) ?: 0) as $name) {
                UserShiftStaff::create(['user_shift_id' => $shift->id, 'name' => $name]);
            }
        });
    }

    public function morning(): static
    {
        return $this->state(fn () => [
            'shift_type' => ShiftType::MORNING,
            'start_time' => '07:00:00',
            'end_time' => '15:00:00',
        ]);
    }

    public function night(): static
    {
        return $this->state(fn () => [
            'shift_type' => ShiftType::NIGHT,
            'start_time' => '21:00:00',
            'end_time' => '05:00:00',
        ]);
    }
}
