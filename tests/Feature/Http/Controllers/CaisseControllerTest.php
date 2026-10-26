<?php

declare(strict_types=1);

use App\Enums\CashShiftStatus;
use App\Enums\PaymentMethod;
use App\Models\CashPayment;
use App\Models\CashShift;
use App\Models\Patient;
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
    $this->cashier = tap(User::factory()->create())->assignRole('reception');
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
        PreventRequestForgery::class,
    ]);
});

/**
 * Open a shift for the cashier, straight through the model.
 */
function openShiftFor(User $user, float $openingBalance = 10000): CashShift
{
    return CashShift::create([
        'user_id' => $user->id,
        'payment_point' => 'Main',
        'opening_balance' => $openingBalance,
        'status' => CashShiftStatus::Open->value,
        'opened_at' => now(),
    ]);
}

it('closes a shift with the counted balance and records the variance', function () {
    $shift = openShiftFor($this->cashier);

    $this->actingAs($this->cashier)
        ->post(route('caisse.close'), ['closing_balance' => 12000])
        ->assertRedirect()
        ->assertSessionHas('success');

    $shift->refresh();

    expect($shift->status)->toBe(CashShiftStatus::Closed)
        ->and((float) $shift->closing_balance)->toEqual(12000.00)
        ->and((float) $shift->expected_total)->toEqual(10000.00)
        ->and((float) $shift->variance)->toEqual(2000.00);
});

it('falls back to the expected total when no balance is posted', function () {
    $shift = openShiftFor($this->cashier, 5000);

    $this->actingAs($this->cashier)
        ->post(route('caisse.close'))
        ->assertRedirect()
        ->assertSessionHas('success');

    $shift->refresh();

    expect((float) $shift->closing_balance)->toEqual(5000.00)
        ->and((float) $shift->variance)->toEqual(0.00);
});

it('rejects a negative closing balance', function () {
    $shift = openShiftFor($this->cashier);

    $this->actingAs($this->cashier)
        ->post(route('caisse.close'), ['closing_balance' => -500])
        ->assertSessionHasErrors(['closing_balance']);

    expect($shift->fresh()->status)->toBe(CashShiftStatus::Open);
});

it('rejects a non numeric closing balance', function () {
    $shift = openShiftFor($this->cashier);

    $this->actingAs($this->cashier)
        ->post(route('caisse.close'), ['closing_balance' => 'not-a-number'])
        ->assertSessionHasErrors(['closing_balance']);

    expect($shift->fresh()->status)->toBe(CashShiftStatus::Open);
});

it('records a payment with a positive amount and a patient', function () {
    $shift = openShiftFor($this->cashier);
    $patient = Patient::factory()->create();

    $this->actingAs($this->cashier)
        ->post(route('caisse.pay'), [
            'patient_id' => $patient->id,
            'amount_paid' => 2500,
            'payment_method' => PaymentMethod::Cash->value,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(CashPayment::where('cash_shift_id', $shift->id)->count())->toBe(1);
});

it('rejects a zero payment amount', function () {
    $shift = openShiftFor($this->cashier);
    $patient = Patient::factory()->create();

    $this->actingAs($this->cashier)
        ->post(route('caisse.pay'), [
            'patient_id' => $patient->id,
            'amount_paid' => 0,
            'payment_method' => PaymentMethod::Cash->value,
        ])
        ->assertSessionHasErrors(['amount_paid']);

    expect(CashPayment::where('cash_shift_id', $shift->id)->count())->toBe(0);
});

it('rejects a payment with neither a patient nor an invoice', function () {
    $shift = openShiftFor($this->cashier);

    $this->actingAs($this->cashier)
        ->post(route('caisse.pay'), [
            'amount_paid' => 1000,
            'payment_method' => PaymentMethod::Cash->value,
        ])
        ->assertSessionHasErrors(['patient_id']);

    expect(CashPayment::where('cash_shift_id', $shift->id)->count())->toBe(0);
});

it('renders the cash shift ledger with open and closed shifts', function () {
    openShiftFor($this->cashier);

    CashShift::create([
        'user_id' => $this->cashier->id,
        'payment_point' => 'Main',
        'opening_balance' => 10000,
        'closing_balance' => 12500,
        'expected_total' => 12250,
        'variance' => 250,
        'status' => CashShiftStatus::Closed->value,
        'opened_at' => now()->subDay(),
        'closed_at' => now()->subDay()->addHours(6),
    ]);

    $this->actingAs($this->cashier)
        ->get(route('caisse.shifts'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Caisse/Shifts')
            ->has('shifts.data', 2)
            ->where('filters.status', 'all')
            ->where('shifts.data.0.status', 'open')
            ->where('shifts.data.1.variance', 250)
        );
});

it('filters the cash shift ledger by status', function () {
    openShiftFor($this->cashier);

    CashShift::create([
        'user_id' => $this->cashier->id,
        'payment_point' => 'Main',
        'opening_balance' => 10000,
        'status' => CashShiftStatus::Closed->value,
        'opened_at' => now()->subDay(),
        'closed_at' => now()->subDay()->addHours(6),
    ]);

    $this->actingAs($this->cashier)
        ->get(route('caisse.shifts', ['status' => 'open']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('shifts.data', 1)
            ->where('shifts.data.0.status', 'open')
        );
});
