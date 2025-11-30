// database/migrations/2025_01_01_000017_create_invoices_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['unpaid', 'paid', 'partial'])->default('unpaid');
            $table->enum('payment_method', ['Cash', 'Mobile Money', 'Bank Transfer', 'Card', 'Insurance'])->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
