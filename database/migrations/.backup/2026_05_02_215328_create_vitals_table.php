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
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('recorded_at');
            $table->decimal('temperature', 4, 1)->nullable(); // e.g., 37.2°C
            $table->integer('systolic_bp')->nullable(); // upper number
            $table->integer('diastolic_bp')->nullable(); // lower number
            $table->integer('heart_rate')->nullable(); // beats per minute
            $table->integer('oxygen_saturation')->nullable(); // SpO2 percentage
            $table->integer('respiratory_rate')->nullable();
            $table->boolean('is_abnormal')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('recorded_at');
            $table->index(['patient_id', 'recorded_at']);
            $table->index('is_abnormal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitals');
    }
};
