<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\Bed;
use App\Models\BedType;
use App\Models\Department;
use App\Models\Subscription;
use App\Models\Supply;
use App\Models\Ward;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

    // =========================================================================
    // PROPERTIES
    // =========================================================================

    #[Url()]
    public string $activeTab = 'general';

    // General Settings
    public string $hospitalName = '';

    public ?string $hospitalAddress = '';

    public ?string $hospitalEmail = '';

    public $hospitalLogo;

    public ?string $currentLogoUrl = null;

    // Search Filters
    public string $searchDepartment = '';

    public string $searchWard = '';

    public string $searchBedType = '';

    public string $searchBed = '';

    public string $searchSupply = '';

    // Unified Modal State
    public bool $showModal = false;

    public string $modalType = ''; // 'department', 'ward', 'bed-type', 'bed', 'supply'

    public string $modalAction = ''; // 'create', 'edit', 'delete'

    public ?int $editingId = null;

    // Unified Form Data
    public array $form = [];

    // Subscription modals
    public bool $showUpgradeModal = false;

    public bool $showCancelModal = false;

    public ?string $cancelReason = null;

    public ?string $cancelFeedback = null;

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount(): void
    {
        $tenant = tenant();
        $this->hospitalName = $tenant->name ?? '';
        $this->hospitalAddress = $tenant->address ?? '';
        $this->hospitalEmail = $tenant->contact_email ?? '';

        if ($tenant->logo) {
            try {
                $this->currentLogoUrl = Storage::disk('s3')->temporaryUrl($tenant->logo, now()->addMinutes(10));
            } catch (\Exception $e) {
                Log::error('S3 Logo Error: '.$e->getMessage());
            }
        }
    }

    // =========================================================================
    // COMPUTED PROPERTIES (CACHED QUERIES)
    // =========================================================================

    #[Computed]
    public function subscription(): ?Subscription
    {
        return Subscription::first();
    }

    public function getAllDepartmentsProperty()
    {
        if (! $this->showModal || ! in_array($this->modalType, ['ward'])) {
            return collect();
        }

        return Department::where('tenant_id', tenant('id'))->get(['id', 'name']);
    }

    public function getAllWardsProperty()
    {
        if (! $this->showModal || ! in_array($this->modalType, ['bed'])) {
            return collect();
        }

        return Ward::where('tenant_id', tenant('id'))->get(['id', 'name']);
    }

    public function getAllBedTypesProperty()
    {
        if (! $this->showModal || ! in_array($this->modalType, ['bed'])) {
            return collect();
        }

        return BedType::where('tenant_id', tenant('id'))->get(['id', 'name']);
    }

    public function getFilteredDepartmentsProperty()
    {
        if ($this->activeTab !== 'departments') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6, 1, ['path' => request()->url()]);
        }

        return Department::where('tenant_id', tenant('id'))
            ->when($this->searchDepartment, fn ($q) => $q->where('name', 'like', '%'.$this->searchDepartment.'%'))
            ->orderBy('name')
            ->paginate(6, ['*'], 'deptPage');
    }

    public function getFilteredWardsProperty()
    {
        if ($this->activeTab !== 'wards') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 8, 1, ['path' => request()->url()]);
        }

        return Ward::with('department')
            ->where('tenant_id', tenant('id'))
            ->when($this->searchWard, fn ($q) => $q->where('name', 'like', '%'.$this->searchWard.'%'))
            ->orderBy('name')
            ->paginate(8, ['*'], 'wardPage');
    }

    public function getFilteredBedTypesProperty()
    {
        if ($this->activeTab !== 'bed-types') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6, 1, ['path' => request()->url()]);
        }

        return BedType::where('tenant_id', tenant('id'))
            ->when($this->searchBedType, fn ($q) => $q->where('name', 'like', '%'.$this->searchBedType.'%'))
            ->orderBy('name')
            ->paginate(6, ['*'], 'btPage');
    }

    public function getFilteredBedsProperty()
    {
        if ($this->activeTab !== 'beds') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12, 1, ['path' => request()->url()]);
        }

        return Bed::with(['ward.department', 'bedType'])
            ->where('tenant_id', tenant('id'))
            ->when($this->searchBed, fn ($q) => $q->where('bed_number', 'like', '%'.$this->searchBed.'%'))
            ->orderBy('bed_number')
            ->paginate(12, ['*'], 'bedPage');
    }

    public function getFilteredSuppliesProperty()
    {
        if ($this->activeTab !== 'supplies') {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['path' => request()->url()]);
        }

        return Supply::where('tenant_id', tenant('id'))
            ->when($this->searchSupply, fn ($q) => $q->where('name', 'like', '%'.$this->searchSupply.'%'))
            ->orderBy('name')
            ->paginate(10, ['*'], 'supplyPage');
    }

    // =========================================================================
    // PAGINATION RESET HOOKS
    // =========================================================================

    public function updatingSearchDepartment(): void
    {
        $this->resetPage('deptPage');
    }

    public function updatingSearchWard(): void
    {
        $this->resetPage('wardPage');
    }

    public function updatingSearchBedType(): void
    {
        $this->resetPage('btPage');
    }

    public function updatingSearchBed(): void
    {
        $this->resetPage('bedPage');
    }

    public function updatingSearchSupply(): void
    {
        $this->resetPage('supplyPage');
    }

    // =========================================================================
    // MODAL MANAGEMENT
    // =========================================================================

    public function openModal(string $type, string $action, ?int $id = null): void
    {
        $this->modalType = $type;
        $this->modalAction = $action;
        $this->editingId = $id;
        $this->resetValidation();
        $this->form = [];

        if ($action === 'edit' && $id) {
            $this->loadFormData($type, $id);
        } elseif ($action === 'create') {
            $this->initializeFormData($type);
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->form = [];
        $this->editingId = null;
        $this->modalType = '';
        $this->modalAction = '';
    }

    private function loadFormData(string $type, int $id): void
    {
        $tenantId = tenant('id');
        $model = match ($type) {
            'department' => Department::where('tenant_id', $tenantId)->findOrFail($id),
            'ward' => Ward::where('tenant_id', $tenantId)->findOrFail($id),
            'bed-type' => BedType::where('tenant_id', $tenantId)->findOrFail($id),
            'bed' => Bed::where('tenant_id', $tenantId)->findOrFail($id),
            'supply' => Supply::where('tenant_id', $tenantId)->findOrFail($id),
        };

        $this->form = match ($type) {
            'department' => ['name' => $model->name, 'description' => $model->description],
            'ward' => ['name' => $model->name, 'ward_number' => $model->ward_number, 'department_id' => $model->department_id, 'description' => $model->description],
            'bed-type' => ['name' => $model->name, 'price_per_day' => $model->price_per_day, 'description' => $model->description],
            'bed' => ['bed_number' => $model->bed_number, 'ward_id' => $model->ward_id, 'bed_type_id' => $model->bed_type_id],
            'supply' => ['name' => $model->name, 'unit_of_measure' => $model->unit_of_measure, 'current_stock' => $model->current_stock, 'min_stock_level' => $model->min_stock_level],
        };
    }

    private function initializeFormData(string $type): void
    {
        $this->form = match ($type) {
            'department' => ['name' => '', 'description' => ''],
            'ward' => ['name' => '', 'ward_number' => '', 'department_id' => '', 'description' => ''],
            'bed-type' => ['name' => '', 'price_per_day' => '', 'description' => ''],
            'bed' => ['bed_number' => '', 'ward_id' => '', 'bed_type_id' => ''],
            'supply' => ['name' => '', 'unit_of_measure' => '', 'current_stock' => '', 'min_stock_level' => ''],
        };
    }

    // =========================================================================
    // GENERAL SETTINGS
    // =========================================================================

    public function saveGeneralSettings(): void
    {
        $this->validate([
            'hospitalName' => 'required|string|max:255',
            'hospitalAddress' => 'nullable|string|max:500',
            'hospitalEmail' => 'nullable|email|max:255',
            'hospitalLogo' => 'nullable|image|max:2048',
        ]);

        $tenant = tenant();
        $tenant->name = $this->hospitalName;
        $tenant->address = $this->hospitalAddress;
        $tenant->contact_email = $this->hospitalEmail;

        if ($this->hospitalLogo) {
            if ($tenant->logo) {
                Storage::disk('s3')->delete($tenant->logo);
            }
            $tenant->logo = $this->hospitalLogo->store('logos', 's3');
        }

        $tenant->save();
        $this->mount();
        $this->hospitalLogo = null;

        LivewireAlert::title('Success')->success()->text('General settings updated successfully')->show();
    }

    // =========================================================================
    // UNIFIED CRUD OPERATIONS
    // =========================================================================

    public function saveForm(): void
    {
        $this->validateForm();

        if ($this->modalAction === 'create') {
            $this->createItem();
        } else {
            $this->updateItem();
        }

        $this->closeModal();
    }

    public function confirmDelete(): void
    {
        $tenantId = tenant('id');
        $model = match ($this->modalType) {
            'department' => Department::where('tenant_id', $tenantId)->where('id', $this->editingId),
            'ward' => Ward::where('tenant_id', $tenantId)->where('id', $this->editingId),
            'bed-type' => BedType::where('tenant_id', $tenantId)->where('id', $this->editingId),
            'bed' => Bed::where('tenant_id', $tenantId)->where('id', $this->editingId),
            'supply' => Supply::where('tenant_id', $tenantId)->where('id', $this->editingId),
        };

        $model->delete();
        $this->closeModal();

        LivewireAlert::title('Deleted')->success()->text(ucfirst(str_replace('-', ' ', $this->modalType)).' deleted successfully')->show();
    }

    private function validateForm(): void
    {
        $rules = match ($this->modalType) {
            'department' => [
                'form.name' => ['required', 'string', 'max:255', Rule::unique('departments', 'name')->where('tenant_id', tenant('id'))->ignore($this->editingId)],
                'form.description' => 'nullable|string',
            ],
            'ward' => [
                'form.name' => 'required|string|max:255',
                'form.ward_number' => 'required|string|max:50',
                'form.department_id' => 'required|exists:departments,id',
                'form.description' => 'nullable|string',
            ],
            'bed-type' => [
                'form.name' => 'required|string|max:255',
                'form.price_per_day' => 'required|numeric|min:0',
                'form.description' => 'nullable|string',
            ],
            'bed' => [
                'form.bed_number' => 'required|string|max:50',
                'form.ward_id' => 'required|exists:wards,id',
                'form.bed_type_id' => 'required|exists:bed_types,id',
            ],
            'supply' => [
                'form.name' => 'required|string|max:255',
                'form.unit_of_measure' => 'nullable|string|max:50',
                'form.current_stock' => 'required|numeric|min:0',
                'form.min_stock_level' => 'nullable|numeric|min:0',
            ],
            default => [],
        };

        $this->validate($rules);
    }

    private function createItem(): void
    {
        $tenantId = tenant('id');

        match ($this->modalType) {
            'department' => Department::create([
                'name' => $this->form['name'],
                'description' => $this->form['description'] ?? null,
                'tenant_id' => $tenantId,
            ]),
            'ward' => Ward::create([
                'name' => $this->form['name'],
                'ward_number' => $this->form['ward_number'],
                'department_id' => $this->form['department_id'],
                'description' => $this->form['description'] ?? null,
                'tenant_id' => $tenantId,
            ]),
            'bed-type' => BedType::create([
                'name' => $this->form['name'],
                'price_per_day' => $this->form['price_per_day'],
                'description' => $this->form['description'] ?? null,
                'tenant_id' => $tenantId,
            ]),
            'bed' => Bed::create([
                'bed_number' => $this->form['bed_number'],
                'ward_id' => $this->form['ward_id'],
                'bed_type_id' => $this->form['bed_type_id'],
                'is_occupied' => false,
                'tenant_id' => $tenantId,
            ]),
            'supply' => Supply::create([
                'name' => $this->form['name'],
                'unit_of_measure' => $this->form['unit_of_measure'] ?? null,
                'current_stock' => $this->form['current_stock'],
                'min_stock_level' => $this->form['min_stock_level'] ?? 0,
                'tenant_id' => $tenantId,
            ]),
        };

        LivewireAlert::title('Created')->success()->text(ucfirst(str_replace('-', ' ', $this->modalType)).' created successfully')->show();
    }

    private function updateItem(): void
    {
        $tenantId = tenant('id');

        $model = match ($this->modalType) {
            'department' => Department::where('tenant_id', $tenantId)->findOrFail($this->editingId),
            'ward' => Ward::where('tenant_id', $tenantId)->findOrFail($this->editingId),
            'bed-type' => BedType::where('tenant_id', $tenantId)->findOrFail($this->editingId),
            'bed' => Bed::where('tenant_id', $tenantId)->findOrFail($this->editingId),
            'supply' => Supply::where('tenant_id', $tenantId)->findOrFail($this->editingId),
        };

        $data = match ($this->modalType) {
            'department' => ['name' => $this->form['name'], 'description' => $this->form['description'] ?? null],
            'ward' => ['name' => $this->form['name'], 'ward_number' => $this->form['ward_number'], 'department_id' => $this->form['department_id'], 'description' => $this->form['description'] ?? null],
            'bed-type' => ['name' => $this->form['name'], 'price_per_day' => $this->form['price_per_day'], 'description' => $this->form['description'] ?? null],
            'bed' => ['bed_number' => $this->form['bed_number'], 'ward_id' => $this->form['ward_id'], 'bed_type_id' => $this->form['bed_type_id']],
            'supply' => ['name' => $this->form['name'], 'unit_of_measure' => $this->form['unit_of_measure'] ?? null, 'current_stock' => $this->form['current_stock'], 'min_stock_level' => $this->form['min_stock_level'] ?? 0],
        };

        $model->update($data);

        LivewireAlert::title('Updated')->success()->text(ucfirst(str_replace('-', ' ', $this->modalType)).' updated successfully')->show();
    }

    // =========================================================================
    // SUBSCRIPTION MANAGEMENT
    // =========================================================================

    public function cancelSubscription(): void
    {
        $this->validate(['cancelReason' => 'required|string']);

        $sub = $this->subscription;
        if ($sub) {
            $sub->cancel();
            $this->showCancelModal = false;
            LivewireAlert::title('Cancelled')->success()->text('Subscription cancelled successfully')->show();
        }
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.tenants.admin.settings');
    }
}
