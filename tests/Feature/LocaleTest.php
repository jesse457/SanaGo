<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure lang files exist for tests
    if (! File::exists(lang_path('en.json'))) {
        File::put(lang_path('en.json'), json_encode(['common' => ['save' => 'Save']]));
    }
    if (! File::exists(lang_path('fr.json'))) {
        File::put(lang_path('fr.json'), json_encode(['common' => ['save' => 'Sauvegarder']]));
    }
});

test('locale defaults to en when no session or browser preference', function () {
    $response = $this->get('/login');

    $response->assertSessionHas('locale', 'en');
    expect(App::getLocale())->toBe('en');
});

test('locale is set from browser Accept-Language header', function () {
    $response = $this->withHeader('Accept-Language', 'fr')->get('/login');

    $response->assertSessionHas('locale', 'fr');
    expect(App::getLocale())->toBe('fr');
});

test('locale is set from session when available', function () {
    Session::put('locale', 'fr');

    $response = $this->withHeader('Accept-Language', 'en')->get('/login');

    $response->assertSessionHas('locale', 'fr');
    expect(App::getLocale())->toBe('fr');
});

test('locale falls back to config default for unsupported locales', function () {
    $response = $this->withHeader('Accept-Language', 'de')->get('/login');

    $response->assertSessionHas('locale', 'en');
    expect(App::getLocale())->toBe('en');
});

test('locale switch endpoint validates allowed locales', function () {
    $response = $this->post('/locale', ['locale' => 'de']);

    $response->assertSessionHasErrors('locale');
});

test('locale switch endpoint sets locale in session', function () {
    $response = $this->post('/locale', ['locale' => 'fr']);

    $response->assertRedirect();
    expect(session('locale'))->toBe('fr');
});

test('locale switch persists to authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/locale', ['locale' => 'fr']);

    $response->assertRedirect();
    expect($user->fresh()->locale)->toBe('fr');
});

test('translations are shared via Inertia', function () {
    $response = $this->get('/login');

    $response->assertInertia(fn ($page) => $page
        ->has('locale')
        ->has('translations')
        ->where('locale', 'en')
    );
});

test('translations change when locale switches', function () {
    Session::put('locale', 'fr');

    $response = $this->get('/login');

    $response->assertInertia(fn ($page) => $page
        ->where('locale', 'fr')
    );
});

test('authenticated page loads with translations', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession(['locale' => 'en'])
        ->get('/profile');

    $response->assertInertia(fn ($page) => $page
        ->where('locale', 'en')
        ->has('translations')
    );
});
