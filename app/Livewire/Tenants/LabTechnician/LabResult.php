<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Services\LabService;
use App\Models\LabResult as ModelsLabResult;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.lab-technician')]
class LabResult extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Use LabService to fetch filtered results.
     */
    public function render(LabService $labService)
    {
        // Define filters to pass to the service
        $filters = [
            'search' => $this->search,
            'date'   => $this->dateFilter,
        ];

        // Get the query from the service and paginate
        $results = $labService->getLabResultsQuery($filters)->paginate(10);

        return view('livewire.tenants.lab-technician.lab-result', [
            'results' => $results,
        ]);
    }

    /**
     * Placeholder for PDF download.
     */
    public function downloadPdf(int $labResultId)
    {
        $labResult = ModelsLabResult::find($labResultId);

        if (! $labResult) {
            session()->flash('error', 'Lab result not found.');
            return;
        }

        // Logic for PDF generation goes here (e.g., using Snappy or DomPDF)

        session()->flash('success', "PDF download requested for Lab Result ID: {$labResultId}");
    }
}
