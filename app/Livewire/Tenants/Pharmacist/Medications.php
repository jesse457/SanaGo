<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\Dispensation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.pharmacist')]
class Medications extends Component
{
    use UserActivitiesTrait, WithPagination;

    // Patient list & search
    public $search = '';

    // Selected patient & their prescriptions
    public ?int $selectedPatientId = null;

    public $selectedPatient = null;

    public $patientPrescriptions = [];

    // Selected prescription & its items for dispensing
    public ?int $selectedPrescriptionId = null;

    public $selectedPrescription = null;

    public $prescriptionItemsToDispense = []; // Collection of PrescriptionItem models

    // Dispense modal state & inputs
    public bool $showDispenseModal = false;

    public array $dispensedQuantities = []; // keyed by prescription_item_id => int (This will be NEWLY issued amount)

    public array $pharmacistNotes = []; // keyed by prescription_item_id => string

    public array $availableToDispense = []; // To display remaining quantity to user

    protected $listeners = ['refreshPatients' => '$refresh'];

    protected array $rules = [
        // We cannot define per-id rules statically, so validate in updateDispensation()
    ];

    protected array $messages = [
        'dispensedQuantities.*.integer' => 'pharmacist.medications_component.validation_quantity_must_be_whole',
        'dispensedQuantities.*.min' => 'pharmacist.medications_component.validation_quantity_cannot_be_negative',
        'pharmacistNotes.*.max' => 'pharmacist.medications_component.validation_notes_cannot_exceed',
    ];

    public function mount()
    {
        // No need to load patients here, we'll handle it in render()
    }

    // ---------------------------
    // Patients / Prescriptions
    // ---------------------------
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function selectPatient(int $patientId): void
    {
        $this->resetPrescriptionSelection();
        $this->selectedPatientId = $patientId;
        $this->selectedPatient = Patient::find($patientId);
        if ($this->selectedPatient) {
            $this->loadPatientPrescriptions();
        } else {
            LivewireAlert::title(__('pharmacist.medications_component.alert_title_warning'))
                ->warning()
                ->text(__('pharmacist.medications_component.alert_selected_patient_not_found'))
                ->show();
        }
    }

    protected function resetPrescriptionSelection(): void
    {
        $this->selectedPrescriptionId = null;
        $this->selectedPrescription = null;
        $this->prescriptionItemsToDispense = [];
        $this->dispensedQuantities = [];
        $this->pharmacistNotes = [];
        $this->availableToDispense = [];
        $this->showDispenseModal = false;
    }

    public function loadPatientPrescriptions(): void
    {
        if (! $this->selectedPatient) {
            $this->patientPrescriptions = [];

            return;
        }
        $this->patientPrescriptions = $this->selectedPatient
            ->prescriptions()
            ->latest('prescription_date')
            ->with('doctor')
            ->get();
    }

    // ---------------------------
    // Prescription Items & Modal
    // ---------------------------

    public function viewPrescriptionItems(int $prescriptionId): void
    {
        $this->resetPrescriptionSelection();
        $prescription = Prescription::with(['items.medication', 'doctor', 'patient'])
            ->find($prescriptionId);
        if (! $prescription) {
            LivewireAlert::title(__('pharmacist.medications_component.alert_title_warning'))
                ->warning()
                ->text(__('pharmacist.medications_component.alert_prescription_not_found'))
                ->show();

            return;
        }
        $this->selectedPrescriptionId = $prescription->id;
        $this->selectedPrescription = $prescription;
        $this->prescriptionItemsToDispense = $prescription->items;
        $this->dispensedQuantities = [];
        $this->pharmacistNotes = [];
        $this->availableToDispense = [];

        foreach ($this->prescriptionItemsToDispense as $item) {
            $this->availableToDispense[$item->id] = ($item->quantity_prescribed ?? 0) - ($item->dispensed_quantity ?? 0);
            $this->dispensedQuantities[$item->id] = null; // Initialize input field for new dispensation
            $this->pharmacistNotes[$item->id] = $item->notes ?? '';
        }

        $this->showDispenseModal = true;
    }

    public function closeDispenseModal(): void
    {
        $this->resetPrescriptionSelection();
    }

    // ---------------------------
    // Dispensation update
    // ---------------------------

