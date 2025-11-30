<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('dosage_unit')->nullable(); // e.g., 'mg', 'ml', 'tabs'
            $table->text('description')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0); // Threshold for low stock alerts
            $table->decimal('unit_price_purchase', 10, 2)->default(0.00); // Price hospital buys it for
            $table->timestamps();
            $table->index('tenant_id', 'medications_tenant_id_index'); // Explicit index for tenant_id
            $table->unique(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
