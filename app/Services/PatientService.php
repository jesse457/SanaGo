<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientService
{
    /**
     * Private helper to log user activity
     */
    private function logActivity(string $type, string $description): void
    {
        UserActivity::create([
            'user_id' => Auth::id(),
            'activity_type' => $type, // 'created', 'updated', etc.
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a new patient.
     */
    // In App\Services\PatientService.php

    public function createPatient(array $data): Patient
    {
        return DB::transaction(function () use ($data) {

            // EFFICIENT ID GENERATION
            // Format: PT-YYMMDD-RAND (e.g., PT-240123-X92Z)
            // This sorts chronologically and is extremely unlikely to collide.
            if (empty($data['patient_uid'])) {
                $data['patient_uid'] = 'PT-'.now()->format('ymd').'-'.strtoupper(Str::random(4));
            }

            $patient = Patient::create($data);

            // Logging handled here
            $this->logActivity('created', "Registered patient {$patient->full_name}");

            return $patient;
        });
    }

    /**
     * Update an existing patient.
     */
    public function updatePatient(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            $patient->update($data);

            $this->logActivity('updated', "Updated patient: {$patient->first_name} {$patient->last_name} (UID: {$patient->patient_uid})");

            return $patient;
        });
    }

    /**
     * Find a patient by ID or UID.
     */
    public function findPatient(string|int $identifier): ?Patient
    {
        return Patient::where('id', $identifier)
            ->orWhere('patient_uid', $identifier)
            ->first();
    }

    /**
     * Search patients using blind indexes for encrypted fields.
     */
    public function search(string $query): Builder
    {
        $builder = Patient::query();
        $query = trim($query);

        if (empty($query)) {
            return $builder->latest();
        }

        $terms = explode(' ', $query);

        return $builder->where(function (Builder $matchQ) use ($terms) {
            if (count($terms) === 2) {
                $matchQ->where(function ($strictQ) use ($terms) {
                    $strictQ->whereBlind('first_name', 'first_name_index', $terms[0])
                        ->whereBlind('last_name', 'last_name_index', $terms[1]);
                })
                    ->orWhere(function ($looseQ) use ($terms) {
                        foreach ($terms as $term) {
                            $looseQ->orWhere(function ($fieldQ) use ($term) {
                                $this->applyBlindSearchFilters($fieldQ, $term);
                            });
                        }
                    });
            } else {
                foreach ($terms as $term) {
                    $matchQ->orWhere(function ($fieldQ) use ($term) {
                        $this->applyBlindSearchFilters($fieldQ, $term);
                    });
                }
            }
        });
    }

    /**
     * Helper to apply blind index filters to a query.
     */
    private function applyBlindSearchFilters(Builder $query, string $term): void
    {
        $query->whereBlind('first_name', 'first_name_index', $term)
            ->orWhereBlind('last_name', 'last_name_index', $term)
            ->orWhereBlind('phone', 'phone_index', $term)
            ->orWhereBlind('email', 'email_index', $term)
            ->orWhere('patient_uid', 'like', "%{$term}%");
    }

    /**
     * Get patients that have a relationship with a specific doctor.
     */
    public function getPatientsForDoctor(int $doctorId, string $search = ''): Builder
    {
        $query = $this->search($search);

        return $query->where(function ($q) use ($doctorId) {
            $q->whereHas('appointments', function ($sub) use ($doctorId) {
                $sub->where('doctor_id', $doctorId);
            })->orWhereHas('admissions', function ($sub) use ($doctorId) {
                $sub->where('doctor_id', $doctorId);
            });
        });
    }
}
