<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\BedType;
use App\Models\Department;
use App\Models\Subscription;
use App\Models\Ward;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Settings extends Component
{
    use WithFileUploads, WithPagination;

    #[Url]
    public string $activeTab = 'general';

    public string $hospitalName = '';

    public string $hospitalAddress = '';

    public string $hospitalEmail = '';

    public $hospitalLogo;

    public ?string $currentLogoUrl = null;

    public string $searchDepartment = '';

    public string $searchWard = '';

    public string $searchBedType = '';

    public string $searchBed = '';

    public string $searchSupply = '';

    public bool $showModal = false;

    public string $modalType = '';

    public string $modalAction = '';

    public ?int $editingId = null;

    public array $form = [];

    protected function service(): SettingsService
    {
        return app(SettingsService::class);
    }

    public function mount(): void
    {
        $tenant = tenant();
        $this->hospitalName = $tenant->name ?? '';
        $this->hospitalAddress = $tenant->address ?? '';
        $this->hospitalEmail = $tenant->contact_email ?? '';
        if ($tenant->logo) {
            $this->currentLogoUrl = Storage::disk('s3')->temporaryUrl($tenant->logo, now()->addMinutes(10));
        }
    }

    // --- Computed Data ---

    #[Computed]
    public function subscription()
    {
        return Subscription::where('tenant_id', tenant('id'))->first();
    }

    #[Computed]
    public function filteredDepartments()
    {
        return $this->service()->getEntityQuery('department', $this->searchDepartment)->paginate(6, ['*'], 'deptPage');
    }

    #[Computed]
    public function filteredWards()
    {
        return $this->service()->getEntityQuery('ward', $this->searchWard)->paginate(8, ['*'], 'wardPage');
    }

    #[Computed]
    public function filteredBedTypes()
    {
        return $this->service()->getEntityQuery('bed-type', $this->searchBedType)->paginate(6, ['*'], 'btPage');
    }

    #[Computed]
    public function filteredBeds()
    {
        return $this->service()->getEntityQuery('bed', $this->searchBed)->paginate(12, ['*'], 'bedPage');
    }

    #[Computed]
    public function filteredSupplies()
    {
        return $this->service()->getEntityQuery('supply', $this->searchSupply)->paginate(10, ['*'], 'supplyPage');
    }

    // --- CRUD Actions ---

    public function saveGeneralSettings(): void
    {
        $this->validate([
            'hospitalName' => 'required|max:255',
            'hospitalEmail' => 'nullable|email',
            'hospitalLogo' => 'nullable|image|max:2048',
        ]);

        $this->service()->updateGeneralSettings([
            'name' => $this->hospitalName,
            'address' => $this->hospitalAddress,
            'email' => $this->hospitalEmail,
        ], $this->hospitalLogo);

        $this->mount();
        LivewireAlert::title('General Settings Updated!')
            ->success()
            ->text('General settings updated successfully.')
            ->show();

    }

    public function openModal(string $type, string $action, ?int $id = null)
    {
        $this->modalType = $type;
        $this->modalAction = $action;
        $this->editingId = $id;

        if ($action === 'edit') {
            $this->form = $this->service()->getEntityQuery($type)->findOrFail($id)->toArray();
        } elseif ($action === 'delete') {
            $this->editingId = $id;
        } else {
            $this->form = $this->getEmptyForm($type);
        }
        $this->showModal = true;
    }

    public function saveForm(): void
    {
        $this->validateForm();

        try {
            if ($this->modalAction === 'create') {
                $this->service()->createItem($this->modalType, $this->form);
            } else {
                $this->service()->updateItem($this->modalType, $this->editingId, $this->form);
            }

            $this->closeModal();
            LivewireAlert::title("{$this->modalType} Saved!")
                ->success()
                ->text('Record saved successfully.')
                ->show();

        } catch (\Exception $e) {
            LivewireAlert::title('Error!')
                ->error()
                ->text('Operation failed')
                ->show();

        }
    }

    public function confirmDelete(): void
    {
        $this->service()->deleteItem($this->modalType, $this->editingId);
        $this->closeModal();
        LivewireAlert::title("{$this->modalType} Deleted!")
            ->success()
            ->text("Record deleted successfully.');")
            ->show();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['form', 'editingId', 'modalType', 'modalAction']);
    }

    // --- Helpers ---
    private function getEmptyForm($type)
    {
        return match ($type) {
            'department' => ['name' => '', 'description' => ''],
            'ward' => ['name' => '', 'ward_number' => '', 'department_id' => ''],
            'bed' => ['bed_number' => '', 'ward_id' => '', 'bed_type_id' => ''],
            'bed-type' => ['name' => '', 'price_per_day' => '', 'description' => ''],
            'supply' => ['name' => '', 'unit_of_measure' => '', 'current_stock' => 0, 'min_stock_level' => 0],
            default => []
        };
    }

    private function validateForm()
    {
        $rules = match ($this->modalType) {
            'department' => ['form.name' => 'required|max:255'],
            'ward' => ['form.name' => 'required', 'form.department_id' => 'required'],
            'bed' => ['form.bed_number' => 'required', 'form.ward_id' => 'required', 'form.bed_type_id' => 'required'],
            'bed-type' => ['form.name' => 'required', 'form.price_per_day' => 'required|numeric'],
            default => ['form.name' => 'required']
        };
        $this->validate($rules);
    }

    #[Computed]
    public function allDepartments()
    {
        return Department::all();
    }

    #[Computed]
    public function allWards()
    {
        return Ward::all();
    }

    #[Computed]
    public function allBedTypes()
    {
        return BedType::all();
    }

    public function render()
    {
        return view('livewire.tenants.admin.settings');
    }
}
