<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Models\User;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Departments/Index', [
            'departments' => Department::with(['headOfDepartment:id,name'])
                ->withCount(['users', 'wards'])
                ->latest()
                ->get(),
            'users' => User::select('id', 'name', 'department_id')->get(),
        ]);
    }

    public function store(DepartmentRequest $request)
    {
        Department::create($request->validated());

        return back()->with('success', 'Department created.');
    }

    public function update(DepartmentRequest $request, Department $department)
    {

        $department->update($request->validated());

        return back()->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        // Safety check: Prevents deletion if relations exist
        if ($department->users()->count() > 0 || $department->wards()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete department with active staff or wards.']);
        }

        $department->delete();

        return back()->with('success', 'Department deleted.');
    }
}
