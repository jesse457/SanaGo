<?php

namespace App\Livewire\Tenants\Admin;

use App\Services\Dashboards\AdminRevenueService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class RevenueDashboard extends Component
{
    use WithPagination;

    public string $timePeriod = 'month';

    // Stats Properties
    public float $totalRevenue = 0.0;

    public float $revenueGrowth = 0.0;

    public array $breakdown = [];

    public function mount(AdminRevenueService $service)
    {
        $this->loadStats($service);
    }

    public function updatedTimePeriod()
    {
        $this->resetPage();
        // Stats are reloaded in render or explicitly here depending on preference
        // If we want reactive props immediately:
        $this->loadStats(app(AdminRevenueService::class));
    }

    private function loadStats(AdminRevenueService $service): void
    {
        $stats = $service->getRevenueStats($this->timePeriod);

        $this->totalRevenue = $stats['total_revenue'];
        $this->revenueGrowth = $stats['growth_percentage'];
        $this->breakdown = $stats['breakdown'];
    }

    public function render(AdminRevenueService $service)
    {
        return view('livewire.tenants.admin.revenue-dashboard', [
            'patientRevenues' => $service->getPatientRevenueList($this->timePeriod, 10),
        ]);
    }
}
