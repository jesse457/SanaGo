<?php

namespace App\Http\Requests;

use App\Enums\RadiologyStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRadiologyResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-radiology');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'request_id' => ['required', 'integer', 'exists:radiology_requests,id'],
            'status' => ['nullable', 'string', Rule::in(RadiologyStatus::values())],
            'findings' => ['required', 'string'],
            'conclusion' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'request_id.exists' => 'The imaging request this report belongs to no longer exists.',
        ];
    }
}
