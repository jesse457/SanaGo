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
        Schema::create('nurse_care_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('report_date');
            $table->string('shift_type');
            $table->text('care_details');
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index('report_date');
            $table->index(['patient_id', 'report_date']);
            $table->index('shift_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_care_reports');
    }
};
