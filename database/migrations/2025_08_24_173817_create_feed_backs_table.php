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
        Schema::create('feed_backs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Changed to nullOnDelete

            $table->string('subject');
            $table->string('category')->default('General'); // Added default
            $table->string('department')->nullable();
            $table->text('message');
            $table->text('response')->nullable(); // Changed to text
            $table->text('response_draft')->nullable(); // Changed to text
            $table->string('status')->default('Pending'); // pending, in_progress, resolved, closed

            $table->timestamps();

            // --- INDEXES FOR PERFORMANCE ---

            // Core multi-tenancy index for tenant-specific lookups
            $table->index('tenant_id', 'feed_backs_tenant_id_index');

            // Index for finding all feedback from a specific user within a tenant
            $table->index(['tenant_id', 'user_id'], 'feed_backs_user_id_index');

    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_backs');
    }
};
