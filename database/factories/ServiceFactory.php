<?php

namespace Database\Factories;

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    private const SERVICES = [
        // Consultations
        ['name' => 'Consultation Générale', 'code' => 'CONS-GEN', 'category' => ServiceCategory::Consultation, 'default_price' => 3000, 'requires_upfront_payment' => true],
        ['name' => 'Consultation Spécialisée', 'code' => 'CONS-SPE', 'category' => ServiceCategory::Consultation, 'default_price' => 5000, 'requires_upfront_payment' => true],
        ['name' => 'Consultation Pédiatrique', 'code' => 'CONS-PED', 'category' => ServiceCategory::Consultation, 'default_price' => 3500, 'requires_upfront_payment' => true],
        ['name' => 'Consultation Prénatale', 'code' => 'CONS-CPN', 'category' => ServiceCategory::Consultation, 'default_price' => 2000, 'requires_upfront_payment' => false],

        // Procedures
        ['name' => 'Petite Chirurgie', 'code' => 'PROC-MIN', 'category' => ServiceCategory::Procedure, 'default_price' => 15000, 'requires_upfront_payment' => false],
        ['name' => 'Accouchement Normal', 'code' => 'PROC-ACC', 'category' => ServiceCategory::Procedure, 'default_price' => 25000, 'requires_upfront_payment' => false],
        ['name' => 'Césarienne', 'code' => 'PROC-CES', 'category' => ServiceCategory::Procedure, 'default_price' => 80000, 'requires_upfront_payment' => false],
        ['name' => 'Circoncision', 'code' => 'PROC-CIR', 'category' => ServiceCategory::Procedure, 'default_price' => 10000, 'requires_upfront_payment' => false],
        ['name' => 'Suture de Plaie', 'code' => 'PROC-SUT', 'category' => ServiceCategory::Procedure, 'default_price' => 5000, 'requires_upfront_payment' => false],

        // Lab
        ['name' => 'Hémogramme (NFS)', 'code' => 'LAB-NFS', 'category' => ServiceCategory::Lab, 'default_price' => 5000, 'requires_upfront_payment' => true],
        ['name' => 'Glycémie à Jeun', 'code' => 'LAB-GLY', 'category' => ServiceCategory::Lab, 'default_price' => 2000, 'requires_upfront_payment' => true],
        ['name' => 'Test de Grossesse', 'code' => 'LAB-GRO', 'category' => ServiceCategory::Lab, 'default_price' => 1500, 'requires_upfront_payment' => true],
        ['name' => 'Sérologie VIH', 'code' => 'LAB-VIH', 'category' => ServiceCategory::Lab, 'default_price' => 3000, 'requires_upfront_payment' => false],
        ['name' => 'Goutte Épaisse (Paludisme)', 'code' => 'LAB-PAL', 'category' => ServiceCategory::Lab, 'default_price' => 2500, 'requires_upfront_payment' => true],
        ['name' => 'ECBU (Urines)', 'code' => 'LAB-ECB', 'category' => ServiceCategory::Lab, 'default_price' => 3500, 'requires_upfront_payment' => true],
        ['name' => 'Crachats BAAR (Tuberculose)', 'code' => 'LAB-BAA', 'category' => ServiceCategory::Lab, 'default_price' => 3000, 'requires_upfront_payment' => false],
        ['name' => 'Widal (Typhoïde)', 'code' => 'LAB-WID', 'category' => ServiceCategory::Lab, 'default_price' => 4000, 'requires_upfront_payment' => true],
        ['name' => 'Groupage Sanguin', 'code' => 'LAB-GRP', 'category' => ServiceCategory::Lab, 'default_price' => 2000, 'requires_upfront_payment' => true],

        // Radiology
        ['name' => 'Radiographie Thorax', 'code' => 'RAD-THO', 'category' => ServiceCategory::Radiology, 'default_price' => 8000, 'requires_upfront_payment' => true],
        ['name' => 'Radiographie Membre', 'code' => 'RAD-MEM', 'category' => ServiceCategory::Radiology, 'default_price' => 6000, 'requires_upfront_payment' => true],
        ['name' => 'Radiographie Abdomen', 'code' => 'RAD-ABD', 'category' => ServiceCategory::Radiology, 'default_price' => 8000, 'requires_upfront_payment' => true],

        // Imaging (Ultrasound)
        ['name' => 'Échographie Abdominale', 'code' => 'IMG-ABD', 'category' => ServiceCategory::Imaging, 'default_price' => 10000, 'requires_upfront_payment' => true],
        ['name' => 'Échographie Obstétricale', 'code' => 'IMG-OBS', 'category' => ServiceCategory::Imaging, 'default_price' => 8000, 'requires_upfront_payment' => true],
        ['name' => 'Échographie Pelvienne', 'code' => 'IMG-PEL', 'category' => ServiceCategory::Imaging, 'default_price' => 10000, 'requires_upfront_payment' => true],

        // Bed
        ['name' => 'Lit/jour — Salle Commune', 'code' => 'BED-COM', 'category' => ServiceCategory::Bed, 'default_price' => 2000, 'requires_upfront_payment' => false],
        ['name' => 'Lit/jour — Chambre Privée', 'code' => 'BED-PRV', 'category' => ServiceCategory::Bed, 'default_price' => 5000, 'requires_upfront_payment' => false],
        ['name' => 'Lit/jour — VIP', 'code' => 'BED-VIP', 'category' => ServiceCategory::Bed, 'default_price' => 10000, 'requires_upfront_payment' => false],

        // Nursing
        ['name' => 'Soins Infirmiers (Pansement)', 'code' => 'NUR-PAN', 'category' => ServiceCategory::Nursing, 'default_price' => 2000, 'requires_upfront_payment' => false],
        ['name' => 'Injection', 'code' => 'NUR-INJ', 'category' => ServiceCategory::Nursing, 'default_price' => 1000, 'requires_upfront_payment' => false],
        ['name' => 'Perfusion IV', 'code' => 'NUR-PER', 'category' => ServiceCategory::Nursing, 'default_price' => 3000, 'requires_upfront_payment' => false],

        // Emergency
        ['name' => 'Consultation Urgence', 'code' => 'EMG-CON', 'category' => ServiceCategory::Emergency, 'default_price' => 5000, 'requires_upfront_payment' => false],
        ['name' => 'Triage', 'code' => 'EMG-TRI', 'category' => ServiceCategory::Emergency, 'default_price' => 0, 'requires_upfront_payment' => false],

        // Medication
        ['name' => 'Médicament (Dispensation)', 'code' => 'MED-DIS', 'category' => ServiceCategory::Medication, 'default_price' => 0, 'requires_upfront_payment' => true],
    ];

    private int $index = 0;

    public function definition(): array
    {
        $service = self::SERVICES[$this->index % count(self::SERVICES)];
        $this->index++;

        return [
            'name' => $service['name'],
            'code' => $service['code'].'-'.fake()->unique()->numerify('###'),
            'category' => $service['category'],
            'description' => fake()->optional(0.7)->sentence(),
            'source_model' => null,
            'source_id' => null,
            'default_price' => $service['default_price'],
            'is_active' => true,
            'requires_upfront_payment' => $service['requires_upfront_payment'],
        ];
    }

    public function consultation(): static
    {
        return $this->state(fn () => ['category' => ServiceCategory::Consultation]);
    }

    public function lab(): static
    {
        return $this->state(fn () => ['category' => ServiceCategory::Lab]);
    }

    public function radiology(): static
    {
        return $this->state(fn () => ['category' => ServiceCategory::Radiology]);
    }

    public function procedure(): static
    {
        return $this->state(fn () => ['category' => ServiceCategory::Procedure]);
    }

    public function bed(): static
    {
        return $this->state(fn () => ['category' => ServiceCategory::Bed]);
    }

    public function upfront(): static
    {
        return $this->state(fn () => ['requires_upfront_payment' => true]);
    }

    public function postPay(): static
    {
        return $this->state(fn () => ['requires_upfront_payment' => false]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
