<?php

namespace App\Livewire\Tenants\Admin;

// Included but not directly used in logic, keeping it for context
use App\Models\UserShift;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule; // Used for type-hinting paginated results
use Livewire\Component; // Used for type-hinting the render return value
use Livewire\WithPagination; // Explicit exception

#[Layout('components.layouts.admin')]
class Shifts extends Component
{
    // Livewire trait to handle pagination functionality
    use WithPagination;

    // --- Component State Properties ---

    /**
     * Controls the visibility of the shift creation/edit modal.
     */
    public bool $showModal = false;

    /**
     * Holds the ID of the shift being edited. Null for a new shift.
     */
    public ?int $shiftId = null;

    // --- Form Properties (Model: UserShift) ---

    /**
     * Type of shift (Must be one of the required values).
     */
    #[Rule('required|string|in:Morning,Afternoon,Night')]
    public string $shift_type = 'Morning';

    /**
     * The specific date for the shift (YYYY-MM-DD format).
     */
    #[Rule('required|date')]
    public string $shift_date = '';

    /**
     * The start time of the shift (HH:MM format).
     */
    #[Rule('required|date_format:H:i')]
    public string $start_time = '';

    /**
     * The end time of the shift (HH:MM format). Must be after start_time.
     */
    #[Rule('required|date_format:H:i|after:start_time')]
    public string $end_time = '';

    // --- Lifecycle Hooks ---

    /**
     * Runs once immediately after the component is instantiated.
     * Sets the default shift date to today.
     */
    public function mount(): void
    {
        // Set default shift date to the current date in YYYY-MM-DD format
        $this->shift_date = \now()->format('Y-m-d');
    }

    // --- Modal Control Methods ---

    /**
     * Resets the form and opens the modal for creating a new shift.
     */
    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Closes the modal and resets the form state.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Resets all form properties to their default state.
     */
    public function resetForm(): void
    {
        // Reset Livewire properties to their default values
        $this->reset(['shiftId', 'shift_type', 'start_time', 'end_time']);
        // Re-apply today's date, as it's not reset by the previous call
        $this->shift_date = \now()->format('Y-m-d');
        // Clear any validation errors
        $this->resetErrorBag();
    }

    // --- CRUD Action Methods ---

    /**
     * Loads the data of an existing shift into the form for editing.
     *
     * @param  int  $id  The ID of the UserShift record to edit.
     *
     * @throws ModelNotFoundException If the shift ID does not exist.
     */
    public function edit(int $id): void
    {
        /** @var \App\Models\UserShift $shift */
        $shift = UserShift::findOrFail($id);

        // Populate form properties with shift data
        $this->shiftId = $shift->id;
        $this->shift_type = $shift->shift_type;

        // Ensure date/time values are formatted correctly for the form inputs
        $this->shift_date = $shift->shift_date->format('Y-m-d');
        $this->start_time = $shift->start_time->format('H:i');
        $this->end_time = $shift->end_time->format('H:i');

        $this->showModal = true;
    }

    /**
     * Validates the form data and either creates a new shift or updates an existing one.
     */
    public function save(): void
    {
        $this->validate();

        // Use updateOrCreate: if shiftId exists, update; otherwise, create new.
        UserShift::updateOrCreate(
            ['id' => $this->shiftId],
            [
                'shift_type' => $this->shift_type,
                'shift_date' => $this->shift_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
            ]
        );

        // Prepare success message based on whether it was a create or update action
        $message = $this->shiftId ? 'Shift updated successfully!' : 'Shift created successfully!';

        // Show success notification using Livewire Alert
        LivewireAlert::title('Success')
            ->success()
            ->text($message)
            ->show();

        $this->closeModal();
        // Ensure pagination is reset to the first page to see the new/updated record
        $this->resetPage();
    }

    /**
     * Deletes a specific shift record and shows a confirmation alert.
     *
     * @param  int  $id  The ID of the UserShift record to delete.
     *
     * @throws ModelNotFoundException If the shift ID does not exist.
     */
    public function delete(int $id): void
    {
        UserShift::findOrFail($id)->delete();

        // Show success notification
        LivewireAlert::title('Deleted successfully!')
            ->success()
            ->show();

        // Refresh the current page to update the list immediately
        $this->resetPage();
    }

    // --- Render Method ---

    /**
     * Renders the view and supplies the paginated list of shifts.
     */
    public function render(): View
    {
        /** @var LengthAwarePaginator $shifts */
        $shifts = UserShift::query()
            // Count the number of users assigned to each shift
            ->withCount('user')
            // Order by date descending (most recent first)
            ->orderBy('shift_date', 'desc')
            // Then order by start time ascending
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        return \view('livewire.tenants.admin.shifts', [
            'shifts' => $shifts,
        ]);
    }
}
