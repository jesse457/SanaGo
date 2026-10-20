<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-doctor prices.
 *
 * A consultation, a minor procedure or an ultrasound is not billed at one
 * hospital-wide rate: each doctor sets their own. The service catalogue keeps
 * the default price; this table stores the doctor's override and the billing
 * code resolves `override ?? default`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_service_prices');
    }
};
