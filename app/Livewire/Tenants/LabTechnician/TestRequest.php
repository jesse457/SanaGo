<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabRequest;
use App\Services\LabService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.lab-technician')]
class TestRequest extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    /**
     * Reset pagination when search term is updated.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when status filter is updated.
     */
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Transition a request to 'In Progress' using LabService.
     */
    public function updateStatus(int $id, LabService $service): void
    {
        $labRequest = LabRequest::find($id);

        // Basic validation: ensure request exists and isn't already started/completed
        if (!$labRequest || $labRequest->status !== 'Pending') {
            return;
        }

        try {
            // Service handles DB transaction, status update, and activity logging
            $service->startRequest($labRequest);

            LivewireAlert::title('Success')
                ->text('Test is now In Progress.')
                ->success()
                ->show();

        } catch (\Exception $e) {
            LivewireAlert::title('Error')
                ->text('Could not start test request.')
                ->error()
                ->show();
        }
    }

    /**
     * Render the list using the centralized query logic in LabService.
     */
    public function render(LabService $labService)
    {
        // Define filters for the service
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
        ];

        // Fetch query from service and apply pagination
        $requests = $labService->getLabRequestsQuery($filters)->paginate(10);

        return view('livewire.tenants.lab-technician.test-request', [
            'requests' => $requests
        ]);
    }
}
