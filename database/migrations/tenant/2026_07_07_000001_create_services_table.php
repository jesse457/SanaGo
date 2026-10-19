<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->index();
            $table->string('code', 50)->unique();
            $table->string('category')->index();
            $table->text('description')->nullable();
            $table->string('source_model')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('default_price', 10, 2);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('requires_upfront_payment')->default(false);
            $table->timestamps();

            $table->index(['source_model', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
