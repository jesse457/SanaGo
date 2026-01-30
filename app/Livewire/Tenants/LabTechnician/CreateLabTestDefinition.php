<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Services\LabService;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.lab-technician')]
class CreateLabTestDefinition extends Component
{
    #[Rule('required|string|max:255')]
    public string $test_name;

    #[Rule('nullable|string')]
    public string $description;

    #[Rule('required|numeric|min:0')]
    public int $price;

    #[Rule('nullable|string|max:50')]
    public string $units;

    /**
     * Saves a new test or updates an existing one using LabService.
     */
    public function saveTest(LabService $labService)
    {
        // Validate all properties using the #[Rule] attributes
        $validatedData = $this->validate();

        try {
            // Delegate logic to the service
            $labService->createTestDefinition([
                'test_name' => $this->test_name,
                'description' => $this->description,
                'price' => $this->price,
                'units' => $this->units,
            ]);

            LivewireAlert::title('Success')
                ->text('Lab test definition created successfully!')
                ->success()
                ->show();

            $this->resetForm();

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error while saving Lab test: ' . $e->getMessage());

            LivewireAlert::title('Error')
                ->text('Server Error please Contact us in Feedback if this error persists')
                ->error()
                ->show();
        }
    }

    /**
     * Resets all form properties to their initial state.
     */
    public function resetForm()
    {
        $this->reset([
            'test_name',
            'description',
            'price',
            'units',
        ]);
    }

    /**
     * Renders the component view.
     */
    public function render()
    {
        return view('livewire.tenants.lab-technician.create-lab-test-definition');
    }
}
