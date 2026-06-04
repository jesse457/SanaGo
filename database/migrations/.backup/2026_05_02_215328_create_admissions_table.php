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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('attending_doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('admission_date');
            $table->date('discharge_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('reason_for_admission');
            $table->decimal('observation_fee', 10, 2)->nullable();
            $table->timestamps();

            $table->index('admission_date');
            $table->index('status');
            $table->index('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
