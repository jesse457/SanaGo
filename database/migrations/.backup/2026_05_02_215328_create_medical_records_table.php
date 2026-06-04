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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->date('record_date');
            $table->text('chief_complaint')->nullable();
            $table->text('history_of_present_illness')->nullable();
            $table->text('physical_exam')->nullable();
            $table->text('diagnosis');
            $table->text('treatment_plan');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // array of {id, name, file_path}
            $table->timestamps();

            $table->index('record_date');
            $table->index(['patient_id', 'record_date']);
            $table->index('doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
