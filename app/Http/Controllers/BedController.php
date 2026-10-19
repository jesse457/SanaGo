<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBedRequest;
use App\Http\Requests\TransferBedRequest;
use App\Http\Requests\UpdateBedRequest;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\Ward;
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

    public function transfer(TransferBedRequest $request, Bed $bed)
    {
        $bed->update($request->validated());

        return back()->with('success', 'Bed transferred successfully.');
    }
}
