<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attending_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('triage_nurse_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('arrival_mode')->nullable();
            $table->string('chief_complaint');
            $table->text('primary_assessment')->nullable();
            $table->string('triage_level')->default('normal');
            $table->string('status')->default('triage');
            $table->string('disposition')->nullable();
            $table->foreignId('admission_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('arrived_at');
            $table->dateTime('triaged_at')->nullable();
            $table->dateTime('stabilized_at')->nullable();
            $table->dateTime('discharged_at')->nullable();
            $table->decimal('emergency_fee', 10, 2)->default(0);
            $table->text('treatment_notes')->nullable();
            $table->text('discharge_instructions')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('triage_level');
            $table->index('arrived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_cases');
    }
};
