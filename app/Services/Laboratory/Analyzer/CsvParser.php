<?php

namespace App\Services\Laboratory\Analyzer;

/**
 * CSV / TSV run reports.
 *
 * The fallback that works when an instrument has no licensed LIS interface —
 * which is the common case for the mid-range analyzers used in Cameroon
 * (Biosystems, Genrui, Rayto, Mindray chemistry): the operator exports the run
 * to a USB stick, or the analyzer's PC software drops one file per run, and
 * that file is imported here.
 *
 * Headers vary wildly, so columns are matched by alias rather than position:
 *
 *   sample_id,test_code,test_name,value,unit,reference_range,flag,measured_at
 *   ACC-20261026-0007,GLU,Glucose,6.4,mmol/L,3.9-5.5,H,2026-10-26 10:12
 *
 * Delimiter is detected (comma, semicolon — the French Excel default — or tab).
 */
class CsvParser
{
    public function supports(string $payload): bool
    {
        $firstLine = strtok($payload, "\r\n");

        if ($firstLine === false || ! str_contains($firstLine, $this->sniffDelimiter($payload))) {
            return false;
        }

        // A header row naming at least one recognised column.
        foreach (['sample', 'accession', 'test', 'analyte', 'value', 'result'] as $alias) {
            if (stripos($firstLine, $alias) !== false) {
                return true;
            }
        }

        return false;
    }

    public function parse(string $payload): ParsedAnalyzerMessage
    {
        $delimiter = $this->sniffDelimiter($payload);
        $lines = preg_split('/\r\n|\r|\n/', trim($payload)) ?: [];

        if (count($lines) < 2) {
            return new ParsedAnalyzerMessage(
                format: 'csv',
                results: [],
                error: 'The CSV export has a header but no result rows.',
            );
        }

        $headers = $this->normaliseHeaders(
            // Escape char disabled: RFC 4180 has none, and PHP 8.4 deprecates
            // relying on the default backslash escape.
            str_getcsv(array_shift($lines), $delimiter, '"', '')
        );
        $index = $this->columnIndex($headers);

        if ($index['value'] === null || $index['code'] === null) {
            return new ParsedAnalyzerMessage(
                format: 'csv',
                results: [],
                error: 'The CSV header has no test-code or value column.',
            );
        }

        $results = [];
        $sampleId = null;
        $patientId = null;
        $patientName = null;
        $measuredAt = null;

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $cells = str_getcsv($line, $delimiter, '"', '');

            $value = $this->cell($cells, $index['value']);

            if ($value === null) {
                continue;
            }

            $code = $this->cell($cells, $index['code']) ?? '';
            $name = $this->cell($cells, $index['name']) ?? $code;

            if ($code === '' && $name === '') {
                continue;
            }

            $sampleId ??= $this->cell($cells, $index['sample']);
            $patientId ??= $this->cell($cells, $index['patient']);
            $patientName ??= $this->cell($cells, $index['patient_name']);
            $measuredAt ??= $this->cell($cells, $index['measured_at']);

            $results[] = new AnalyteResult(
                code: strtoupper($code),
                name: $name,
                value: $value,
                unit: $this->cell($cells, $index['unit']),
                referenceRange: $this->cell($cells, $index['reference']),
                flag: $this->normaliseFlag($this->cell($cells, $index['flag']) ?? ''),
                measuredAt: $this->cell($cells, $index['measured_at']),
            );
        }

        if ($results === []) {
            return new ParsedAnalyzerMessage(
                format: 'csv',
                results: [],
                sampleId: $sampleId,
                patientId: $patientId,
                error: 'Every CSV row was empty or had no value.',
            );
        }

        return new ParsedAnalyzerMessage(
            format: 'csv',
            results: $results,
            sampleId: $sampleId,
            patientId: $patientId,
            patientName: $patientName,
            measuredAt: $measuredAt,
        );
    }

    /**
     * Semicolon is included because a French-locale Windows install (the
     * default in Cameroon) exports CSV with `;` as the list separator.
     */
    private function sniffDelimiter(string $payload): string
    {
        $firstLine = strtok($payload, "\r\n") ?: '';

        $counts = [
            ',' => substr_count($firstLine, ','),
            ';' => substr_count($firstLine, ';'),
            "\t" => substr_count($firstLine, "\t"),
        ];

        arsort($counts);
        $delimiter = array_key_first($counts);

        return $counts[$delimiter] > 0 ? $delimiter : ',';
    }

    /**
     * @param  array<int, string|null>  $headers
     * @return array<int, string>
     */
    private function normaliseHeaders(array $headers): array
    {
        return array_map(
            static fn (?string $header): string => strtolower(
                preg_replace('/[^a-z0-9]+/i', '', (string) $header) ?? '',
            ),
            $headers,
        );
    }

    /**
     * Resolve the columns the parser needs, by alias.
     *
     * @param  array<int, string>  $headers
     * @return array<string, int|null>
     */
    private function columnIndex(array $headers): array
    {
        $aliases = [
            'code' => ['testcode', 'code', 'analytecode', 'test', 'analyte', 'parameter', 'parametre', 'paramètre'],
            'name' => ['testname', 'name', 'analytename', 'testlabel', 'parametername', 'libelle', 'libellé'],
            'value' => ['value', 'result', 'resultat', 'résultat', 'measuredvalue', 'reading', 'valeur'],
            'unit' => ['unit', 'units', 'unite', 'unité'],
            'reference' => ['referencerange', 'refrange', 'reference', 'range', 'normalrange', 'valeursnormales'],
            'flag' => ['flag', 'abnormalflag', 'status', 'interpretation', 'drapeau'],
            'sample' => ['sampleid', 'sample', 'accession', 'accessionnumber', 'barcode', 'samplebarcode', 'echantillon', 'échantillon'],
            'patient' => ['patientid', 'patient', 'patientuid', 'pid', 'patientnumber'],
            'patient_name' => ['patientname', 'nameofpatient', 'nompatient', 'nomdupattient'],
            'measured_at' => ['measuredat', 'date', 'datetime', 'resultdate', 'analyzedat', 'time', 'dateanalyse'],
        ];

        $index = [];

        foreach ($aliases as $field => $candidates) {
            $index[$field] = null;

            foreach ($candidates as $candidate) {
                $position = array_search($candidate, $headers, true);

                if ($position !== false) {
                    $index[$field] = $position;

                    break;
                }
            }
        }

        return $index;
    }

    /**
     * @param  array<int, string|null>  $cells
     */
    private function cell(array $cells, ?int $index): ?string
    {
        if ($index === null) {
            return null;
        }

        $value = trim((string) ($cells[$index] ?? ''));

        return $value === '' ? null : $value;
    }

    private function normaliseFlag(string $flag): string
    {
        $normalised = strtoupper(preg_replace('/[^A-Za-z_*]/', '', $flag) ?? '');

        return match ($normalised) {
            'HH', 'CRITICAL', 'PANIC' => 'HH',
            'H', 'HIGH', 'ABOVE' => 'H',
            'LL' => 'LL',
            'L', 'LOW', 'BELOW' => 'L',
            '*', 'A', 'ABNORMAL' => 'A',
            default => 'N',
        };
    }
}
