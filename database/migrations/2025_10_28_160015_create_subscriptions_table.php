// database/migrations/YYYY_MM_DD_HHMMSS_create_subscriptions_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('plan'); // basic, standard, premium, enterprise
            $table->string('status')->default('active'); // active, inactive, cancelled, expired, suspended
            $table->decimal('amount', 10, 2); // Monthly/annual amount
            $table->string('currency', 3)->default('USD');
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('features')->nullable(); // Store plan features as JSON
            $table->integer('max_users')->default(10); // Maximum users allowed
            $table->integer('max_storage')->default(1024); // Maximum storage in MB
            $table->string('stripe_subscription_id')->nullable(); // For Stripe integration
            $table->string('stripe_customer_id')->nullable(); // For Stripe integration
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['status', 'ends_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
