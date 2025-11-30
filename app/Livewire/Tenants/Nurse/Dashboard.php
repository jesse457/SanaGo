<?php

namespace App\Livewire\Tenants\Nurse;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\Supply;
use App\Models\SupplyUsage;
use Illuminate\Support\Facades\Session; // <-- NEW: Import the Admission model
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.nurse')]
class Dashboard extends Component
{
    /* filters */
    public $search = '';

    /* quick vitals form */
    public $selectedPatient = null;

    public $temp;

    public $sys;

    public $dia;


    public function recordVitalsForPatient(int $patientId)
    {
        // Flash the patient ID to the session. It will only be available for the next request.
        Session::flash('selectedPatientId', $patientId);

        // Redirect to the record-vitals route using Livewire's redirector.
        return $this->redirect(route('nurse.record-vitals'), navigate: true);
    }

    public function render()
    {


        /* KPIs */
        // These KPIs are already correctly counting 'Admitted' from the Admission model.
        $admitted = Admission::where('status', 'Admitted')->count();
        $vitalsDue = Supply::all()->count();
        $lowStock = Supply::whereColumn('current_stock', '<=', 'min_stock_level')->count();
        $runningIVs = SupplyUsage::whereHas('supply', fn ($q) => $q->where('name', 'like', '%IV%'))
            ->whereDate('usage_date', now()->toDateString())
            ->count();

        /*
         * Main change: Fetch Admitted Patients for the Bed Map table
         * This will list currently admitted patients, ordered by their admission date (latest first).
         */
        $admittedPatients = Admission::where('status', 'Admitted')
            ->with(['patient', 'bed.ward']) // Eager load patient and bed's ward
            ->orderByDesc('admission_date') // Assuming an 'admission_date' column. If not, use 'created_at'.
            ->get();

        /* Low-stock items */
        $lowStockItems = Supply::whereColumn('current_stock', '<=', 'min_stock_level')
            ->get(['name', 'current_stock']);

        return view('livewire.tenants.nurse.dashboard', compact(
            'admitted',
            'vitalsDue',
            'lowStock',
            'runningIVs',
            'admittedPatients', // <-- NEW: Pass this to the view
            'lowStockItems'
        ));
    }
}
