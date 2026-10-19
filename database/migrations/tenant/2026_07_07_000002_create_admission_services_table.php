<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('performed_at')->nullable();
            $table->timestamps();

            $table->index(['admission_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_services');
    }
};
