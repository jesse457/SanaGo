<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adapts the appointments table for a queue-based system.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();

            $table->date('appointment_date');
            $table->datetime('appointment_time'); // Stores scheduled arrival date and time

            $table->text('reason_for_visit')->nullable();

            // Status reflects the patient's position in the flow
            $table->string('status')->default('Waiting'); // e.g., Waiting, In Consultation, Completed, Canceled

            $table->decimal('price', 10, 2)->nullable();
            $table->text('notes')->nullable();

            // --- QUEUE SYSTEM COLUMNS ---
            // The patient's position in the queue for the doctor on the appointment_date
            $table->unsignedInteger('queue_position')->default(0);
            // Optional: Track actual consultation times for records and analytics
            $table->timestamp('actual_start_time')->nullable();
            $table->timestamp('actual_end_time')->nullable();
            // --- END QUEUE SYSTEM COLUMNS ---

            $table->timestamps();
            $table->index('tenant_id', 'appointments_tenant_id_index');
            // Index for quickly finding a doctor's queue on a specific day
            $table->index(['tenant_id', 'doctor_id', 'appointment_date'], 'appointments_doctor_date_index');
            // Unique constraint to ensure no two appointments have the same queue position for a doctor on a specific day
            $table->unique(['tenant_id', 'doctor_id', 'appointment_date', 'queue_position'], 'appointments_queue_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
