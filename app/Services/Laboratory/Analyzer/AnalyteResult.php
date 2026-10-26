<?php

namespace App\Services\Laboratory\Analyzer;

/**
 * One measured analyte as the instrument reported it.
 *
 * `flag` is normalised to the ASTM set (`N`, `H`, `L`, `HH`, `LL`, `A`) so the
 * HL7 and CSV readers can reuse it: HL7 sends `N/H/L/HH/LL`, CSV exports
 * usually send `*`/`normal`/`High`/`LOW`.
 */
final readonly class AnalyteResult
{
    public function __construct(
        public string $code,
        public string $name,
        public string $value,
        public ?string $unit = null,
        public ?string $referenceRange = null,
        public string $flag = 'N',
        public ?string $measuredAt = null,
    ) {}

    /**
     * The line this analyte becomes inside `lab_results.results_text`.
     *
     * The shape (`Name: value unit (range)`) is the one the result entry grid
     * already parses in `LabTests/components/ParameterResultsTable`, so values
     * that arrive from a machine are editable in the same grid a human uses.
     */
    public function toResultLine(): string
    {
        $line = $this->name.': '.$this->value;

        if ($this->unit !== null && $this->unit !== '') {
            $line .= ' '.$this->unit;
        }

        if ($this->referenceRange !== null && $this->referenceRange !== '') {
            $line .= ' ('.$this->referenceRange.')';
        }

        return $line;
    }

    /** True when the instrument flagged the value outside the reference range. */
    public function isAbnormal(): bool
    {
        return in_array($this->flag, ['H', 'L', 'HH', 'LL', 'A'], true);
    }

    /** True when the value is a panic/critical result that must be called out. */
    public function isCritical(): bool
    {
        return in_array($this->flag, ['HH', 'LL'], true);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'value' => $this->value,
            'unit' => $this->unit,
            'reference_range' => $this->referenceRange,
            'flag' => $this->flag,
            'measured_at' => $this->measuredAt,
        ];
    }
}
