<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;

class UpdateTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-tenants') ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->route('tenant')?->getTenantKey();

        $currentDomainId = $tenantId === null
            ? null
            : Domain::query()->where('tenant_id', $tenantId)->value('id');

        return [
            'name' => ['required', 'string', 'max:255'],

            // The domain used to be read-only in the UI even though a typo in
            // it makes the hospital unreachable.
            'domain' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('domains', 'domain')->ignore($currentDomainId),
            ],
        ];
    }
}
