<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabRequest;
use App\Services\LabService;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\WithFileUploads;

#[Layout('components.layouts.lab-technician')]
class EnterResults extends Component
{
    use UserActivitiesTrait, WithFileUploads;

    public LabRequest $labRequest;

    public string $results_text = '';

    public string $analysis_comments = '';

    public string $status = 'Completed';

    public $attachments = []; // Don't typehint as array if using Livewire file uploads sometimes

    public Collection $existingAttachments;

    public function mount(LabRequest $labRequest)
    {
        $this->labRequest = $labRequest->load(['patient', 'testDefinition', 'doctor', 'result.attachments']);

        if ($this->labRequest->result) {
            $this->results_text = $this->labRequest->result->results_text ?? '';
            $this->analysis_comments = $this->labRequest->result->analysis_comments ?? '';
            $this->status = $this->labRequest->result->status ?? 'Completed';
            $this->existingAttachments = $this->labRequest->result->attachments;
        } else {
            $this->existingAttachments = collect();
        }
    }

    public function saveResults(LabService $service)
    {
        $this->validate([
            'results_text' => 'required|string|min:5',
            'analysis_comments' => 'nullable|string',
            'attachments.*' => 'nullable|file|image|max:10240', // 10MB limit
        ]);

        try {
            $data = [
                'technician_id' => Auth::id(),
                'results_text' => $this->results_text,
                'analysis_comments' => $this->analysis_comments,
            ];

            // Pass the attachments array to the service
            $service->submitResults($this->labRequest, $data, $this->attachments);

            $this->logActivity('lab_result_submitted', "Result for Req #{$this->labRequest->id}");

            LivewireAlert::title('Success')
                ->text('Lab results saved and doctor notified.')
                ->success();

            return redirect()->route('lab-technician.lab-results');
        } catch (\Exception $e) {
            Log::error('Lab Error: '.$e->getMessage(), [
                'request_id' => $this->labRequest->id,
                'trace' => $e->getTraceAsString(),
            ]);
            LivewireAlert::title('Error')
                ->text('Failed to save lab results. Please try again.')
                ->error();
        }
    }

    public function render()
    {
        return view('livewire.tenants.lab-technician.enter-results');
    }
}
