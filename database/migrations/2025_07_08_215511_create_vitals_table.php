// database/migrations/2025_01_01_000008_create_vitals_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete(); // Nurse who recorded vitals
            $table->timestamp('recorded_at')->useCurrent();
            $table->decimal('temperature_celsius', 4, 2)->nullable(); // °C
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->decimal('height_cm', 6, 2)->nullable(); // cm
            $table->decimal('weight_kg', 6, 2)->nullable(); // kg
            $table->integer('heart_rate_bpm')->nullable(); // bpm
            $table->integer('respiratory_rate')->nullable(); // breaths/min
            $table->decimal('spo2_percentage', 4, 2)->nullable(); // %
            $table->decimal('bmi', 4, 2)->nullable(); // optional: calculated
            $table->boolean('flag_abnormal')->default(false); // Calculated in app logic
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'patient_id', 'nurse_id'], 'patient_nurse_vitals_index');
            $table->index('tenant_id', 'vitals_tenant_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vitals');
    }
};
