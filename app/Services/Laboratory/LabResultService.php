<?php

namespace App\Services\Laboratory;

use App\Enums\LabRequestStatus;
use App\Models\LabResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class LabResultService
{
    public function createResult(array $validated, ?array $attachments = null): LabResult
    {
        return DB::transaction(function () use ($validated, $attachments) {
            $result = LabResult::create($validated);
            $result->labRequest->update(['status' => LabRequestStatus::COMPLETED->value]);

            if ($attachments) {
                foreach ($attachments as $file) {
                    if ($file instanceof UploadedFile) {
                        $result->addMedia($file)->toMediaCollection('report');
                    }
                }
            }

            return $result;
        });
    }

    public function updateResult(LabResult $labResult, array $validated, ?array $attachments = null): LabResult
    {
        return DB::transaction(function () use ($labResult, $validated, $attachments) {
            $labResult->update($validated);

            // `lab_results.status` is a LabResultStatus value (normal|abnormal|critical),
            // while `lab_requests.status` casts to LabRequestStatus and has no such
            // values. Writing the result status across used to throw a ValueError on
            // the next read; a reported result always completes its request.
            $labResult->labRequest->update(['status' => LabRequestStatus::COMPLETED->value]);

            if ($attachments) {
                foreach ($attachments as $file) {
                    if ($file instanceof UploadedFile) {
                        $labResult->addMedia($file)->toMediaCollection('report');
                    }
                }
            }

            return $labResult;
        });
    }
}
