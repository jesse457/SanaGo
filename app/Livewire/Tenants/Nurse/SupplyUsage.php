<?php

namespace App\Livewire\Tenants\Nurse;

use App\Models\Supply;
use App\Models\SupplyUsage as SupplyUsageModel;
use App\Traits\UserActivitiesTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.nurse')]
class SupplyUsage extends Component
{
    use UserActivitiesTrait;

    public Collection $supplies;

    public array $quantitiesUsed = [];

    // Properties for the success modal
    public $showSuccessModal = false;

    public $successMessage = '';

    public function mount()
    {
        $this->supplies = Supply::all();
        foreach ($this->supplies as $supply) {
            $this->quantitiesUsed[$supply->id] = 0;
        }
    }

    public function recordSupplyUsage($supplyId)
    {
        $supply = Supply::findOrFail($supplyId);
        $quantityUsed = (int) $this->quantitiesUsed[$supplyId];

        // Validate the input
        $this->validate([
            "quantitiesUsed.{$supplyId}" => 'required|integer|min:1',
        ], [
            "quantitiesUsed.{$supplyId}.required" => 'Usage quantity is required.',
            "quantitiesUsed.{$supplyId}.integer" => 'Usage quantity must be an integer.',
            "quantitiesUsed.{$supplyId}.min" => 'Usage quantity must be at least 1.',
        ]);

        // Check if there's enough stock
        if ($supply->current_stock < $quantityUsed) {
            session()->flash('error', 'Not enough ' . $supply->name . ' in stock. Available: ' . $supply->current_stock);

            return;
        }
        DB::connection('pgsql_transaction')->transaction(function () use ($quantityUsed,$supply,$supplyId) {
            // Create a new SupplyUsage record
            SupplyUsageModel::create([
                'supply_id' => $supply->id,
                'user_id' => Auth::id(),
                'patient_id' => null, // Placeholder: You'll need a mechanism to select a patient if this is for specific patient usage
                'quantity_used' => $quantityUsed,
                'usage_date' => now(),
            ]);

            // Decrease the current stock of the supply
            $supply->current_stock -= $quantityUsed;
            $supply->save();

            // Reset the quantity used input for this supply
            $this->quantitiesUsed[$supplyId] = 0;

            $this->logActivity(
                'supply_used',
                Auth::user()->name . ' recorded usage of ' . $quantityUsed . ' ' . $supply->unit_of_measure . ' of ' . $supply->name,
                [
                    'supply_id' => $supply->id,
                    'user_id' => Auth::id(),
                    'quantity_used' => $quantityUsed,
                ]
            );
        });

        LivewireAlert::title('Success')
            ->success()
            ->text($quantityUsed . ' ' . $supply->unit_of_measure . ' of ' . $supply->name . ' recorded. Stock: ' . $supply->current_stock)
            ->show();
    }

    public function render()
    {
        return view('livewire.tenants.nurse.supply-usage');
    }
}
