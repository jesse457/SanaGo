<?php

namespace App\Http\Requests;

use App\Enums\RadiologyStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRadiologyResultRequest extends FormRequest
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
            'status' => ['nullable', 'string', Rule::in(RadiologyStatus::values())],
            'findings' => ['required', 'string'],
            'conclusion' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
        ];
    }
}
