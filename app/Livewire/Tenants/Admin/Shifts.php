<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\UserShift;
use App\Services\AdminShiftService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Shifts extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?int $shiftId = null;

    #[Rule('required|string|in:Morning,Afternoon,Night')]
    public string $shift_type = 'Morning';

    #[Rule('required|date')]
    public string $shift_date = '';

    #[Rule('required')]
    public string $start_time = '';

    #[Rule('required|after:start_time')]
    public string $end_time = '';

    public function mount()
    {
        $this->shift_date = now()->format('Y-m-d');
    }

    public function save(AdminShiftService $service)
    {
        $data = $this->validate();
        $service->saveShift($data, $this->shiftId);

        LivewireAlert::title($this->shiftId ? 'Updated' : 'Created')->success()->show();
        $this->closeModal();
    }

    public function delete(int $id, AdminShiftService $service)
    {
        $service->deleteShift($id);
        LivewireAlert::title('Deleted')->success()->show();
    }

    public function edit(int $id)
    {
        $shift = UserShift::findOrFail($id);
        $this->shiftId = $shift->id;
        $this->shift_type = $shift->shift_type;
        $this->shift_date = $shift->shift_date->format('Y-m-d');
        $this->start_time = $shift->start_time->format('H:i');
        $this->end_time = $shift->end_time->format('H:i');
        $this->showModal = true;
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['shiftId', 'shift_type', 'start_time', 'end_time']);
        $this->shift_date = now()->format('Y-m-d');
    }

    public function render(AdminShiftService $service)
    {
        return view('livewire.tenants.admin.shifts', [
            'shifts' => $service->getPaginatedShifts(10),
        ]);
    }
}
