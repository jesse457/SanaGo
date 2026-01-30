<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\UserActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdmissionService
{
    protected PatientService $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Log user activity helper.
     */
    private function logActivity(string $type, string $description, ?array $metadata = null): void
    {
        UserActivity::create([
            'user_id' => Auth::id(),
            'activity_type' => $type,
            'description' => $description,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get all currently available (unoccupied) beds.
     * Useful for dropdowns in UI.
     */
    public function getAvailableBeds(): Collection
    {
        return Bed::where('is_occupied', false)
            ->with(['ward', 'bedType']) // Eager load for better UI display (e.g., "ICU - Bed 1")
            ->get();
    }

    /**
     * Get patients for check-in list.
     */
    public function getPatientsForCheckin(string $search, int $perPage = 10)
    {
        return $this->patientService->search($search)
            ->with(['admissions' => function ($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get admission history for a patient.
     */
    public function getAdmissionsForPatient(Patient $patient): Collection
    {
        $patient->load(['admissions' => function ($query) {
            $query->with(['doctor', 'bed.ward']);
        }]);

        return $patient->admissions->sortByDesc('admission_date');
    }

    /**
     * Confirm an admission (Update Pending -> Admitted).
     * This replaces the logic in AdmitPatient::saveAdmission.
     *
     * @param  Admission  $admission  The existing admission record (likely pending).
     * @param  array  $data  Validated data ['bed_id', 'reason_for_admission', 'admission_date', 'observation_fee'].
     *
     * @throws RuntimeException
     */
    public function confirmAdmission(Admission $admission, array $data): Admission
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($admission, $data) {

            // 1. Sanity Check: Is already admitted?
            if ($admission->status === 'Admitted') {
                throw new RuntimeException("Patient {$admission->patient->full_name} is already admitted.");
            }

            // 2. Bed Availability Check (Concurrency Protection)

            $bed = Bed::find($data['bed_id']);

            if (! $bed) {
                throw new RuntimeException('The selected bed does not exist.');
            }

            // If checking a new bed, or if the current admission doesn't own this bed yet
            if ($bed->is_occupied && $admission->bed_id !== $bed->id) {
                throw new RuntimeException("Bed {$bed->code} has just been occupied by another patient. Please select a different bed.");
            }

            // 3. Update Admission Record
            $admission->load('patient')->update([
                'bed_id' => $bed->id,
                'reason_for_admission' => $data['reason_for_admission'],
                'admission_date' => $data['admission_date'],
                'observation_fee' => $data['observation_fee'] ?? 0,
                'status' => 'Admitted',
                'admitted_by' => Auth::id(),
            ]);
            $admission->patient->update(['is_admitted_approve' => false]);
            // 4. Mark Bed as Occupied
            $bed->update(['is_occupied' => true]);

            // 5. Log Activity
            $this->logActivity(
                'Patient_Admission_Confirmed',
                "Confirmed admission for patient {$admission->patient->full_name} to Bed {$bed->code}",
                [
                    'patient_id' => $admission->patient_id,
                    'admission_id' => $admission->id,
                    'bed_id' => $bed->id,
                ]
            );

            return $admission;
        });
    }

    /**
     * Discharge a patient.
     */
    public function dischargePatient(int $admissionId): Admission
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($admissionId) {
            $admission = Admission::with('patient')->findOrFail($admissionId);

            if ($admission->status === 'Discharged') {
                throw new RuntimeException('Patient is already Discharged.');
            }

            // Update Admission
            $admission->update([
                'status' => 'Discharged',
                'discharge_date' => now(),
            ]);

            // Free the Bed
            if ($admission->bed_id) {
                Bed::where('id', $admission->bed_id)->update(['is_occupied' => false]);
            }

            // Reset Patient Approval flag
            $admission->patient->update(['is_admitted_approve' => false]);

            $this->logActivity('Patient_discharged', "Patient {$admission->patient->full_name} was Discharged.");

            return $admission;
        });
    }
}
