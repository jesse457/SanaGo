<?php

namespace App\Livewire\Tenants\LabTechnician;

use App\Models\LabTestDefinition;
use App\Traits\UserActivitiesTrait;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.lab-technician')]
class ManageLabTestDefinitions extends Component
{
    use UserActivitiesTrait, WithPagination;

    public $search = '';

    public $test_name;

    public $description;

    public $price;

    public $units;

    public $testId;

    public bool $showTestEditModal = false;

    public bool $showTestDeleteModal = false;

    public $deletingTestId = null;

    protected $rules = [
        // Validate attributes on the $test model using dot notation
        'test_name' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
        'price' => 'nullable|numeric|min:0',
        'units' => 'nullable|string',

    ];

    protected $listeners = [
        // If you want to use events to open modals from JS, etc.
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Open edit modal and populate $test.
     * Accepts either an ID or a model (Livewire can pass an id).
     */
    public function viewEditTest($id)
    {
        $this->testId = $id;
        // If the blade calls with a model (rare), ensure you handle both.
        $test = LabTestDefinition::find($id);
        $this->test_name = $test->test_name;
        $this->price = $test->price;
        $this->description = $test->description;
        $this->units = $test->units;
        if (! $test) {
            LivewireAlert::error('Not found', 'Lab test definition not found.');

            return;
        }

        $this->showTestEditModal = true;
    }

    public function updateTest()
    {
        // Validate against $rules
        $this->validate();

        $test = LabTestDefinition::findOrFail($this->testId);
        $test->update([
            'test_name' => $this->test_name,
            'description' => $this->description,
            'price' => $this->price,
            'units' => $this->units,
        ]);
        $this->logActivity(
            'Lab Test Updated',
            'Lab test definition updated',
            ['lab_test_definition_id' => $test->id]
        );

        $this->showTestEditModal = false;
        $this->resetPage(); // optional, depending on UX
        LivewireAlert::title('Success')->success()->text('Lab test updated successfully')->show();
    }

    public function viewDeleteTest($id)
    {
        $this->deletingTestId = $id;
        $this->showTestDeleteModal = true;
    }

    public function deleteTest()
    {
        if (! $this->deletingTestId) {
            LivewireAlert::error('Error', 'No test selected for deletion.');

            return;
        }

        $model = LabTestDefinition::find($this->deletingTestId);
        if (! $model) {
            LivewireAlert::error('Not found', 'Lab test definition not found.');
            $this->showTestDeleteModal = false;

            return;
        }

        $model->delete();

        $this->showTestDeleteModal = false;
        $this->deletingTestId = null;
        LivewireAlert::title('Deleted')->success()->text('Lab test deleted successfully')->show();
    }

    public function render()
    {
        $query = LabTestDefinition::query();

        if ($this->search) {
            $terms = explode(' ', $this->search);

            $query->where(function ($q) use ($terms) {

                // Search by test name (still indexed with blind search)
                foreach ($terms as $term) {
                    $q->orWhereBlind('test_name', 'test_name_index', $term);
                }
            });
        }

        $labTests = $query->orderBy('test_name')->paginate(10);

        return view('livewire.tenants.lab-technician.manage-lab-test-definitions', compact('labTests'));
    }
}
