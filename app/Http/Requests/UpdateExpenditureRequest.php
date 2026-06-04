<?php

namespace App\Http\Requests;

use App\Enums\ExpenditureCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenditureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-finance');
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(ExpenditureCategory::class)],
            'description' => ['required', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expenditure_date' => ['required', 'date'],
            'vendor' => ['nullable', 'string', 'max:200'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
