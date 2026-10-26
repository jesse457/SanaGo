<?php

namespace App\Services\Laboratory\Analyzer;

/**
 * ASTM E1394 (with its E1381 transport framing) — the protocol behind most of
 * the analyzers in service in Cameroonian labs.
 *
 * A session looks like this, one record per line, `\r` (sometimes `\n`)
 * terminated:
 *
 *   H|\^&|||Mindray^BC-5150^V1.2|||||||P|1|20261026101500
 *   P|1||CM-0098123||MBALLA^Jean^Paul||19850312|M
 *   O|1|ACC-20261026-0007||^^^CBC|R||||||||1
 *   R|1|^^^WBC|6.8|10^3/uL|4.0-11.0|N||F||||20261026101612
 *   R|2|^^^HGB|9.4|g/dL|11.5-16.5|LL||F||||20261026101612
 *   L|1|N
 *
 * Only the records that carry clinical meaning are read: `H` (analyzer
 * identity), `P` (patient), `O` (order, which carries the sample id) and `R`
 * (a measured analyte). `C`, `Q`, `M`, `S` and `L` are transport chatter.
 */
class AstmParser
{
    /** Field repeat delimiter used by nearly every E1394 sender. */
    private const FIELD = '|';

    private const COMPONENT = '^';

    public function supports(string $payload): bool
    {
        // A header record, or any result record, is unambiguous.
        return preg_match('/^\s*H[|\\\\^&]/m', $payload) === 1
            || preg_match('/^\s*R\|\d+\|/m', $payload) === 1;
    }

    public function parse(string $payload): ParsedAnalyzerMessage
    {
        $records = $this->records($payload);

        if ($records === []) {
            return new ParsedAnalyzerMessage(
                format: 'astm',
                results: [],
                error: 'No ASTM records found in the payload.',
            );
        }

        $analyzerName = null;
        $analyzerSerial = null;
        $patientId = null;
        $patientName = null;
        $sampleId = null;
        $measuredAt = null;
        $sessionAt = null;
        $results = [];

        foreach ($records as $fields) {
            $type = $fields[0];

            if ($type === 'H') {
                [$analyzerName, $analyzerSerial] = $this->readHeader($fields);
                // The header carries the session start; a result record's own
                // timestamp is what the clinician wants, so this is a fallback.
                $sessionAt = $this->readTimestamp($fields[13] ?? null);

                continue;
            }

            if ($type === 'P') {
                $patientId = $this->firstNonEmpty($fields[2] ?? null, $fields[3] ?? null);
                $patientName = $this->readPatientName($fields[5] ?? null);

                continue;
            }

            if ($type === 'O') {
                // O|seq|sample_id|instrument_sample|test_id|priority|…
                $sampleId = $this->firstNonEmpty($fields[2] ?? null, $fields[3] ?? null);

                continue;
            }

            if ($type === 'R') {
                $analyte = $this->readResult($fields);

                if ($analyte === null) {
                    continue;
                }

                $results[] = $analyte;
                $measuredAt ??= $analyte->measuredAt;
            }
        }

        if ($results === []) {
            return new ParsedAnalyzerMessage(
                format: 'astm',
                results: [],
                analyzerName: $analyzerName,
                analyzerSerial: $analyzerSerial,
                sampleId: $sampleId,
                patientId: $patientId,
                patientName: $patientName,
                error: 'The ASTM frame carried no result (R) records.',
            );
        }

        return new ParsedAnalyzerMessage(
            format: 'astm',
            results: $results,
            analyzerName: $analyzerName,
            analyzerSerial: $analyzerSerial,
            sampleId: $sampleId,
            patientId: $patientId,
            patientName: $patientName,
            measuredAt: $measuredAt ?? $sessionAt,
        );
    }

    /**
     * Split a raw session into typed records.
     *
     * E1381 wraps frames in <STX> … <ETX>/<ETB> with a trailing checksum, and
     * vendors disagree on the line terminator, so the control characters are
     * normalised before splitting.
     *
     * @return array<int, array<int, string>> Exploded fields, record type first.
     */
    private function records(string $payload): array
    {
        $clean = str_replace(
            ["\x02", "\x03", "\x04", "\x05", "\x06", "\x15", "\x17"],
            ['', '', '', '', '', '', ''],
            $payload,
        );

        // <CR> is the E1394 terminator; some senders only emit <LF>.
        $lines = preg_split('/\r\n|\r|\n/', $clean) ?: [];

        $records = [];

        foreach ($lines as $line) {
            $line = trim($line, " \t");

            if ($line === '') {
                continue;
            }

            // Strip the E1381 frame number + checksum the transport prepends to
            // the header record ("1H|…" / "H|…9700").
            $line = preg_replace('/^\d{1,2}(?=H\|)/', '', $line) ?? $line;
            $line = preg_replace('/\|\d{1,2}[0-9A-F]{2}$/', '|', $line) ?? $line;

            $type = strtoupper($line[0]);

            if (! in_array($type, ['H', 'P', 'O', 'R', 'C', 'Q', 'M', 'S', 'L'], true)) {
                continue;
            }

            // The record type stays at index 0 so the remaining indexes match
            // ASTM field numbers: R-2 = sequence (1), R-3 = test id (2),
            // R-4 = value (3), and so on. That is what every vendor emits.
            $records[] = explode(self::FIELD, $line);
        }

        return $records;
    }

