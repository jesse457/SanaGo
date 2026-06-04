<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    private const NIGERIAN_FIRST_NAMES = [
        'Chidi', 'Amina', 'Emeka', 'Funke', 'Tunde', 'Ngozi', 'Obinna', 'Fatima',
        'Chinwe', 'Ibrahim', 'Blessing', 'Oluwaseun', 'Chiamaka', 'Yusuf', 'Adaeze',
        'Musa', 'Ifeanyi', 'Zainab', 'Nnamdi', 'Hauwa', 'Kelechi', 'Aisha', 'Obi',
        'Maryam', 'Uche', 'Bola', 'Efe', 'Sade', 'Gbenga', 'Nkechi',
    ];

    private const NIGERIAN_LAST_NAMES = [
        'Okonkwo', 'Bello', 'Okafor', 'Adebayo', 'Balogun', 'Eze', 'Musa', 'Abubakar',
        'Nwachukwu', 'Oluwole', 'Adamu', 'Obi', 'Yakubu', 'Chukwu', 'Suleiman',
        'Ogunleye', 'Emenike', 'Danladi', 'Olanrewaju', 'Mohammed', 'Ibekwe',
        'Babangida', 'Onyeka', 'Lawal', 'Akpan', 'Usman', 'Duru', 'Idris', 'Nwosu', 'Sani',
    ];

    private const NIGERIAN_CITIES = [
        '12 Broad Street, Lagos', '45 Ahmadu Bello Way, Abuja', '3 Hospital Road, Enugu',
        '78 Aba Road, Port Harcourt', '15 Murtala Mohammed Way, Kano', '22 Ring Road, Ibadan',
        '56 Benin-Sapele Road, Benin City', '9 Wurukum Road, Makurdi', '34 Ibrahim Taiwo Road, Ilorin',
        '67 Bauchi Road, Jos', '11 Olu Obasanjo Road, Warri', '88 Upper New Market Road, Onitsha',
    ];

    private const BLOOD_TYPES = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];

    private const ALLERGIES_OPTIONS = [
        null, null, null, null, null,
        'Penicillin', 'Sulfa Drugs', 'Aspirin', 'Latex',
        'Iodine', 'Codeine', null, null, 'Peanuts',
    ];

    public function definition(): array
    {
        $firstName = fake()->randomElement(self::NIGERIAN_FIRST_NAMES);
        $lastName = fake()->randomElement(self::NIGERIAN_LAST_NAMES);

        return [
            'patient_uid' => 'PT-' . date('Y') . '-' . strtoupper(fake()->unique()->bothify('????###')),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower($firstName . '.' . $lastName . fake()->numerify('##') . '@email.com'),
            'phone' => '+234' . fake()->numerify('##########'),
            'date_of_birth' => fake()->dateTimeBetween('-85 years', '-1 year')->format('Y-m-d'),
            'gender' => fake()->randomElement(Gender::cases()),
            'address' => fake()->randomElement(self::NIGERIAN_CITIES),
            'last_visit_date' => fake()->optional(0.6)->dateTimeBetween('-6 months', 'now'),
            'allergies' => fake()->randomElement(self::ALLERGIES_OPTIONS),
            'blood_type' => fake()->randomElement(self::BLOOD_TYPES),
        ];
    }

    public function male(): static
    {
        return $this->state(fn () => ['gender' => Gender::MALE]);
    }

    public function female(): static
    {
        return $this->state(fn () => ['gender' => Gender::FEMALE]);
    }

    public function withAllergies(): static
    {
        return $this->state(fn () => [
            'allergies' => fake()->randomElement(['Penicillin', 'Sulfa Drugs', 'Aspirin', 'Latex']),
        ]);
    }

    public function pediatric(): static
    {
        return $this->state(fn () => [
            'date_of_birth' => fake()->dateTimeBetween('-16 years', '-1 year')->format('Y-m-d'),
        ]);
    }
}
