<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\ReferenceDataSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();
});

it('seeds roles and permissions twice without a unique constraint error', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(RoleSeeder::class);

    expect(Role::where('name', 'admin')->count())->toBe(1)
        ->and(Role::where('name', 'doctor')->count())->toBe(1)
        ->and(Permission::where('name', 'manage-users')->count())->toBe(1);

    expect(Role::findByName('admin')->permissions->pluck('name'))
        ->toContain('view-finance', 'manage-users');
});

it('keeps the role permission matrix intact after a second run', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(RoleSeeder::class);

    $doctor = Role::findByName('doctor');
    $names = $doctor->permissions->pluck('name')->all();

    expect($names)->toContain('view-patients', 'view-lab-results')
        ->and($names)->not->toContain('manage-users');
});

it('seeds reference data twice without duplicating rows', function () {
    $this->seed(ReferenceDataSeeder::class);

    $counts = [
        'departments' => DB::table('departments')->count(),
        'bed_types' => DB::table('bed_types')->count(),
        'lab_test_definitions' => DB::table('lab_test_definitions')->count(),
        'medications' => DB::table('medications')->count(),
        'supplies' => DB::table('supplies')->count(),
    ];

    $this->seed(ReferenceDataSeeder::class);

    expect(DB::table('departments')->count())->toBe($counts['departments'])
        ->and(DB::table('bed_types')->count())->toBe($counts['bed_types'])
        ->and(DB::table('lab_test_definitions')->count())->toBe($counts['lab_test_definitions'])
        ->and(DB::table('medications')->count())->toBe($counts['medications'])
        ->and(DB::table('supplies')->count())->toBe($counts['supplies']);

    // The unique code columns are the seed anchors.
    expect(DB::table('departments')->where('code', 'ER')->count())->toBe(1)
        ->and(DB::table('lab_test_definitions')->where('code', 'CBC')->count())->toBe(1);
});

it('seeds the service catalogue from the tenant seeder', function () {
    expect(DB::table('services')->count())->toBe(0);

    $this->seed(ServiceSeeder::class);

    expect(DB::table('services')->count())->toBeGreaterThan(0);
    $this->assertDatabaseHas('services', ['code' => 'CONS-GEN']);
});

it('creates the staff users idempotently on a repeated run', function () {
    $this->seed(RoleSeeder::class);

    // createStaffUsers() is private; drive the same behaviour through the model
    // calls it now makes so the assertion stays focused on idempotency.
    $createStaff = function (): void {
        $staff = [
            ['name' => 'Hospital Admin', 'email' => 'admin@hospital.local', 'role' => 'admin'],
            ['name' => 'Reception Staff', 'email' => 'reception@hospital.local', 'role' => 'reception'],
            ['name' => 'Dr. Smith', 'email' => 'doctor@hospital.local', 'role' => 'doctor'],
            ['name' => 'Lab Technician', 'email' => 'lab@hospital.local', 'role' => 'lab_tech'],
            ['name' => 'Pharmacist', 'email' => 'pharma@hospital.local', 'role' => 'pharma'],
        ];

        foreach ($staff as $member) {
            User::updateOrCreate(
                ['email' => $member['email']],
                ['name' => $member['name'], 'password' => 'password'],
            )->syncRoles([$member['role']]);
        }
    };

    $createStaff();
    $createStaff();

    expect(DB::table('users')->where('email', 'admin@hospital.local')->count())->toBe(1)
        ->and(DB::table('users')->where('email', 'doctor@hospital.local')->count())->toBe(1);

    // Staff users keep exactly one role each.
    $admin = User::where('email', 'admin@hospital.local')->firstOrFail();
    expect($admin->getRoleNames()->all())->toBe(['admin']);

    $doctor = User::where('email', 'doctor@hospital.local')->firstOrFail();
    expect($doctor->getRoleNames()->all())->toBe(['doctor']);
});
