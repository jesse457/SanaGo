<?php

namespace App\Services\Laboratory\Analyzer;

/**
 * HL7 v2.x ORU^R01 — the result message shape used by the analyzers that ship
 * a network/LIS interface (Sysmex XN/CS, Mindray BS series with the HL7 option,
 * Roche cobas, Abbott Architect) and by any lab running a middleware box.
 *
 *   MSH|^~\&|BC-5150|LAB|SanaGo|HOSPITAL|20261026101500||ORU^R01|MSG00012|P|2.3.1
 *   PID|1||CM-0098123||MBALLA^Jean^Paul||19850312|M
 *   OBR|1||ACC-20261026-0007|CBC^Full blood count|||20261026101500
 *   OBX|1|NM|WBC^White blood cells^L||6.8|10^3/uL|4.0-11.0|N|||F
 *   OBX|2|NM|HGB^Haemoglobin^L||9.4|g/dL|11.5-16.5|LL|||F
 *
 * MLLP framing (<VT> … <FS><CR>) is stripped, so both a framed TCP stream and
 * a flat dump from the analyzer's serial log parse identically.
 */
class Hl7Parser
{
    private const FIELD = '|';

    private const COMPONENT = '^';

    private const REPETITION = '~';

    public function supports(string $payload): bool
    {
        return preg_match('/^[\x0b\s]*MSH\|/m', $payload) === 1
            || str_contains($payload, 'OBX|');
    }

    public function parse(string $payload): ParsedAnalyzerMessage
    {
        $segments = $this->segments($payload);

        if ($segments === []) {
            return new ParsedAnalyzerMessage(
                format: 'hl7',
                results: [],
                error: 'No HL7 segments found in the payload.',
            );
        }

        $analyzerName = null;
        $analyzerSerial = null;
        $sampleId = null;
        $patientId = null;
        $patientName = null;
        $measuredAt = null;
        $results = [];

        foreach ($segments as $fields) {
            $segment = strtoupper($fields[0]);

            if ($segment === 'MSH') {
                // MSH-3 is the sending application (the instrument or its
                // middleware); MSH-4 is the facility, kept as the fallback so
                // a lab that only fills one still gets a readable name.
                $analyzerName = $this->component($fields[2], 0)
                    ?? $this->component($fields[3], 0);

                continue;
            }

            if ($segment === 'PID') {
                // PID-3 = patient identifier list, PID-5 = patient name.
                $patientId = $this->component($fields[3], 0) ?? $this->component($fields[3], 1);
                $patientName = $this->readPatientName($fields[5]);

                continue;
            }

            if ($segment === 'OBR') {
                // OBR-3 = filler order number (the sample/accession id).
                $sampleId ??= $this->component($fields[3], 0)
                    ?? $this->component($fields[2], 0);
                $measuredAt ??= $this->readTimestamp($fields[7] ?? null);

                continue;
            }

            if ($segment === 'OBX') {
                $analyte = $this->readObx($fields);

                if ($analyte === null) {
                    continue;
                }

                $results[] = $analyte;
                $measuredAt ??= $analyte->measuredAt;
            }
        }

        if ($results === []) {
            return new ParsedAnalyzerMessage(
                format: 'hl7',
                results: [],
                analyzerName: $analyzerName,
                analyzerSerial: $analyzerSerial,
                sampleId: $sampleId,
                patientId: $patientId,
                patientName: $patientName,
                error: 'The HL7 message carried no observation (OBX) segments.',
            );
        }

        return new ParsedAnalyzerMessage(
            format: 'hl7',
            results: $results,
            analyzerName: $analyzerName,
            analyzerSerial: $analyzerSerial,
            sampleId: $sampleId,
            patientId: $patientId,
            patientName: $patientName,
            measuredAt: $measuredAt,
        );
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function segments(string $payload): array
    {
        // MLLP: <VT> start block, <FS><CR> end block.
        $clean = str_replace(["\x0b", "\x1c"], ['', ''], $payload);
        $lines = preg_split('/\r\n|\r|\n/', $clean) ?: [];

        $segments = [];

        foreach ($lines as $line) {
            $line = trim($line, " \t");

            if ($line === '') {
                continue;
            }

            // A plain split keeps MSH aligned: index 0 is the segment name,
            // index 1 holds MSH-2 (the encoding characters), and MSH-3 is the
            // sending application at index 2.
            $segments[] = explode(self::FIELD, $line);
        }

        return $segments;
    }

    /**
     * @param  array<int, string>  $fields
     */
    private function readObx(array $fields): ?AnalyteResult
    {
        // OBX|set_id|value_type|observation_id|sub_id|value|units|reference|flag|…
        $observationId = $fields[3] ?? '';
        $value = trim($fields[5] ?? '');
        $unit = trim($fields[6] ?? '');
        $reference = trim($fields[7] ?? '');
        $flag = strtoupper(trim($fields[8] ?? ''));
        $observedAt = $fields[14] ?? null;

        if ($value === '') {
            return null;
        }

        // OBX-3 is a coded element: code^display name^coding system.
        $code = $this->component($observationId, 0) ?? '';
        $name = $this->component($observationId, 1) ?? $code;

        if ($code === '' && $name === '') {
            return null;
        }

        return new AnalyteResult(
            code: strtoupper($code),
            name: $name,
            value: $value,
            unit: $unit === '' ? null : $unit,
            referenceRange: $reference === '' ? null : str_replace('~', ' ', $reference),
            flag: $this->normaliseFlag($flag),
            measuredAt: $this->readTimestamp($observedAt),
        );
    }

    private function readPatientName(?string $field): ?string
    {
        if ($field === null || $field === '') {
            return null;
        }

        // PID-5 repeats are separated by ~; each is family^given^middle.
        $first = explode(self::REPETITION, $field)[0];
        $family = $this->component($first, 0);
        $given = $this->component($first, 1);

        if ($family !== null && $given !== null) {
            return trim($given.' '.$family);
        }

        return $family ?? $given;
    }

    private function component(?string $field, int $index): ?string
    {
        if ($field === null || $field === '') {
            return null;
        }

        $parts = explode(self::COMPONENT, $field);
        $value = trim($parts[$index] ?? '');

        return $value === '' ? null : $value;
    }

    private function normaliseFlag(string $flag): string
    {
        return match ($flag) {
            'HH', 'AH' => 'HH',
            'H' => 'H',
            'LL', 'AL' => 'LL',
            'L' => 'L',
            'A', 'AA' => 'A',
            default => 'N',
        };
    }

    private function readTimestamp(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})?(\d{2})?(\d{2})?$/', $value, $m) === 1) {
            return sprintf(
                '%s-%s-%s %s:%s:%s',
                $m[1],
                $m[2],
                $m[3],
                ($m[4] ?? '') !== '' ? $m[4] : '00',
                ($m[5] ?? '') !== '' ? $m[5] : '00',
                ($m[6] ?? '') !== '' ? $m[6] : '00',
            );
        }

        return null;
    }
}
