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
        Schema::create('procedure_kits', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_level')->default(1);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('category', 100)->nullable();
            $table->json('contents')->nullable(); // array of supplies included
            $table->timestamps();

            $table->index('name');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedure_kits');
    }
};
