<?php

namespace App\Livewire\Tenants\Doctor\Components;

use App\Events\SendLabRequest;
use App\Models\LabRequest;
use App\Models\LabTestDefinition;
use App\Models\MedicalRecord;
use App\Models\Notification;
use App\Models\User;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class LabRequestModal extends Component
{
    use UserActivitiesTrait;
    public $recordId;

    public $testId;

    public $reason;

    public $urgency = 'Normal';

    public $labTechId;

    protected $listeners = [
        'open-lab-request-modal' => 'open',
    ];

    public function open($recordId)
    {
        $this->recordId = $recordId;
        $this->reset(['testId', 'reason', 'urgency', 'labTechId']);
    }

    protected function rules()
    {
        return [
            'testId' => 'required|exists:lab_test_definitions,id',
            'reason' => 'nullable|string|max:255',
            'labTechId' => 'required|exists:users,id', // Made labTechId required for targeted notification
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $record = MedicalRecord::findOrFail($this->recordId);
            $labTest = LabTestDefinition::findOrFail($this->testId);
            $doctor = Auth::user();
            $patient = $record->patient;

            // Create the LabRequest record
            $labRequest = LabRequest::create([
                'patient_id' => $patient->id,
                'requested_by_doctor_id' => $doctor->id,
                'lab_test_definition_id' => $this->testId,
                'lab_tech_id' => $this->labTechId,
                'consultation_id' => $record->id,
                'reason_for_test' => $this->reason,
                'urgency_level' => $this->urgency,
                'request_date' => now(),
                'status' => 'Pending',
            ]);
            $doctor = Auth::user()->name;
            $this->logActivity(
                'lab_request',
                "Dr {$doctor} requested lab Test for {$patient->first_name} . {$patient->last_name}",
                [
                    'lab_tech_id' => $this->labTechId,
                    'patient_id' => $patient->id,
                ]
            ); // ← Log activity

            // Dispatch event to notify the lab technician via websockets
            SendLabRequest::dispatch(
                $labRequest->id,
                $patient->first_name . ' ' . $patient->last_name,
                $labTest->test_name,
                $this->labTechId
            );

            LivewireAlert::title('Success')->text('Lab request sent successfully.')->success()->show();
            $this->dispatch('close-lab-modal');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            Log::error('Error saving lab request: ' . $th->getMessage());
            LivewireAlert::title('Error')->text('Failed to send lab request.')->error()->show();
        }
    }

    public function render()
    {
        $labTechnicians = User::where('role', 'lab-technician')->get();

        return view('livewire.tenants.doctor.components.lab-request-modal', [
            'tests' => LabTestDefinition::orderBy('test_name')->get(),
            'labTechnicians' => $labTechnicians,
        ]);
    }
}
