<?php

namespace App\Http\Controllers\Api\Tenants\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\Doctor\StoreConsultationRequest;
use App\Http\Resources\MedicalRecordResource;
use App\Models\Patient;
use App\Services\MedicalRecordService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MedicalRecordApiController extends Controller
{
    use HttpResponses;

    protected MedicalRecordService $service;

    public function __construct(MedicalRecordService $service)
    {
        $this->service = $service;
    }

    /**
     * Get context for consultation (medications, labs, drafts).
     */
    public function getConsultationContext(int $patientId): JsonResponse
    {
        try {

            $draft = $this->service->findLatestDraft($patientId, Auth::id());

            return $this->success([
                'medications' => $this->service->getAllMedications(),
                'lab_definitions' => $this->service->getAllLabDefinitions(),
                'draft' => $draft ? new MedicalRecordResource($draft) : null,
                'prescription_items' => $draft ? $this->service->getDraftPrescriptionItems($draft->id) : [],
                'lab_items' => $draft ? $this->service->getDraftLabRequests($draft->id) : [],
            ], 'Consultation context loaded.');
        } catch (\Exception $e) {
            Log::error("Failed to load consultation context: {$e->getMessage()}", [
                'doctor_id' => Auth::id(),
                'patient_id' => $patientId,
            ]);

            return $this->error(null, 'Unable to load consultation resources.', 500);
        }
    }

    /**
     * Save a draft or finalize a consultation.
     */
    public function store(StoreConsultationRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated['doctor_id'] = Auth::id();

            $record = $this->service->saveOrUpdate(
                $validated,
                $request->file('attachments') ?? [],
                $validated['prescription_items'] ?? [],
                $validated['lab_items'] ?? [],
                $validated['finalize']
            );

            $message = $validated['finalize']
                ? 'Consultation finalized successfully.'
                : 'Consultation draft saved.';

            return $this->success(new MedicalRecordResource($record), $message);

        } catch (\Exception $e) {
            Log::error("Consultation save failed: {$e->getMessage()}", [
                'doctor_id' => Auth::id(),
                'patient_id' => $request->patient_id,
            ]);

            return $this->error(null, 'An error occurred while saving the consultation.', 500);
        }
    }

    /**
     * Get patient medical history/profile.
     */
    public function show(Patient $patient): JsonResponse
    {
        try {
            $profile = $this->service->getPatientProfile($patient);

            return $this->success($profile, 'Patient profile retrieved.');
        } catch (\Exception $e) {
            Log::error("Failed to fetch patient profile: {$e->getMessage()}", ['patient_id' => $patient->id]);

            return $this->error(null, 'Patient profile is currently unavailable.', 500);
        }
    }

    /**
     * Get details of a specific consultation record for the Detail View.
     * Matches React call: apiClient.get(`/doctor/consultations/${id}`)
     */
    public function showConsultation(int $id): JsonResponse
    {
        try {
            // 1. Fetch the record via the service
            // The service should handle the "with()" relationships:
            // ['patient', 'doctor', 'prescriptions.items.medication', 'labRequests.result.attachments', 'labRequests.testDefinition']
            $record = $this->service->getConsultationDetail($id);

            if (! $record) {
                return $this->error(null, 'Consultation record not found.', 404);
            }

            // 2. Return using the resource to ensure proper JSON formatting
            return $this->success(new MedicalRecordResource($record), 'Consultation record retrieved.');

        } catch (\Exception $e) {
            Log::error('Consultation Detail Error: '.$e->getMessage(), [
                'consultation_id' => $id,
                'doctor_id' => Auth::id(),
            ]);

            return $this->error(null, 'Unable to load consultation details.', 500);
        }
    }

    /**
     * Request patient admission.
     */
    public function admit(Patient $patient): JsonResponse
    {
        try {
            $admission = $this->service->requestAdmission($patient, Auth::user());

            return $this->success($admission, 'Admission request sent to reception.');
        } catch (\Exception $e) {
            Log::error("Admission request failed: {$e->getMessage()}", ['patient_id' => $patient->id]);

            return $this->error(null, $e->getMessage(), 422);
        }
    }

    public function medications(): JsonResponse
    {
        try {
            $medications = $this->service->getAllMedications();

            return $this->success($medications, 'Medications retrieved.');
        } catch (\Exception $e) {
            return $this->error(null, 'Medications are currently unavailable.', 500);
        }
    }

    public function labDefinitions(): JsonResponse
    {
        try {
            $labDefinitions = $this->service->getAllLabDefinitions();

            return $this->success($labDefinitions, 'Lab definitions retrieved.');
        } catch (\Exception $e) {
            return $this->error(null, 'Lab definitions are currently unavailable.', 500);
        }
    }

    public function labTechnicians(): JsonResponse
    {
        try {
            $labTechnicians = $this->service->getAllLabTechnicians();

            return $this->success($labTechnicians, 'Lab technicians retrieved.');
        } catch (\Exception $e) {
            return $this->error(null, 'Lab technicians are currently unavailable.', 500);
        }
    }
}
