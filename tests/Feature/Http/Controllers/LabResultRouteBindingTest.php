<?php

declare(strict_types=1);

use App\Enums\LabRequestStatus;
use App\Enums\LabResultStatus;
use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->labTech = tap(User::factory()->create())->assignRole('lab_tech');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

it('resolves the route model binding for the update route', function () {
    $result = LabResult::factory()->create();

    $this->actingAs($this->labTech)
        ->get(route('results.edit', $result))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('LabTests/Results/Enter'));
});

it('updates a lab result through the {result} route binding', function () {
    $result = LabResult::factory()->create();
    $this->assertDatabaseHas('lab_results', ['id' => $result->id]);

    $this->actingAs($this->labTech)
        ->put(route('results.update', $result), [
            'lab_request_id' => $result->lab_request_id,
            'status' => LabResultStatus::ABNORMAL->value,
            'result_date' => now()->toDateString(),
            'results_text' => 'Elevated white cell count.',
        ])
        ->assertRedirect(route('results.index'))
        ->assertSessionHas('success');

    expect($result->fresh()->status)->toBe(LabResultStatus::ABNORMAL);
});

it('marks the lab request completed instead of copying the result status', function () {
    $labRequest = LabRequest::factory()->create([
        'status' => LabRequestStatus::PENDING->value,
    ]);
    $result = LabResult::factory()->create([
        'lab_request_id' => $labRequest->id,
        'status' => LabResultStatus::NORMAL->value,
    ]);

    $this->actingAs($this->labTech)
        ->put(route('results.update', $result), [
            'lab_request_id' => $labRequest->id,
            'status' => LabResultStatus::CRITICAL->value,
            'result_date' => now()->toDateString(),
            'results_text' => 'Critical values detected.',
        ])
        ->assertRedirect(route('results.index'));

    // A LabResultStatus value stored here used to raise a ValueError when the
    // LabRequestStatus cast read it back.
    expect($labRequest->fresh()->status)->toBe(LabRequestStatus::COMPLETED);
});

it('deletes a lab result through the {result} route binding', function () {
    $result = LabResult::factory()->create();

    $this->actingAs($this->labTech)
        ->delete(route('results.destroy', $result))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(LabResult::count())->toBe(0);
});
