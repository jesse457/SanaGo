<?php

declare(strict_types=1);

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

/**
 * A page that blows up the way a real one does: the cash desk writing a column
 * the tenant database does not have yet.
 */
beforeEach(function () {
    $this->withoutMiddleware([PreventRequestForgery::class]);

    Route::middleware('web')->get('/__error-probe', function (): never {
        throw new RuntimeException('SQLSTATE[HY000]: General error: 1 no such column: paid_amount');
    });
});

it('shows what actually failed on the error page while debugging', function () {
    $this->get('/__error-probe')
        ->assertStatus(500)
        ->assertInertia(fn ($page) => $page
            ->component('Error')
            ->where('status', 500)
            ->where('debug.exception', RuntimeException::class)
            ->where('debug.message', 'SQLSTATE[HY000]: General error: 1 no such column: paid_amount')
            ->where('debug.request', fn (string $request): bool => str_contains($request, '/__error-probe'))
            ->where('debug.location', fn (?string $location): bool => $location !== null
                && str_contains($location, 'ErrorPageTest.php'))
            ->where('debug.trace', fn (Collection $trace): bool => $trace->isNotEmpty()
                && array_key_exists('call', $trace->first())
                && array_key_exists('location', $trace->first()))
            ->etc());
});

it('keeps the exception out of the page when debugging is off', function () {
    config(['app.debug' => false]);

    $this->get('/__error-probe')
        ->assertStatus(500)
        ->assertInertia(fn ($page) => $page
            ->component('Error')
            ->where('status', 500)
            ->where('debug', null)
            ->etc());
});

it('renders the branded page for a missing route', function () {
    $this->get('/this-page-does-not-exist')
        ->assertStatus(404)
        ->assertInertia(fn ($page) => $page
            ->component('Error')
            ->where('status', 404)
            ->etc());
});
