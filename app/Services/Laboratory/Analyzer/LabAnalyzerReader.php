<?php

namespace App\Services\Laboratory\Analyzer;

use InvalidArgumentException;

/**
 * LabAnalyzerReader — the front door for instrument output.
 *
 * Cameroonian hospital labs run a mix of instruments and only some of them
 * have an LIS interface licence, so three transports are accepted and the
 * format is detected when the caller does not know it:
 *
 * - `astm` — ASTM E1381/E1394 over serial (Mindray BC-3000/BC-5150, Sysmex
 *   KX-21/XP-100, Biosystems A15, Genrui KT-6300, URIT, Rayto)
 * - `hl7`  — HL7 v2 ORU^R01 over TCP/MLLP (Sysmex XN, Mindray BS with HL7,
 *   Roche cobas, Abbott Architect)
 * - `csv`  — a run export to USB or a shared folder
 */
class LabAnalyzerReader
{
    public const FORMATS = ['astm', 'hl7', 'csv'];

    public function __construct(
        private readonly AstmParser $astm,
        private readonly Hl7Parser $hl7,
        private readonly CsvParser $csv,
    ) {}

    public function detect(string $payload): string
    {
        return match (true) {
            $this->hl7->supports($payload) => 'hl7',
            $this->astm->supports($payload) => 'astm',
            $this->csv->supports($payload) => 'csv',
            default => 'astm',
        };
    }

    /**
     * @param  string|null  $format  One of self::FORMATS, or null to auto-detect.
     */
    public function read(string $payload, ?string $format = null): ParsedAnalyzerMessage
    {
        $payload = trim($payload);

        if ($payload === '') {
            return new ParsedAnalyzerMessage(
                format: $format ?? 'astm',
                results: [],
                error: 'The payload is empty.',
            );
        }

        $autoDetected = $format === null;
        $format = $this->normaliseFormat($format ?? $this->detect($payload));

        $parsed = $this->run($format, $payload);

        // Detection is a heuristic; if the guessed reader found nothing, give
        // the other two a chance before reporting an unreadable frame. An
        // explicit format is honoured as-is, because the operator knows the
        // instrument.
        if ($autoDetected && $parsed->isEmpty()) {
            foreach (self::FORMATS as $fallback) {
                if ($fallback === $format) {
                    continue;
                }

                $retry = $this->run($fallback, $payload);

                if (! $retry->isEmpty()) {
                    return $retry;
                }
            }
        }

        return $parsed;
    }

    private function run(string $format, string $payload): ParsedAnalyzerMessage
    {
        return match ($format) {
            'astm' => $this->astm->parse($payload),
            'hl7' => $this->hl7->parse($payload),
            'csv' => $this->csv->parse($payload),
        };
    }

    private function normaliseFormat(string $format): string
    {
        $format = strtolower(trim($format));

        if (! in_array($format, self::FORMATS, true)) {
            throw new InvalidArgumentException(
                "Unsupported analyzer format [{$format}]. Use one of: ".implode(', ', self::FORMATS).'.',
            );
        }

        return $format;
    }
}
