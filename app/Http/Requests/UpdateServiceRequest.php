<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-finance');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'code' => ['required', 'string', 'max:50', Rule::unique('services', 'code')->ignore($this->route('service'))],
            'category' => ['required', 'string', Rule::in([
                'consultation', 'procedure', 'lab', 'radiology', 'imaging',
                'medication', 'bed', 'nursing', 'emergency', 'other',
            ])],
            'description' => ['nullable', 'string', 'max:1000'],
            'source_model' => ['nullable', 'string', 'max:200'],
            'source_id' => ['nullable', 'integer'],
            'default_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'requires_upfront_payment' => ['boolean'],
        ];
    }
}
