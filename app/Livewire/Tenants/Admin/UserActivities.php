<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection; // Used for type-hinting 'users' collection in render view data
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator; // Used for activities type-hint

#[Layout('components.layouts.admin')]
class UserActivities extends Component
{
    // Livewire trait to handle pagination functionality
    use WithPagination;

    // --- Public Properties (Filters & State) ---

    /**
     * Search term for activity description and user name/email.
     * Persisted in the URL query string as 'q'.
     */
    #[Url(as: 'q')]
    public string $search = '';

    /**
     * Filter by a specific User ID.
     * Persisted in the URL query string as 'user'.
     */
    #[Url(as: 'user')]
    public string $filterUser = '';

    /**
     * Filter by a specific activity type (e.g., 'login', 'create_post').
     * Persisted in the URL query string as 'type'.
     */
    #[Url(as: 'type')]
    public string $filterType = '';

    /**
     * Filter activities by a specific date (YYYY-MM-DD).
     * Persisted in the URL query string as 'date'.
     */
    #[Url(as: 'date')]
    public ?string $dateFilter = null;

    /**
     * Holds the unique list of available activity types for the filter dropdown.
     * Filled during the component's mount.
     */
    public array $activityTypes = [];

    /**
     * Data array for the selected activity when the details modal is opened.
     */
    public ?array $selectedActivity = null;

    /**
     * Specifies the Livewire pagination view to use (Tailwind CSS by default).
     */
    protected string $paginationTheme = 'tailwind';

    // --- Lifecycle Hooks ---

    /**
     * Runs once immediately after the component is instantiated.
     * Populates the list of unique activity types from the database.
     */
    public function mount(): void
    {
        // Get all unique, non-null activity types, sort them, and convert to an array.
        $this->activityTypes = UserActivity::query()
            ->select('activity_type')
            ->whereNotNull('activity_type')
            ->distinct()
            ->orderBy('activity_type')
            ->pluck('activity_type')
            ->toArray();
    }

    /**
     * Hook that runs after a public property is updated from the front-end.
     *
     * @param string $propertyName The name of the property that was updated.
     * @return void
     */
    public function updated(string $propertyName): void
    {
        // Reset pagination to the first page when any primary filter changes
        if (\in_array($propertyName, ['search', 'filterUser', 'filterType', 'dateFilter'], true)) {
            $this->resetPage();
        }
    }

    // --- Action Methods ---

    /**
     * Clears all filter properties and resets the pagination.
     *
     * @return void
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterUser = '';
        $this->filterType = '';
        $this->dateFilter = null;
        $this->resetPage();
    }

    /**
     * Fetches the details for a specific activity and dispatches an event
     * to show the details modal on the front-end.
     *
     * @param int $activityId The ID of the UserActivity record to display.
     * @return void
     */
    public function showDetails(int $activityId): void
    {
        /** @var \App\Models\UserActivity|null $activity */
        $activity = UserActivity::query()
            // Eager load the user with only necessary columns
            ->with(['user:id,name,email,profile_picture'])
            ->find($activityId);

        if (!$activity) {
            // Dispatch a notification error if the activity is not found
            $this->dispatch('notify', type: 'error', message: 'Activity not found.');
            return;
        }

        // Prepare the activity data into a clean, simple array for the front-end (Alpine)
        $payload = [
            'id' => $activity->id,
            'activity_type' => $activity->activity_type,
            'description' => $activity->description,
            'ip_address' => $activity->ip_address,
            'user_agent' => $activity->user_agent,
            // Convert timestamp to ISO 8601 string for consistent front-end formatting
            'created_at' => $activity->created_at?->toIso8601String(),
            // Only include user data if the relation exists and is loaded
            'user' => $activity->relationLoaded('user') && $activity->user
                ? [
                    'id' => $activity->user->id,
                    'name' => $activity->user->name,
                    'email' => $activity->user->email,
                    'profile_picture' => $activity->user->profile_picture,
                ]
                : null,
        ];

        // Store the payload in the component state (optional, but good practice)
        $this->selectedActivity = $payload;

        // Dispatch an event to the browser to open the modal with the activity data
        $this->dispatch('open-activity-details', activity: $payload);
    }

    // --- Render Method ---

    /**
     * Renders the view and supplies data, including the paginated activities list.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        // Check if the database driver is PostgreSQL (for 'ilike' vs 'like' optimization)
        $isPgsql = DB::connection()->getDriverName() === 'pgsql';

        /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $activities */
        $activities = UserActivity::query()
            // Eager load the user relation
            ->with('user:id,name,email,profile_picture')

            // Apply search filter when the search term is present
            ->when(\filled($this->search), function (Builder $query) use ($isPgsql): void {
                $searchTerm = \trim($this->search);

                // Determine the correct case-insensitive LIKE operator
                $likeOperator = $isPgsql ? 'ilike' : 'like';
                $pattern = '%' . $searchTerm . '%';

                $query->where(function (Builder $q) use ($likeOperator, $pattern, $isPgsql, $searchTerm): void {
                    // 1. Search in the activity description field
                    if ($isPgsql) {
                        // PostgreSQL: uses case-insensitive 'ilike' directly
                        $q->where('description', $likeOperator, $pattern);
                    } else {
                        // MySQL/SQLite: uses LOWER() wrapper for case-insensitivity
                        $q->whereRaw('LOWER(description) like ?', [\mb_strtolower($pattern)]);
                    }

                    // 2. Search in the related user's name or email fields
                    $q->orWhereHas('user', function (Builder $uq) use ($likeOperator, $pattern, $isPgsql): void {
                        if ($isPgsql) {
                            // PostgreSQL: uses case-insensitive 'ilike' directly
                            $uq->where('name', $likeOperator, $pattern)
                               ->orWhere('email', $likeOperator, $pattern);
                        } else {
                            // MySQL/SQLite: uses LOWER() wrapper for case-insensitivity
                            $uq->whereRaw('LOWER(name) like ?', [\mb_strtolower($pattern)])
                               ->orWhereRaw('LOWER(email) like ?', [\mb_strtolower($pattern)]);
                        }
                    });
                });
            })

            // Apply filter by specific user ID
            ->when(\filled($this->filterUser),
                fn (Builder $q) => $q->where('user_id', $this->filterUser)
            )

            // Apply filter by specific activity type
            ->when(\filled($this->filterType),
                fn (Builder $q) => $q->where('activity_type', $this->filterType)
            )

            // Apply filter by specific date
            ->when(\filled($this->dateFilter), function (Builder $q): void {
                // Validate date format (YYYY-MM-DD) before applying the filter
                if (\preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $this->dateFilter)) {
                    $q->whereDate('created_at', $this->dateFilter);
                }
            })

            // Order results by the most recent activity first
            ->orderByDesc('created_at')

            // Paginate the results
            ->paginate(30)
            ->onEachSide(1); // Show one page number on each side of the current page

        // Fetch all users for the 'Filter by User' dropdown
        /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\User> $users */
        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return \view('livewire.tenants.admin.user-activities', [
            'activities' => $activities,
            'users' => $users,
            'activityTypes' => $this->activityTypes,
            'selectedActivity' => $this->selectedActivity,
        ]);
    }
}
