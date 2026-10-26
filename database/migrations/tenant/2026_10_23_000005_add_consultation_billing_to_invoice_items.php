<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A charge knows which consultation ordered it, which doctor owns it, and
 * whether it has been paid. `paid_amount` lets a partial counter payment leave
 * the line `partially_paid` instead of pretending it is settled.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('consultation_id')->nullable()->after('item_id')
                ->constrained('medical_records')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->after('consultation_id')
                ->constrained('users')->nullOnDelete();
            $table->string('payment_status', 20)->default('pending')->after('doctor_id');
            $table->decimal('paid_amount', 12, 2)->default(0)->after('total_price');

            $table->index(['doctor_id', 'payment_status']);
        });

        // Lines settled before payment_status existed are already paid.
        DB::table('invoice_items')
            ->whereNotNull('settled_at')
            ->update([
                'payment_status' => 'paid',
                'paid_amount' => DB::raw('total_price'),
            ]);
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'payment_status']);
            $table->dropConstrainedForeignId('consultation_id');
            $table->dropConstrainedForeignId('doctor_id');
            $table->dropColumn(['payment_status', 'paid_amount']);
        });
    }
};
