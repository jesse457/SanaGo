<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\Medication;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.pharmacist')]
class ManageDrugsInventory extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    // modal toggle
    public bool $showDrugEditModal = false;

    // currently edited drug id
    public ?int $drugId = null;

    // editable fields
    public $drug_name;
    public $dosage_units;
    public $price;
    public $stock_quantity;
    public $min_stock_level;
    public $description;

    protected $rules = [
        'drug_name' => 'required|string|max:255',
        'dosage_units' => 'nullable|string|max:100',
        'price' => 'nullable|numeric|min:0',
        'stock_quantity' => 'nullable|integer|min:0',
        'min_stock_level' => 'nullable|integer|min:0',
        'description' => 'nullable|string|max:1000',
    ];

    /**
     * Resets the pagination to the first page when a filter or search term changes.
     */
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    /**
     * Opens the edit modal and populates fields for the given drug.
     *
     * @param int $drugId
     * @return void
     */
    public function viewEditDrug(int $drugId)
    {
        $drug = Medication::findOrFail($drugId);

        $this->drugId = $drug->id;
        $this->drug_name = $drug->name;
        $this->dosage_units = $drug->dosage_units ?? $drug->units ?? '';
        $this->price = $drug->unit_price_purchase ?? 0;
        $this->stock_quantity = $drug->stock_quantity ?? 0;
        $this->min_stock_level = $drug->min_stock_level ?? 0;
        $this->description = $drug->description ?? '';

        $this->showDrugEditModal = true;
    }

    /**
     * Update the drug record
     */
    public function updateDrug()
    {
        $this->validate();

        try {
            $drug = Medication::findOrFail($this->drugId);

            $drug->name = $this->drug_name;
            // adapt field names to your model as needed:
            if (isset($drug->dosage_units)) {
                $drug->dosage_units = $this->dosage_units;
            } elseif (isset($drug->units)) {
                $drug->units = $this->dosage_units;
            }
            $drug->unit_price_purchase = $this->price;
            $drug->stock_quantity = $this->stock_quantity;
            $drug->min_stock_level = $this->min_stock_level;
            $drug->description = $this->description;

            $drug->save();

            $this->showDrugEditModal = false;

            LivewireAlert::title(__('pharmacist.manage_drugs_component.alert_success'))->success()->text(__('pharmacist.manage_drugs_component.alert_drug_updated_successfully'))->show();
        } catch (\Exception $e) {
            Log::error('Failed to update drug: '.$e->getMessage());
            LivewireAlert::title(__('pharmacist.manage_drugs_component.alert_error'))->error()->text(__('pharmacist.manage_drugs_component.alert_failed_to_update_drug'))->show();
        }
    }

    public function viewDeleteDrug($drugId)
    {
        $drug = Medication::findOrFail($drugId);
        $this->drugId = $drug->id;
        LivewireAlert::title((__('pharmacist.manage_drugs_component.alert_are_you_sure')))
            ->warning()
            ->text(__('pharmacist.manage_drugs_component.alert_confirm_delete_text', ['drug_name' => $drug->name]))
            ->asConfirm()
            ->onConfirm('deleteDrug')
            ->show();
    }

    /**
     * Deletes a specific medication from the database.
     *
     * @param  int  $id  The ID of the medication to delete.
     */
    #[On('deleteDrug')]
    public function deleteDrug()
    {
        try {
            Medication::findOrFail($this->drugId)->delete();
            LivewireAlert::title(__('pharmacist.manage_drugs_component.alert_success'))
                ->success()
                ->text(__('pharmacist.manage_drugs_component.alert_drug_deleted_successfully'))
                ->show();
        } catch (\Exception $e) {
            Log::error('error', 'Failed to delete drug: '.$e->getMessage());
            LivewireAlert::title(__('pharmacist.manage_drugs_component.alert_error'))->error()->text(__('pharmacist.manage_drugs_component.alert_failed_to_delete_drug'))->show();
        }
    }

    public function render()
    {
        $query = Medication::query();

        // Apply search filter
        if ($this->search) {
            $query->whereBlind('name', 'name_index', $this->search);
        }

        // Apply status filter for low stock
        if ($this->statusFilter === 'low-stock') {
            $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
        } elseif ($this->statusFilter === 'in-stock') {
            $query->whereColumn('stock_quantity', '>', 'min_stock_level');
        }

        $drugs = $query->paginate(10); // Paginate with 10 items per page

        return view('livewire.tenants.pharmacist.manage-drugs-inventory', [
            'drugs' => $drugs,
        ]);
    }
}
