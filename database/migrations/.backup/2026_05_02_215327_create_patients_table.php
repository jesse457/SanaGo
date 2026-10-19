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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_uid')->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->boolean('is_admitted_approve')->default(false);

            // New fields added to match the model's fillable array
            $table->string('blood_type', 10)->nullable();
            $table->text('allergies')->nullable();
            $table->text('address')->nullable();

            $table->timestamp('last_visit_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('patient_uid');
            $table->index(['first_name', 'last_name']);
            $table->index('gender');
            $table->index('last_visit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
