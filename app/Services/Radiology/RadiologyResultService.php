<?php

namespace App\Services\Radiology;

use App\Enums\RadiologyStatus;
use App\Models\RadiologyRequest;
use App\Models\RadiologyResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RadiologyResultService
{
    /**
     * Record the report of an imaging request.
     *
     * The request and its result always move together: a report that is only
     * being drafted leaves the order in progress, while a signed one closes it.
     *
     * @param  array<string, mixed>  $validated
     */
    public function createResult(array $validated): RadiologyResult
    {
        return DB::transaction(function () use ($validated): RadiologyResult {
            $status = $this->normaliseStatus($validated['status'] ?? null);

            $result = RadiologyResult::create([
                'request_id' => $validated['request_id'],
                'performed_by' => auth()->id(),
                'findings' => $validated['findings'],
                'conclusion' => $validated['conclusion'] ?? null,
                'status' => $status,
                ...$this->validationStamp($status),
            ]);

            $this->attachReport($result, $validated['attachments'] ?? null);
            $this->syncRequestStatus($result->request, $status);

            return $result;
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function updateResult(RadiologyResult $result, array $validated): RadiologyResult
    {
        return DB::transaction(function () use ($result, $validated): RadiologyResult {
            $status = $this->normaliseStatus($validated['status'] ?? $result->getRawOriginal('status'));

            $result->update([
                'findings' => $validated['findings'],
                'conclusion' => $validated['conclusion'] ?? null,
                'status' => $status,
                ...$this->validationStamp($status, $result),
            ]);

            $this->attachReport($result, $validated['attachments'] ?? null);
            $this->syncRequestStatus($result->request, $status);

            return $result;
        });
    }

    /**
     * Sign an existing report and publish it to the ward.
     */
    public function validateResult(RadiologyResult $result): RadiologyResult
    {
        return DB::transaction(function () use ($result): RadiologyResult {
            $result->update([
                'status' => RadiologyStatus::Validated,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

            $this->syncRequestStatus($result->request, RadiologyStatus::Validated);

            return $result;
        });
    }

    /**
     * A result cannot sit on an unknown lifecycle value: the column defaults to
     * `draft`, which predates the enum, so anything unrecognised is treated as
     * work in progress.
     */
    private function normaliseStatus(mixed $status): RadiologyStatus
    {
        if ($status instanceof RadiologyStatus) {
            return $status;
        }

        return is_string($status)
            ? RadiologyStatus::tryFrom($status) ?? RadiologyStatus::InProgress
            : RadiologyStatus::InProgress;
    }

    /**
     * Validation stamps belong to a signed report; dropping back to a draft
     * clears them so the archive never claims an unsigned report was verified.
     *
     * @return array<string, mixed>
     */
    private function validationStamp(RadiologyStatus $status, ?RadiologyResult $existing = null): array
    {
        if ($status !== RadiologyStatus::Validated) {
            return ['validated_by' => null, 'validated_at' => null];
        }

        return [
            'validated_by' => $existing?->validated_by ?? auth()->id(),
            'validated_at' => $existing?->validated_at ?? now(),
        ];
    }

    /**
     * @param  array<int, UploadedFile>|null  $attachments
     */
    private function attachReport(RadiologyResult $result, ?array $attachments): void
    {
        foreach ($attachments ?? [] as $file) {
            if ($file instanceof UploadedFile) {
                $result->addMedia($file)->toMediaCollection('report');
            }
        }
    }

    private function syncRequestStatus(?RadiologyRequest $request, RadiologyStatus $resultStatus): void
    {
        $request?->update(['status' => $resultStatus->value]);
    }
}
