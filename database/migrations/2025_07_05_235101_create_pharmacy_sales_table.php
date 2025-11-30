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
        Schema::create('pharmacy_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medications')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamp('sold_at')->useCurrent();
            $table->index(['tenant_id', 'patient_id', 'medicine_id'], 'pharmacy_sales_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_sales');
    }
};
