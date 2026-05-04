<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabTestDefinition;
use App\Services\LabService;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.lab-technician')]
class ManageLabTestDefinitions extends Component
{
    use WithPagination;

    public $search = '';

    // Form properties
    #[Rule('required|string|max:255')]
    public $test_name;
    #[Rule('nullable|string|max:1000')]
    public $description;
    #[Rule('required|numeric|min:0')]
    public $price;
    #[Rule('nullable|string')]
    public $units;

    public $testId;
    public bool $showTestEditModal = false;
    public bool $showTestDeleteModal = false;
    public $deletingTestId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function viewEditTest($id)
    {
        $test = LabTestDefinition::find($id);

        if (! $test) {
            LivewireAlert::title('Error')->text('Lab test definition not found.')->error()->show();
            return;
        }

        $this->testId = $id;
        $this->test_name = $test->test_name;
        $this->price = $test->price;
        $this->description = $test->description;
        $this->units = $test->units;

        $this->showTestEditModal = true;
    }

    public function updateTest(LabService $labService)
    {
        $this->validate();

        try {
            $test = LabTestDefinition::findOrFail($this->testId);

            $labService->updateTestDefinition($test, [
                'test_name' => $this->test_name,
                'description' => $this->description,
                'price' => $this->price,
                'units' => $this->units,
            ]);

            $this->showTestEditModal = false;
            LivewireAlert::title('Success')->success()->text('Lab test updated successfully')->show();

        } catch (\Exception $e) {
            Log::error('Error updating Lab Test: ' . $e->getMessage());
            LivewireAlert::title('Error')->text('Update failed. Please try again.')->error()->show();
        }
    }

    public function viewDeleteTest($id)
    {
        $this->deletingTestId = $id;
        $this->showTestDeleteModal = true;
    }

    public function deleteTest(LabService $labService)
    {
        try {
            $test = LabTestDefinition::find($this->deletingTestId);

            if ($test) {
                $labService->deleteTestDefinition($test);
                LivewireAlert::title('Deleted')->success()->text('Lab test deleted successfully')->show();
            }

        } catch (\Exception $e) {
            Log::error('Error deleting Lab Test: ' . $e->getMessage());
            LivewireAlert::title('Error')->text('Could not delete test. It may be linked to existing requests.')->error()->show();
        }

        $this->showTestDeleteModal = false;
        $this->deletingTestId = null;
    }

    public function render(LabService $labService)
    {
        $filters = ['search' => $this->search];

        $labTests = $labService->getTestDefinitionsQuery($filters)->paginate(10);

        return view('livewire.tenants.lab-technician.manage-lab-test-definitions', [
            'labTests' => $labTests
        ]);
    }
}
