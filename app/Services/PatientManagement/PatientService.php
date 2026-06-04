<?php

namespace App\Services\PatientManagement;

use App\Models\Patient;

class PatientService
{
    public function createPatient(array $data): Patient
    {
        return Patient::create($data);
    }

    public function updatePatient(Patient $patient, array $data): Patient
    {
        $patient->update($data);

        return $patient;
    }
}
