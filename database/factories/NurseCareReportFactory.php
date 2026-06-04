<?php

namespace Database\Factories;

use App\Enums\ShiftType;
use App\Models\NurseCareReport;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NurseCareReport>
 */
class NurseCareReportFactory extends Factory
{
    protected $model = NurseCareReport::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'nurse_id' => User::factory(),
            'report_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'shift_type' => fake()->randomElement(ShiftType::cases()),
            'care_details' => fake()->randomElement([
                'Vital signs monitored Q4H. Patient stable. Medications administered as prescribed. No complaints.',
                'Wound dressing changed. Site clean and dry with no signs of infection. Pain level 2/10.',
                'IV fluids running at 80ml/hr. Site checked, no infiltration. Patient tolerating oral fluids.',
                'Patient ambulated with assistance x2. Tolerated activity well. Encouraged deep breathing exercises.',
                'NG tube feeding administered at 50ml/hr. Residual checked q4h, within normal limits. Bowel sounds present.',
                'Pain medication given at 0600. Patient reports pain 4/10 reducing to 1/10 post-medication.',
            ]),
            'observations' => fake()->optional(0.4)->sentence(),
        ];
    }

    public function morningShift(): static
    {
        return $this->state(fn () => ['shift_type' => ShiftType::MORNING]);
    }

    public function nightShift(): static
    {
        return $this->state(fn () => ['shift_type' => ShiftType::NIGHT]);
    }
}
