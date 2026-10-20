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

        // 3. Service catalogue — billing depends on it, so it must be seeded
        //    before any clinical or financial record is generated.
        $this->call(ServiceSeeder::class);

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

    /**
     * Create one demo user per staff role. Re-running the seeder updates the
     * existing account instead of tripping the unique `email` constraint.
     */
    private function createStaffUsers(): void
    {
        $staff = [
            ['name' => 'Hospital Admin', 'email' => 'admin@hospital.local', 'role' => 'admin'],
            ['name' => 'Reception Staff', 'email' => 'reception@hospital.local', 'role' => 'reception'],
            ['name' => 'Dr. Smith', 'email' => 'doctor@hospital.local', 'role' => 'doctor'],
            ['name' => 'Lab Technician', 'email' => 'lab@hospital.local', 'role' => 'lab_tech'],
            ['name' => 'Pharmacist', 'email' => 'pharma@hospital.local', 'role' => 'pharma'],
        ];

        foreach ($staff as $member) {
            $user = User::updateOrCreate(
                ['email' => $member['email']],
                ['name' => $member['name'], 'password' => 'password'],
            );

            $user->syncRoles([$member['role']]);
        }
    }
}
