<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBedTypeRequest;
use App\Http\Requests\UpdateBedTypeRequest;
use App\Models\BedType;
use Inertia\Inertia;

class BedTypeController extends Controller
{
    public function index()
    {
        return Inertia::render('BedType/Index', [
            'bedTypes' => BedType::all(),
        ]);
    }

    public function store(StoreBedTypeRequest $request)
    {
        BedType::create($request->validated());

        return back()->with('success', 'Bed type created.');
    }

    public function update(UpdateBedTypeRequest $request, BedType $bedType)
    {
        $bedType->update($request->validated());

        return back()->with('success', 'Bed type updated.');
    }

    public function destroy(BedType $bedType)
    {
        $bedType->delete();

        return back()->with('success', 'Bed type deleted.');
    }
}
