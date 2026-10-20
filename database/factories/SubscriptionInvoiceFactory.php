<?php

namespace Database\Factories;

use App\Enums\SubscriptionInvoiceStatus;
use App\Models\SubscriptionInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionInvoice>
 */
class SubscriptionInvoiceFactory extends Factory
{
    protected $model = SubscriptionInvoice::class;

    public function definition(): array
    {
        $start = now()->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return [
            'tenant_id' => TenantFactory::new(),
            'subscription_id' => null,
            'number' => 'SNG-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'currency' => 'XAF',
            'subtotal' => 15000,
            'tax' => 0,
            'total' => 15000,
            'status' => SubscriptionInvoiceStatus::PENDING,
            'period_start' => $start,
            'period_end' => $end,
            'issued_at' => now(),
            'due_at' => now()->addDays(7),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionInvoiceStatus::PAID,
            'paid_at' => now(),
            'payment_method' => 'mobile_money',
            'reference' => 'MOMO'.fake()->numerify('######'),
        ]);
    }
}
