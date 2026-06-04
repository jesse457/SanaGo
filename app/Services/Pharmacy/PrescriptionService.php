<?php

namespace App\Services\Pharmacy;

use App\Enums\PrescriptionStatus;
use App\Models\Prescription;

class PrescriptionService
{
    public function createPrescription(array $data): Prescription
    {
        return Prescription::create(array_merge($data, [
            'status' => PrescriptionStatus::NEW->value,
            'prescription_date' => now(),
        ]));
    }

    public function updateStatus(Prescription $prescription, PrescriptionStatus $status): Prescription
    {
        $prescription->update(['status' => $status->value]);

        return $prescription;
    }
}
