<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('labtech_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('consultation_id')->constrained('medical_records')->cascadeOnDelete();
            $table->string('test_name');
            $table->timestamp('sample_collected_at')->nullable();
            $table->text('result')->nullable();
            $table->timestamp('result_at')->nullable();
            $table->index(['tenant_id', 'patient_id', 'test_name'], 'lab_tests_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
