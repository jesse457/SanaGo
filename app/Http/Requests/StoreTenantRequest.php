<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-tenants') ?? false;
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'unique:tenants,id'],
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', Rule::unique('domains', 'domain')],

            // First hospital administrator. The tenant database is seeded with
            // a placeholder admin, so the operator supplies real credentials
            // here instead of sharing a demo account.
            'admin_name' => ['nullable', 'string', 'max:255'],
            'admin_email' => ['nullable', 'email', 'max:255'],
            'admin_password' => ['nullable', 'string', 'min:8'],

            // Plan assignment at provisioning time.
            'plan_id' => ['nullable', 'integer', Rule::exists('plans', 'id')],
            'billing_cycle' => ['nullable', 'in:monthly,yearly'],
            'trial' => ['boolean'],

            // Demo data must be opt-in: a real hospital must not receive fake
            // patients and invoices.
            'demo_data' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'Use lowercase letters, numbers and dashes only (e.g. city-hospital).',
            'domain.unique' => 'That domain already belongs to another hospital.',
            'admin_password.min' => 'Use at least 8 characters for the administrator password.',
        ];
    }
}
