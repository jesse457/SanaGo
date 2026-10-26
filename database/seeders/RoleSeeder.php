<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Patient Management
            'view-patients',
            'create-patients',
            'edit-patients',
            'delete-patients',

            // Appointments
            'view-appointments',
            'create-appointments',
            'edit-appointments',

            // Admissions
            'view-admissions',
            'create-admissions',
            'discharge-admissions',

            // Consultations & Medical Records
            'view-consultations',
            'create-consultations',

            // Vitals
            'view-vitals',
            'record-vitals',

            // Lab
            'request-lab-tests',
            'view-lab-tests',
            'manage-lab-tests',
            'view-lab-results',
            'create-lab-results',

            // Pharmacy
            'view-drugs',
            'manage-drugs',
            'dispense-drugs',

            // Supplies
            'view-supplies',
            'manage-supplies',

            // Infrastructure
            'manage-infrastructure',

            // Departments
            'manage-departments',

            // Finance
            'view-finance',

            // User & Role Management
            'manage-users',
            'manage-roles',

            // System
            'view-audit-logs',

            // Radiology
            'request-radiology',
            'view-radiology',
            'validate-radiology',

            // Caisse (Cash Register)
            'manage-caisse',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions(Permission::all());

        $reception = Role::findOrCreate('reception', 'web');
        $reception->syncPermissions([
            'view-patients',
            'create-patients',
            'edit-patients',
            'view-appointments',
            'create-appointments',
            'edit-appointments',
            'view-admissions',
            'create-admissions',
            'discharge-admissions',
            'manage-caisse',
        ]);

        $doctor = Role::findOrCreate('doctor', 'web');
        $doctor->syncPermissions([
            'view-patients',
            'create-patients',
            'edit-patients',
            'view-appointments',
            'create-appointments',
            'view-admissions',
            'view-consultations',
            'create-consultations',
            'view-vitals',
            'record-vitals',
            // Doctors raise lab orders — on their own or from a consultation.
            'request-lab-tests',
            'view-lab-tests',
            'view-lab-results',
            'view-radiology',
            'validate-radiology',
            'dispense-drugs',
            'view-drugs',
        ]);

        $labTech = Role::findOrCreate('lab_tech', 'web');
        $labTech->syncPermissions([
            'view-patients',
            'view-lab-tests',
            'manage-lab-tests',
            'view-lab-results',
            'create-lab-results',
            'view-radiology',
            'validate-radiology',
        ]);

        $pharma = Role::findOrCreate('pharma', 'web');
        $pharma->syncPermissions([
            'view-drugs',
            'manage-drugs',
            'dispense-drugs',
            'view-supplies',
            'manage-supplies',
        ]);
    }
}
