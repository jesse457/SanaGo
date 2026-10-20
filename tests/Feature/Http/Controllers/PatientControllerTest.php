<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();
    $this->seed(RoleSeeder::class);
    $this->admin = tap(User::factory()->create())->assignRole('admin');
    // Tenant routes use tenancy middleware. Disable so routes resolve in tests.
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

// ─── Index ─────────────────────────────────────────────────────────────────

describe('index', function () {

    it('renders the patients index with Inertia', function () {
        Patient::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('patients.index'))
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Patients/Index'));
    });

    it('lists patients on the index page', function () {
        Patient::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('patients.index'))
            ->assertInertia(fn ($p) => $p->component('Patients/Index')
                ->has('patients.data', 3)
            );
    });

    it('filters patients by first name search', function () {
        Patient::factory()->create(['first_name' => 'ZZZSearchUnique']);
        Patient::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('patients.index', ['search' => 'ZZZSearchUnique']))
            ->assertInertia(fn ($p) => $p->component('Patients/Index')
                ->has('patients.data', 1)
                ->where('filters.search', 'ZZZSearchUnique')
            );
    });

    it('filters patients by last name search', function () {
        Patient::factory()->create(['last_name' => 'ZzUniqueSearchName']);
        Patient::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('patients.index', ['search' => 'ZzUniqueSearchName']))
            ->assertInertia(fn ($p) => $p->component('Patients/Index')
                ->has('patients.data', 1)
            );
    });

    it('filters patients by patient_uid search', function () {
        $patient = Patient::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('patients.index', ['search' => $patient->patient_uid]))
            ->assertInertia(fn ($p) => $p->component('Patients/Index')
                ->has('patients.data', 1)
            );
    });

    it('returns empty results when search matches nothing', function () {
        Patient::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('patients.index', ['search' => 'zzzznonexistent']))
            ->assertInertia(fn ($p) => $p->component('Patients/Index')
                ->has('patients.data', 0)
            );
    });

    it('orders patients by latest first', function () {
        $old = Patient::factory()->create(['created_at' => now()->subDays(2)]);
        $new = Patient::factory()->create(['created_at' => now()->subDay()]);

        $this->actingAs($this->admin)
            ->get(route('patients.index'))
            ->assertInertia(fn ($p) => $p->component('Patients/Index')
                ->where('patients.data.0.id', $new->id)
                ->where('patients.data.1.id', $old->id)
            );
    });

    it('redirects index for unauthenticated users', function () {
        $this->get(route('patients.index'))->assertRedirect(route('login'));
    });

    it('blocks index for users without view-patients permission', function () {
        $this->actingAs(unauthorizedUser())
            ->get(route('patients.index'))
            ->assertForbidden();
    });

});

// ─── Create ────────────────────────────────────────────────────────────────

describe('create', function () {

    it('renders the create patient page', function () {
        $this->actingAs($this->admin)
            ->get(route('patients.create'))
            ->assertOk()
            ->assertInertia(fn ($p) => $p->component('Patients/Create'));
    });

    it('redirects create for unauthenticated users', function () {
        $this->get(route('patients.create'))->assertRedirect(route('login'));
    });

    it('blocks create for users without view-patients permission', function () {
        $this->actingAs(unauthorizedUser())
            ->get(route('patients.create'))
            ->assertForbidden();
    });

});

// ─── Store ─────────────────────────────────────────────────────────────────

