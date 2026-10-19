<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    private static int $counter = 0;

    public function definition(): array
    {
        self::$counter++;
        $subtotal = fake()->randomFloat(2, 500, 20000);
        $taxAmount = round($subtotal * 0.075, 2);
        $invoiceDate = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'invoice_number' => 'INV-'.date('Y').'-'.str_pad((string) self::$counter, 5, '0', STR_PAD_LEFT),
            'patient_id' => Patient::factory(),
            'invoice_date' => $invoiceDate->format('Y-m-d'),
            'due_date' => (clone $invoiceDate)->modify('+30 days')->format('Y-m-d'),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $subtotal + $taxAmount,
            'status' => fake()->randomElement(InvoiceStatus::cases()),
            'paid_at' => null,
            'notes' => fake()->optional(0.4)->sentence(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => InvoiceStatus::PAID,
            'paid_at' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => InvoiceStatus::PENDING,
            'paid_at' => null,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => InvoiceStatus::OVERDUE,
            'due_date' => fake()->dateTimeBetween('-3 months', '-1 day')->format('Y-m-d'),
            'paid_at' => null,
        ]);
    }
}
