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
        Schema::create('notifications', function (Blueprint $table) {
            // 1. Laravel Notifications use UUIDs by default, not auto-increment integers
            $table->uuid('id')->primary();

            // 2. This creates 'type' (string) to store the class name
            $table->string('type');

            // 3. FIX: This creates both 'notifiable_id' and 'notifiable_type'
            // This fixes the SQL error you are seeing.
            $table->morphs('notifiable');

            // 4. Your custom Tenant ID
            // I made it nullable so system-wide notifications won't crash
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();

            // 5. Data payload
            $table->text('data'); // 'text' is often safer than 'json' for varying sizes, but 'json' works too

            // 6. Read Status
            // You don't need 'is_read'. Laravel checks if 'read_at' is null.
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
