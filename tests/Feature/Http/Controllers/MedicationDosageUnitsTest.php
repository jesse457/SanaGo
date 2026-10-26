<?php

declare(strict_types=1);

use App\Models\Medication;
use App\Models\User;
use App\Services\Pharmacy\InventoryService;
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
    $this->pharma = tap(User::factory()->create())->assignRole('pharma');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

it('stores dosage_units from the create form', function () {
    $this->actingAs($this->pharma)
        ->post(route('drugs.store'), [
            'name' => 'Doxycycline',
            'strength' => '100mg',
            'dosage_units' => 'Capsule',
            'min_stock_level' => 25,
        ])
        ->assertRedirect(route('drugs.index'))
        ->assertSessionHas('success');

    $medication = Medication::where('name', 'Doxycycline')->firstOrFail();

    expect($medication->dosage_units)->toBe('Capsule');
});

it('rejects the singular dosage_unit key that the form no longer sends', function () {
    $this->actingAs($this->pharma)
        ->post(route('drugs.store'), [
            'name' => 'Old Key Drug',
            'strength' => '250mg',
            'dosage_unit' => 'Tablet',
            'min_stock_level' => 10,
        ])
        ->assertSessionHasErrors(['dosage_units']);

    expect(Medication::where('name', 'Old Key Drug')->exists())->toBeFalse();
});

it('stores a drug with its first batch from the create form payload', function () {
    $this->actingAs($this->pharma)
        ->post(route('drugs.store'), [
            'name' => 'Cetirizine',
            'dosage_units' => 'ml',
            'min_stock_level' => '12',
            'description' => '',
            'batches' => [
                [
                    'batch_number' => 'CDC23',
                    'expiry_date' => '2026-09-28',
                    'quantity' => '34',
                    'purchase_price' => '2.50',
                    'sale_price' => '4.00',
                    'manufactured_date' => '',
                    'notes' => '',
                ],
            ],
        ])
        ->assertRedirect(route('drugs.index'))
        ->assertSessionHasNoErrors();

    $medication = Medication::where('name', 'Cetirizine')->firstOrFail();

    expect($medication->dosage_units)->toBe('ml')
        ->and($medication->min_stock_level)->toBe(12)
        ->and($medication->total_stock)->toBe(34);

    $batch = $medication->batches()->firstOrFail();

    expect($batch->batch_number)->toBe('CDC23')
        ->and($batch->current_quantity)->toBe(34);
});

it('maps dosage_units explicitly when the inventory service creates a medication', function () {
    $medication = app(InventoryService::class)->createMedication([
        'name' => 'Artemether',
        'dosage_units' => 'Tablet',
        'min_stock_level' => 40,
    ]);

    expect($medication->dosage_units)->toBe('Tablet');
});

it('accepts the same dosage_units key on update', function () {
    $medication = Medication::factory()->create([
        'name' => 'Amlodipine',
        'dosage_units' => 'Tablet',
    ]);

    $this->actingAs($this->pharma)
        ->put(route('drugs.update', $medication), [
            'name' => 'Amlodipine',
            'dosage_units' => 'Capsule',
            'min_stock_level' => 30,
        ])
        ->assertRedirect();

    expect($medication->fresh()->dosage_units)->toBe('Capsule');
});
