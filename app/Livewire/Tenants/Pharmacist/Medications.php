<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\Patient;
use App\Models\Prescription;
use App\Services\PharmacyService;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.pharmacist')]
class Medications extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $selectedPatientId = null;
    public $patientPrescriptions = [];
    public $selectedPrescription = null;

    // Dispensing form state
    public bool $showDispenseModal = false;
    public array $dispensedQuantities = [];
    public array $pharmacistNotes = [];
    public array $availableToDispense = [];

    public function updatingSearch() { $this->resetPage(); }

    public function selectPatient(int $patientId): void
    {
        $this->selectedPatientId = $patientId;
        $patient = Patient::find($patientId);
        $this->patientPrescriptions = $patient ? $patient->prescriptions()->latest()->with('doctor')->get() : [];
    }

    public function viewPrescriptionItems(int $id): void
    {
        $this->selectedPrescription = Prescription::with(['items.medication', 'doctor'])->findOrFail($id);

        foreach ($this->selectedPrescription->items as $item) {
            $this->availableToDispense[$item->id] = $item->quantity_prescribed - ($item->dispensed_quantity ?? 0);
            $this->dispensedQuantities[$item->id] = 0;
            $this->pharmacistNotes[$item->id] = $item->notes ?? '';
        }
        $this->showDispenseModal = true;
    }

    public function updateDispensation(PharmacyService $service)
    {
        $payload = [];
        $rules = [];

        foreach ($this->selectedPrescription->items as $item) {
            $qty = (int) ($this->dispensedQuantities[$item->id] ?? 0);
            $max = $this->availableToDispense[$item->id];

            if ($qty > 0) {
                $rules["dispensedQuantities.{$item->id}"] = "required|integer|min:1|max:{$max}";
            }

            $payload[] = [
                'model' => $item,
                'quantity' => $qty,
                'notes' => trim($this->pharmacistNotes[$item->id] ?? ''),
            ];
        }

        $this->validate($rules);

        try {
            $service->dispenseItems($this->selectedPrescription, $payload, Auth::id());

            LivewireAlert::success(__('Dispensed successfully.'))->show();
            $this->showDispenseModal = false;
            $this->selectPatient($this->selectedPatientId);
        } catch (\Exception $e) {
            LivewireAlert::error($e->getMessage())->show();
        }
    }

    public function render(PharmacyService $service)
    {
        return view('livewire.tenants.pharmacist.medications', [
            'patients' => $service->getPatientsQuery(['search' => $this->search])->paginate(10)
        ]);
    }
}
