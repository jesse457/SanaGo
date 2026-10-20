<?php

declare(strict_types=1);

use App\Enums\CertificateType;
use App\Models\Certificate;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

use function Tests\Helpers\migrateTenantTables;
use function Tests\Helpers\resolveCentralToDefault;

uses(RefreshDatabase::class);

beforeEach(function () {
    resolveCentralToDefault();
    migrateTenantTables();

    // `certificates` only exists in the tenant migration, so the shared test
    // schema carries a stub. Rebuild it with the real tenant columns.
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

    $this->doctor = User::factory()->create();
    $this->patient = Patient::factory()->create();
});

/**
 * Persist a certificate straight through the model.
 */
function certificateFor(Patient $patient, User $doctor, string $type = 'sick_leave'): Certificate
{
    return Certificate::create([
        'patient_id' => $patient->id,
        'issued_by' => $doctor->id,
        'type' => $type,
        'from_date' => now()->toDateString(),
        'to_date' => now()->addDays(3)->toDateString(),
        'status' => 'issued',
    ]);
}

it('generates sequential serial numbers', function () {
    $first = certificateFor($this->patient, $this->doctor);
    $second = certificateFor($this->patient, $this->doctor);

    $year = now()->year;

    expect($first->serial_number)->toBe('CERT-'.$year.'-0001')
        ->and($second->serial_number)->toBe('CERT-'.$year.'-0002');
});

it('does not collide after the newest certificate is deleted', function () {
    $first = certificateFor($this->patient, $this->doctor);
    $second = certificateFor($this->patient, $this->doctor);

    expect($first->serial_number)->not->toBe($second->serial_number);

    // Deleting the newest row used to make the next insert reuse its number,
    // because the old code derived the sequence from max('id').
    $second->delete();

    $third = certificateFor($this->patient, $this->doctor);

    expect($third->serial_number)->toBe('CERT-'.now()->year.'-0002')
        ->and($third->serial_number)->toBe($second->serial_number);
});

it('skips a serial number that is already taken', function () {
    $taken = 'CERT-'.now()->year.'-0001';

    Certificate::create([
        'patient_id' => $this->patient->id,
        'issued_by' => $this->doctor->id,
        'type' => 'sick_leave',
        'serial_number' => $taken,
        'from_date' => now()->toDateString(),
        'to_date' => now()->addDays(3)->toDateString(),
        'status' => 'issued',
    ]);

    // A second row exists, so the count-based candidate 0002 must be produced
    // rather than colliding with the row already occupying 0001.
    $next = certificateFor($this->patient, $this->doctor);

    expect($next->serial_number)->not->toBe($taken)
        ->and(Certificate::where('serial_number', $next->serial_number)->count())->toBe(1);
});

it('keeps every generated serial number unique', function () {
    $serials = collect(range(1, 8))
        ->map(fn () => certificateFor($this->patient, $this->doctor)->serial_number);

    expect($serials->unique())->toHaveCount(8)
        ->and(Certificate::query()->distinct()->count('serial_number'))->toBe(8);
});

it('respects an explicitly supplied serial number', function () {
    $certificate = Certificate::create([
        'patient_id' => $this->patient->id,
        'issued_by' => $this->doctor->id,
        'type' => CertificateType::SickLeave->value,
        'serial_number' => 'MANUAL-0001',
        'from_date' => now()->toDateString(),
        'to_date' => now()->addDays(2)->toDateString(),
        'status' => 'issued',
    ]);

    expect($certificate->serial_number)->toBe('MANUAL-0001');
});
