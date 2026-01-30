<?php

namespace App\Livewire\Tenants\Admin;

use App\Models\User;
use App\Models\UserShift;
use App\Services\UserService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class UserManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public string $filterStatus = '';

    // Form Properties
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'doctor';
    public string $phone_number = '';
    public bool $is_active = true;
    public ?int $selected_shift_id = null;

    // Reset pagination on filter change
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterRole() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    #[Computed]
    public function users()
    {
        return app(UserService::class)->getUsersQuery([
            'search' => $this->search,
            'role' => $this->filterRole,
            'status' => $this->filterStatus,
        ])->paginate(10);
    }

    #[Computed]
    public function availableShifts()
    {
        // Adjust logic to match your requirements (e.g., only future shifts)
        return UserShift::where('shift_date', '>=', now())->get();
    }

    #[Computed]
    public function userShiftHistory()
    {
        if (!$this->userId) return collect();
        
        return UserShift::whereHas('users', function($q) {
            $q->where('users.id', $this->userId);
        })->where('shift_date', '<', now())
          ->orderBy('shift_date', 'desc')
          ->take(5)
          ->get();
    }

    public function editUser(int $id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->phone_number = $user->phone_number ?? '';
        $this->is_active = (bool)$user->is_active;
        // Logic to get the user's currently assigned upcoming shift if applicable
        // $this->selected_shift_id = ... 

        $this->dispatch('open-edit-modal');
    }

    public function closeEditModal()
    {
        $this->reset(['userId', 'name', 'email', 'phone_number', 'selected_shift_id']);
        $this->dispatch('close-edit-modal');
    }

    public function updateUser(UserService $userService)
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required',
            'phone_number' => 'nullable',
            'is_active' => 'boolean',
            'selected_shift_id' => 'nullable|exists:shifts,id',
        ]);

        $userService->updateUser($this->userId, $data);

        $this->dispatch('close-edit-modal');
        LivewireAlert::title('Success')->success()->text('User updated successfully.')->show();
       
    }

    public function resendInvitation(int $id, UserService $userService)
    {
        $user = User::findOrFail($id);
        if ($user->email_verified_at) {
            LivewireAlert::title('Info')->info()->text('User is already verified.')->show();
          
            return;
        }
        $userService->sendInvitation($user);
        LivewireAlert::title('Success')->success()->text('Invitation resent.')->show();
      
    }

    public function render()
    {
        return view('livewire.tenants.admin.user-management');
    }
}