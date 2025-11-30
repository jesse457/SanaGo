<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('requested_by_doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lab_tech_id')->nullable()->constrained('users')->nullOnDelete(); // Changed to nullable
            $table->foreignId('lab_test_definition_id')->constrained('lab_test_definitions')->cascadeOnDelete();
            $table->foreignId('consultation_id')->constrained('medical_records')->cascadeOnDelete();
            $table->text('reason_for_test')->nullable();
            $table->text('urgency_level')->default('Routine');
            $table->timestamp('request_date')->useCurrent();
            $table->string('status')->default('Pending');
            $table->timestamps();

            // --- INDEXES ---

            // For general filtering and relationships on the tenant
            $table->index('tenant_id', 'lab_requests_tenant_id_index');

            // For a specific patient's lab requests within a tenant
            $table->index(['tenant_id', 'patient_id', 'lab_test_definition_id'], 'lab_requests_patient_test_index');

            // For a lab technician to see their requested tests on a specific day
            $table->index(['tenant_id', 'lab_tech_id', 'request_date'], 'lab_requests_tech_date_index');

            // For a doctor to view all their requested lab tests within a tenant
            $table->index(['tenant_id', 'requested_by_doctor_id'], 'lab_requests_requested_by_doctor_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_requests');
    }
};
