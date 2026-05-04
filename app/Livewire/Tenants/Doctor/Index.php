<?php

namespace App\Livewire\Tenants\Doctor;

use App\Services\Dashboards\DoctorDashboardService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.doctor')]
class Index extends Component
{
    public Collection $upcomingAppointments;

    public Collection $patientsUnderCare;

    public Collection $incomingLabResults;

    /**
     * Dependency Injection of the Service
     */
    public function mount(DoctorDashboardService $service)
    {
        $doctorId = Auth::id();

        // Use the service to populate the public properties
        $this->upcomingAppointments = $service->getUpcomingAppointments($doctorId);
        $this->patientsUnderCare = $service->getPatientsUnderCare($doctorId);
        $this->incomingLabResults = $service->getIncomingLabResults($doctorId);
    }

    public function render()
    {
        return view('livewire.tenants.doctor.index');
    }
}
