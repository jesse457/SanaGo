<?php

namespace App\Observers;

use App\Models\MedicalRecord;
use App\Services\Billing\BillingService;
use App\Services\System\NotificationService;

class MedicalRecordObserver
{
    public function created(MedicalRecord $record): void
    {
        // Billing
        $billing = app(BillingService::class);
        $invoice = $billing->findOrCreateDraftInvoice($record->patient_id);
        $billing->addBillableItem(
            $invoice,
            'consultation',
            $record->id,
            'Medical Record: Consultation',
            0,
        );

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
}
