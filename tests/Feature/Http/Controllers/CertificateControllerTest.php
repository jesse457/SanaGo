<?php

declare(strict_types=1);

use App\Models\Certificate;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use function Tests\Helpers\migrateTenantTables;

uses(RefreshDatabase::class);

beforeEach(function () {
    migrateTenantTables();

    // `certificates` is a tenant-only table, so the shared test schema has no
    // stub for it. DDL is not rolled back between tests in the same process,
    // hence the drop before the create.
    Schema::dropIfExists('certificates');
    Schema::create('certificates', function (Blueprint $table): void {
        $table->id();
        $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
        $table->unsignedBigInteger('consultation_id')->nullable();
        $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
        $table->string('type')->default('sick_leave')->index();
        $table->string('serial_number')->unique();
        $table->date('from_date');
        $table->date('to_date');
        $table->integer('duration_days')->nullable();
        $table->text('diagnosis_summary')->nullable();
        $table->text('recommendations')->nullable();
        $table->string('employer_name')->nullable();
        $table->string('employer_address')->nullable();
        $table->string('status')->default('issued')->index();
        $table->timestamps();
    });

    $this->seed(RoleSeeder::class);
    $this->doctor = tap(User::factory()->create())->assignRole('doctor');
    $this->patient = Patient::factory()->create();
    $this->withoutMiddleware([
        InitializeTenancyBySubdomain::class,
        PreventAccessFromCentralDomains::class,
    ]);
});

it('renders the single certificate page for every type', function () {
    $this->actingAs($this->doctor)->get(route('certificates.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Certificates/Create')
            ->has('patients')
            // No tenant context, so the sidebar badge stays empty.
            ->where('certificateCounts', null));
});

it('shares today’s certificate count for the sidebar badge', function () {
    actAsTenant(Tenant::factory()->create());

    // `created_at` is not fillable, so it is backdated after the insert.
    $issue = function (string $issuedAt): void {
        Certificate::create([
            'patient_id' => $this->patient->id,
            'issued_by' => $this->doctor->id,
            'type' => 'sick_leave',
            'from_date' => now()->toDateString(),
            'to_date' => now()->addDays(3)->toDateString(),
            'status' => 'issued',
        ])->forceFill([
            'created_at' => $issuedAt,
            'updated_at' => $issuedAt,
        ])->save();
    };

    $issue(now()->toDateTimeString());
    $issue(now()->subDay()->toDateTimeString());

    $this->actingAs($this->doctor)->get(route('certificates.create'))
        ->assertInertia(fn ($page) => $page
            ->where('certificateCounts.issued_today', 1));
});

it('leaves the certificate badge empty for guests', function () {
    $this->get('/login')
        ->assertInertia(fn ($page) => $page->where('certificateCounts', null));
});

it('issues a certificate of the type chosen in the form', function (string $type) {
    $this->actingAs($this->doctor)->post(route('certificates.store'), [
        'type' => $type,
        'patient_id' => $this->patient->id,
        'from_date' => now()->toDateString(),
        'to_date' => now()->addDays(3)->toDateString(),
        'diagnosis_summary' => 'Acute malaria',
    ])->assertRedirect()->assertSessionHas('success');

    $this->assertDatabaseHas('certificates', [
        'patient_id' => $this->patient->id,
        'type' => $type,
        'issued_by' => $this->doctor->id,
        'status' => 'issued',
    ]);
})->with(['sick_leave', 'medical_fitness', 'death']);

it('rejects a certificate request without a known type', function () {
    $this->actingAs($this->doctor)->post(route('certificates.store'), [
        'type' => 'unheard_of',
        'patient_id' => $this->patient->id,
        'from_date' => now()->toDateString(),
        'to_date' => now()->addDays(3)->toDateString(),
    ])->assertSessionHasErrors('type');

    $this->assertDatabaseCount('certificates', 0);
});
