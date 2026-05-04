<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class UserActivities extends Component
{
    use WithPagination;

    // --- Public Properties (Filters & State) ---

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'user')]
    public string $filterUser = '';

    #[Url(as: 'type')]
    public string $filterType = '';

    #[Url(as: 'date')]
    public ?string $dateFilter = null;

    public array $activityTypes = [];

    public ?array $selectedActivity = null;

    protected string $paginationTheme = 'tailwind';

    // --- Lifecycle Hooks ---

    public function mount(): void
    {
        // Null-safe: filter out nulls and convert to array
        $this->activityTypes = UserActivity::query()
            ->select('activity_type')
            ->whereNotNull('activity_type')
            ->distinct()
            ->orderBy('activity_type')
            ->pluck('activity_type')
            ->filter() // Remove any remaining null/empty values
            ->values()
            ->toArray();
    }

    public function updated(string $propertyName): void
    {
        if (in_array($propertyName, ['search', 'filterUser', 'filterType', 'dateFilter'], true)) {
            $this->resetPage();
        }
    }

    // --- Action Methods ---

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterUser = '';
        $this->filterType = '';
        $this->dateFilter = null;
        $this->resetPage();
    }

    public function showDetails(int $activityId): void
    {
        $activity = UserActivity::query()
            ->with(['user:id,name,email,profile_picture'])
            ->find($activityId);

        if (! $activity) {
            $this->dispatch('notify', type: 'error', message: 'Activity not found.');
            return;
        }

        // Null-safe payload preparation
        $user = $activity->user;
        $payload = [
            'id' => $activity->id,
            'activity_type' => $activity->activity_type ?? '',
            'description' => $activity->description ?? '',
            'ip_address' => $activity->ip_address ?? '',
            'user_agent' => $activity->user_agent ?? '',
            'properties' => $activity->properties ?? [],
            'created_at' => $activity->created_at?->toIso8601String(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'profile_picture' => $user->profile_picture, // Can remain null for frontend handling
            ] : null,
        ];

        $this->selectedActivity = $payload;
        $this->dispatch('open-activity-details', activity: $payload);
    }

    // --- Render Method ---

    public function render()
    {
        $isPgsql = DB::connection()->getDriverName() === 'pgsql';

        $activities = UserActivity::query()
            ->with('user:id,name,email,profile_picture')

            // Search filter with null-safe COALESCE handling
            ->when(filled($this->search), function (Builder $query) use ($isPgsql): void {
                $searchTerm = trim($this->search);
                $pattern = '%'.$searchTerm.'%';

                $query->where(function (Builder $q) use ($isPgsql, $pattern): void {
                    if ($isPgsql) {
                        // PostgreSQL: use COALESCE to handle NULL descriptions
                        $q->whereRaw('COALESCE(description, \'\' ) ILIKE ?', [$pattern]);
                    } else {
                        // MySQL/SQLite: use LOWER + COALESCE for case-insensitive null-safe search
                        $q->whereRaw('LOWER(COALESCE(description, \'\' )) LIKE ?', [mb_strtolower($pattern)]);
                    }

                    $q->orWhereHas('user', function (Builder $uq) use ($isPgsql, $pattern): void {
                        if ($isPgsql) {
                            $uq->whereRaw('COALESCE(name, \'\' ) ILIKE ?', [$pattern])
                                ->orWhereRaw('COALESCE(email, \'\' ) ILIKE ?', [$pattern]);
                        } else {
                            $uq->whereRaw('LOWER(COALESCE(name, \'\' )) LIKE ?', [mb_strtolower($pattern)])
                                ->orWhereRaw('LOWER(COALESCE(email, \'\' )) LIKE ?', [mb_strtolower($pattern)]);
                        }
                    });
                });
            })

            // Filter by user ID (null-safe via filled() check)
            ->when(filled($this->filterUser),
                fn (Builder $q) => $q->where('user_id', $this->filterUser)
            )

            // Filter by activity type
            ->when(filled($this->filterType),
                fn (Builder $q) => $q->where('activity_type', $this->filterType)
            )

            // Filter by date with format validation
            ->when(filled($this->dateFilter), function (Builder $q): void {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $this->dateFilter)) {
                    $q->whereDate('created_at', $this->dateFilter);
                }
            })

            ->orderByDesc('created_at')
            ->paginate(30)
            ->onEachSide(1);

        // Null-safe user list for dropdown
        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name ?? 'Unnamed User',
            ]);

        return view('livewire.tenants.admin.user-activities', [
            'activities' => $activities,
            'users' => $users,
            'activityTypes' => $this->activityTypes,
            'selectedActivity' => $this->selectedActivity,
        ]);
    }
}
