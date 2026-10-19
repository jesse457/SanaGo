<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

// Note: Vitals index page component is not yet implemented — only Create.tsx exists

it('creates a vital record', function () {
    $patient = Patient::factory()->create();
    $this->actingAs($this->admin)->post(route('vitals.store'), [
        'patient_id' => $patient->id,
        'recorded_by' => $this->admin->id,
        'recorded_at' => now()->toDateTimeString(),
        'temperature' => 37.2,
        'systolic_bp' => 120,
        'diastolic_bp' => 80,
        'heart_rate' => 72,
        'oxygen_saturation' => 98,
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('vitals', ['patient_id' => $patient->id]);
});

it('validates required fields on store', function () {
    $this->actingAs($this->admin)->post(route('vitals.store'), [])
        ->assertSessionHasErrors(['patient_id', 'recorded_at', 'temperature']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('vitals.index'))->assertForbidden();
});
