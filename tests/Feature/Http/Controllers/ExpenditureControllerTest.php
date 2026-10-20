<?php

declare(strict_types=1);

use App\Enums\ExpenditureCategory;
use App\Models\Expenditure;
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
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

it('records an expenditure for the authenticated user and redirects to the finance page', function () {
    $this->actingAs($this->admin)
        ->post(route('expenditures.store'), [
            'category' => ExpenditureCategory::UTILITIES->value,
            'description' => 'Generator diesel',
            'amount' => 25000,
            'expenditure_date' => now()->toDateString(),
        ])
        ->assertRedirect(route('finance.expenditures'))
        ->assertSessionHas('success');

    $expenditure = Expenditure::firstOrFail();

    expect($expenditure->recorded_by)->toBe($this->admin->id)
        ->and((float) $expenditure->amount)->toEqual(25000.00);
});

it('rejects an invalid expenditure category on store', function () {
    $this->actingAs($this->admin)
        ->post(route('expenditures.store'), [
            'category' => 'not-a-real-category',
            'description' => 'Bad category',
            'amount' => 1000,
            'expenditure_date' => now()->toDateString(),
        ])
        ->assertSessionHasErrors(['category']);

    expect(Expenditure::count())->toBe(0);
});

it('updates an expenditure and redirects to the finance page', function () {
    $expenditure = Expenditure::create([
        'category' => ExpenditureCategory::SUPPLIES->value,
        'description' => 'Original description',
        'amount' => 5000,
        'expenditure_date' => now()->toDateString(),
        'recorded_by' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
        ->put(route('expenditures.update', $expenditure), [
            'category' => ExpenditureCategory::EQUIPMENT->value,
            'description' => 'Updated description',
            'amount' => 7500,
            'expenditure_date' => now()->toDateString(),
        ])
        ->assertRedirect(route('finance.expenditures'))
        ->assertSessionHas('success');

    $expenditure->refresh();

    expect($expenditure->description)->toBe('Updated description')
        ->and($expenditure->category)->toBe(ExpenditureCategory::EQUIPMENT)
        ->and((float) $expenditure->amount)->toEqual(7500.00);
});

it('keeps the enum check on update', function () {
    $expenditure = Expenditure::create([
        'category' => ExpenditureCategory::SUPPLIES->value,
        'description' => 'Original description',
        'amount' => 5000,
        'expenditure_date' => now()->toDateString(),
        'recorded_by' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
        ->put(route('expenditures.update', $expenditure), [
            'category' => 'invalid-on-update',
            'description' => 'Should not persist',
            'amount' => 7500,
            'expenditure_date' => now()->toDateString(),
        ])
        ->assertSessionHasErrors(['category']);

    expect($expenditure->fresh()->description)->toBe('Original description');
});

it('deletes an expenditure and redirects to the finance page', function () {
    $expenditure = Expenditure::create([
        'category' => ExpenditureCategory::MAINTENANCE->value,
        'description' => 'Broken window',
        'amount' => 1200,
        'expenditure_date' => now()->toDateString(),
        'recorded_by' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
        ->delete(route('expenditures.destroy', $expenditure))
        ->assertRedirect(route('finance.expenditures'))
        ->assertSessionHas('success');

    expect(Expenditure::count())->toBe(0);
});
