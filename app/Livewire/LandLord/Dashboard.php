<?php

namespace App\Livewire\LandLord;

use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

// Use the landlord layout for this Livewire component
#[Layout('components.layouts.landlord')]
class Dashboard extends Component
{
    public $totalTenants;

    public $activeSubscriptions;

    public $monthlyRevenue;

    public $newTenants;

    public $recentTenants = [];

    public $subscriptionStats = [];

    public $revenueChart = [];

    public $tenantGrowthChart = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function setLocal($local)
    {
        Session::put('locale', $local);
        // You might want to reload data or redirect after changing locale
        // For example: return redirect(request()->header('Referer'));
    }

    public function loadDashboardData()
    {
        // Get the current database driver to determine the correct date formatting function
        $dbDriver = DB::connection()->getDriverName();
        $dateGroupingRaw = '';

        // *** KEY CHANGE: Use a switch statement for database-agnostic date formatting ***
        switch ($dbDriver) {
            case 'pgsql':
                // TO_CHAR for PostgreSQL
                $dateGroupingRaw = "TO_CHAR(created_at, 'Mon YYYY')";
                break;
            case 'sqlite':
                // strftime for SQLite. Note: it uses the table name in the function.
                $dateGroupingRaw = "strftime('%b %Y', created_at)";
                break;
            default:
                // DATE_FORMAT for MySQL, MariaDB, etc.
                $dateGroupingRaw = "DATE_FORMAT(created_at, '%b %Y')";
        }

        // Get total tenants count
        $this->totalTenants = Tenant::count();

        // Get active subscriptions count
        $this->activeSubscriptions = Subscription::where('status', 'active')->count();

        // Calculate Monthly Recurring Revenue (MRR) - sums the amount for all active subscriptions
        $this->monthlyRevenue = Subscription::where('status', 'active')
            ->sum('amount');

        // Get new tenants in the last 30 days
        $this->newTenants = Tenant::where('created_at', '>=', now()->subDays(30))->count();

        // Get recent tenants with their subscription info
        $this->recentTenants = Tenant::with('subscription', 'domains')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($tenant) {
                // Use the nullsafe operator (?->) for robust relationship access
                $domain = $tenant->domains->first();
                $subscription = $tenant->subscription;

                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name ?? 'Unknown',
                    'hospital_name' => $tenant->data['hospital_name'] ?? 'Not specified',
                    'domain' => optional($domain)->domain ?? 'N/A', // Using optional helper is also very safe
                    // Safely access plan and status
                    'subscription' => $subscription?->plan ?? 'N/A',
                    'status' => $subscription?->status ?? 'inactive',
                    // Format the date to a string here for easy access in Blade
                    'created_at' => $tenant->created_at->format('M d, Y'),
                ];
            })->toArray(); // Convert to array for Livewire serialization

        // Get subscription statistics
        $this->subscriptionStats = Subscription::select('plan', DB::raw('count(*) as count'))
            ->groupBy('plan')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->plan => $item->count];
            });

        // Prepare the last 6 months labels for chart completeness
        // The format 'M Y' (e.g., "Oct 2025") must match the database grouping output
        $dateLabels = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('M Y');
        })->reverse();

        // --- Revenue Chart Data (Last 6 Months) ---
        $rawRevenueData = Subscription::select(
            DB::raw("{$dateGroupingRaw} as period"),
            DB::raw('SUM(amount) as revenue')
        )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->where('status', 'active')
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        // Map data to ensure every month is present (filling in 0 revenue where none exists)
        $this->revenueChart = $dateLabels->map(function ($month) use ($rawRevenueData) {
            return [
                'month' => Str::before($month, ' '), // e.g., "Oct"
                'revenue' => (float) ($rawRevenueData->get($month)->revenue ?? 0),
            ];
        })->values()->toArray();

        // --- Tenant Growth Data (Last 6 Months) ---
        $rawTenantData = Tenant::select(
            DB::raw("{$dateGroupingRaw} as period"),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('period')
            ->get()
            ->keyBy('period');

        // Map data to ensure every month is present (filling in 0 tenants where none exists)
        $this->tenantGrowthChart = $dateLabels->map(function ($month) use ($rawTenantData) {
            return [
                'month' => Str::before($month, ' '), // e.g., "Oct"
                'count' => (int) ($rawTenantData->get($month)->count ?? 0),
            ];
        })->values()->toArray();
    }

    public function render()
    {
        return view('livewire.land-lord.dashboard');
    }
}
