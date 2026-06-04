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
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('unit_of_measure', 50)->default('Unit');
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(10);
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->string('category', 100)->default('General');
            $table->date('last_restocked')->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('category');
            $table->index(['current_stock', 'min_stock_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
