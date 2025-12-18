<?php

namespace App\Livewire\Tenants\Pharmacist;

use App\Models\Dispensation;
use App\Models\Medication;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.pharmacist')]
class Dashboard extends Component
{
    use WithPagination;

    // Properties for the new summary cards
    public int $prescriptionsDispensedToday;

    public int $prescriptionsPending;

    public int $drugsLeftInInventory;

    // Advanced table properties for Medication Stock Levels
    public string $search = '';

    public string $sortBy = 'name';

    public $sortDirection = 'asc';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    /**
     * Loads the data for the dashboard summary cards.
     * This method is called on mount and can be re-called if needed.
     */
    public function loadDashboardData(): void
    {
        $today = Carbon::today();

        // 1. Prescriptions dispensed today
        // Count prescriptions that have at least one item dispensed today.
        // This now correctly uses the nested relationship from Prescription -> PrescriptionItem -> Dispensation.
        $this->prescriptionsDispensedToday = Prescription::whereHas('items.dispensations', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->count();

        // 2. Prescriptions pending (not fully dispensed)
        $this->prescriptionsPending = Prescription::where('status', 'prescribed')
            ->whereHas('items', function ($query) {
                $query->whereColumn('quantity_prescribed', '>', 'dispensed_quantity');
            })->count();

        // 3. Drugs left in inventory
        // Sum the stock_quantity of all medications.
        $this->drugsLeftInInventory = Medication::sum('stock_quantity');
    }

    /**
     * Computed property for Medication Stock Levels table.
     */
    public function getMedicationsProperty(): LengthAwarePaginator
    {
        $query = Medication::query();

        if ($this->search) {
            $query->where('name', 'ILIKE', "%{$this->search}%");
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(10);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render(): View
    {
        // No additional data needed for the render method now.
        // All necessary data is loaded in mount().
        return view('livewire.tenants.pharmacist.dashboard');
    }
}
