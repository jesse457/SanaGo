<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabResult as ModelsLabResult;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.lab-technician')]
class LabResult extends Component
{
    use WithPagination;

    // Public properties that will sync with your HTML inputs
    public string $search = '';

    public string $dateFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ModelsLabResult::query()->with(['labRequest.patient', 'labRequest.testDefinition', 'doctor']);
        // dd($query);
        // Apply search filter
        if ($this->search) {
           $terms = explode(' ',$this->search);

            $query->when($this->search, function ($q) use ($terms) {
                $q->whereHas('labRequest.patient', function ($patientQuery) use ($terms) {
                    if (count($terms) === 2) {
                        dd($terms);
                        // Exact first + last name search (most efficient)
                        $patientQuery->whereBlind('first_name', 'first_name_index', $terms[0])
                            ->whereBlind('last_name', 'last_name_index', $terms[1]);
                    } else {
                        // Single term or multiple fragments: match against indexed fields
                        foreach ($terms as $term) {
                           $patientQuery->whereBlind('first_name', 'first_name_index', $term)
                                    ->orWhereBlind('last_name', 'last_name_index', $term)
                                    ->orWhere('patient_uid', 'ILIKE', "%{$this->search}%");
                        }
                    }
                });

            });
        }

        // Apply date filter
        if ($this->dateFilter) {
            $query->whereDate('result_date', $this->dateFilter);
        }

        // Fetch paginated results
        $results = $query->latest('result_date')->paginate(10);
// dd($results);

        return view('livewire.tenants.lab-technician.lab-result', [
            'results' => $results,
        ]);
    }

    /**
     * A placeholder method to demonstrate a backend action.
     * In a real app, you would use this to generate and stream a PDF.
     */
    public function downloadPdf(int $labResultId)
    {
        $labResult = LabResult::find($labResultId);

        if (! $labResult) {
            session()->flash('error', 'Lab result not found.');

            return;
        }

        // Here you would use a PDF generation library (like Dompdf or Snappy)
        // to create and download the file.
        // Example: return PDF::loadView('pdf.lab-result', compact('labResult'))->download('report.pdf');

        // For now, let's just show a success message.
        session()->flash('success', "PDF download requested for Lab Result ID: {$labResultId}");
    }
}
