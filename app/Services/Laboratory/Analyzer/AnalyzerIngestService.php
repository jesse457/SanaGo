<?php

namespace App\Services\Laboratory\Analyzer;

use App\Enums\LabRequestStatus;
use App\Enums\LabResultStatus;
use App\Models\LabAnalyzerMessage;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * AnalyzerIngestService — from raw instrument bytes to a verified result row.
 *
 * Two entry points share the same matching rules:
 *
 * - `preview()` reads a frame and reports what *would* happen, without writing.
 *   The result entry screen uses it so a tech can look at the numbers before
 *   they touch the record.
 * - `ingest()` does the same, then writes the result and keeps the raw frame.
 *   This is what a serial-to-HTTP bridge (or an analyzer with a TCP/HL7
 *   interface) hits when a run finishes.
 *
 * Matching order, most reliable first:
 *   1. `lab_requests.accession_number` == the sample id the instrument echoed.
 *   2. The patient id the instrument echoed (`patients.patient_uid`) plus the
 *      most recent request that is still open.
 * A frame that matches nothing is stored as `unmatched` rather than dropped:
 * the values are real, so they must be recoverable by hand.
 */
class AnalyzerIngestService
{
    public function __construct(private readonly LabAnalyzerReader $reader) {}

    /**
     * Read a frame and resolve it against the request queue. Writes nothing.
     *
     * @return array{
     *     format: string,
     *     analyzer_name: string|null,
     *     sample_id: string|null,
     *     patient_name: string|null,
     *     measured_at: string|null,
     *     status: string,
     *     error: string|null,
     *     request: array<string, mixed>|null,
     *     analytes: array<int, array<string, mixed>>,
     *     results_text: string
     * }
     */
    public function preview(string $payload, ?string $format = null): array
    {
        $parsed = $this->reader->read($payload, $format);
        $request = $parsed->failed() ? null : $this->matchRequest($parsed);

        return $this->describe($parsed, $request);
    }

    /**
     * Store the frame and, when it matches an order, the result.
     *
     * @throws Throwable
     */
    public function ingest(string $payload, ?string $format = null, ?User $importedBy = null): LabAnalyzerMessage
    {
        $parsed = $this->reader->read($payload, $format);
        $hash = hash('sha256', trim($payload));

        // A bridge that retries on timeout must not create a second result.
        $existing = LabAnalyzerMessage::query()->where('message_hash', $hash)->first();

        if ($existing !== null) {
            return $existing;
        }

        return DB::transaction(function () use ($payload, $parsed, $hash, $importedBy): LabAnalyzerMessage {
            $request = $parsed->failed() ? null : $this->matchRequest($parsed);

            $message = LabAnalyzerMessage::create([
                'message_hash' => $hash,
                'format' => $parsed->format,
                'analyzer_name' => $parsed->analyzerName,
                'analyzer_serial' => $parsed->analyzerSerial,
                'sample_id' => $parsed->sampleId,
                'status' => $this->statusFor($parsed, $request),
                'raw_payload' => $payload,
                'parsed' => $parsed->toArray(),
                'lab_request_id' => $request?->id,
                'imported_by' => $importedBy?->id,
                'received_at' => now(),
                'notes' => $parsed->error,
            ]);

            if ($request === null) {
                return $message;
            }

            $result = $this->persistResult($request, $parsed, $message);

            $message->update([
                'status' => 'imported',
                'lab_result_id' => $result->id,
            ]);

            return $message->refresh();
        });
    }

    /**
     * Find the order a frame belongs to.
     */
    public function matchRequest(ParsedAnalyzerMessage $parsed): ?LabRequest
    {
        if ($parsed->sampleId !== null) {
            $byAccession = LabRequest::query()
                ->where('accession_number', $parsed->sampleId)
                ->first();

            if ($byAccession !== null) {
                return $byAccession;
            }
        }

        if ($parsed->patientId !== null) {
            return $this->latestOpenRequestForPatientUid($parsed->patientId);
        }

        return null;
    }

