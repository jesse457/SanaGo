<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Roles/Index', [
            'roles' => Role::with('permissions')
                ->withCount('users')
                ->get()
                ->map(fn ($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->name,
                    'description' => 'System access level for '.$role->name,
                    'permissions' => $role->permissions->pluck('name'),
                    'user_count' => $role->users_count,
                ]),
            'available_permissions' => static::getGroupedPermissions(),
        ]);
    }

    /**
     * Load all permissions from the database, grouped by logical category.
     */
    protected static function getGroupedPermissions(): array
    {
        $permissions = Permission::all();

        // Map permission slugs to human-readable labels and categories
        $map = [
            'view-patients' => ['Patient Management', 'View Patient Records'],
            'create-patients' => ['Patient Management', 'Register New Patients'],
            'edit-patients' => ['Patient Management', 'Edit Patient Details'],
            'delete-patients' => ['Patient Management', 'Delete Patient Records'],
            'view-admissions' => ['Patient Management', 'View Admissions'],
            'create-admissions' => ['Patient Management', 'Create Admissions'],
            'discharge-admissions' => ['Patient Management', 'Discharge Patients'],
            'view-appointments' => ['Appointments', 'View Appointments'],
            'create-appointments' => ['Appointments', 'Create/Book Appointments'],
            'edit-appointments' => ['Appointments', 'Edit Appointments'],
            'delete-appointments' => ['Appointments', 'Delete Appointments'],
            'view-consultations' => ['Clinical', 'View Consultations'],
            'create-consultations' => ['Clinical', 'Create Consultations/MR'],
            'edit-consultations' => ['Clinical', 'Edit Medical Records'],
            'delete-consultations' => ['Clinical', 'Delete Medical Records'],
            'view-vitals' => ['Clinical', 'View Patient Vitals'],
            'view-lab-tests' => ['Laboratory', 'View Lab Tests'],
            'create-lab-tests' => ['Laboratory', 'Order Lab Tests'],
            'view-lab-results' => ['Laboratory', 'View Lab Results'],
            'create-lab-results' => ['Laboratory', 'Enter Lab Results'],
            'view-drugs' => ['Pharmacy', 'View Drug Inventory'],
            'create-drugs' => ['Pharmacy', 'Add Medications'],
            'edit-drugs' => ['Pharmacy', 'Edit Medications'],
            'dispense-drugs' => ['Pharmacy', 'Dispense Medications'],
            'view-supplies' => ['Infrastructure', 'View Supplies'],
            'manage-supplies' => ['Infrastructure', 'Manage Supplies'],
            'manage-infrastructure' => ['Infrastructure', 'Manage Infrastructure'],
            'manage-departments' => ['Infrastructure', 'Manage Departments'],
            'view-finance' => ['Finance', 'View Financial Data'],
            'manage-finance' => ['Finance', 'Manage Finances'],
            'manage-users' => ['Administration', 'Manage Users'],
            'manage-roles' => ['Administration', 'Manage Roles & Permissions'],
            'view-audit-logs' => ['Administration', 'View Audit Logs'],
            'view-radiology' => ['Radiology', 'View Radiology'],
        ];

        $grouped = [];
        foreach ($permissions as $perm) {
            $info = $map[$perm->name] ?? ['Other', ucwords(str_replace('-', ' ', $perm->name))];
            $grouped[$info[0]][] = ['slug' => $perm->name, 'label' => $info[1]];
        }

        return $grouped;
    }

    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
            $role->syncPermissions($validated['permissions'] ?? []);
        });

        return back();
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $role) {
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions'] ?? []);
        });

        return back();
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete role assigned to users.']);
        }

        $role->delete();

        return back();
    }
}
