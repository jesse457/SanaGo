<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabRequest;
use App\Traits\UserActivitiesTrait;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use phpDocumentor\Reflection\Types\This;

#[Layout('components.layouts.lab-technician')]
class TestRequest extends Component
{
    use WithPagination, UserActivitiesTrait;

    public $search = '';
    public $statusFilter = '';

    // Reset paging when search changes
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Reset paging when status filter changes
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updateStatus($id): void
    {
        $labRequest = LabRequest::find($id);

        if (! $labRequest) {
            LivewireAlert::title('Error')->error()->text('Lab request not found.')->show();
            return;
        }

        if ($labRequest->status === 'In Progress') {
            LivewireAlert::title('Info')->info()->text('Test is already in progress.')->show();
            return;
        }

        $labRequest->update([
            'status' => 'In_Progress',
        ]);
        $this->logActivity(
            'Status Updated',
            'Lab request status changed to In_Progress',
            ['lab_request_id' => $labRequest->id, 'new_status' => 'In_Progress']
        );
        LivewireAlert::title('Success')->success()->text('Test request is now In Progress.')->show();

        // Optionally re-query / refresh the component view
        $this->resetPage();
    }

  public function render()
{
    $requests = LabRequest::query()->with(['patient', 'testDefinition'])
        ->when($this->search, function ($query) {
            $terms = explode(' ', $this->search);

            $query->whereHas('patient', function ($patientQuery) use ($terms) {
                if (count($terms) === 2) {
                    // Exact first + last name search (most efficient)
                    $patientQuery->whereBlind('first_name', 'first_name_index', $terms[0])
                        ->whereBlind('last_name', 'last_name_index', $terms[1]);
                } else {
                    // Single term or multiple fragments: match against indexed fields
                    foreach ($terms as $term) {
                        $patientQuery->whereBlind('first_name', 'first_name_index', $term)
                            ->orWhereBlind('last_name', 'last_name_index', $term)
                            ->orWhere('patient_uid', 'ILIKE', "%{$term}%");
                    }
                }
            });
        })
        ->when($this->statusFilter, function ($query) {
            $query->where('status', $this->statusFilter);
        })
        ->orderBy('request_date', 'desc')
        ->paginate(10);

    return view('livewire.tenants.lab-technician.test-request', [
        'requests' => $requests,
    ]);
}
}
