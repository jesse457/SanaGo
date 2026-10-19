<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payment_point');
            $table->decimal('opening_balance', 12, 2);
            $table->decimal('closing_balance', 12, 2)->nullable();
            $table->decimal('expected_total', 12, 2)->nullable();
            $table->decimal('variance', 12, 2)->nullable();
            $table->string('status')->default('open')->index();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_shifts');
    }
};
