<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\Bed;
use App\Models\BedType;
use App\Models\Department;
use App\Models\Supply;
use App\Models\Ward;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Settings extends Component
{
    use WithFileUploads;
    use WithPagination;

    // General Settings
    public $hospitalName;

    public $hospitalAddress;

    public $hospitalEmail;

    public $hospitalLogo;

    public $currentLogoUrl;

    // Department Management
    public $newDepartmentName;

    public $newDepartmentDescription;

    // Removed public $departments (moved to render)
    public $searchDepartment = '';

    public $editingDepartmentId = null;

    public $editDepartmentName;

    public $editDepartmentDescription;

    public $showDepartmentEditModal = false;

    public $showDepartmentDeleteModal = false;

    public $deletingDepartmentId;

    // Ward Management
    public $newWardName;

    public $newWardNumber;

    public $newWardDepartmentId;

    public $newWardDescription;

    // Removed public $wards (moved to render)
    public $searchWard = '';

    public $editingWardId = null;

    public $editWardName;

    public $editWardNumber;

    public $editWardDepartmentId;

    public $editWardDescription;

    public $showWardEditModal = false;

    public $showWardDeleteModal = false;

    public $deletingWardId;

    // Bed Type Management
    public $newBedTypeName;

    public $newBedTypeDescription;

    public $newBedTypePrice;

    // Removed public $bedTypes (moved to render)
    public $searchBedType = '';

    public $editingBedTypeId = null;

    public $editBedTypeName;

    public $editBedTypeDescription;

    public $editBedTypePrice;

    public $showBedTypeEditModal = false;

    public $showBedTypeDeleteModal = false;

    public $deletingBedTypeId;

    // Bed Management
    public $newBedNumber;

    public $newBedWardId;

    public $newBedTypeId;

    public $newBedIsOccupied = false;

    // Removed public $beds (moved to render)
    public $searchBed = '';

    public $editingBedId = null;

    public $editBedNumber;

    public $editBedWardId;

    public $editBedTypeId;

    public $editBedIsOccupied = false;

    public $showBedEditModal = false;

    public $showBedDeleteModal = false;

    public $deletingBedId;

    // Supply Management
    public $newSupplyName;

    public $newSupplyUnitOfMeasure;

    public $newSupplyCurrentStock;

    public $newSupplyMinStockLevel;

    // Removed public $supplies (moved to render)
    public $searchSupply = '';

    public $editingSupplyId = null;

    public $editSupplyName;

    public $editSupplyUnitOfMeasure;

    public $editSupplyCurrentStock;

    public $editSupplyMinStockLevel;

    public $showSupplyEditModal = false;

    public $showSupplyDeleteModal = false;

    public $deletingSupplyId;

    public $tenant;

    public function mount()
    {
        $this->tenant = tenant();
        $this->hospitalName = $this->tenant->name ?? '';
        $this->hospitalAddress = $this->tenant->address ?? '';
        $this->hospitalEmail = $this->tenant->contact_email ?? '';

        $this->currentLogoUrl = $this->tenant?->logo
            ? Storage::disk('s3')->temporaryUrl($this->tenant->logo, now()->addMinutes(5))
            : null;
    }

    // -----------------------
    // General Settings
    // -----------------------
    public function saveGeneralSettings()
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
            if ($tenant->logo && Storage::disk('s3')->exists($tenant->logo)) {
                Storage::disk('s3')->delete($tenant->logo);
            }
            $path = $this->hospitalLogo->store('logos', 's3');
            $tenant->logo = $path;
        }
        $tenant->save();

        $this->currentLogoUrl = $tenant->logo ? Storage::disk('s3')->temporaryUrl($tenant->logo, now()->subMinutes(5)) : null;
        $this->hospitalLogo = null;
        LivewireAlert::title('Success')->success()->text('General info updated successfully')->show();
    }

    // -----------------------
    // Department CRUD
    // -----------------------
    public function addDepartment()
    {
        $this->validate([
            'newDepartmentName' => ['required', 'string', 'max:255', Rule::unique('departments', 'name')->where('tenant_id', tenant('id'))],
            'newDepartmentDescription' => 'nullable|string|max:1000',
        ]);

        Department::create([
            'name' => $this->newDepartmentName,
            'description' => $this->newDepartmentDescription,
            'tenant_id' => tenant('id'), // Added missing tenant_id
        ]);

        $this->reset(['newDepartmentName', 'newDepartmentDescription']);
        LivewireAlert::title('Success')->success()->text('Department created successfully')->show();
    }

    public function editDepartment(int $id)
    {
        $dept = Department::findOrFail($id);
        $this->editingDepartmentId = $dept->id;
        $this->editDepartmentName = $dept->name;
        $this->editDepartmentDescription = $dept->description;
        $this->showDepartmentEditModal = true;
    }

    public function updateDepartment()
    {
        $this->validate([
            'editDepartmentName' => ['required', 'string', 'max:255', Rule::unique('departments', 'name')->ignore($this->editingDepartmentId)->where('tenant_id', tenant('id'))],
            'editDepartmentDescription' => 'nullable|string|max:1000',
        ]);

        $dept = Department::findOrFail($this->editingDepartmentId);
        $dept->name = $this->editDepartmentName;
        $dept->description = $this->editDepartmentDescription;
        $dept->save();

        $this->editingDepartmentId = null;
        $this->editDepartmentName = null;
        $this->editDepartmentDescription = null;
        $this->showDepartmentEditModal = false;

        LivewireAlert::title('Success')->success()->text('Department updated successfully')->show();
    }

    public function confirmDeleteDepartment($id)
    {
        $this->deletingDepartmentId = $id;
        $this->showDepartmentDeleteModal = true;
    }

    public function deleteDepartment()
    {
        Department::findOrFail($this->deletingDepartmentId)->delete();
        $this->deletingDepartmentId = null;
        $this->showDepartmentDeleteModal = false;
        LivewireAlert::title('Success')->success()->text('Department deleted successfully')->show();
    }

    // -----------------------
    // Ward CRUD
    // -----------------------
    public function addWard()
    {
        $this->validate([
            'newWardName' => 'required|string|max:255',
            'newWardNumber' => ['nullable', 'string', 'max:255', Rule::unique('wards', 'ward_number')->where('tenant_id', tenant('id'))],
            'newWardDepartmentId' => 'required|exists:departments,id',
            'newWardDescription' => 'nullable|string|max:1000',
        ]);

        Ward::create([
            'name' => $this->newWardName,
            'ward_number' => $this->newWardNumber,
            'department_id' => $this->newWardDepartmentId,
            'description' => $this->newWardDescription,
            'tenant_id' => tenant('id'),
        ]);

        $this->reset(['newWardName', 'newWardNumber', 'newWardDepartmentId', 'newWardDescription']);
        LivewireAlert::title('Success')->success()->text('Ward added successfully')->show();
    }

    public function editWard(int $id)
    {
        $ward = Ward::findOrFail($id);
        $this->editingWardId = $ward->id;
        $this->editWardName = $ward->name;
        $this->editWardNumber = $ward->ward_number;
        $this->editWardDepartmentId = $ward->department_id;
        $this->editWardDescription = $ward->description;
        $this->showWardEditModal = true;
    }

    public function updateWard()
    {
        $this->validate([
            'editWardName' => 'required|string|max:255',
            'editWardNumber' => ['nullable', 'string', 'max:255', Rule::unique('wards', 'ward_number')->ignore($this->editingWardId)->where('tenant_id', tenant('id'))],
            'editWardDepartmentId' => 'required|exists:departments,id',
            'editWardDescription' => 'nullable|string|max:1000',
        ]);

        $ward = Ward::findOrFail($this->editingWardId);
        $ward->name = $this->editWardName;
        $ward->ward_number = $this->editWardNumber;
        $ward->department_id = $this->editWardDepartmentId;
        $ward->description = $this->editWardDescription;
        $ward->save();

        $this->editingWardId = null;
        $this->editWardName = null;
        $this->editWardNumber = null;
        $this->editWardDepartmentId = null;
        $this->editWardDescription = null;
        $this->showWardEditModal = false;

        LivewireAlert::title('Success')->success()->text('Ward updated successfully')->show();
    }

    public function confirmDeleteWard($id)
    {
        $this->deletingWardId = $id;
        $this->showWardDeleteModal = true;
    }

    public function deleteWard()
    {
        Ward::findOrFail($this->deletingWardId)->delete();
        $this->deletingWardId = null;
        $this->showWardDeleteModal = false;
        LivewireAlert::title('Success')->success()->text('Ward deleted successfully')->show();
    }

    // -----------------------
    // BedType CRUD
    // -----------------------
    public function addBedType()
    {
        $this->validate([
            'newBedTypeName' => ['required', 'string', 'max:255', Rule::unique('bed_types', 'name')->where('tenant_id', tenant('id'))],
            'newBedTypeDescription' => 'nullable|string|max:1000',
            'newBedTypePrice' => 'required|numeric|min:0',
        ]);

        BedType::create([
            'name' => $this->newBedTypeName,
            'description' => $this->newBedTypeDescription,
            'price_per_day' => $this->newBedTypePrice,
            'tenant_id' => tenant('id'),
        ]);

        $this->reset(['newBedTypeName', 'newBedTypeDescription', 'newBedTypePrice']);
        LivewireAlert::title('Success')->success()->text('Bed type added successfully')->show();
    }

    public function editBedType(int $id)
    {
        $bt = BedType::findOrFail($id);
        $this->editingBedTypeId = $bt->id;
        $this->editBedTypeName = $bt->name;
        $this->editBedTypeDescription = $bt->description;
        $this->editBedTypePrice = $bt->price_per_day;
        $this->showBedTypeEditModal = true;
    }

    public function updateBedType()
    {
        $this->validate([
            'editBedTypeName' => ['required', 'string', 'max:255', Rule::unique('bed_types', 'name')->ignore($this->editingBedTypeId)->where('tenant_id', tenant('id'))],
            'editBedTypeDescription' => 'nullable|string|max:1000',
            'editBedTypePrice' => 'required|numeric|min:0',
        ]);

        $bt = BedType::findOrFail($this->editingBedTypeId);
        $bt->name = $this->editBedTypeName;
        $bt->description = $this->editBedTypeDescription;
        $bt->price_per_day = $this->editBedTypePrice;
        $bt->save();

        $this->editingBedTypeId = null;
        $this->editBedTypeName = null;
        $this->editBedTypeDescription = null;
        $this->editBedTypePrice = null;
        $this->showBedTypeEditModal = false;

        LivewireAlert::title('Success')->success()->text('Bed type updated successfully')->show();
    }

    public function confirmDeleteBedType($id)
    {
        $this->deletingBedTypeId = $id;
        $this->showBedTypeDeleteModal = true;
    }

    public function deleteBedType()
    {
        BedType::findOrFail($this->deletingBedTypeId)->delete();
        $this->deletingBedTypeId = null;
        $this->showBedTypeDeleteModal = false;
        LivewireAlert::title('Success')->success()->text('Bed type deleted successfully')->show();
    }

    // -----------------------
    // Bed CRUD
    // -----------------------
    public function addBed()
    {
        $this->validate([
            'newBedNumber' => ['required', 'string', 'max:255', Rule::unique('beds', 'bed_number')->where('tenant_id', tenant('id'))],
            'newBedWardId' => 'required|exists:wards,id',
            'newBedTypeId' => 'required|exists:bed_types,id',
            'newBedIsOccupied' => 'boolean',
        ]);

        Bed::create([
            'bed_number' => $this->newBedNumber,
            'ward_id' => $this->newBedWardId,
            'bed_type_id' => $this->newBedTypeId,
            'is_occupied' => (bool) $this->newBedIsOccupied,
            'tenant_id' => tenant('id'),
        ]);

        $this->reset(['newBedNumber', 'newBedWardId', 'newBedTypeId', 'newBedIsOccupied']);
        LivewireAlert::title('Success')->success()->text('Bed added successfully')->show();
    }

    public function editBed(int $id)
    {
        $bed = Bed::findOrFail($id);
        $this->editingBedId = $bed->id;
        $this->editBedNumber = $bed->bed_number;
        $this->editBedWardId = $bed->ward_id;
        $this->editBedTypeId = $bed->bed_type_id;
        $this->editBedIsOccupied = (bool) $bed->is_occupied;
        $this->showBedEditModal = true;
    }

    public function updateBed()
    {
        $this->validate([
            'editBedNumber' => ['required', 'string', 'max:255', Rule::unique('beds', 'bed_number')->ignore($this->editingBedId)->where('tenant_id', tenant('id'))],
            'editBedWardId' => 'required|exists:wards,id',
            'editBedTypeId' => 'required|exists:bed_types,id',
            'editBedIsOccupied' => 'boolean',
        ]);

        $bed = Bed::findOrFail($this->editingBedId);
        $bed->bed_number = $this->editBedNumber;
        $bed->ward_id = $this->editBedWardId;
        $bed->bed_type_id = $this->editBedTypeId;
        $bed->is_occupied = (bool) $this->editBedIsOccupied;
        $bed->save();

        $this->editingBedId = null;
        $this->editBedNumber = null;
        $this->editBedWardId = null;
        $this->editBedTypeId = null;
        $this->editBedIsOccupied = false;
        $this->showBedEditModal = false;

        LivewireAlert::title('Success')->success()->text('Bed updated successfully')->show();
    }

    public function confirmDeleteBed($id)
    {
        $this->deletingBedId = $id;
        $this->showBedDeleteModal = true;
    }

    public function deleteBed()
    {
        Bed::findOrFail($this->deletingBedId)->delete();
        $this->deletingBedId = null;
        $this->showBedDeleteModal = false;
        LivewireAlert::title('Success')->success()->text('Bed deleted successfully')->show();
    }

    // -----------------------
    // Supply CRUD
    // -----------------------
    public function addSupply()
    {
        $this->validate([
            'newSupplyName' => ['required', 'string', 'max:255', Rule::unique('supplies', 'name')->where('tenant_id', tenant('id'))],
            'newSupplyUnitOfMeasure' => 'nullable|string|max:255',
            'newSupplyCurrentStock' => 'required|numeric|min:0',
            'newSupplyMinStockLevel' => 'nullable|numeric|min:0',
        ]);

        Supply::create([
            'name' => $this->newSupplyName,
            'unit_of_measure' => $this->newSupplyUnitOfMeasure,
            'current_stock' => $this->newSupplyCurrentStock,
            'min_stock_level' => $this->newSupplyMinStockLevel,
            'tenant_id' => tenant('id'),
        ]);

        $this->reset(['newSupplyName', 'newSupplyUnitOfMeasure', 'newSupplyCurrentStock', 'newSupplyMinStockLevel']);
        LivewireAlert::title('Success')->success()->text('Supply added successfully')->show();
    }

    public function editSupply(int $id)
    {
        $s = Supply::findOrFail($id);
        $this->editingSupplyId = $s->id;
        $this->editSupplyName = $s->name;
        $this->editSupplyUnitOfMeasure = $s->unit_of_measure;
        $this->editSupplyCurrentStock = $s->current_stock;
        $this->editSupplyMinStockLevel = $s->min_stock_level;
        $this->showSupplyEditModal = true;
    }

    public function updateSupply()
    {
        $this->validate([
            'editSupplyName' => ['required', 'string', 'max:255', Rule::unique('supplies', 'name')->ignore($this->editingSupplyId)->where('tenant_id', tenant('id'))],
            'editSupplyUnitOfMeasure' => 'nullable|string|max:255',
            'editSupplyCurrentStock' => 'required|numeric|min:0',
            'editSupplyMinStockLevel' => 'nullable|numeric|min:0',
        ]);

        $s = Supply::findOrFail($this->editingSupplyId);
        $s->name = $this->editSupplyName;
        $s->unit_of_measure = $this->editSupplyUnitOfMeasure;
        $s->current_stock = $this->editSupplyCurrentStock;
        $s->min_stock_level = $this->editSupplyMinStockLevel;
        $s->save();

        $this->editingSupplyId = null;
        $this->editSupplyName = null;
        $this->editSupplyUnitOfMeasure = null;
        $this->editSupplyCurrentStock = null;
        $this->editSupplyMinStockLevel = null;
        $this->showSupplyEditModal = false;

        LivewireAlert::title('Success')->success()->text('Supply updated successfully')->show();
    }

    public function confirmDeleteSupply($id)
    {
        $this->deletingSupplyId = $id;
        $this->showSupplyDeleteModal = true;
    }

    public function deleteSupply()
    {
        Supply::findOrFail($this->deletingSupplyId)->delete();
        $this->deletingSupplyId = null;
        $this->showSupplyDeleteModal = false;
        LivewireAlert::title('Success')->success()->toast()->position('top-end')->text('Supply deleted successfully')->show();
    }

    // -----------------------
    // Render
    // -----------------------
    public function render()
    {
        return view('livewire.tenants.admin.settings', [
            'filteredDepartments' => Department::when($this->searchDepartment, fn ($q) => $q->where('name', 'like', '%'.$this->searchDepartment.'%'))->get(),

            'filteredWards' => Ward::with('department')
                ->when($this->searchWard, fn ($q) => $q->where('name', 'like', '%'.$this->searchWard.'%'))
                ->get(),

            'filteredBedTypes' => BedType::when($this->searchBedType, fn ($q) => $q->where('name', 'like', '%'.$this->searchBedType.'%'))->get(),

            'filteredBeds' => Bed::with(['ward.department', 'bedType'])
                ->when($this->searchBed, fn ($q) => $q->where('bed_number', 'like', '%'.$this->searchBed.'%'))
                ->get(),

            'filteredSupplies' => Supply::when($this->searchSupply, fn ($q) => $q->where('name', 'like', '%'.$this->searchSupply.'%'))->get(),
        ]);
    }
}
