<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_insurance_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('claim_amount', 12, 2);
            $table->dateTime('submitted_at')->nullable();
            $table->string('status')->default('draft')->index();
            $table->text('rejection_reason')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->string('remittance_reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
