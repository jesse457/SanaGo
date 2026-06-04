<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Http\Requests\StoreWardRequest;
use App\Http\Requests\UpdateWardRequest;
use Inertia\Inertia;

class WardController extends Controller
{
    public function index()
    {
        $wards = Ward::withCount('beds')->get();
        return Inertia::render('Wards/Index', ['wards' => $wards]);
    }

    public function store(StoreWardRequest $request)
    {
        Ward::create($request->validated());
        return back()->with('success', 'Ward created');
    }

    public function update(UpdateWardRequest $request, Ward $ward)
    {
        $ward->update($request->validated());
        return back()->with('success', 'Ward updated');
    }

    public function destroy(Ward $ward)
    {
        if ($ward->beds()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete ward with beds']);
        }
        $ward->delete();
        return back()->with('success', 'Ward deleted');
    }
}