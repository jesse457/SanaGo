<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\Medication;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.pharmacist')]
class CreateDrugs extends Component
{
    use UserActivitiesTrait;

    public string $name;

    public float $unit_price_purchase;

    public int $stock_quantity;

    public int $min_stock_level;

    public string $description;

    public string $dosage_unit;

    protected $rules = [
        'name' => 'required|string|max:255',
        'unit_price_purchase' => 'required|numeric|min:0',
        'stock_quantity' => 'required|numeric|min:0',
        'min_stock_level' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ];

    protected $messages = [
        'name.required' => 'pharmacist.create_drugs_component.validation_name_required',
        'name.string' => 'pharmacist.create_drugs_component.validation_name_string',
        'name.max' => 'pharmacist.create_drugs_component.validation_name_max',
        'unit_price_purchase.required' => 'pharmacist.create_drugs_component.validation_unit_price_required',
        'unit_price_purchase.numeric' => 'pharmacist.create_drugs_component.validation_unit_price_numeric',
        'unit_price_purchase.min' => 'pharmacist.create_drugs_component.validation_unit_price_min',
        'stock_quantity.required' => 'pharmacist.create_drugs_component.validation_stock_quantity_required',
        'stock_quantity.numeric' => 'pharmacist.create_drugs_component.validation_stock_quantity_numeric',
        'stock_quantity.min' => 'pharmacist.create_drugs_component.validation_stock_quantity_min',
        'min_stock_level.required' => 'pharmacist.create_drugs_component.validation_min_stock_level_required',
        'min_stock_level.numeric' => 'pharmacist.create_drugs_component.validation_min_stock_level_numeric',
        'min_stock_level.min' => 'pharmacist.create_drugs_component.validation_min_stock_level_min',
        'description.string' => 'pharmacist.create_drugs_component.validation_description_string',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function saveDrug()
    {
        $this->validate();

        try {
            DB::connection('pgsql_transaction')->transaction(function () {
                $drug = Medication::create([
                    'name' => $this->name,
                    'unit_price_purchase' => $this->unit_price_purchase,
                    'min_stock_level' => $this->min_stock_level,
                    'stock_quantity' => $this->stock_quantity,
                    'description' => $this->description,
                    'dosage_unit' => $this->dosage_unit,
                ]);
                $this->logActivity('Drug_created', "New drug '{$this->name}' was created.", ['drug_id' => $drug->id]);

            });
            LivewireAlert::title(__('pharmacist.create_drugs_component.alert_success'))
                ->text(__('pharmacist.create_drugs_component.alert_drug_created_successfully'))
                ->success()
                ->show();
            $this->reset(); // Reset form fields after successful submission

            return redirect()->route('pharmacist.manage-drugs');
        } catch (\Exception $e) {
            LivewireAlert::title(__('pharmacist.create_drugs_component.alert_error'))
                ->text(__('pharmacist.create_drugs_component.alert_failed_to_create_drug', ['error' => $e->getMessage()]))
                ->error()
                ->show();
            session()->flash('error', __('pharmacist.create_drugs_component.alert_failed_to_create_drug', ['error' => $e->getMessage()]));
        }
    }

    public function render()
    {
        return view('livewire.tenants.pharmacist.create-drugs');
    }
}
