<?php

namespace App\Http\Requests;

use App\Enums\ExpenditureCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenditureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view-finance');
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(ExpenditureCategory::class)],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expenditure_date' => ['required', 'date'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'recorded_by' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * `recorded_by` is NOT NULL with a FK to `users`, and an expenditure is
     * always attributed to the authenticated user, never to the client.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['recorded_by' => auth()->id()]);
    }
}
