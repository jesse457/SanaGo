<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The counter's "pay service" action: a patient, the services being paid for,
 * and the money handed over.
 */
class StoreSettlementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-caisse');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            // Either already-billed charges or catalogue services must be
            // present; the controller checks that the combination is non-empty.
            'item_ids' => ['nullable', 'array'],
            'item_ids.*' => ['integer', 'exists:invoice_items,id'],
            'service_ids' => ['array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'amount_tendered' => ['required', 'numeric', 'min:0'],
            // Idempotency key generated per counter attempt.
            'client_uuid' => ['nullable', 'string', 'max:64'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function settlementPayload(): array
    {
        return $this->validated();
    }
}
