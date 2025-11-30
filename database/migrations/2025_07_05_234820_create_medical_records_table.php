// database/migrations/2025_01_01_000006_create_medical_records_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->string('complaint'); // e.g., 'SOAP Note', 'Consultation'
            $table->text('general_notes')->nullable(); // Any other general notes
            $table->text('diagnosis_text')->nullable(); // Free-form diagnosis text
            $table->text('treatment_plan')->nullable(); // Treatment plan details
            $table->boolean('finalized')->default(false); // Whether the record is finalized
            $table->string('record_type')->default('consultation'); // Type of record (e.g., consultation, follow-up)

            $table->timestamps();
            $table->index('tenant_id', 'medical_records_tenant_id_index');
            $table->index(['tenant_id', 'patient_id', 'doctor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
