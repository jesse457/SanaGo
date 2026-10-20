<?php

namespace App\Services\Billing;

use App\Enums\ServiceCategory;
use App\Models\DoctorServicePrice;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Which price applies to a charge.
 *
 * Doctors do not charge the same: the price of a consultation (or a procedure,
 * an ultrasound) follows the doctor who provides it. The catalogue holds the
 * default; a doctor's override wins when it exists:
 *
 *     doctor override  →  service default  →  0
 *
 * Appointments are consultations, not a separate product: when a patient books
 * (and pays) by phone, the money is a consultation fee, resolved here too.
 */
class PriceListService
{
    /**
     * The doctor's price for a service, falling back to the catalogue.
     */
    public function priceFor(?User $doctor, Service $service): float
    {
        if ($doctor === null) {
            return (float) $service->default_price;
        }

        $override = DoctorServicePrice::query()
            ->where('user_id', $doctor->id)
            ->where('service_id', $service->id)
            ->value('price');

        return $override === null
            ? (float) $service->default_price
            : (float) $override;
    }

    /**
     * The service that represents a standard consultation.
     */
    public function consultationService(): ?Service
    {
        return Service::query()
            ->where('is_active', true)
            ->where('category', ServiceCategory::Consultation->value)
            ->orderByRaw("case when code = 'CONS-GEN' then 0 else 1 end")
            ->orderBy('id')
            ->first();
    }

    /**
     * What this doctor charges for a consultation.
     *
     * Appointments are billed as consultations, so this is also the price the
     * counter expects when a patient books by phone.
     */
    public function consultationPrice(?User $doctor): float
    {
        $service = $this->consultationService();

        if ($service === null) {
            return 0.0;
        }

        return $this->priceFor($doctor, $service);
    }

    /**
     * Price for a free-text service code (used by observers and the counter).
     */
    public function priceForCode(?User $doctor, string $code, float $fallback = 0.0): float
    {
        $service = Service::query()->where('code', $code)->first();

        if ($service === null) {
            return $fallback;
        }

        return $this->priceFor($doctor, $service);
    }

    /**
     * Services whose price belongs to the doctor who provides them.
     *
     * Laboratory tests, beds and drugs are not priced per doctor: they follow
     * the catalogue (or the pharmacy's own list).
     *
     * @return Collection<int, Service>
     */
    public function doctorPricedServices(): Collection
    {
        return Service::query()
            ->where('is_active', true)
            ->whereIn('category', [
                ServiceCategory::Consultation->value,
                ServiceCategory::Procedure->value,
                ServiceCategory::Nursing->value,
                ServiceCategory::Emergency->value,
                ServiceCategory::Imaging->value,
                ServiceCategory::Radiology->value,
            ])
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * The doctor's effective price list: every doctor-priced service with the
     * price that will actually be billed (`override` when set).
     *
     * @return array<int, array{id: int, name: string, code: string, category: string, default_price: float, price: float, is_override: bool}>
     */
    public function priceListFor(User $doctor): array
    {
        $overrides = DoctorServicePrice::query()
            ->where('user_id', $doctor->id)
            ->pluck('price', 'service_id');

        return $this->doctorPricedServices()
            ->map(fn (Service $service): array => [
                'id' => $service->id,
                'name' => $service->name,
                'code' => $service->code,
                'category' => $service->category?->value ?? 'other',
                'default_price' => (float) $service->default_price,
                'price' => $overrides->has($service->id)
                    ? (float) $overrides[$service->id]
                    : (float) $service->default_price,
                'is_override' => $overrides->has($service->id),
            ])
            ->all();
    }

    /**
     * Persist the doctor's overrides.
     *
     * Passing the catalogue default removes the override instead of storing a
     * redundant row, so the list stays meaningful ("this doctor has custom
     * prices only where they differ").
     *
     * @param  array<int|string, mixed>  $prices  service id => price
     */
    public function syncPrices(User $doctor, array $prices): void
    {
        foreach ($prices as $serviceId => $price) {
            $service = Service::find((int) $serviceId);

            if ($service === null || $price === null || $price === '') {
                continue;
            }

            $value = round((float) $price, 2);

            if (abs($value - (float) $service->default_price) < 0.01) {
                DoctorServicePrice::query()
                    ->where('user_id', $doctor->id)
                    ->where('service_id', $service->id)
                    ->delete();

                continue;
            }

            DoctorServicePrice::updateOrCreate(
                ['user_id' => $doctor->id, 'service_id' => $service->id],
                ['price' => $value],
            );
        }
    }
}
