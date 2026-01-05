<?php

namespace App\Livewire\LandLord;

use App\Models\DemoRequest;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.landlord')]
class ManageDemo extends Component
{
    use WithPagination;

    public $search = '';

    public $filterStatus = ''; // 'new', 'contacted', 'converted'

    // Modal State
    public $showDeleteModal = false;

    public $deleteId = null;

    public $deleteName = '';

    public function deleteConfirm($id)
    {
        $this->deleteId = $id;
        $this->deleteName = DemoRequest::find($id)->full_name;
        $this->showDeleteModal = true;
    }

    public function deleteRequest()
    {
        DemoRequest::destroy($this->deleteId);
        $this->showDeleteModal = false;
        $this->dispatch('notify', title: 'Deleted', message: 'Request deleted successfully.', type: 'success');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function render()
    {
        $requests = DemoRequest::query()
            ->when($this->search, function ($q) {
                $q->where('full_name', 'like', '%'.$this->search.'%')
                    ->orWhere('facility_name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(9);

        return view('livewire.land-lord.manage-demo', [
            'requests' => $requests,
            'total_count' => DemoRequest::count(),
            'new_count' => DemoRequest::where('status', 'new')->count(),
            'contacted_count' => DemoRequest::where('status', 'contacted')->count(),
        ]);
    }
}