describe('store', function () {

    it('creates a patient and redirects to index with a success message', function () {
        $this->actingAs($this->admin)
            ->post(route('patients.store'), [
                'first_name' => 'Chidi',
                'last_name' => 'Okonkwo',
                'email' => 'chidi@example.com',
                'gender' => 'male',
            ])
            ->assertRedirect(route('patients.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('patients', [
            'first_name' => 'Chidi',
            'last_name' => 'Okonkwo',
            'email' => 'chidi@example.com',
        ]);
    });

    it('generates a unique patient_uid on creation', function () {
        $this->actingAs($this->admin)
            ->post(route('patients.store'), [
                'first_name' => 'Chidi',
                'last_name' => 'Okonkwo',
            ]);

        $patient = Patient::where('first_name', 'Chidi')->first();

        expect($patient->patient_uid)->toMatch('/^PT-'.date('Y').'-[A-Z0-9]{6}$/');
    });

    it('requires first_name and last_name on store', function () {
        $this->actingAs($this->admin)
            ->post(route('patients.store'), [])
            ->assertSessionHasErrors(['first_name', 'last_name']);
    });

    it('validates email uniqueness on store', function () {
        Patient::factory()->create(['email' => 'used@example.com']);

        $this->actingAs($this->admin)
            ->post(route('patients.store'), [
                'first_name' => 'Chidi',
                'last_name' => 'Okonkwo',
                'email' => 'used@example.com',
            ])
            ->assertSessionHasErrors(['email']);
    });

    it('validates gender against valid enum values on store', function () {
        $this->actingAs($this->admin)
            ->post(route('patients.store'), [
                'first_name' => 'Chidi',
                'last_name' => 'Okonkwo',
                'gender' => 'invalid-gender',
            ])
            ->assertSessionHasErrors(['gender']);
    });

    it('accepts all optional fields on store', function () {
        $this->actingAs($this->admin)
            ->post(route('patients.store'), [
                'first_name' => 'Amina',
                'last_name' => 'Bello',
                'email' => 'amina@example.com',
                'phone' => '+2348012345678',
                'date_of_birth' => '1990-05-15',
                'gender' => 'female',
                'address' => '45 Ahmadu Bello Way, Abuja',
                'blood_type' => 'O+',
                'allergies' => 'Penicillin',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('patients', [
            'first_name' => 'Amina',
            'last_name' => 'Bello',
            'email' => 'amina@example.com',
            'phone' => '+2348012345678',
            'blood_type' => 'O+',
            'allergies' => 'Penicillin',
        ]);
    });

    it('rejects first_name exceeding 100 characters on store', function () {
        $this->actingAs($this->admin)
            ->post(route('patients.store'), [
                'first_name' => str_repeat('A', 101),
                'last_name' => 'Okonkwo',
            ])
            ->assertSessionHasErrors(['first_name']);
    });

    it('redirects store for unauthenticated users', function () {
        $this->post(route('patients.store'), [
            'first_name' => 'Chidi',
            'last_name' => 'Okonkwo',
        ])->assertRedirect(route('login'));
    });

    it('blocks store for users without create-patients permission', function () {
        $user = userWithPermission('view-patients');

        $this->actingAs($user)
            ->post(route('patients.store'), [
                'first_name' => 'Chidi',
                'last_name' => 'Okonkwo',
            ])
            ->assertForbidden();
    });

    it('blocks store for users without any permissions', function () {
        $this->actingAs(unauthorizedUser())
            ->post(route('patients.store'), [
                'first_name' => 'Chidi',
                'last_name' => 'Okonkwo',
            ])
            ->assertForbidden();
    });

});

// ─── Update ────────────────────────────────────────────────────────────────

describe('update', function () {

    it('updates a patient and redirects back with a success message', function () {
        $patient = Patient::factory()->create(['first_name' => 'Old']);

        $this->actingAs($this->admin)
            ->put(route('patients.update', $patient), [
                'first_name' => 'New',
                'last_name' => 'Name',
            ])
            ->assertRedirect();

        expect($patient->fresh()->first_name)->toBe('New');
    });

    it('requires first_name and last_name on update', function () {
        $patient = Patient::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('patients.update', $patient), [])
            ->assertSessionHasErrors(['first_name', 'last_name']);
    });

    it('allows a patient to keep the same email on update', function () {
        $patient = Patient::factory()->create(['email' => 'same@example.com']);

        $this->actingAs($this->admin)
            ->put(route('patients.update', $patient), [
                'first_name' => 'Updated',
                'last_name' => 'Name',
                'email' => 'same@example.com',
            ])
            ->assertRedirect();

        expect($patient->fresh()->email)->toBe('same@example.com');
    });

    it('validates email uniqueness on update when email is changed', function () {
        Patient::factory()->create(['email' => 'existing@example.com']);
        $patient = Patient::factory()->create(['email' => 'original@example.com']);

        $this->actingAs($this->admin)
            ->put(route('patients.update', $patient), [
                'first_name' => 'Updated',
                'last_name' => 'Name',
                'email' => 'existing@example.com',
            ])
            ->assertSessionHasErrors(['email']);
    });

    it('validates gender on update', function () {
        $patient = Patient::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('patients.update', $patient), [
                'first_name' => 'Updated',
                'last_name' => 'Name',
                'gender' => 'alien',
            ])
            ->assertSessionHasErrors(['gender']);
    });

    it('redirects update for unauthenticated users', function () {
        $patient = Patient::factory()->create();

        $this->put(route('patients.update', $patient), [
            'first_name' => 'New',
            'last_name' => 'Name',
        ])->assertRedirect(route('login'));
    });

    it('blocks update for users without edit-patients permission', function () {
        $patient = Patient::factory()->create();
        $user = userWithPermission('view-patients');

        $this->actingAs($user)
            ->put(route('patients.update', $patient), [
                'first_name' => 'New',
                'last_name' => 'Name',
            ])
            ->assertForbidden();
    });

});

// ─── Permission Boundary Tests ─────────────────────────────────────────────

describe('permission boundaries', function () {

    it('allows a user with create-patients to store a patient', function () {
        // The route middleware requires view-patients; the form request checks create-patients.
        $user = userWithPermission('view-patients');
        $user->givePermissionTo('create-patients');

        $this->actingAs($user)
            ->post(route('patients.store'), [
                'first_name' => 'Reception',
                'last_name' => 'Test',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('patients', ['first_name' => 'Reception']);
    });

    it('allows a user with edit-patients to update a patient', function () {
        // The route middleware requires view-patients; the form request checks edit-patients.
        $patient = Patient::factory()->create(['first_name' => 'Before']);
        $user = userWithPermission('view-patients');
        $user->givePermissionTo('edit-patients');

        $this->actingAs($user)
            ->put(route('patients.update', $patient), [
                'first_name' => 'After',
                'last_name' => 'Changed',
            ])
            ->assertRedirect();

        expect($patient->fresh()->first_name)->toBe('After');
    });

});
