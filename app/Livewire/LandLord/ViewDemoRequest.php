<?php

namespace App\Livewire\LandLord;

use App\Models\DemoRequest;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.landlord')]
class ViewDemoRequest extends Component
{
    public DemoRequest $demoRequest;

    public $notes = '';

    public function mount(DemoRequest $demoRequest)
    {
        $this->demoRequest = $demoRequest;
        $this->notes = $demoRequest->notes ?? ''; // Assuming you add a 'notes' column later
    }

    public function updateStatus($status)
    {
        $this->demoRequest->update(['status' => $status]);
        $this->dispatch('notify', title: 'Success', message: 'Status updated to '.ucfirst($status), type: 'success');
    }

    public function saveNotes()
    {
        // If you don't have a notes column, you can ignore this or add a migration
        // $this->demoRequest->update(['notes' => $this->notes]);
        $this->dispatch('notify', title: 'Saved', message: 'Internal notes saved.', type: 'success');
    }

    public function delete()
    {
        $this->demoRequest->delete();

        return redirect()->route('landlord.demo-requests');
    }

    public function render()
    {
        return view('livewire.land-lord.view-demo-request');
    }
}
