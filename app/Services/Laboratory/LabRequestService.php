<?php

namespace App\Services\Laboratory;

use App\Enums\LabRequestStatus;
use App\Models\LabRequest;

class LabRequestService
{
    public function createRequest(array $data): LabRequest
    {
        return LabRequest::create([
            ...$data,
            'request_date' => now(),
            'status' => LabRequestStatus::PENDING->value,
        ]);
    }

    public function updateRequest(LabRequest $labRequest, array $data): LabRequest
    {
        $labRequest->update($data);

        return $labRequest;
    }
}
