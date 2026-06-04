<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class LandlordRoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::with('permissions')->get()->map(fn (Role $r) => [
            'id' => $r->id,
            'name' => $r->name,
            'permissions' => $r->permissions->pluck('name'),
            'users_count' => $r->users()->count(),
        ]);

        return Inertia::render('LandlordRoles/Index', ['roles' => $roles]);
    }

    public function create(): Response
    {
        $permissions = Permission::all()->pluck('name');

        return Inertia::render('LandlordRoles/Create', ['permissions' => $permissions]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions($validated['permissions'] ?? []);

        return Redirect::route('landlord-roles.index')->with('flash', 'Role created.');
    }

    public function edit(Role $role): Response
    {
        $permissions = Permission::all()->pluck('name');

        return Inertia::render('LandlordRoles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'current_permissions' => $role->permissions->pluck('name'),
            ],
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return Redirect::route('landlord-roles.index')->with('flash', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return Redirect::route('landlord-roles.index')->with('flash', 'Role deleted.');
    }
}
