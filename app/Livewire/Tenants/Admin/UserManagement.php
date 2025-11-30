<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\User;
use App\Models\UserShift;
use App\Traits\UserActivitiesTrait; // <-- Import the activity logging trait
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule as ValidationRule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

// Assigns the admin layout to this component.
#[Layout('components.layouts.admin')]
class UserManagement extends Component
{
    // Enables pagination functionality for the user list.
    use WithPagination;
    // Enables logging activities for admin actions.
    use UserActivitiesTrait;

    // --- Search and Filter Properties ---
    /** @var string The search term used to filter users by name or email. */
    public string $search = '';

    /** @var string Filter for user role (e.g., 'doctor', 'nurse'). */
    public string $filterRole = '';

    /** @var string Filter for user status ('active' or empty for all). */
    public string $filterStatus = '';

    // --- Form Properties for Edit Modal ---

    /** @var int|null The ID of the user currently being edited or deleted. */
    public ?int $userId = null;

    /** @var string The user's name, required for updating. */
    #[Rule('required|string|max:255')]
    public string $name = '';

    /** @var string The user's email, required and unique validation is handled in updateUser(). */
    #[Rule('required|email')]
    public string $email = '';

    /** @var string The user's assigned role, required. */
    #[Rule('required|string')]
    public string $role = '';

    /** @var string|null The user's phone number, optional. */
    #[Rule('nullable|string|max:20')]
    public ?string $phone_number = '';

    /** @var bool The user's active status. */
    #[Rule('boolean')]
    public bool $is_active = true;

    /** @var int|null The ID of the shift assigned to the user. */
    #[Rule('nullable|exists:user_shifts,id')]
    public ?int $selected_shift_id = null;

    /** @var Collection History of past user shifts (used in the edit modal). */
    public Collection $userShiftHistory;

    /**
     * Initializes component state when mounted.
     *
     * @return void
     */
    public function mount()
    {
        // Initializes the shift history collection to prevent errors if empty.
        $this->userShiftHistory = new Collection();
    }