    public function updateDispensation()
    {
        if (! $this->selectedPrescription) {
            LivewireAlert::title(__('pharmacist.medications_component.alert_title_error'))
                ->error()
                ->text(__('pharmacist.medications_component.alert_no_prescription_selected'))
                ->show();

            return;
        }

        $items = $this->prescriptionItemsToDispense;

        // Validate inputs per item
        $rules = [];
        $messages = [];
        $anyDispensed = false;

        foreach ($items as $item) {
            $id = $item->id;
            $newlyIssued = (int) ($this->dispensedQuantities[$id] ?? 0);
            $remaining = ($item->quantity_prescribed ?? 0) - ($item->dispensed_quantity ?? 0);

            if ($newlyIssued > 0) {
                // If a new amount is entered, validate it
                $rules["dispensedQuantities.{$id}"] = ['required', 'integer', 'min:1', 'max:'.$remaining];
                $messages["dispensedQuantities.{$id}.min"] = __('pharmacist.medications_component.validation_quantity_min_for', ['medication' => $item->medication->name]);
                $messages["dispensedQuantities.{$id}.max"] = __('pharmacist.medications_component.validation_quantity_max_for', ['medication' => $item->medication->name, 'remaining' => $remaining]);
                $anyDispensed = true;
            }
            // Add rules for notes
            $rules["pharmacistNotes.{$id}"] = ['nullable', 'string', 'max:500'];
        }

        if (! $anyDispensed && array_filter($this->pharmacistNotes) === []) {
            LivewireAlert::title(__('pharmacist.medications_component.alert_title_info'))
                ->info()
                ->text(__('pharmacist.medications_component.alert_no_new_items_to_dispense'))
                ->show();

            return;
        }

        $this->validate($rules, $messages);

        $pharmacistId = Auth::id();
        $pharmacistName = Auth::user()->name ?? __('pharmacist.medications_component.fallback_pharmacist_name');

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $itemId = $item->id;
                $newlyIssued = (int) ($this->dispensedQuantities[$itemId] ?? 0);
                $notes = trim($this->pharmacistNotes[$itemId] ?? '');

                if ($newlyIssued <= 0) {
                    if ($notes !== ($item->notes ?? '')) {
                        $item->notes = $notes;
                        $item->save();
                    }

                    continue;
                }

                $med = $item->medication;
                if (! $med) {
                    throw new \Exception(__('pharmacist.medications_component.exception_medication_not_found', ['itemId' => $itemId]));
                }

                if (($med->stock_quantity ?? 0) < $newlyIssued) {
                    throw new \Exception(__('pharmacist.medications_component.exception_insufficient_stock', [
                        'medication' => $med->name,
                        'available' => $med->stock_quantity,
                        'required' => $newlyIssued,
                    ]));
                }

                $totalPrice = ($med->unit_price_purchase ?? 0) * $newlyIssued;

                // Create dispensation record
                Dispensation::create([
                    'prescription_item_id' => $itemId,
                    'pharmacist_id' => $pharmacistId,
                    'quantity_issued' => $newlyIssued,
                    'batch_number' => 'N/A',
                    'total_price' => $totalPrice,
                    'dispensed_at' => now(),
                ]);
                $med->decrement('stock_quantity', $newlyIssued);
                // Increment the dispensed quantity on the prescription item
                $item->increment('dispensed_quantity', $newlyIssued);

                // Update notes if they were provided
                if ($notes) {
                    $item->notes = $notes;
                    $item->save();
                }

                $this->logActivity(
                    'dispensation',
                    __('pharmacist.medications_component.activity_log_dispensed', [
                        'pharmacist' => $pharmacistName,
                        'quantity' => $newlyIssued,
                        'medication' => $med->name,
                        'prescription_id' => $this->selectedPrescriptionId,
                    ]),
                    [
                        'pharmacist_id' => $pharmacistId,
                        'prescription_item_id' => $itemId,
                        'quantity_issued' => $newlyIssued,
                        'notes' => $notes,
                    ]
                );
            }

            // Update prescription status
            $this->selectedPrescription->refresh(); // Get the latest dispensed quantities
            $allFullyDispensed = $this->selectedPrescription->items->every(function ($it) {
                return $it->quantity_prescribed <= ($it->dispensed_quantity ?? 0);
            });
            // NOTE: It's best practice to store status in a consistent format (e.g., English or a short code)
            // and translate it only when displaying to the user.
            $newStatus = $allFullyDispensed ? 'dispensed' : 'partial';
            $this->selectedPrescription->update(['status' => $newStatus]);

            DB::commit();

            LivewireAlert::title(__('pharmacist.medications_component.alert_title_success'))
                ->success()
                ->text(__('pharmacist.medications_component.alert_dispensed_successfully'))
                ->show();
            $this->closeDispenseModal();
            $this->loadPatientPrescriptions();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dispensation failed: '.$e->getMessage());
            // The exception message is now already localized before being thrown.
            LivewireAlert::title(__('pharmacist.medications_component.alert_title_error'))
                ->error()
                ->text($e->getMessage())
                ->show();
        }
    }

    public function render()
    {
        $query = Patient::query();

        if ($this->search) {
            $terms = explode(' ', $this->search);

            $query->where(function ($q) use ($terms) {
                if (count($terms) === 2) {
                    // Exact first + last name search (most efficient)
                    $q->whereBlind('first_name', 'first_name_index', $terms[0])
                        ->whereBlind('last_name', 'last_name_index', $terms[1]);
                } else {
                    // Single term or multiple fragments: match against indexed fields
                    foreach ($terms as $term) {
                        $q->orWhereBlind('first_name', 'first_name_index', $term)
                            ->orWhereBlind('last_name', 'last_name_index', $term)
                            ->orWhere('patient_uid', 'ILIKE', "%{$term}%");
                    }
                }
            });
        }

        $patients = $query->latest()->paginate(10);

        return view('livewire.tenants.pharmacist.medications', [
            'patients' => $patients,
        ]);
    }
}
