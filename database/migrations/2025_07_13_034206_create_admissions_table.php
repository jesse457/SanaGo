
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->cascadeOnDelete(); // Doctor overseeing admission
            $table->foreignId('bed_id')->nullable()->constrained('beds')->cascadeOnDelete(); // Assigned bed
            $table->timestamp('admission_date')->useCurrent()->nullable();
            $table->timestamp('discharge_date')->nullable();
            $table->text('reason_for_admission')->nullable();
            $table->decimal('observation_fee', 10, 2)->nullable();
            $table->string('status')->default('Admitted')->nullable();
            $table->timestamps();
            $table->index('tenant_id', 'admissions_tenant_id_index');
            $table->index(['tenant_id', 'patient_id', 'admission_date']);
            $table->index(['tenant_id', 'patient_id', 'doctor_id']);
            $table->index(['tenant_id', 'patient_id', 'bed_id', 'discharge_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
