<?php

declare(strict_types=1);

use App\Enums\LabRequestStatus;
use App\Enums\LabResultStatus;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Services\Laboratory\LabResultService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();
});

it('maps every result status to the completed request status', function (LabResultStatus $resultStatus) {
    $labRequest = LabRequest::factory()->create([
        'status' => LabRequestStatus::PENDING->value,
    ]);
    $result = LabResult::factory()->create([
        'lab_request_id' => $labRequest->id,
        'status' => LabResultStatus::NORMAL->value,
    ]);

    app(LabResultService::class)->updateResult($result, [
        'lab_request_id' => $labRequest->id,
        'status' => $resultStatus->value,
        'result_date' => now()->toDateString(),
        'results_text' => 'Updated findings.',
    ]);

    $labRequest->refresh();

    // The request status column casts to LabRequestStatus; writing a
    // LabResultStatus value there raised a ValueError on the next read.
    expect($labRequest->status)->toBe(LabRequestStatus::COMPLETED)
        ->and($result->fresh()->status)->toBe($resultStatus);
})->with([
    'normal' => LabResultStatus::NORMAL,
    'abnormal' => LabResultStatus::ABNORMAL,
    'critical' => LabResultStatus::CRITICAL,
]);

it('marks the request completed when a result is created', function () {
    $labRequest = LabRequest::factory()->create([
        'status' => LabRequestStatus::IN_PROGRESS->value,
    ]);

    app(LabResultService::class)->createResult([
        'lab_request_id' => $labRequest->id,
        'status' => LabResultStatus::NORMAL->value,
        'result_date' => now()->toDateString(),
        'results_text' => 'All parameters normal.',
    ]);

    expect($labRequest->fresh()->status)->toBe(LabRequestStatus::COMPLETED);
});
