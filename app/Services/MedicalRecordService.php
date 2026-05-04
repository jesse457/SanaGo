<?php

namespace App\Services;

use App\Models\Admission;
use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAttachment;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicalRecordService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Internal helper to record audit logs for user actions.
     */
    private function logActivity(string $type, string $description): void
    {
        UserActivity::create([
            'user_id'       => Auth::id(),
            'activity_type' => $type,
            'description'   => $description,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }

    /**
     * Generate a temporary S3 URL for file preview.
     */
    public function getAttachmentPreview(int $attachmentId): array
    {
        $attachment = MedicalRecordAttachment::findOrFail($attachmentId);
        $url = Storage::disk('s3')->temporaryUrl($attachment->file_path, now()->addMinutes(10));

        return [
            'url'       => $url,
            'file_name' => $attachment->file_name,
            'file_type' => $attachment->file_type,
        ];
    }

    /**
     * Retrieve full patient history including records, prescriptions, and labs.
     */
    public function getPatientProfile(Patient $patient): Patient
    {
        return $patient->load([
            'medicalRecords.doctor',
            'medicalRecords.attachments',
            'medicalRecords.prescription.items.medication',
            'labRequests.testDefinition',
            'admissions' => fn($q) => $q->latest(),
        ]);
    }

    /**
     * Get specific consultation details with all related medical data.
     */
    public function getConsultationDetail(int $id): MedicalRecord
    {
        return MedicalRecord::with([
            'patient',
            'doctor',
            'prescription.items.medication',
            'labResults.attachments',
            'labResults.labTechnician',
        ])->findOrFail($id);
    }

    /**
     * Persist consultation data. Handles creation, updates, file uploads,
     * prescription syncing, and lab request generation.
     *
     * Notifications are sent only after the transaction commits successfully.
     */
 public function saveOrUpdate(array $data, array $attachments, array $prescriptionItems, array $labItems, bool $finalize): MedicalRecord
{
    $notifyPrescription = null;
    $notifyLabOrder = false;

    // Use the specific connection if required, or default to DB::transaction
    $record = DB::transaction(function () use ($data, $attachments, $prescriptionItems, $labItems, $finalize, &$notifyPrescription, &$notifyLabOrder) {

        $record = MedicalRecord::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'patient_id'     => $data['patient_id'],
                'doctor_id'      => $data['doctor_id'],
                'complaint'      => $data['complaint'],
                'diagnosis_text' => $data['diagnosis_text'],
                'general_notes'  => $data['general_notes'],
                'finalized'      => $finalize,
                'record_type'    => 'consultation',
            ]
        );

        // ... [Attachments Logic stays the same] ...

        // --- C. Sync Prescription ---
        if (count($prescriptionItems) > 0) {
            $prescription = Prescription::updateOrCreate(
                ['consultation_id' => $record->id],
                [
                    'patient_id'        => $data['patient_id'],
                    'doctor_id'         => $data['doctor_id'],
                    'prescription_date' => now(),
                    'status'            => $finalize ? 'prescribed' : 'draft',
                ]
            );

            if ($finalize) { $notifyPrescription = $prescription; }

            $prescription->items()->delete();
            $prescItemsData = array_map(function ($item) use ($prescription) {
                return [
                    'tenant_id'           => tenant('id'),
                    'prescription_id'     => $prescription->id,
                    'medication_id'       => $item['medication_id'],
                    'dosage'              => $item['dosage'] ?? '',
                    'frequency'           => $item['frequency'] ?? '',
                    'duration'            => $item['duration'] ?? '',
                    'quantity_prescribed' => 1,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }, $prescriptionItems);

            PrescriptionItem::insert($prescItemsData);
        }

        // --- D. Sync Lab Requests ---
        foreach ($labItems as $lab) {
            $labReq = LabRequest::updateOrCreate(
                [
                    'consultation_id'        => $record->id,
                    'lab_test_definition_id' => $lab['lab_test_definition_id']
                ],
                [
                    'patient_id'             => $data['patient_id'],
                    'requested_by_doctor_id' => $data['doctor_id'],
                    'urgency_level'          => $lab['urgency'] ?? 'Normal',
                    'reason_for_test'        => $lab['reason'] ?? null,
                    'status'                 => 'requested',
                    'request_date'           => now(),
                    // FIXED: Ensure this matches the key passed from Livewire
                    'lab_tech_id'            => $lab['lab_tech_id'] ?? null,
                ]
            );

            if ($labReq->wasRecentlyCreated && $finalize) {
                $notifyLabOrder = true;
            }
        }

        return $record;
    });

    // Notifications
    if ($finalize) {
        if ($notifyPrescription) $this->notificationService->sendNewPrescriptionNotification($notifyPrescription);
        if ($notifyLabOrder) $this->notificationService->sendNewLabOrderNotification($record);
    }

    return $record;
}

    /**
     * Update an existing record without full sync logic (Quick Edit).
     * Restricted to the original doctor.
     */
    public function updateConsultation(MedicalRecord $record, array $data, User $doctor): MedicalRecord
    {
        if ($record->doctor_id !== $doctor->id) {
            throw new AuthorizationException('Unauthorized edit attempt.');
        }

        return DB::transaction(function () use ($record, $data, $doctor) {
            $record->update($data);

            $this->logActivity('updated', "Doctor {$doctor->name} updated medical record #{$record->id} for Patient ID: {$record->patient_id}");

            return $record->refresh();
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
                'doctor_id'  => $doctor->id,
                'status'     => 'Pending',
            ]);

            $this->logActivity('created', "Admission request created for patient {$patient->patient_uid} by Dr. {$doctor->name}");

            return $admission;
        });

        // Notify nurses after transaction commit
        $this->notificationService->sendNewPatientAdmissionNotification($admission);

        return $admission;
    }

    /**
     * Remove attachment from DB and S3.
     */
    public function deleteAttachment(int $attachmentId): void
    {
        DB::transaction(function () use ($attachmentId) {
            $att = MedicalRecordAttachment::find($attachmentId);

            if ($att) {
                $fileName = $att->file_name;
                $recordId = $att->medical_record_id;

                // Delete physical file
                Storage::disk('s3')->delete($att->file_path);

                // Delete DB record
                $att->delete();

                $this->logActivity('deleted', "Deleted attachment '{$fileName}' from medical record #{$recordId}");
            }
        });
    }

    /**
     * Build query for filtering medical records.
     */
    public function getMedicalRecordsQuery(array $filters): Builder
    {
        $query = MedicalRecord::query()->with(['doctor', 'patient', 'attachments']);

        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }
        if (!empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }
        if (!empty($filters['startDate']) && !empty($filters['endDate'])) {
            $query->whereBetween('created_at', [$filters['startDate'], $filters['endDate']]);
        }

        return $query->latest();
    }

    public function getMedicalRecordById(int $id): ?MedicalRecord
    {
        return MedicalRecord::with(['doctor', 'patient', 'attachments', 'prescription.items', 'labRequests'])
            ->find($id);
    }

    /**
     * Find an incomplete draft for a specific patient/doctor pair.
     */
    public function findLatestDraft(int $patientId, int $doctorId): ?MedicalRecord
    {
        return MedicalRecord::with(['attachments'])
            ->where('patient_id', $patientId)
            ->where('doctor_id', $doctorId)
            ->where('finalized', false)
            ->first();
    }

    /**
     * Retrieve draft prescription items formatted for frontend.
     */
    public function getDraftPrescriptionItems(int $medicalRecordId): array
    {
        $prescription = Prescription::with('items.medication')
            ->where('consultation_id', $medicalRecordId)
            ->first();

        return $prescription ? $prescription->items->map(fn($i) => [
            'medication_id' => $i->medication_id,
            'name'          => $i->medication->name,
            'dosage'        => $i->dosage,
            'frequency'     => $i->frequency,
            'duration'      => $i->duration,
        ])->toArray() : [];
    }

    /**
     * Retrieve draft lab requests formatted for frontend.
     */
    public function getDraftLabRequests(int $medicalRecordId): array
    {
        return LabRequest::with('testDefinition')
            ->where('consultation_id', $medicalRecordId)
            ->get()
            ->map(fn($l) => [
                'lab_test_definition_id' => $l->lab_test_definition_id,
                'test_name'              => $l->testDefinition->test_name,
                'urgency'                => $l->urgency_level,
                'reason'                 => $l->reason_for_test,
            ])->toArray();
    }

    public function getAllMedications()
    {
        return Medication::select('id', 'name', 'stock_quantity')
            ->orderBy('name')
            ->get();
    }

    public function getAllLabDefinitions()
    {
        return LabTestDefinition::orderBy('test_name')->get();
    }

    public function getAllLabTechnicians()
    {
        return User::where('role', 'lab-technician')->get();
    }
}
