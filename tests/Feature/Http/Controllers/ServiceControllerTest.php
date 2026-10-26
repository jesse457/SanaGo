<?php

declare(strict_types=1);

use App\Enums\ServiceCategory;
use App\Models\Service;
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

it('renders the catalogue with every module available as a tab', function () {
    Service::factory()->create(['name' => 'Consultation Générale', 'code' => 'CONS-GEN']);
    Service::factory()->create([
        'name' => 'Hémogramme (NFS)',
        'code' => 'LAB-NFS',
        'category' => ServiceCategory::Lab,
    ]);

    $this->actingAs($this->admin)
        ->get(route('finance.services.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Finance/Services/Index')
            ->has('services.data', 2)
            ->where('filters.search', '')
            ->where('filters.module', '')
            ->has('modules', 7)
            ->where('modules.0.value', 'consultation')
            ->etc());
});

it('searches the whole catalogue, not just the page on screen', function () {
    Service::factory()->create(['name' => 'Consultation Générale', 'code' => 'CONS-GEN']);
    Service::factory()->create([
        'name' => 'Hémogramme (NFS)',
        'code' => 'LAB-NFS',
        'category' => ServiceCategory::Lab,
    ]);

    $this->actingAs($this->admin)
        ->get(route('finance.services.index', ['search' => 'LAB-NFS']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Finance/Services/Index')
            ->has('services.data', 1)
            ->where('services.data.0.code', 'LAB-NFS')
            ->where('filters.search', 'LAB-NFS')
            ->etc());
});

it('filters the catalogue by module', function () {
    Service::factory()->create(['name' => 'Consultation Générale', 'code' => 'CONS-GEN']);
    Service::factory()->create([
        'name' => 'Hémogramme (NFS)',
        'code' => 'LAB-NFS',
        'category' => ServiceCategory::Lab,
    ]);

    $this->actingAs($this->admin)
        ->get(route('finance.services.index', ['module' => 'laboratory']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Finance/Services/Index')
            ->has('services.data', 1)
            ->where('services.data.0.code', 'LAB-NFS')
            ->where('filters.module', 'laboratory')
            ->etc());
});

it('keeps the filters on the pagination links', function () {
    Service::factory()->count(31)->create(['category' => ServiceCategory::Lab]);

    $this->actingAs($this->admin)
        ->get(route('finance.services.index', ['module' => 'laboratory']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('services.next_page_url', fn (?string $url): bool => $url !== null
                && str_contains($url, 'module=laboratory'))
            ->etc());
});

it('hands the create form every category, so the picker needs no second list', function () {
    $this->actingAs($this->admin)
        ->get(route('finance.services.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Finance/Services/Create')
            ->where('categories', ServiceCategory::values())
            ->etc());
});

it('hands the edit form every category, so the picker needs no second list', function () {
    $service = Service::factory()->create(['category' => ServiceCategory::Lab]);

    $this->actingAs($this->admin)
        ->get(route('finance.services.edit', $service->id))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Finance/Services/Edit')
            ->where('categories', ServiceCategory::values())
            ->where('service.id', $service->id)
            ->etc());
});
