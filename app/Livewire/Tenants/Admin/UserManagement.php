<?php

namespace App\Livewire\Tenants\Admin;

use App\Mail\UserInvitationMail; // Import your mailable
use App\Models\User;
use App\Models\UserShift;
use App\Traits\UserActivitiesTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail; // Import Mail facade
use Illuminate\Support\Facades\Password; // Import Password facade
use Illuminate\Validation\Rule as ValidationRule;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class UserManagement extends Component
{
    use UserActivitiesTrait;
    use WithPagination;

    // --- Search and Filter Properties ---
    public string $search = '';
    public string $filterRole = '';
    public string $filterStatus = '';

    // --- Form Properties ---
    public ?int $userId = null;

    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|string')]
    public string $role = '';

    #[Rule('nullable|string|max:20')]
    public ?string $phone_number = '';

    #[Rule('boolean')]
    public bool $is_active = true;

    #[Rule('nullable|exists:user_shifts,id')]
    public ?int $selected_shift_id = null;

    public Collection $userShiftHistory;

    public function mount()
    {
        $this->userShiftHistory = new Collection;
    }

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
        $this->userShiftHistory = new Collection;
        $this->resetErrorBag();
    }

    #[Computed]
    public function users()
    {
        return User::with('shifts')
            ->where('role', '!=', 'admin')
            ->when($this->search, fn($q) => $q->where(fn($sq) => $sq->where('name', 'LIKE', "%{$this->search}%")->orWhere('email', 'LIKE', "%{$this->search}%")))
            ->when($this->filterRole, fn($q) => $q->where('role', $this->filterRole))
            ->when($this->filterStatus !== '', fn($q) => $q->where('is_active', $this->filterStatus === 'active'))
            ->orderBy('name')
            ->paginate(10);
    }

    #[Computed]
    public function availableShifts()
    {
        return UserShift::where('shift_date', '>=', now()->toDateString())
            ->orderBy('shift_date')
            ->orderBy('start_time')
            ->get();
    }

    public function editUser(int $userId): void
    {
        $this->resetForm();
        $user = User::findOrFail($userId);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->phone_number = $user->phone_number;
        $this->is_active = $user->is_active;

        $now = now()->startOfDay();
        $upcomingShift = $user->shifts()->where('shift_date', '>=', $now)->orderBy('shift_date')->first();
        $this->userShiftHistory = $user->shifts()->where('shift_date', '<', $now)->orderBy('shift_date', 'desc')->get();
        $this->selected_shift_id = $upcomingShift?->id;

        $this->dispatch('open-edit-modal');
    }

    public function closeEditModal(): void
    {
        $this->resetForm();
        $this->dispatch('close-edit-modal');
    }

    public function updateUser()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', ValidationRule::unique('users')->ignore($this->userId)],
            'role' => 'required|string',
            'phone_number' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'selected_shift_id' => 'nullable|exists:user_shifts,id',
        ]);

        if (! $this->userId) return;

        $user = User::findOrFail($this->userId);
        $user->update($validated);

        // Shift Logic
        $currentUpcomingShift = $user->shifts()->where('shift_date', '>=', now()->startOfDay())->first();

        if ($currentUpcomingShift?->id !== $this->selected_shift_id) {
            if ($currentUpcomingShift) {
                $user->shifts()->detach($currentUpcomingShift->id);
            }
            if ($this->selected_shift_id) {
                $user->shifts()->attach($this->selected_shift_id);
            }
        }

        $this->logActivity('user_updated', "Updated user: {$user->name} ({$user->id})", ['user_id' => $user->id]);

        $this->closeEditModal();
        LivewireAlert::title('Updated successfully!')->success()->show();
    }

    public function viewDeleteUser(int $userId): void
    {
        $this->userId = $userId;
        $user = User::findOrFail($userId);
        LivewireAlert::title('Are you sure?')
            ->warning()
            ->text("Delete {$user->name}? This cannot be undone.")
            ->asConfirm()
            ->onConfirm('deleteUser')
            ->show();
    }

    #[On('deleteUser')]
    public function deleteUser(): void
    {
        $user = User::findOrFail($this->userId);
        $user->delete();
        $this->logActivity('user_deleted', "Deleted user: {$user->name} ({$user->id})", ['user_id' => $user->id]);
        $this->resetPage();
        LivewireAlert::title('Deleted successfully!')->success()->show();
    }

    /**
     * Resends the user invitation email if the user is verified.
     * Checks verification again to prevent race conditions.
     */
    public function resendInvitation(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->email_verified_at) {
            LivewireAlert::title('Already verified!')
                ->info()
                ->text('This user has already verified their email address.')
                ->show();
            return;
        }

        try {
            // Generate a fresh token using the Password broker
            $token = Password::broker()->createToken($user);

            // Queue the existing Mailable
            Mail::to($user->email)->queue(new UserInvitationMail(
                $user,
                $token,
                tenant()->domains->first()->domain ?? request()->getHost(),
                tenant('name')
            ));

            $this->logActivity('invitation_resent', "Resent invitation to: {$user->email}", ['user_id' => $user->id]);

            LivewireAlert::title('Invitation Sent!')
                ->success()
                ->text("A fresh invitation link has been sent to {$user->email}.")
                ->show();
        } catch (\Exception $e) {
            // Log error internally if needed
            LivewireAlert::title('Error sending email')
                ->error()
                ->text('Could not send the invitation. Please check your mail configuration.')
                ->show();
        }
    }

    public function render()
    {
        return view('livewire.tenants.admin.user-management', [
            'users' => $this->users,
            'availableShifts' => $this->availableShifts,
        ]);
    }
}
