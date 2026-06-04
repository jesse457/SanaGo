<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/index', [
            'users' => User::with(['department:id,name', 'roles', 'shifts'])
                ->latest()
                ->get(),
            'departments' => Department::select('id', 'name')->get(),
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $role = $validated['role'];
        unset($validated['role']);

        $user = User::create($validated);
        $user->assignRole($role);

        if ($request->hasFile('profile_picture')) {
            $user->addMediaFromRequest('profile_picture')
                ->toMediaCollection('profile_picture');
        }

        return back()->with('success', 'User created successfully.');
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'departments' => Department::select('id', 'name')->get(),
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $role = $validated['role'] ?? null;
        $shifts = $validated['shifts'] ?? [];
        unset($validated['role'], $validated['shifts']);

        $user->update($validated);

        if ($role) {
            $user->syncRoles($role);
        }

        // Sync shifts: delete existing, create new
        $user->shifts()->delete();
        foreach ($shifts as $shift) {
            if (!empty($shift['shift_date']) && !empty($shift['shift_type'])) {
                $user->shifts()->create([
                    'shift_date' => $shift['shift_date'],
                    'shift_type' => $shift['shift_type'],
                    'start_time' => $shift['start_time'] ?: '08:00',
                    'end_time' => $shift['end_time'] ?: '17:00',
                ]);
            }
        }

        if ($request->hasFile('profile_picture')) {
            $user->addMediaFromRequest('profile_picture')
                ->toMediaCollection('profile_picture');
        }

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent self-deletion if necessary
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
