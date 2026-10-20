<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cash points + per-service settlement.
 *
 * Until now a payment was only tied to a free-text `payment_point` on the
 * shift, and an invoice was paid as one lump: there was no way to know which
 * service had actually been paid for. A hospital counter needs both:
 *
 *  - `cash_points` gives every till (reception, laboratory, pharmacy, imaging)
 *    an identity, a receipt prefix and its own shift, while every payment still
 *    posts to one ledger.
 *  - `invoice_items.settled_at` marks the exact service that was paid, so the
 *    lab can require payment before sampling and the pharmacy before dispensing.
 *  - `cash_payments.client_uuid` makes a double-submitted counter payment
 *    idempotent instead of charging the patient twice.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('module', 50)->nullable()->index();
            $table->string('receipt_prefix', 10)->default('REC');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('cash_shifts', function (Blueprint $table) {
            $table->foreignId('cash_point_id')->nullable()->after('user_id')
                ->constrained('cash_points')->nullOnDelete();
        });

        Schema::table('cash_payments', function (Blueprint $table) {
            $table->foreignId('cash_point_id')->nullable()->after('cash_shift_id')
                ->constrained('cash_points')->nullOnDelete();
            $table->string('client_uuid', 64)->nullable()->unique();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dateTime('settled_at')->nullable()->after('total_price')->index();
        });

        $now = now();

        DB::table('cash_points')->insert([
            ['name' => 'Caisse principale', 'code' => 'main', 'module' => null, 'receipt_prefix' => 'REC', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Laboratoire', 'code' => 'lab', 'module' => 'laboratory', 'receipt_prefix' => 'LAB', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pharmacie', 'code' => 'pharmacy', 'module' => 'pharmacy', 'receipt_prefix' => 'PHM', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Radiologie', 'code' => 'radiology', 'module' => 'radiology', 'receipt_prefix' => 'RAD', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('settled_at');
        });

        Schema::table('cash_payments', function (Blueprint $table) {
            $table->dropUnique(['client_uuid']);
            $table->dropConstrainedForeignId('cash_point_id');
            $table->dropColumn('client_uuid');
        });

        Schema::table('cash_shifts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cash_point_id');
        });

        Schema::dropIfExists('cash_points');
    }
};
