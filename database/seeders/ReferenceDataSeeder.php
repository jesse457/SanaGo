<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDepartments();
        $this->seedBedTypes();
        $this->seedLabTestDefinitions();
        $this->seedMedications();
        $this->seedSupplies();
    }

    /**
     * Departments are keyed on their unique `code`.
     */
    private function seedDepartments(): void
    {
        $rows = [
            ['name' => 'Emergency', 'code' => 'ER', 'type' => 'clinical', 'is_active' => true],
            ['name' => 'Cardiology', 'code' => 'CARD', 'type' => 'clinical', 'is_active' => true],
            ['name' => 'Pediatrics', 'code' => 'PED', 'type' => 'clinical', 'is_active' => true],
            ['name' => 'Orthopedics', 'code' => 'ORTHO', 'type' => 'clinical', 'is_active' => true],
            ['name' => 'Radiology', 'code' => 'RAD', 'type' => 'support', 'is_active' => true],
            ['name' => 'Laboratory', 'code' => 'LAB', 'type' => 'support', 'is_active' => true],
            ['name' => 'Pharmacy', 'code' => 'PHARM', 'type' => 'support', 'is_active' => true],
            ['name' => 'Administration', 'code' => 'ADMIN', 'type' => 'administrative', 'is_active' => true],
        ];

        DB::table('departments')->upsert(
            $this->withTimestamps($rows),
            ['code'],
            ['name', 'type', 'is_active', 'updated_at'],
        );
    }

    /**
     * Bed types have no natural unique column, so they are matched by name.
     */
    private function seedBedTypes(): void
    {
        $rows = [
            ['name' => 'General Ward', 'daily_rate' => 50.00, 'capacity' => 1],
            ['name' => 'ICU', 'daily_rate' => 500.00, 'capacity' => 1],
            ['name' => 'HDU', 'daily_rate' => 250.00, 'capacity' => 1],
            ['name' => 'Private Room', 'daily_rate' => 150.00, 'capacity' => 1],
            ['name' => 'Pediatric Cot', 'daily_rate' => 30.00, 'capacity' => 1],
        ];

        foreach ($rows as $row) {
            if (DB::table('bed_types')->where('name', $row['name'])->doesntExist()) {
                DB::table('bed_types')->insert($row + $this->timestampsFor());
            }
        }
    }

    /**
     * Lab test definitions are keyed on their unique `code`.
     */
    private function seedLabTestDefinitions(): void
    {
        $rows = [
            ['test_name' => 'Complete Blood Count', 'code' => 'CBC', 'price' => 25.00, 'units' => 'cells/mL', 'is_active' => true],
            ['test_name' => 'Malaria Rapid Test', 'code' => 'MRDT', 'price' => 10.00, 'units' => 'Positive/Negative', 'is_active' => true],
            ['test_name' => 'Blood Glucose', 'code' => 'GLU', 'price' => 8.00, 'units' => 'mmol/L', 'is_active' => true],
            ['test_name' => 'Urinalysis', 'code' => 'UA', 'price' => 12.00, 'units' => 'N/A', 'is_active' => true],
            ['test_name' => 'Liver Function Test', 'code' => 'LFT', 'price' => 35.00, 'units' => 'IU/L', 'is_active' => true],
            ['test_name' => 'Kidney Function Test', 'code' => 'KFT', 'price' => 35.00, 'units' => 'mg/dL', 'is_active' => true],
            ['test_name' => 'HIV Test', 'code' => 'HIV', 'price' => 15.00, 'units' => 'Positive/Negative', 'is_active' => true],
            ['test_name' => 'X-Ray Chest', 'code' => 'CXR', 'price' => 40.00, 'units' => 'N/A', 'is_active' => true],
        ];

        DB::table('lab_test_definitions')->upsert(
            $this->withTimestamps($rows),
            ['code'],
            ['test_name', 'price', 'units', 'is_active', 'updated_at'],
        );
    }

    /**
     * Medications have no natural unique column, so they are matched by the
     * drug name plus its strength.
     */
    private function seedMedications(): void
    {
        $rows = [
            ['name' => 'Paracetamol', 'strength' => '500mg', 'dosage_units' => 'Tablet', 'min_stock_level' => 100, 'total_stock' => 1000, 'unit_cost' => 0.05, 'is_active' => true],
            ['name' => 'Ibuprofen', 'strength' => '400mg', 'dosage_units' => 'Tablet', 'min_stock_level' => 50, 'total_stock' => 500, 'unit_cost' => 0.08, 'is_active' => true],
            ['name' => 'Amoxicillin', 'strength' => '250mg', 'dosage_units' => 'Capsule', 'min_stock_level' => 80, 'total_stock' => 800, 'unit_cost' => 0.12, 'is_active' => true],
            ['name' => 'Ciprofloxacin', 'strength' => '500mg', 'dosage_units' => 'Tablet', 'min_stock_level' => 60, 'total_stock' => 600, 'unit_cost' => 0.15, 'is_active' => true],
            ['name' => 'Metformin', 'strength' => '500mg', 'dosage_units' => 'Tablet', 'min_stock_level' => 70, 'total_stock' => 700, 'unit_cost' => 0.06, 'is_active' => true],
            ['name' => 'Omeprazole', 'strength' => '20mg', 'dosage_units' => 'Capsule', 'min_stock_level' => 50, 'total_stock' => 400, 'unit_cost' => 0.10, 'is_active' => true],
            ['name' => 'Salbutamol Inhaler', 'strength' => '100mcg', 'dosage_units' => 'Inhaler', 'min_stock_level' => 20, 'total_stock' => 150, 'unit_cost' => 3.50, 'is_active' => true],
            ['name' => 'Normal Saline IV', 'strength' => '0.9%', 'dosage_units' => '500mL bag', 'min_stock_level' => 30, 'total_stock' => 200, 'unit_cost' => 1.20, 'is_active' => true],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('medications')
                ->where('name', $row['name'])
                ->where('strength', $row['strength'])
                ->exists();

            if (! $exists) {
                DB::table('medications')->insert($row + $this->timestampsFor());
            }
        }
    }

    /**
     * Supplies have no natural unique column, so they are matched by name.
     */
    private function seedSupplies(): void
    {
        $rows = [
            ['name' => 'Surgical Gloves (box)', 'unit_of_measure' => 'Box', 'current_stock' => 200, 'min_stock_level' => 20, 'cost_per_unit' => 5.00, 'category' => 'PPE'],
            ['name' => 'Syringes 5mL', 'unit_of_measure' => 'Piece', 'current_stock' => 500, 'min_stock_level' => 50, 'cost_per_unit' => 0.15, 'category' => 'Consumables'],
            ['name' => 'Cotton Wool Roll', 'unit_of_measure' => 'Roll', 'current_stock' => 80, 'min_stock_level' => 10, 'cost_per_unit' => 2.00, 'category' => 'Consumables'],
            ['name' => 'Face Masks (box)', 'unit_of_measure' => 'Box', 'current_stock' => 150, 'min_stock_level' => 15, 'cost_per_unit' => 8.00, 'category' => 'PPE'],
        ];

        foreach ($rows as $row) {
            if (DB::table('supplies')->where('name', $row['name'])->doesntExist()) {
                DB::table('supplies')->insert($row + $this->timestampsFor());
            }
        }
    }

    /**
     * Attach the same created_at/updated_at to every row of a batch.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function withTimestamps(array $rows): array
    {
        return array_map(fn (array $row): array => $row + $this->timestampsFor(), $rows);
    }

    /**
     * @return array{created_at: Carbon, updated_at: Carbon}
     */
    private function timestampsFor(): array
    {
        return ['created_at' => now(), 'updated_at' => now()];
    }
}
