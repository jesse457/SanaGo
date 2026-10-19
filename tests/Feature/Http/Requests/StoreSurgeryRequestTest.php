<?php

declare(strict_types=1);

use App\Enums\UrgencyLevel;
use App\Http\Requests\StoreSurgeryRequest;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->patient = Patient::factory()->create();
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('requires at least one of surgeon_id or surgeon_name', function () {
    $request = new StoreSurgeryRequest;

    $validator = Validator::make([
        'patient_id' => $this->patient->id,
        'surgery_name' => 'Appendectomy',
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        'urgency' => UrgencyLevel::NORMAL->value,
        'surgery_fee' => 5000,
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('surgeon_name'))->toBeTrue();
});

it('passes validation when surgeon_name is provided without surgeon_id', function () {
    $request = new StoreSurgeryRequest;

    $validator = Validator::make([
        'patient_id' => $this->patient->id,
        'surgeon_name' => 'Dr. Smith',
        'anesthetist_name' => 'Dr. Jones',
        'surgery_name' => 'Appendectomy',
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        'urgency' => UrgencyLevel::NORMAL->value,
        'surgery_fee' => 5000,
    ], $request->rules());

    expect($validator->fails())->toBeFalse();
});

it('returns custom error message for missing surgeon', function () {
    $request = new StoreSurgeryRequest;

    $validator = Validator::make([
        'patient_id' => $this->patient->id,
        'surgery_name' => 'Appendectomy',
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        'urgency' => UrgencyLevel::NORMAL->value,
        'surgery_fee' => 5000,
    ], $request->rules(), $request->messages());

    expect($validator->errors()->first('surgeon_name'))
        ->toBe('Please select a surgeon or enter an external surgeon name.');
});
