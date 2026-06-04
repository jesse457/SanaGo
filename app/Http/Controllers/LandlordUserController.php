<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class LandlordUserController extends Controller
{
    public function index(): Response
    {
        $users = User::with('roles')->get()->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'roles' => $u->roles->pluck('name'),
            'is_active' => $u->is_active,
            'created_at' => $u->created_at?->toDateTimeString(),
        ]);

        return Inertia::render('LandlordUsers/Index', ['users' => $users]);
    }

    public function create(): Response
    {
        $roles = Role::all()->pluck('name');

        return Inertia::render('LandlordUsers/Create', ['roles' => $roles]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        return Redirect::route('landlord-users.index')->with('flash', 'User created.');
    }

    public function edit(User $user): Response
    {
        $roles = Role::all()->pluck('name');

        return Inertia::render('LandlordUsers/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'current_role' => $user->roles->first()?->name,
                'is_active' => $user->is_active,
            ],
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return Redirect::route('landlord-users.index')->with('flash', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return Redirect::route('landlord-users.index')->with('flash', 'User deleted.');
    }
}