    /**
     * Write the values onto the order.
     *
     * The text follows the `Name: value unit (range)` convention the result
     * grid parses, so an imported run stays editable by hand. A request that
     * already has a result is updated in place — analyzers re-run samples, and
     * the newest run is the one that counts.
     */
    private function persistResult(
        LabRequest $request,
        ParsedAnalyzerMessage $parsed,
        LabAnalyzerMessage $message,
    ): LabResult {
        $lines = array_map(
            static fn (AnalyteResult $analyte): string => $analyte->toResultLine(),
            $parsed->results,
        );

        $attributes = [
            'status' => $this->resultStatus($parsed),
            // The readers hand back `Y-m-d H:i:s`; the column is a date.
            'result_date' => $parsed->measuredAt !== null
                ? substr($parsed->measuredAt, 0, 10)
                : now()->toDateString(),
            'results_text' => implode("\n", $lines),
            'source' => 'analyzer',
            'analyzer_name' => $parsed->analyzerName,
            'analyzer_sample_id' => $parsed->sampleId,
            'analyzer_analytes' => $parsed->toArray()['results'],
        ];

        $result = $request->labResult()->updateOrCreate(
            ['lab_request_id' => $request->id],
            $attributes,
        );

        // Labs that require a supervisor's verification keep the order open:
        // the values are filed, but the request still shows in the queue.
        if (config('lab-analyzer.auto_complete_request', true)) {
            $request->update(['status' => LabRequestStatus::COMPLETED->value]);
        }

        return $result;
    }

    /**
     * The result status is derived from what the instrument flagged, so a
     * panic value can never land as "normal".
     */
    private function resultStatus(ParsedAnalyzerMessage $parsed): string
    {
        return match (true) {
            $parsed->hasCriticalResult() => LabResultStatus::CRITICAL->value,
            $parsed->hasAbnormalResult() => LabResultStatus::ABNORMAL->value,
            default => LabResultStatus::NORMAL->value,
        };
    }

    private function statusFor(ParsedAnalyzerMessage $parsed, ?LabRequest $request): string
    {
        return match (true) {
            $parsed->failed() => 'rejected',
            $request === null => 'unmatched',
            default => 'received',
        };
    }

    private function latestOpenRequestForPatientUid(string $patientUid): ?LabRequest
    {
        return LabRequest::query()
            ->whereHas('patient', function ($query) use ($patientUid): void {
                $query->where('patient_uid', $patientUid);
            })
            ->whereIn('status', [
                LabRequestStatus::PENDING->value,
                LabRequestStatus::IN_PROGRESS->value,
            ])
            ->latest('request_date')
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function describe(ParsedAnalyzerMessage $parsed, ?LabRequest $request): array
    {
        $analytes = array_map(
            static fn (AnalyteResult $analyte): array => $analyte->toArray(),
            $parsed->results,
        );

        return [
            'format' => $parsed->format,
            'analyzer_name' => $parsed->analyzerName,
            'sample_id' => $parsed->sampleId,
            'patient_name' => $parsed->patientName,
            'measured_at' => $parsed->measuredAt,
            'status' => $this->statusFor($parsed, $request),
            'error' => $parsed->error,
            'request' => $request === null ? null : [
                'id' => $request->id,
                'accession_number' => $request->accession_number,
                'patient' => $request->patient === null ? null : [
                    'id' => $request->patient->id,
                    'first_name' => $request->patient->first_name,
                    'last_name' => $request->patient->last_name,
                    'patient_uid' => $request->patient->patient_uid,
                ],
                'test_definition' => $request->testDefinition === null ? null : [
                    'id' => $request->testDefinition->id,
                    'test_name' => $request->testDefinition->test_name,
                    'code' => $request->testDefinition->code,
                ],
            ],
            'analytes' => $analytes,
            'results_text' => implode("\n", array_map(
                static fn (AnalyteResult $analyte): string => $analyte->toResultLine(),
                $parsed->results,
            )),
        ];
    }
}
