<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class LandlordUserController extends Controller
{
    /**
     * Landlord permissions that identify a platform (non-tenant) account.
     *
     * The central user table also holds records created while a tenant context
     * was active, so listing every row exposed hospital staff in the platform
     * panel.
     *
     * @var array<int, string>
     */
    private const LANDLORD_PERMISSIONS = [
        'view-super-admin',
        'manage-tenants',
        'manage-landlord-users',
        'manage-platform-settings',
    ];

    public function index(): Response
    {
        $users = $this->landlordUsersQuery()
            ->with('roles')
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => [
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
        return Inertia::render('LandlordUsers/Create', ['roles' => $this->landlordRoles()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in($this->landlordRoles()->all())],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $user->assignRole($validated['role']);

        return Redirect::route('landlord-users.index')->with('success', 'User created.');
    }

    public function edit(User $user): Response
    {
        abort_unless($this->isLandlordUser($user), 404);

        $roles = $this->landlordRoles();

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
            'role' => ['required', 'string', Rule::in($this->landlordRoles()->all())],
            'is_active' => ['boolean'],
        ]);

        abort_unless($this->isLandlordUser($user), 404);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (array_key_exists('is_active', $validated)) {
            $user->update(['is_active' => (bool) $validated['is_active']]);
        }

        if (! empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        return Redirect::route('landlord-users.index')->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($this->isLandlordUser($user), 404);

        if ($user->is($request->user())) {
            return Redirect::route('landlord-users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return Redirect::route('landlord-users.index')->with('success', 'User deleted.');
    }

    /**
     * Central users that hold a landlord permission.
     *
     * @return Builder<User>
     */
    private function landlordUsersQuery()
    {
        return User::query()->where(function ($query) {
            $query->whereHas('permissions', fn ($permission) => $permission->whereIn('name', self::LANDLORD_PERMISSIONS))
                ->orWhereHas('roles.permissions', fn ($permission) => $permission->whereIn('name', self::LANDLORD_PERMISSIONS));
        });
    }

    private function isLandlordUser(User $user): bool
    {
        return $user->getAllPermissions()->pluck('name')->intersect(self::LANDLORD_PERMISSIONS)->isNotEmpty();
    }

    /**
     * Roles that only exist for the platform (they carry landlord permissions).
     *
     * @return Collection<int, string>
     */
    private function landlordRoles()
    {
        return Role::query()
            ->whereHas('permissions', fn ($permission) => $permission->whereIn('name', self::LANDLORD_PERMISSIONS))
            ->orderBy('name')
            ->pluck('name');
    }
}
