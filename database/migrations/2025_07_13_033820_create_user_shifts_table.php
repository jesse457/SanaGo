<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->enum('shift_type', ['Day', 'Night', 'Morning', 'Evening']);
            $table->time('start_time');
            $table->time('end_time');
            $table->date('shift_date');
            $table->timestamps();
            $table->index('tenant_id', 'user_shifts_tenant_id_index');
            $table->index(['tenant_id', 'shift_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_shifts');
    }
};
