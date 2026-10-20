<?php

namespace App\Observers;

use App\Models\InvoiceItem;
use App\Models\MedicalRecord;
use App\Services\Billing\BillingService;
use App\Services\Billing\PriceListService;
use App\Services\System\NotificationService;

class MedicalRecordObserver
{
    public function created(MedicalRecord $record): void
    {
        $this->billConsultation($record);

        // Notify relevant staff
        $patientName = $record->patient?->first_name.' '.$record->patient?->last_name;
        $doctorName = $record->doctor?->name ?? 'Unknown';

        app(NotificationService::class)->notifyReception(
            message: "New consultation: {$patientName} seen by Dr. {$doctorName}",
            category: 'clinical',
            link: '/consultations/'.$record->id,
            typeLabel: 'info',
        );

        app(NotificationService::class)->notifyDoctors(
            message: "Consultation recorded: {$patientName}",
            category: 'clinical',
            link: '/consultations/'.$record->id,
            typeLabel: 'info',
        );
    }

    /**
     * Charge the consultation at the doctor's own price.
     *
     * When the visit started as an appointment, that appointment already put a
     * consultation charge on the bill today — charging again here would make the
     * patient pay twice for one visit.
     */
    private function billConsultation(MedicalRecord $record): void
    {
        $alreadyBilledToday = InvoiceItem::query()
            ->where('item_type', 'consultation')
            ->whereDate('created_at', today())
            ->whereHas('invoice', function ($query) use ($record): void {
                $query->where('patient_id', $record->patient_id);
            })
            ->exists();

        if ($alreadyBilledToday) {
            return;
        }

        $doctor = $record->doctor;
        $price = app(PriceListService::class)->consultationPrice($doctor);

        if ($price <= 0) {
            return;
        }

        $billing = app(BillingService::class);
        $invoice = $billing->findOrCreateDraftInvoice($record->patient_id);

        $billing->addBillableItem(
            $invoice,
            'consultation',
            $record->id,
            $doctor === null
                ? 'Consultation'
                : 'Consultation — Dr. '.$doctor->name,
            $price,
        );
    }
}
