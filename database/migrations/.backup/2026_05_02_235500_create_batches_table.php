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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->morphs('batchable'); // batchable_id, batchable_type for polymorphic relation
            $table->string('batch_number', 100)->unique();
            $table->date('expiry_date');
            $table->integer('initial_quantity');
            $table->integer('current_quantity');
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('supplier', 200)->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('batch_number');
            $table->index('expiry_date');
            $table->index('status');
            // $table->index(['batchable_type', 'batchable_id']);
            $table->index(['current_quantity', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
