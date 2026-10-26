<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

// The DemoRequest model uses the 'central' connection.
// We resolve it to the default connection so RefreshDatabase's
// transaction covers all writes in SQLite :memory: tests.
beforeEach(function () {
    resolveCentralToDefault();
});

// ─── Page Rendering ───────────────────────────────────────────

describe('Landing Pages', function () {
    $pages = [
        ['home', 'Home'],
        ['features', 'Features'],
        ['pricing', 'Pricing'],
        ['about', 'About'],
        ['contact', 'Contact'],
    ];

    it('renders the :route page with the :component component')
        ->with($pages)
        ->expect(function (string $route, string $component) {
            $response = $this->get(route($route));

            $response->assertOk();
            $response->assertInertia(fn ($page) => $page->component($component));
        });
});

// ─── Demo Request Submission ──────────────────────────────────

describe('Demo Request Submission', function () {
    $validData = [
        'name' => 'Dr. Jane Smith',
        'email' => 'jane@hospital.com',
        'company' => 'City General Hospital',
        'message' => 'We are interested in a demo for our 200-bed facility.',
    ];

    it('creates a demo request and redirects with a success message', function () use ($validData) {
        $response = $this->from(route('contact'))->post(route('contact.submit'), $validData);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('demo_requests', [
            'name' => 'Dr. Jane Smith',
            'email' => 'jane@hospital.com',
            'company' => 'City General Hospital',
            'status' => 'new',
        ]);
    });

    it('accepts optional company and message fields', function () {
        $response = $this->from(route('contact'))->post(route('contact.submit'), [
            'name' => 'Dr. Jane Smith',
            'email' => 'jane@hospital.com',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('demo_requests', [
            'name' => 'Dr. Jane Smith',
            'email' => 'jane@hospital.com',
            'company' => null,
            'message' => null,
        ]);
    });
});

// ─── Validation ───────────────────────────────────────────────

describe('Demo Request Validation', function () {
    it('requires name and email fields', function () {
        $response = $this->post(route('contact.submit'), [
            'name' => '',
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
    });

    it('rejects invalid email formats', function () {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Dr. Jane Smith',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    });

    $validationCases = [
        'name exceeds 100 chars' => [
            ['name' => str_repeat('A', 101), 'email' => 'test@example.com'],
            ['name'],
        ],
        'email exceeds 200 chars' => [
            ['name' => 'Dr. Smith', 'email' => str_repeat('a', 191).'@toolong.com'],
            ['email'],
        ],
        'company exceeds 200 chars' => [
            ['name' => 'Dr. Smith', 'email' => 'test@example.com', 'company' => str_repeat('X', 201)],
            ['company'],
        ],
        'message exceeds 2000 chars' => [
            ['name' => 'Dr. Smith', 'email' => 'test@example.com', 'message' => str_repeat('Y', 2001)],
            ['message'],
        ],
    ];

    it('validates :label')
        ->with($validationCases)
        ->expect(function (array $data, array $expectedErrors) {
            $response = $this->post(route('contact.submit'), $data);

            $response->assertSessionHasErrors($expectedErrors);
        });
});

// ─── Tenant Subdomains ────────────────────────────────────────

describe('Tenant Subdomains', function () {
    it('sends hospital visitors from the marketing pages into the app')
        ->with(['/', '/features', '/pricing', '/about', '/contact'])
        ->expect(function (string $uri) {
            $response = $this->get('http://city-hospital.example.test'.$uri);

            $response->assertRedirect('http://city-hospital.example.test/dashboard');
        });

    it('does not accept demo requests on a hospital subdomain', function () {
        $response = $this->post('http://city-hospital.example.test/contact', [
            'name' => 'Dr. Jane Smith',
            'email' => 'jane@hospital.com',
        ]);

        $response->assertRedirect('http://city-hospital.example.test/dashboard');
        $this->assertDatabaseCount('demo_requests', 0);
    });
});
