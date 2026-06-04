<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::inRandomOrder()->take(10)->get();
        if ($patients->isEmpty()) {
            return;
        }

        $statuses = ['draft', 'pending', 'paid', 'overdue', 'cancelled'];
        $itemTypes = ['consultation', 'lab_test', 'radiology', 'surgery', 'medication', 'admission', 'emergency', 'other'];
        $descriptions = ['General Consultation', 'Lab Test - CBC', 'X-Ray Chest', 'Medication Dispensing', 'Surgical Dressing', 'Admission Fee', 'Emergency Triage', 'Specialist Review'];
        $prices = [3000, 5000, 7500, 10000, 15000, 20000, 50000];

        foreach ($patients as $patient) {
            $invoiceCount = rand(1, 2);
            for ($i = 0; $i < $invoiceCount; $i++) {
                $invoiceDate = now()->subDays(rand(0, 90))->format('Y-m-d');
                $status = $statuses[array_rand($statuses)];

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . date('ym') . str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'patient_id' => $patient->id,
                    'invoice_date' => $invoiceDate,
                    'due_date' => now()->addDays(30)->format('Y-m-d'),
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 0,
                    'status' => $status,
                    'paid_at' => $status === 'paid' ? now()->subDays(rand(0, 30))->format('Y-m-d') : null,
                ]);

                $subtotal = 0;
                $itemCount = rand(1, 3);
                for ($j = 0; $j < $itemCount; $j++) {
                    $qty = rand(1, 3);
                    $price = $prices[array_rand($prices)];
                    $total = $qty * $price;

                    $invoice->items()->create([
                        'item_type' => $itemTypes[array_rand($itemTypes)],
                        'description' => $descriptions[array_rand($descriptions)],
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'total_price' => $total,
                    ]);
                    $subtotal += $total;
                }

                $invoice->update([
                    'subtotal' => $subtotal,
                    'total_amount' => $subtotal,
                ]);
            }
        }
    }
}
