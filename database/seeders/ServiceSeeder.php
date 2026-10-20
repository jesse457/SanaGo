<?php

namespace Database\Seeders;

use App\Enums\ServiceCategory;
use App\Models\BedType;
use App\Models\LabTestDefinition;
use App\Models\Medication;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Consultations (upfront payment)
            ['name' => 'Consultation Générale', 'code' => 'CONS-GEN', 'category' => ServiceCategory::Consultation, 'default_price' => 3000, 'requires_upfront_payment' => true, 'description' => 'Consultation médicale générale'],
            ['name' => 'Consultation Spécialisée', 'code' => 'CONS-SPE', 'category' => ServiceCategory::Consultation, 'default_price' => 5000, 'requires_upfront_payment' => true, 'description' => 'Consultation avec un médecin spécialiste'],
            ['name' => 'Consultation Pédiatrique', 'code' => 'CONS-PED', 'category' => ServiceCategory::Consultation, 'default_price' => 3500, 'requires_upfront_payment' => true, 'description' => 'Consultation pour enfants de 0-15 ans'],
            ['name' => 'Consultation Prénatale (CPN)', 'code' => 'CONS-CPN', 'category' => ServiceCategory::Consultation, 'default_price' => 2000, 'requires_upfront_payment' => false, 'description' => 'Consultation prénatale gratuite dans le cadre du programme de santé maternelle'],

            // Procedures (post-pay)
            ['name' => 'Petite Chirurgie', 'code' => 'PROC-MIN', 'category' => ServiceCategory::Procedure, 'default_price' => 15000, 'requires_upfront_payment' => false, 'description' => 'Intervention chirurgicale mineure'],
            ['name' => 'Accouchement Normal', 'code' => 'PROC-ACC', 'category' => ServiceCategory::Procedure, 'default_price' => 25000, 'requires_upfront_payment' => false, 'description' => 'Accouchement par voie basse'],
            ['name' => 'Césarienne', 'code' => 'PROC-CES', 'category' => ServiceCategory::Procedure, 'default_price' => 80000, 'requires_upfront_payment' => false, 'description' => 'Accouchement par césarienne'],
            ['name' => 'Circoncision', 'code' => 'PROC-CIR', 'category' => ServiceCategory::Procedure, 'default_price' => 10000, 'requires_upfront_payment' => false, 'description' => 'Circoncision masculine'],
            ['name' => 'Suture de Plaie', 'code' => 'PROC-SUT', 'category' => ServiceCategory::Procedure, 'default_price' => 5000, 'requires_upfront_payment' => false, 'description' => 'Suture simple de plaie superficielle'],

            // Lab Tests (upfront payment)
            ['name' => 'Hémogramme (NFS)', 'code' => 'LAB-NFS', 'category' => ServiceCategory::Lab, 'default_price' => 5000, 'requires_upfront_payment' => true, 'description' => 'Numération formule sanguine complète'],
            ['name' => 'Glycémie à Jeun', 'code' => 'LAB-GLY', 'category' => ServiceCategory::Lab, 'default_price' => 2000, 'requires_upfront_payment' => true, 'description' => 'Mesure du taux de glucose dans le sang'],
            ['name' => 'Test de Grossesse', 'code' => 'LAB-GRO', 'category' => ServiceCategory::Lab, 'default_price' => 1500, 'requires_upfront_payment' => true, 'description' => 'Test urinaire de grossesse'],
            ['name' => 'Sérologie VIH', 'code' => 'LAB-VIH', 'category' => ServiceCategory::Lab, 'default_price' => 3000, 'requires_upfront_payment' => false, 'description' => 'Test de dépistage VIH (gratuit dans le cadre du programme national)'],
            ['name' => 'Goutte Épaisse / TDR Paludisme', 'code' => 'LAB-PAL', 'category' => ServiceCategory::Lab, 'default_price' => 2500, 'requires_upfront_payment' => true, 'description' => 'Dépistage du paludisme'],
            ['name' => 'ECBU (Examen Cytobactériologique des Urines)', 'code' => 'LAB-ECB', 'category' => ServiceCategory::Lab, 'default_price' => 3500, 'requires_upfront_payment' => true, 'description' => 'Analyse d\'urine complète'],
            ['name' => 'Crachats BAAR (Tuberculose)', 'code' => 'LAB-BAA', 'category' => ServiceCategory::Lab, 'default_price' => 3000, 'requires_upfront_payment' => false, 'description' => 'Recherche de BAAR pour tuberculose (subventionné)'],
            ['name' => 'Sérologie Widal (Typhoïde)', 'code' => 'LAB-WID', 'category' => ServiceCategory::Lab, 'default_price' => 4000, 'requires_upfront_payment' => true, 'description' => 'Test de dépistage de la fièvre typhoïde'],
            ['name' => 'Groupage Sanguin', 'code' => 'LAB-GRP', 'category' => ServiceCategory::Lab, 'default_price' => 2000, 'requires_upfront_payment' => true, 'description' => 'Détermination du groupe sanguin ABO/Rhésus'],

            // Radiology (upfront payment)
            ['name' => 'Radiographie Thorax (Face)', 'code' => 'RAD-THO', 'category' => ServiceCategory::Radiology, 'default_price' => 8000, 'requires_upfront_payment' => true, 'description' => 'Radiographie pulmonaire de face'],
            ['name' => 'Radiographie Membre', 'code' => 'RAD-MEM', 'category' => ServiceCategory::Radiology, 'default_price' => 6000, 'requires_upfront_payment' => true, 'description' => 'Radiographie d\'un membre (bras, jambe)'],
            ['name' => 'Radiographie Abdomen Sans Préparation (ASP)', 'code' => 'RAD-ABD', 'category' => ServiceCategory::Radiology, 'default_price' => 8000, 'requires_upfront_payment' => true, 'description' => 'Radiographie de l\'abdomen sans préparation'],

            // Imaging / Ultrasound (upfront payment)
            ['name' => 'Échographie Abdominale', 'code' => 'IMG-ABD', 'category' => ServiceCategory::Imaging, 'default_price' => 10000, 'requires_upfront_payment' => true, 'description' => 'Échographie de l\'abdomen'],
            ['name' => 'Échographie Obstétricale', 'code' => 'IMG-OBS', 'category' => ServiceCategory::Imaging, 'default_price' => 8000, 'requires_upfront_payment' => true, 'description' => 'Échographie de suivi de grossesse'],
            ['name' => 'Échographie Pelvienne', 'code' => 'IMG-PEL', 'category' => ServiceCategory::Imaging, 'default_price' => 10000, 'requires_upfront_payment' => true, 'description' => 'Échographie de la région pelvienne'],

            // Bed Charges (post-pay, calculated at discharge)
            ['name' => 'Lit/jour — Salle Commune', 'code' => 'BED-COM', 'category' => ServiceCategory::Bed, 'default_price' => 2000, 'requires_upfront_payment' => false, 'description' => 'Hospitalisation en salle commune'],
            ['name' => 'Lit/jour — Chambre Privée', 'code' => 'BED-PRV', 'category' => ServiceCategory::Bed, 'default_price' => 5000, 'requires_upfront_payment' => false, 'description' => 'Hospitalisation en chambre privée'],
            ['name' => 'Lit/jour — VIP', 'code' => 'BED-VIP', 'category' => ServiceCategory::Bed, 'default_price' => 10000, 'requires_upfront_payment' => false, 'description' => 'Hospitalisation en chambre VIP'],

            // Nursing Care (post-pay)
            ['name' => 'Soins Infirmiers (Pansement)', 'code' => 'NUR-PAN', 'category' => ServiceCategory::Nursing, 'default_price' => 2000, 'requires_upfront_payment' => false, 'description' => 'Pansement simple'],
            ['name' => 'Injection IM/IV', 'code' => 'NUR-INJ', 'category' => ServiceCategory::Nursing, 'default_price' => 1000, 'requires_upfront_payment' => false, 'description' => 'Injection intramusculaire ou intraveineuse'],
            ['name' => 'Perfusion IV', 'code' => 'NUR-PER', 'category' => ServiceCategory::Nursing, 'default_price' => 3000, 'requires_upfront_payment' => false, 'description' => 'Pose et surveillance de perfusion intraveineuse'],

            // Emergency (post-pay)
            ['name' => 'Consultation d\'Urgence', 'code' => 'EMG-CON', 'category' => ServiceCategory::Emergency, 'default_price' => 5000, 'requires_upfront_payment' => false, 'description' => 'Consultation en service d\'urgence'],
            ['name' => 'Triage', 'code' => 'EMG-TRI', 'category' => ServiceCategory::Emergency, 'default_price' => 0, 'requires_upfront_payment' => false, 'description' => 'Évaluation de triage à l\'arrivée (gratuit)'],
        ];

        // Idempotent: re-running the seeder fills gaps and never overwrites a
        // price the hospital has already adjusted.
        foreach ($services as $service) {
            Service::firstOrCreate(['code' => $service['code']], $service);
        }

        // Link existing entities to services
        $this->linkExistingEntities();
    }

    /**
     * Link existing LabTestDefinitions, BedTypes, and Medications to services.
     */
    private function linkExistingEntities(): void
    {
        // Link lab test definitions
        $labTests = LabTestDefinition::whereNull('service_id')->get();
        foreach ($labTests as $test) {
            $service = Service::where('source_model', LabTestDefinition::class)
                ->where('source_id', $test->id)
                ->first();

            if (! $service) {
                $service = Service::create([
                    'name' => $test->test_name,
                    'code' => $test->code ?? 'LAB-SYNC-'.$test->id,
                    'category' => ServiceCategory::Lab,
                    'description' => $test->description,
                    'source_model' => LabTestDefinition::class,
                    'source_id' => $test->id,
                    'default_price' => $test->price ?? 0,
                    'is_active' => $test->is_active,
                    'requires_upfront_payment' => true,
                ]);
            }
            $test->update(['service_id' => $service->id]);
        }

        // Link bed types
        $bedTypes = BedType::whereNull('service_id')->get();
        foreach ($bedTypes as $bed) {
            $service = Service::where('source_model', BedType::class)
                ->where('source_id', $bed->id)
                ->first();

            if (! $service) {
                $service = Service::create([
                    'name' => $bed->name,
                    'code' => 'BED-SYNC-'.$bed->id,
                    'category' => ServiceCategory::Bed,
                    'description' => $bed->description,
                    'source_model' => BedType::class,
                    'source_id' => $bed->id,
                    'default_price' => $bed->daily_rate ?? 0,
                    'is_active' => true,
                    'requires_upfront_payment' => false,
                ]);
            }
            $bed->update(['service_id' => $service->id]);
        }

        // Link medications
        $medications = Medication::whereNull('service_id')->get();
        foreach ($medications as $med) {
            $service = Service::where('source_model', Medication::class)
                ->where('source_id', $med->id)
                ->first();

            $name = $med->strength ? "{$med->name} {$med->strength}" : $med->name;

            if (! $service) {
                $service = Service::create([
                    'name' => $name,
                    'code' => 'MED-SYNC-'.$med->id,
                    'category' => ServiceCategory::Medication,
                    'description' => $med->description,
                    'source_model' => Medication::class,
                    'source_id' => $med->id,
                    'default_price' => $med->unit_cost ?? 0,
                    'is_active' => $med->is_active,
                    'requires_upfront_payment' => true,
                ]);
            }
            $med->update(['service_id' => $service->id]);
        }
    }
}
