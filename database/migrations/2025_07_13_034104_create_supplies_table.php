// database/migrations/2025_01_01_000018_create_supplies_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('unit_of_measure')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->decimal('price', 10, 2)->nullable();

            $table->timestamps();
            // For general filtering and relationships on the tenant
            $table->index('tenant_id', 'supplies_tenant_id_index');
            $table->unique(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
