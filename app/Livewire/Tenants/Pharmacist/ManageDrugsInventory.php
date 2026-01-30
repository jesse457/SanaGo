<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\Medication;
use App\Services\PharmacyService;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.pharmacist')]
class ManageDrugsInventory extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public bool $showDrugEditModal = false;
    public ?int $drugId = null;

    /**
     * Declarative Validation Rules
     */
    #[Rule('required|string|max:255')]
    public $drug_name;

    #[Rule('nullable|string|max:100')]
    public $dosage_units;

    #[Rule('required|numeric|min:0')]
    public $price;

    #[Rule('required|integer|min:0')]
    public $stock_quantity;

    #[Rule('required|integer|min:0')]
    public $min_stock_level;

    #[Rule('nullable|string|max:1000')]
    public $description;

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }

    /**
     * Populate modal with medication data.
     */
    public function viewEditDrug(int $drugId)
    {
        $drug = Medication::findOrFail($drugId);

        $this->drugId = $drug->id;
        $this->drug_name = $drug->name;
        // Logic to handle varying column names in your schema (dosage_units vs units)
        $this->dosage_units = $drug->dosage_units ?? $drug->units ?? '';
        $this->price = $drug->unit_price_purchase ?? 0;
        $this->stock_quantity = $drug->stock_quantity ?? 0;
        $this->min_stock_level = $drug->min_stock_level ?? 0;
        $this->description = $drug->description ?? '';

        $this->showDrugEditModal = true;
    }

    /**
     * Update drug using Service.
     */
    public function updateDrug(PharmacyService $service)
    {
        $this->validate();

        try {
            $service->updateMedication($this->drugId, [
                'name' => $this->drug_name,
                'dosage_units' => $this->dosage_units,
                'unit_price_purchase' => $this->price,
                'stock_quantity' => $this->stock_quantity,
                'min_stock_level' => $this->min_stock_level,
                'description' => $this->description,
            ]);

            $this->showDrugEditModal = false;
            LivewireAlert::success(__('Drug updated successfully'))->show();
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            LivewireAlert::error(__('Failed to update drug.'))->show();
        }
    }

    /**
     * Trigger SweetAlert confirmation.
     */
    public function viewDeleteDrug(int $drugId)
    {
        $drug = Medication::findOrFail($drugId);
        $this->drugId = $drug->id;

        LivewireAlert::title(__('Are you sure?'))
            ->warning()
            ->text(__('Delete :name? This action is permanent.', ['name' => $drug->name]))
            ->asConfirm()
            ->onConfirm('deleteDrug')
            ->show();
    }

    /**
     * Confirmed deletion via Service.
     */
    #[On('deleteDrug')]
    public function deleteDrug(PharmacyService $service)
    {
        try {
            if ($service->deleteMedication($this->drugId)) {
                LivewireAlert::success(__('Drug deleted successfully'))->show();
            }
        } catch (\Exception $e) {
            Log::error('Delete error: ' . $e->getMessage());
            LivewireAlert::error(__('Could not delete drug.'))->show();
        }
    }

    public function render(PharmacyService $service)
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->statusFilter,
        ];

        return view('livewire.tenants.pharmacist.manage-drugs-inventory', [
            'drugs' => $service->getInventoryQuery($filters)->paginate(10),
        ]);
    }
}
