<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabRequest;
use App\Models\LabResult;
use App\Models\LabResultAttachment;
use App\Notifications\LabResultNotification; // Import the notification
use App\Traits\UserActivitiesTrait;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.lab-technician')]
class EnterResults extends Component
{
    use UserActivitiesTrait, WithFileUploads;

    public LabRequest $labRequest;
    public string $results_text = '';
    public string $analysis_comments = '';
    public string $status = 'Completed';
    public array $attachments = [];
    public Collection $existingAttachments;

    public function mount(LabRequest $labRequest)
    {
        $this->labRequest = $labRequest->load(['patient', 'testDefinition', 'doctor']);

        if ($this->labRequest->result) {
            $result = $this->labRequest->result;
            $this->results_text = $result->results_text;
            $this->analysis_comments = $result->analysis_comments ?? '';
            $this->status = $result->status;
            $this->existingAttachments = $result->attachments;
        } else {
            $this->status = 'In Progress';
            $this->existingAttachments = collect();
        }
    }

    public function saveResults()
    {
        $this->validate([
            'results_text' => 'required|string',
            'analysis_comments' => 'nullable|string',
            'status' => 'required',
            'attachments.*' => 'nullable|file|max:10240', // 10MB Max
        ]);

        try {
            DB::connection('pgsql_transaction')->transaction(function () {
            // 1. Update Request Status
            $this->labRequest->update([
                'status' => 'Completed',
            ]);

            $currentPrice = $this->labRequest->testDefinition->price ?? 0;

            // 2. Create/Update Result
            $result = LabResult::updateOrCreate(
                ['lab_request_id' => $this->labRequest->id],
                [
                    'consultation_id' => $this->labRequest->consultation_id,
                    'lab_technician_id' => Auth::id(),
                    'result_date' => now(),
                    'doctor_id' => $this->labRequest->requested_by_doctor_id,
                    'results_text' => $this->results_text,
                    'analysis_comments' => $this->analysis_comments,
                    'status' => 'Completed',
                    'price' => $currentPrice,
                ]
            );

            // 3. Handle Attachments
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    $path = $attachment->store('lab-attachments', 's3');

                    LabResultAttachment::create([
                        'lab_result_id' => $result->id,
                        'file_path' => $path,
                        'file_name' => $attachment->getClientOriginalName(),
                        'file_type' => $attachment->getClientMimeType(),
                    ]);
                }
            }

            // 4. Notifications
            // Vital: Reload relationships to ensure data exists for the Notification class
            $result->load(['labRequest.patient', 'labRequest.testDefinition']);

            // Send Notification (This handles DB and Broadcast)
            if($this->labRequest->doctor) {
                $this->labRequest->doctor->notify(new LabResultNotification($result));
            }

            // 5. Logging
            $this->logActivity(
                'lab_test_updated',
                Auth::user()->name . " submitted result for Doc ID: {$this->labRequest->requested_by_doctor_id}",
                [
                    'lab_tech_id' => Auth::id(),
                    'lab_result_id' => $result->id,
                ]
            );
        });
            LivewireAlert::title('Success')->text('Lab results saved & sent to doctor.')->success()->show();

            return redirect()->route('lab-technician.lab-results');

        } catch (Exception $e) {
            Log::error('Error saving Lab results: ' . $e->getMessage());
            LivewireAlert::title('Error')->text('Failed to save. Please try again.')->error()->show();
        }
    }

    public function render()
    {
        return view('livewire.tenants.lab-technician.enter-results');
    }
}
