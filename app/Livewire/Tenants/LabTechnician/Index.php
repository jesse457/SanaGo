<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabRequest;
use App\Models\LabResult;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('components.layouts.lab-technician')]
class Index extends Component
{
    use WithFileUploads;

    public $pendingLabRequests;

    public $completedTestsToday;

    public $inProgessTest;

    public $selectedRequestId;

    public $resultsText;

    public $analysisComments;

    public $resultFile;

    public $resultStatus = 'Received';

    /* ------------- notifications handled entirely by Livewire ------------- */
    public array $notifications = [];

    protected function rules(): array
    {
        return [
            'selectedRequestId' => 'required|exists:lab_requests,id',
            'resultsText' => 'nullable|string|max:5000',
            'analysisComments' => 'nullable|string|max:5000',
            'resultFile' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            'resultStatus' => 'required|in:Received,Urgent',
        ];
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $today = Carbon::today();

        $this->pendingLabRequests = LabRequest::query()
            ->where('status', 'Pending')
            ->with(['patient', 'testDefinition', 'doctor'])
            ->orderBy('request_date', 'desc') // Added ordering for display
            ->get();

        $this->completedTestsToday = LabResult::query()
            ->whereDate('result_date', $today)
            ->with(['labRequest.patient', 'labRequest.testDefinition'])
            ->get();

        $this->inProgessTest = LabRequest::query()
            ->whereDate('request_date', $today)
            ->where('status', 'In_Progress')
            ->withCount(['patient', 'testDefinition'])
            ->orderBy('request_date', 'desc') // Added ordering for display
            ->get();
    }

    public function render()
    {
        return view('livewire.tenants.lab-technician.index');
    }
}
