<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabTestDefinition;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.lab-technician')]
class CreateLabTestDefinition extends Component
{
    use UserActivitiesTrait;

    #[Rule('required|string|max:255')]
    public string $test_name;

    #[Rule('nullable|string')]
    public string $description;

    #[Rule('required|numeric|min:0')]
    public int $price;

    #[Rule('nullable|string|max:50')]
    public string $units;

    /**
     * Saves a new test or updates an existing one.
     */
    public function saveTest()
    {
        // Validate all properties using the #[Rule] attributes
        $this->validate();

        $data = [
            'test_name' => $this->test_name,
            'description' => $this->description,
            'price' => $this->price,
            'units' => $this->units,
        ];
        $labTech = Auth::user()->name;
        try {
            // Create new record
            $labTest = LabTestDefinition::create($data);
            $this->logActivity(
                'lab_test_updated',
                "{$labTech} created labtest {$labTest->test_name} ",
                [
                    'lab_tech_id' => Auth::id(),
                    'lab_test_id' => $labTest->id,
                ]
            );
            LivewireAlert::title('Success')->text('Lab test definition created successfully!')->success()->show();

            $this->resetForm();
        } catch (\Exception $e) {
            LivewireAlert::title('Error')->text('Server Error please Contact us in Feedback if this error persist')->error()->show();
            Log::error('Error while savig Lab test'.$e->getMessage());
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