    /**
     * @param  array<int, string>  $fields
     * @return array{0: ?string, 1: ?string}
     */
    private function readHeader(array $fields): array
    {
        // H|delimiters|message_id|…|sender_name^sender_model^version|…
        // Index 1 is the delimiter definition itself ("\^&"), never identity.
        foreach (array_slice($fields, 2) as $field) {
            if (! str_contains($field, self::COMPONENT)) {
                continue;
            }

            $parts = array_values(array_filter(
                array_map('trim', explode(self::COMPONENT, $field)),
                static fn (string $part): bool => preg_match('/[A-Za-z0-9]/', $part) === 1,
            ));

            if ($parts === []) {
                continue;
            }

            // Model codes trail the vendor name; the last part of the identity
            // field is the firmware version, which is useful as a serial.
            $name = implode(' ', array_slice($parts, 0, 2));
            $serial = count($parts) > 2 ? end($parts) : null;

            return [$name, $serial];
        }

        return [null, null];
    }

    /**
     * @param  array<int, string>  $fields
     */
    private function readResult(array $fields): ?AnalyteResult
    {
        // R|seq|universal_test_id|value|unit|reference|flag|…|timestamp
        $testId = $fields[2] ?? '';
        $value = trim($fields[3] ?? '');
        $unit = trim($fields[4] ?? '');
        $reference = trim($fields[5] ?? '');
        $flag = strtoupper(trim($fields[6] ?? ''));
        $measuredAt = $fields[12] ?? null;

        // A blank value is an unmeasured analyte: analyzers still emit the row,
        // and a real "0" must survive (chemistry panels legitimately report it).
        if ($value === '') {
            return null;
        }

        $components = array_map('trim', explode(self::COMPONENT, $testId));
        $code = $this->firstNonEmpty(
            $components[3] ?? null,
            $components[2] ?? null,
            $components[0] ?? null,
        ) ?? '';

        $name = $this->firstNonEmpty(
            $components[1] ?? null,
            $components[4] ?? null,
            $code,
        ) ?? $code;

        if ($code === '' && $name === '') {
            return null;
        }

        return new AnalyteResult(
            code: strtoupper($code),
            name: $name,
            value: $value,
            unit: $unit === '' ? null : $unit,
            referenceRange: $reference === '' ? null : $reference,
            flag: $this->normaliseFlag($flag),
            measuredAt: $this->readTimestamp($measuredAt),
        );
    }

    private function readPatientName(?string $field): ?string
    {
        if ($field === null || $field === '') {
            return null;
        }

        // P|…|last^first^middle^suffix
        $parts = array_map('trim', explode(self::COMPONENT, $field));
        $parts = array_values(array_filter(
            $parts,
            static fn (string $part): bool => $part !== '',
        ));

        if ($parts === []) {
            return null;
        }

        // "MBALLA^Jean" reads better as "Jean MBALLA" on screen.
        if (count($parts) >= 2) {
            return trim($parts[1].' '.$parts[0]);
        }

        return $parts[0];
    }

    /**
     * Normalise the numerous spellings analyzers use for an abnormal flag into
     * the ASTM set (N / H / L / HH / LL / A).
     */
    private function normaliseFlag(string $flag): string
    {
        $normalised = strtoupper(preg_replace('/[^A-Za-z_]/', '', $flag) ?? '');

        return match ($normalised) {
            'HH', 'AH', 'CRITICAL', 'PANIC' => 'HH',
            'H', 'HIGH', 'ABOVE' => 'H',
            'LL', 'AL' => 'LL',
            'L', 'LOW', 'BELOW' => 'L',
            'A', 'AA', 'ABNORMAL' => 'A',
            default => 'N',
        };
    }

    private function readTimestamp(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        // YYYYMMDDHHMMSS — the ASTM date/time field.
        if (preg_match('/^(\d{4})(\d{2})(\d{2})(\d{2})?(\d{2})?(\d{2})?$/', $value, $m) === 1) {
            return sprintf(
                '%s-%s-%s %s:%s:%s',
                $m[1],
                $m[2],
                $m[3],
                $m[4] !== '' ? $m[4] : '00',
                ($m[5] ?? '') !== '' ? $m[5] : '00',
                ($m[6] ?? '') !== '' ? $m[6] : '00',
            );
        }

        return null;
    }

    private function firstNonEmpty(?string ...$values): ?string
    {
        foreach ($values as $value) {
            if ($value !== null && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }
}