    /**
     * Resets all form and temporary state properties.
     *
     * @return void
     */
    public function resetForm(): void
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'role',
            'phone_number',
            'is_active',
            'selected_shift_id',
        ]);
        // Resets the shift history and clears any validation errors.
        $this->userShiftHistory = new Collection();
        $this->resetErrorBag();
    }

    /**
     * Fetches the filtered and paginated list of users.
     *
     * This is a Livewire computed property, meaning it automatically updates
     * when `search`, `filterRole`, or `filterStatus` changes.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    #[Computed]
    public function users()
    {
        return User::with('shifts') // Eager-load shifts to avoid N+1 issues
            ->where('role', '!=', 'admin') // Exclude current admin users from the list
            // Apply search filter to name or email
            ->when($this->search, fn ($q) => $q->where(fn ($sq) => $sq->where('name', 'LIKE', "%{$this->search}%")->orWhere('email', 'LIKE', "%{$this->search}%")))
            // Apply role filter
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            // Apply status filter ('active' or inactive)
            ->when($this->filterStatus !== '', fn ($q) => $q->where('is_active', $this->filterStatus === 'active'))
            ->orderBy('name')
            ->paginate(10);
    }

    /**
     * Fetches shifts available for assignment from today onwards.
     *
     * This is a Livewire computed property.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    #[Computed]
    public function availableShifts()
    {
        return UserShift::where('shift_date', '>=', now()->toDateString())
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Fetches user data and dispatches an event to open the view modal.
     *
     * @param int $userId The ID of the user to view.
     * @return void
     */
    public function viewUser(int $userId): void
    {
        // Eager-load related shifts and activities for the modal display.
        $user = User::with('shifts', 'activities')->findOrFail($userId);
        $this->dispatch('open-view-modal', user: $user->toArray());
    }

    /**
     * Loads user data into the form properties and prepares the edit modal.
     *
     * @param int $userId The ID of the user to edit.
     * @return void
     */
    public function editUser(int $userId): void
    {
        $this->resetForm();
        $user = User::findOrFail($userId);

        // Populate form properties
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->phone_number = $user->phone_number;
        $this->is_active = $user->is_active;

        $now = now()->startOfDay();

        // Query for the single upcoming shift
        $upcomingShift = $user->shifts()->where('shift_date', '>=', $now)->orderBy('shift_date')->first();
        // Query for past shift history
        $this->userShiftHistory = $user->shifts()->where('shift_date', '<', $now)->orderBy('shift_date', 'desc')->get();

        $this->selected_shift_id = $upcomingShift?->id;

        $this->dispatch('open-edit-modal');
    }

    /**
     * Closes the user edit modal and clears the form state.
     *
     * @return void
     */
    public function closeEditModal(): void
    {
        $this->resetForm();
        $this->dispatch('close-edit-modal');
    }

    /**
     * Validates the form, updates the user record, and manages shift assignment.
     *
     * @return void
     */
    public function updateUser()
    {
        // Validate specific fields, including dynamic unique rule for email.
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', ValidationRule::unique('users')->ignore($this->userId)],
            'role' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'selected_shift_id' => 'nullable|exists:user_shifts,id',
        ]);

        if (! $this->userId) {
            return;
        }

        /** @var User $user */
        $user = User::findOrFail($this->userId);
        $user->update($validated);

        // --- Shift Assignment Logic ---
        $currentUpcomingShift = $user->shifts()->where('shift_date', '>=', now()->startOfDay())->first();

        // Check if the assigned shift needs to change
        if ($currentUpcomingShift?->id !== $this->selected_shift_id) {
            // Detach the old upcoming shift to ensure only one is scheduled
            if ($currentUpcomingShift) {
                $user->shifts()->detach($currentUpcomingShift->id);
            }
            // Attach the new selected shift, if one was chosen
            if ($this->selected_shift_id) {
                $user->shifts()->attach($this->selected_shift_id);
            }
        }
        // Log the activity
        $this->logActivity(
            'user_updated',
            "Updated user profile and assignment for user: {$user->name} ({$user->id})",
            ['user_id' => $user->id]
        );

        $this->closeEditModal();
        LivewireAlert::title('Updated successfully!')
            ->success()
            ->show();
    }

    /**
     * Sets the user ID and displays a confirmation alert for deletion.
     *
     * @param int $userId The ID of the user targeted for deletion.
     * @return void
     */
    public function viewDeleteUser(int $userId): void
    {
        $this->userId = $userId;
        $user = User::findOrFail($userId);

        // Show a Livewire Alert confirmation box before executing deletion.
        LivewireAlert::title('Are you sure?')
            ->warning()
            ->text("You are about to delete {$user->name}. This action cannot be undone. Once done all data concerned with user will be deleted")
            ->asConfirm()
            ->onConfirm('deleteUser') // The action to call on confirmation
            ->show();
    }

    /**
     * Deletes the selected user and logs the action.
     *
     * This method is triggered by the Livewire Alert confirmation.
     *
     * @return void
     */
    #[On('deleteUser')]
    public function deleteUser(): void
    {
        /** @var User $user */
        $user = User::findOrFail($this->userId);

        $user->delete();

        // Log the deletion activity
        $this->logActivity(
            'user_deleted',
            "Deleted user: {$user->name} ({$user->id})",
            ['user_id' => $user->id]
        );

        // Ensure pagination is reset to page 1 after deletion.
        $this->resetPage();

        LivewireAlert::title('Deleted successfully!')
            ->success()
            ->show();
    }

    /**
     * Renders the component view, passing computed data.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.tenants.admin.user-management', [
            'users' => $this->users, // Passes the computed users list
            'availableShifts' => $this->availableShifts, // Passes the computed shifts list
        ]);
    }
}
