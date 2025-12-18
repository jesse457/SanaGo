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
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // The Nurse
            $table->dateTime('report_time');
            $table->enum('shift_type', ['Morning', 'Afternoon', 'Night']);
            $table->text('interventions');
            $table->text('observations');
            $table->timestamps();
            $table->index(['tenant_id', 'user_id']);
            $table->index('tenant_id');
            // Tenancy constraint if required:
            // $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
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
