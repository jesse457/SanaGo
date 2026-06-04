<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenditures', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('expenditure_date');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->string('vendor')->nullable();
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('category');
            $table->index('expenditure_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenditures');
    }
};
