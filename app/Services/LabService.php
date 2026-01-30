<?php

namespace App\Services;

use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\LabResultAttachment;
use App\Models\LabTestDefinition; // Imported
use App\Models\UserActivity;
use App\Notifications\LabResultNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabService
{
    /**
     * Private helper to log user activity
     */
    private function logActivity(string $type, string $description): void
    {
        UserActivity::create([
            'user_id' => Auth::id(),
            'activity_type' => $type, // 'created', 'updated', 'deleted'
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Create a new lab request.
     */
    public function createLabRequest(array $data): LabRequest
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($data) {
            $request = LabRequest::create([
                'patient_id' => $data['patient_id'],
                'requested_by_doctor_id' => $data['doctor_id'],
                'consultation_id' => $data['consultation_id'] ?? null,
                'lab_test_definition_id' => $data['test_id'],
                'request_date' => $data['request_date'] ?? now(),
                'clinical_notes' => $data['clinical_notes'] ?? null,
                'priority' => $data['priority'] ?? 'Standard',
                
                'status' => 'Pending',
            ]);

            $testName = $request->testDefinition->test_name ?? 'Unknown Test';
            $this->logActivity('created', "Created lab request for {$testName} (Request #{$request->id})");

            return $request;
        });
    }

    /**
     * Create a new Lab Test Definition (Service Price List).
     */
    public function createTestDefinition(array $data): LabTestDefinition
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($data) {
            $test = LabTestDefinition::create([
                'test_name' => $data['test_name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'units' => $data['units'] ?? null,
            ]);

            $this->logActivity('created', "Created new lab test definition: {$test->test_name}");

            return $test;
        });
    }


    /**
     * Query building for lab results list.
     */
    public function getLabResultsQuery(array $filters)
    {
        $query = \App\Models\LabResult::query()
            ->with(['labRequest.patient', 'labRequest.testDefinition', 'doctor']);

        // Filter by Date
        if (! empty($filters['date'])) {
            $query->whereDate('result_date', $filters['date']);
        }

        // Filter by Search (UID or Patient Name)
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $terms = array_filter(explode(' ', $search));

            $query->whereHas('labRequest.patient', function ($patientQuery) use ($search, $terms) {
                $patientQuery->where(function ($q) use ($search, $terms) {
                    // UID Search
                    $q->where('patient_uid', 'ILIKE', "%{$search}%");

                    // Name Search (using Blind Indexes)
                    if (! empty($terms)) {
                        foreach ($terms as $term) {
                            $q->orWhereBlind('first_name', 'first_name_index', $term)
                                ->orWhereBlind('last_name', 'last_name_index', $term);
                        }
                    }
                });
            });
        }

        return $query->latest('result_date');
    }


    /**
     * Query building for lab test definitions.
     */
    public function getTestDefinitionsQuery(array $filters)
    {
        $query = LabTestDefinition::query();

        if (! empty($filters['search'])) {
            $terms = array_filter(explode(' ', trim($filters['search'])));

            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    // Using the blind index for encrypted/searchable test names
                    $q->orWhereBlind('test_name', 'test_name_index', $term);
                }
            });
        }

        return $query->orderBy('test_name');
    }

    /**
     * Update an existing lab test definition.
     */
    public function updateTestDefinition(LabTestDefinition $test, array $data): LabTestDefinition
    {
        return DB::connection('pgsql_transaction')->transaction(function () use ($test, $data) {
            $test->update([
                'test_name'   => $data['test_name'] ?? $test->test_name,
                'description' => $data['description'] ?? $test->description,
                'price'       => $data['price'] ?? $test->price,
                'units'       => $data['units'] ?? $test->units,
            ]);

            $this->logActivity('updated', "Updated lab test definition: {$test->test_name}");

            return $test;
        });
    }

    /**
     * Delete a lab test definition.
     */
    public function deleteTestDefinition(LabTestDefinition $test): void
    {
        DB::connection('pgsql_transaction')->transaction(function () use ($test) {
            $testName = $test->test_name;
            $test->delete();

            $this->logActivity('deleted', "Deleted lab test definition: {$testName}");
        });
    }
    /**
     * Mark a request as in progress.
     */
    public function startRequest(LabRequest $request): void
    {
        DB::connection('pgsql_transaction')->transaction(function () use ($request) {
            $request->update(['status' => 'In_Progress']);

            $this->logActivity('updated', "Started processing lab request #{$request->id}");
        });
    }

    /**
     * Submit lab results and notify the doctor.
     */
    public function submitResults(LabRequest $request, array $data, array $attachments): void
    {
        DB::connection('pgsql_transaction')->transaction(function () use ($request, $data, $attachments) {

            // 1. Update Request Status
            $request->update(['status' => 'Completed']);

            $currentPrice = $request->testDefinition->price ?? 0;

            // 2. Create or Update Result
            $result = LabResult::updateOrCreate(
                ['lab_request_id' => $request->id],
                [
                    'consultation_id' => $request->consultation_id,
                    'lab_technician_id' => $data['technician_id'],
                    'result_date' => now(),
                    'doctor_id' => $request->requested_by_doctor_id,
                    'results_text' => $data['results_text'],
                    'analysis_comments' => $data['analysis_comments'] ?? null,
                    'status' => 'Completed',
                    'price' => $currentPrice,
                ]
            );

            // 3. Handle Attachments
            foreach ($attachments as $file) {
                $path = $file->store('lab-attachments', 's3');

                LabResultAttachment::create([
                    'lab_result_id' => $result->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientMimeType(),
                ]);
            }

            // 4. Logging
            $this->logActivity('updated', "Submitted results for lab request #{$request->id} (Result #{$result->id})");

            // 5. Notify Doctor
            $result->load(['labRequest.patient', 'labRequest.testDefinition']);

            if ($request->doctor) {
                $request->doctor->notify(new LabResultNotification($result));
            }
        });
    }

    /**
     * Query building for lab requests list.
     */
    public function getLabRequestsQuery(array $filters)
    {
        $query = LabRequest::query()
            ->with(['patient', 'testDefinition', 'doctor']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['doctor_id'])) {
            $query->where('requested_by_doctor_id', $filters['doctor_id']);
        }

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $terms = array_filter(explode(' ', $search));

            $query->where(function ($q) use ($search, $terms) {
                $q->whereHas('patient', function ($patientQuery) use ($search) {
                    $patientQuery->where('patient_uid', 'ILIKE', "%{$search}%");
                })
                    ->orWhereHas('testDefinition', function ($testQuery) use ($search) {
                        $testQuery->where('test_name', 'ILIKE', "%{$search}%");
                    });

                if (! empty($terms)) {
                    $q->orWhereHas('patient', function ($patientQ) use ($terms) {
                        $patientQ->where(function ($matchQ) use ($terms) {
                            foreach ($terms as $term) {
                                $matchQ->orWhereBlind('first_name', 'first_name_index', $term)
                                    ->orWhereBlind('last_name', 'last_name_index', $term);
                            }
                        });
                    });
                }
            });
        }

        return $query->orderBy('request_date', 'desc');
    }
}
