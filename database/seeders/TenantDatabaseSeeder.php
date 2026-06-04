<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Permissions and roles
        $this->call(RoleSeeder::class);

        // 2. Reference data (departments, bed types, lab tests, medications, supplies)
        $this->call(ReferenceDataSeeder::class);

        // 4. Hospital staff users (must exist before clinical data)
        $this->createStaffUsers();

        // 5. Facility structure (wards → beds)
        $this->call(WardSeeder::class);
        $this->call(BedSeeder::class);

        // 6. Patients (root of all clinical data)
        $this->call(PatientSeeder::class);

        // 7. Clinical workflows
        $this->call(AppointmentSeeder::class);
        $this->call(AdmissionSeeder::class);
        $this->call(VitalSeeder::class);
        $this->call(MedicalRecordSeeder::class);
        $this->call(NurseCareReportSeeder::class);

        // 8. Pharmacy workflows
        $this->call(PrescriptionSeeder::class);
        $this->call(PrescriptionItemSeeder::class);
        $this->call(DispensationSeeder::class);
        $this->call(SupplyUsageSeeder::class);

        // 9. Lab workflows
        $this->call(LabRequestSeeder::class);
        $this->call(LabResultSeeder::class);

        // 10. Financial
        $this->call(InvoiceSeeder::class);

        // 11. Feedback and shifts
        $this->call(FeedbackSeeder::class);
        $this->call(UserShiftSeeder::class);
    }

    private function createStaffUsers(): void
    {
        $admin = User::factory()->create([
            'name' => 'Hospital Admin',
            'email' => 'admin@hospital.local',
            'password' => 'password',
        ]);
        $admin->assignRole('admin');

        $reception = User::factory()->create([
            'name' => 'Reception Staff',
            'email' => 'reception@hospital.local',
            'password' => 'password',
        ]);
        $reception->assignRole('reception');

        $doctor = User::factory()->create([
            'name' => 'Dr. Smith',
            'email' => 'doctor@hospital.local',
            'password' => 'password',
        ]);
        $doctor->assignRole('doctor');

        $labTech = User::factory()->create([
            'name' => 'Lab Technician',
            'email' => 'lab@hospital.local',
            'password' => 'password',
        ]);
        $labTech->assignRole('lab_tech');

        $pharma = User::factory()->create([
            'name' => 'Pharmacist',
            'email' => 'pharma@hospital.local',
            'password' => 'password',
        ]);
        $pharma->assignRole('pharma');
    }
}
