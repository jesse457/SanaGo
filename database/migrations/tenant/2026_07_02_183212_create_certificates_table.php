<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('medical_records')->nullOnDelete();
            $table->foreignId('issued_by')->constrained('users')->cascadeOnDelete();
            $table->string('type')->default('sick_leave')->index();
            $table->string('serial_number')->unique();
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('duration_days')->nullable();
            $table->text('diagnosis_summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('employer_name')->nullable();
            $table->string('employer_address')->nullable();
            $table->string('status')->default('issued')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
