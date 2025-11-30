<?php

namespace App\Livewire\Tenants\Doctor\Components;

use App\Models\MedicalRecord;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class PrescriptionModal extends Component
{
    use UserActivitiesTrait; // ← Add this line

    public int $recordId;

    public $items = [];

    public $generalNotes = '';

    protected $listeners = [
        'open-prescription-modal' => 'open',
    ];

    protected $rules = [
        'items.*.medication_id' => 'required|exists:medications,id',
        'items.*.dosage' => 'required|string|max:100',
        'items.*.frequency' => 'required|string|max:100',
        'items.*.duration' => 'required|string|max:100',
        'items.*.qty' => 'required|numeric|min:1',
        'generalNotes' => 'nullable|string|max:1000',
    ];

    public function open($recordId)
    {
        $this->recordId = $recordId;
        $this->reset(['items', 'generalNotes']);
        $this->addItem();
        $this->dispatch('modal-shown');
    }

    public function mount()
    { /* nothing here anymore */
    }

    public function addItem()
    {
        $this->items[] = [
            'medication_id' => '',
            'dosage' => '',
            'frequency' => '',
            'duration' => '',
            'qty' => 1,
            'notes' => '',
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate();

        $record = MedicalRecord::findOrFail($this->recordId);
        $patient = $record->patient;
        DB::beginTransaction();
        try {
            $pres = $record->prescription()->create([
                'patient_id' => $record->patient_id,
                'doctor_id' => Auth::id(),
                'consultation_id' => $record->id,
                'prescription_date' => now(),
                'general_notes' => $this->generalNotes,
                'status' => 'Pending',
            ]);

            foreach ($this->items as $i) {
                $pres->items()->create([
                    'medication_id' => $i['medication_id'],
                    'dosage' => $i['dosage'],
                    'frequency' => $i['frequency'],
                    'duration' => $i['duration'],
                    'quantity_prescribed' => $i['qty'],
                    'notes' => $i['notes'],
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            LivewireAlert::title('Error')->text('Failed to save prescription: '.$e->getMessage())->error()->show();

            return;
        }
        $doctor = Auth::user()->name;
        LivewireAlert::title('Success')->text('Prescription record saved successfully.')->success()->show();
        $this->logActivity(
            'Prescription created',
            "Dr {$doctor} prescribed prescriptions for {$patient->first_name} . {$patient->last_name}",
            [
                'prescription_id' => $pres->id,
                'record_id' => $this->recordId,
            ]
        ); // ← Log activity

        $this->dispatch('close-prescription-modal');
        $this->dispatch('refresh');

    }

    public function render()
    {
        return view('livewire.tenants.doctor.components.prescription-modal', [
            'medications' => \App\Models\Medication::orderBy('name')->get(),
        ]);
    }
}
