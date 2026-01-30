<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Services\PharmacyService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.pharmacist')]
class CreateDrugs extends Component
{
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|numeric|min:0')]
    public $unit_price_purchase;

    #[Rule('required|integer|min:0')]
    public $stock_quantity;

    #[Rule('required|integer|min:0')]
    public $min_stock_level;

    #[Rule('nullable|string')]
    public string $description = '';

    #[Rule('required|string|max:50')]
    public string $dosage_unit = '';

    public function saveDrug(PharmacyService $service)
    {
        $this->validate();

        try {
            $service->createMedication([
                'name' => $this->name,
                'unit_price_purchase' => $this->unit_price_purchase,
                'min_stock_level' => $this->min_stock_level,
                'stock_quantity' => $this->stock_quantity,
                'description' => $this->description,
                'dosage_unit' => $this->dosage_unit,
            ]);

            LivewireAlert::success(__('Drug created successfully!'))->show();

            return redirect()->route('pharmacist.manage-drugs');
        } catch (\Exception $e) {
            LivewireAlert::error(__('Failed: ') . $e->getMessage())->show();
        }
    }

    public function render()
    {
        return view('livewire.tenants.pharmacist.create-drugs');
    }
}
