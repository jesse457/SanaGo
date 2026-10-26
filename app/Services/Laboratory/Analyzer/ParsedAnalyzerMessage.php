<?php

namespace App\Services\Laboratory\Analyzer;

/**
 * A frame received from a bench analyzer, in the vocabulary the app uses.
 *
 * Every reader (ASTM, HL7, CSV) produces this, so matching and persistence do
 * not care which instrument or protocol the numbers came from.
 */
final readonly class ParsedAnalyzerMessage
{
    /**
     * @param  array<int, AnalyteResult>  $results
     */
    public function __construct(
        public string $format,
        public array $results,
        public ?string $analyzerName = null,
        public ?string $analyzerSerial = null,
        /** Sample / accession id — the join key to `lab_requests`. */
        public ?string $sampleId = null,
        /** Patient identifier the instrument echoed back. */
        public ?string $patientId = null,
        public ?string $patientName = null,
        public ?string $measuredAt = null,
        /** Non-empty when the frame could not be read. */
        public ?string $error = null,
    ) {}

    public function failed(): bool
    {
        return $this->error !== null;
    }

    public function isEmpty(): bool
    {
        return $this->results === [];
    }

    public function hasCriticalResult(): bool
    {
        foreach ($this->results as $result) {
            if ($result->isCritical()) {
                return true;
            }
        }

        return false;
    }

    public function hasAbnormalResult(): bool
    {
        foreach ($this->results as $result) {
            if ($result->isAbnormal()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'format' => $this->format,
            'analyzer_name' => $this->analyzerName,
            'analyzer_serial' => $this->analyzerSerial,
            'sample_id' => $this->sampleId,
            'patient_id' => $this->patientId,
            'patient_name' => $this->patientName,
            'measured_at' => $this->measuredAt,
            'error' => $this->error,
            'results' => array_map(
                static fn (AnalyteResult $result): array => $result->toArray(),
                $this->results,
            ),
        ];
    }
}
