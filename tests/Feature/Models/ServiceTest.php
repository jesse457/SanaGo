<?php

declare(strict_types=1);

use App\Enums\ServiceCategory;
use App\Models\Service;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('can create a service', function () {
    $service = Service::create([
        'name' => 'Consultation Générale',
        'code' => 'CONS-GEN',
        'category' => ServiceCategory::Consultation,
        'default_price' => 3000,
        'is_active' => true,
        'requires_upfront_payment' => true,
    ]);

    expect($service->name)->toBe('Consultation Générale')
        ->and($service->code)->toBe('CONS-GEN')
        ->and($service->category)->toBe(ServiceCategory::Consultation)
        ->and($service->default_price)->toEqual(3000.00);
});

it('casts category to ServiceCategory enum', function () {
    $service = Service::factory()->create(['category' => ServiceCategory::Radiology]);

    expect($service->category)->toBeInstanceOf(ServiceCategory::class)
        ->and($service->category)->toBe(ServiceCategory::Radiology);
});

it('casts boolean fields correctly', function () {
    $service = Service::factory()->create([
        'is_active' => true,
        'requires_upfront_payment' => false,
    ]);

    expect($service->is_active)->toBeTrue()
        ->and($service->requires_upfront_payment)->toBeFalse();
});

it('casts default_price to decimal', function () {
    $service = Service::factory()->create(['default_price' => 7500.50]);

    expect($service->default_price)->toEqual(7500.50);
});

it('scope active returns only active services', function () {
    Service::factory()->count(3)->create(['is_active' => true]);
    Service::factory()->count(2)->create(['is_active' => false]);

    expect(Service::active()->count())->toBe(3);
});

it('scope upfront returns services requiring upfront payment', function () {
    Service::factory()->count(2)->create(['requires_upfront_payment' => true]);
    Service::factory()->count(3)->create(['requires_upfront_payment' => false]);

    expect(Service::upfront()->count())->toBe(2);
});

it('scope postPay returns services not requiring upfront payment', function () {
    Service::factory()->count(2)->create(['requires_upfront_payment' => true]);
    Service::factory()->count(3)->create(['requires_upfront_payment' => false]);

    expect(Service::postPay()->count())->toBe(3);
});

it('factory creates valid service', function () {
    $service = Service::factory()->create();

    expect($service->name)->not->toBeEmpty()
        ->and($service->code)->not->toBeEmpty()
        ->and($service->category)->toBeInstanceOf(ServiceCategory::class)
        ->and($service->default_price)->toBeNumeric()
        ->and($service->is_active)->toBeBool();
});

it('factory has consultation state', function () {
    $service = Service::factory()->consultation()->create();

    expect($service->category)->toBe(ServiceCategory::Consultation);
});

it('factory has lab state', function () {
    $service = Service::factory()->lab()->create();

    expect($service->category)->toBe(ServiceCategory::Lab);
});

it('factory has radiology state', function () {
    $service = Service::factory()->radiology()->create();

    expect($service->category)->toBe(ServiceCategory::Radiology);
});

it('maps every catalog category to a billing module', function () {
    expect(ServiceCategory::Consultation->module())->toBe('consultation')
        ->and(ServiceCategory::Lab->module())->toBe('laboratory')
        ->and(ServiceCategory::Radiology->module())->toBe('radiology')
        ->and(ServiceCategory::Imaging->module())->toBe('radiology')
        ->and(ServiceCategory::Medication->module())->toBe('pharmacy')
        ->and(ServiceCategory::Bed->module())->toBe('admission')
        ->and(ServiceCategory::Procedure->module())->toBe('surgery')
        ->and(ServiceCategory::Other->module())->toBe('other');
});

it('exposes the module labels the counter works with', function () {
    $modules = ServiceCategory::modules();

    expect(array_column($modules, 'value'))->toBe([
        'consultation', 'laboratory', 'radiology', 'pharmacy',
        'admission', 'surgery', 'other',
    ]);
});

it('enforces unique code constraint', function () {
    Service::create([
        'name' => 'Test 1',
        'code' => 'UNIQUE-001',
        'category' => ServiceCategory::Other,
        'default_price' => 1000,
    ]);

    Service::create([
        'name' => 'Test 2',
        'code' => 'UNIQUE-001',
        'category' => ServiceCategory::Other,
        'default_price' => 2000,
    ]);
})->throws(QueryException::class);
