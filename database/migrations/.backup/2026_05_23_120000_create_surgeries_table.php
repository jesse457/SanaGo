<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('surgeon_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('anesthetist_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('bed_id')->nullable()->constrained()->nullOnDelete();
            $table->string('surgery_name');
            $table->text('description')->nullable();
            $table->dateTime('scheduled_at');
            $table->dateTime('completed_at')->nullable();
            $table->integer('estimated_duration_minutes')->nullable();
            $table->string('urgency')->default('normal');
            $table->string('status')->default('scheduled');
            $table->decimal('surgery_fee', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('complications')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
            $table->index('urgency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgeries');
    }
};
