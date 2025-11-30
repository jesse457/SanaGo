
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('lab_request_id')->constrained('lab_requests')->cascadeOnDelete();
            $table->foreignId('lab_technician_id')->constrained('users')->cascadeOnDelete(); // Who performed/uploaded
            $table->foreignId('doctor_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('consultation_id')->constrained('medical_records')->cascadeOnDelete();
            $table->timestamp('result_date')->useCurrent();
            $table->text('results_text')->nullable(); // Form-entered results
            $table->text('analysis_comments')->nullable(); // Technician's comments
            $table->string('status')->default('Received'); // Status for doctor review
            $table->timestamps();
            // For general filtering and relationships on the tenant
            $table->index('tenant_id', 'lab_results_tenant_id_index');

            $table->index(['tenant_id', 'lab_technician_id', 'result_date']);

            $table->index(['tenant_id', 'consultation_id', 'doctor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
