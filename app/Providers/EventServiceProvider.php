<?php

namespace App\Providers;

use App\Models\Admission;
use App\Models\EmergencyCase;
use App\Models\LabRequest;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Surgery;
use App\Observers\AdmissionObserver;
use App\Observers\EmergencyCaseObserver;
use App\Observers\LabRequestObserver;
use App\Observers\MedicalRecordObserver;
use App\Observers\PrescriptionObserver;
use App\Observers\SurgeryObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Surgery::observe(SurgeryObserver::class);
        EmergencyCase::observe(EmergencyCaseObserver::class);
        Admission::observe(AdmissionObserver::class);
        LabRequest::observe(LabRequestObserver::class);
        Prescription::observe(PrescriptionObserver::class);
        MedicalRecord::observe(MedicalRecordObserver::class);
    }
}
