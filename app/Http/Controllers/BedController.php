<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Ward;
use App\Models\BedType;
use App\Http\Requests\StoreBedRequest;
use App\Http\Requests\UpdateBedRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BedController extends Controller
{
    public function index()
    {
        return Inertia::render('Beds/Index', [
            'beds' => Bed::with(['ward', 'bedType'])->get(),
            'wards' => Ward::all(),
            'bedTypes' => BedType::all(),
        ]);
    }

    public function store(StoreBedRequest $request)
    {
        Bed::create($request->validated());
        return back()->with('success', 'Bed created.');
    }

    public function update(UpdateBedRequest $request, Bed $bed)
    {
        $bed->update($request->validated());
        return back()->with('success', 'Bed updated.');
    }

    public function destroy(Bed $bed)
    {
        $bed->delete();
        return back()->with('success', 'Bed deleted.');
    }

    public function transfer(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'ward_id' => ['required', 'exists:wards,id'],
        ]);

        $bed->update(['ward_id' => $validated['ward_id']]);

        return back()->with('success', 'Bed transferred successfully.');
    }
}