<?php

namespace App\Http\Requests;

use App\Services\Laboratory\Analyzer\LabAnalyzerReader;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * One inbound analyzer frame.
 *
 * Two callers share this: a serial-to-HTTP bridge (or an analyzer with a
 * TCP/HL7 interface) posting the raw frame as the request body, and the result
 * entry screen posting it in `payload` after a tech pastes or uploads it. The
 * raw body is lifted into `payload` so a single set of rules covers both.
 */
class IngestAnalyzerMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // A machine has no session; the controller checks its bearer token.
        // A person must hold the same permission that lets them file a result.
        if ($this->user() === null) {
            return true;
        }

        return $this->user()->can('create-lab-results');
    }

    protected function prepareForValidation(): void
    {
        if (trim((string) $this->input('payload', '')) === '') {
            $raw = (string) $this->getContent();

            if (trim($raw) !== '') {
                $this->merge(['payload' => $raw]);
            }
        }

        if (! $this->filled('format')) {
            $this->merge(['format' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxKb = (int) config('lab-analyzer.max_payload_kb', 256);

        return [
            'payload' => 'required|string|max:'.($maxKb * 1024),
            'format' => ['nullable', 'string', Rule::in(LabAnalyzerReader::FORMATS)],
            'analyzer' => 'nullable|string|max:120',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payload.required' => 'No analyzer frame was received.',
            'payload.max' => 'That frame is larger than the configured analyzer limit.',
            'format.in' => 'Use one of: '.implode(', ', LabAnalyzerReader::FORMATS).'.',
        ];
    }

    /** The raw frame, whatever form it arrived in. */
    public function frame(): string
    {
        return (string) $this->validated('payload');
    }

    /** The declared format, or null to let the reader detect it. */
    public function frameFormat(): ?string
    {
        $format = $this->validated('format');

        return is_string($format) && $format !== '' ? $format : null;
    }
}
