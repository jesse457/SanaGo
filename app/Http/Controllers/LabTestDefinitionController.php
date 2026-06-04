<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabTestDefinitionRequest;
use App\Http\Requests\UpdateLabTestDefinitionRequest;
use App\Models\LabTestDefinition;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LabTestDefinitionController extends Controller
{
    public function index(Request $request)
    {
        $tests = LabTestDefinition::query()
            ->when($request->search, function ($query, $search) {
                $query->where('test_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('LabTests/Index', [
            'tests' => $tests,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('LabTests/Create');
    }

    public function store(StoreLabTestDefinitionRequest $request)
    {
        LabTestDefinition::create($request->validated());

        return redirect()->route('tests.index')->with('success', 'Test created.');
    }

    public function update(UpdateLabTestDefinitionRequest $request, LabTestDefinition $test)
    {
        $test->update($request->validated());

        return back()->with('success', 'Test updated.');
    }

    public function destroy(LabTestDefinition $test)
    {
        $test->delete();

        return back()->with('success', 'Test deleted.');
    }
}
