<?php

declare(strict_types=1);

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    $this->withoutMiddleware([
        \Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain::class,
        \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the invoices index', function () {
    Invoice::factory()->count(2)->create();
    $this->actingAs($this->admin)->get(route('finance.invoices.index'))
        ->assertOk()->assertInertia(fn ($p) => $p->component('Finance/Invoices/Index'));
});

it('creates an invoice', function () {
    $patient = Patient::factory()->create();
    $this->actingAs($this->admin)->post(route('finance.invoices.store'), [
        'patient_id' => $patient->id,
        'invoice_date' => now()->toDateString(),
        'due_date' => now()->addDays(30)->toDateString(),
        'status' => 'pending',
        'items' => [['item_type' => 'consultation', 'description' => 'Consultation', 'quantity' => 1, 'unit_price' => 5000, 'total_price' => 5000]],
    ])->assertRedirect()->assertSessionHas('success');
    $this->assertDatabaseHas('invoices', ['patient_id' => $patient->id]);
});

it('validates required fields on store', function () {
    $this->actingAs($this->admin)->post(route('finance.invoices.store'), [])
        ->assertSessionHasErrors(['patient_id', 'items']);
});

it('blocks unauthorized users', function () {
    $this->actingAs(User::factory()->create())->get(route('finance.invoices.index'))->assertForbidden();
});
