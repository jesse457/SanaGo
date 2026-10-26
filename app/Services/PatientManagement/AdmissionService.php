<?php

namespace App\Services\PatientManagement;

use App\Enums\AdmissionStatus;
use App\Enums\BedStatus;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use App\Services\Billing\BillingService;
use Illuminate\Support\Facades\DB;

class AdmissionService
{
    public function admit(array $validated, Admission $admission): Admission
    {
        return DB::transaction(function () use ($validated, $admission) {
            $admission->update([...$validated,
                'status' => AdmissionStatus::Admitted->value, ]);

            Bed::where('id', $validated['bed_id'])
                ->update(['status' => BedStatus::Occupied->value]);

            return $admission;
        });
    }

    /**
     * Discharge an admission and generate its invoice exactly once.
     *
     * The discharge invoice is generated BEFORE the status flip so that
     * AdmissionObserver's `updated` hook can see it already exists and stand
     * down; otherwise both paths charge the observation fee.
     */
    public function discharge(Admission $admission): void
    {
        DB::transaction(function () use ($admission) {
            // Auto-generate invoice at discharge
            $billingService = app(BillingService::class);
            $billingService->generateInvoiceFromAdmission($admission);

            $admission->update([
                'status' => AdmissionStatus::Discharged->value,
                'discharge_date' => now(),
            ]);
            $admission->bed?->update(['status' => BedStatus::Available->value]);
        });
    }

    /**
     * Create an admission request and notify nursing staff.
     */
    public function requestAdmission(Patient $patient, User $doctor): Admission
    {
        $admission = DB::transaction(function () use ($patient, $doctor) {
            $patient->update(['is_admitted_approve' => true]);

            $admission = Admission::create([
                'patient_id' => $patient->id,
                'attending_doctor_id' => $doctor->id,
                'status' => AdmissionStatus::Pending,
            ]);

            return $admission;
        });

        return $admission;
    }
}
