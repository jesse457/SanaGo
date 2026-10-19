<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRadiologyResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-radiology');
    }

    public function rules(): array
    {
        return [
            'findings' => ['required', 'string'],
            'conclusion' => ['required', 'string'],
            'status' => ['nullable', 'string', 'in:completed,validated'],
        ];
    }
}
