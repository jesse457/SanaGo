<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplyRequest;
use App\Http\Requests\UpdateSupplyRequest;
use App\Models\Supply;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplyController extends Controller
{
    public function index()
    {
        $supplies = Supply::orderBy('name')->get();

        return Inertia::render('Supply/Index', ['supplies' => $supplies]);
    }

    public function create()
    {
        return Inertia::render('Supply/Create');
    }

    public function store(StoreSupplyRequest $request)
    {
        $validated = $request->validated();
        $validated['last_restocked'] = now()->toDateString();

        $supply = Supply::create($validated);

        if ($request->hasFile('image')) {
            $supply->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('supplies.index')->with('success', 'Supply added successfully.');
    }

    public function edit(Supply $supply)
    {
        return Inertia::render('Supply/Create', ['supply' => $supply]);
    }

    public function update(UpdateSupplyRequest $request, Supply $supply)
    {
        $supply->update($request->validated());

        if ($request->hasFile('image')) {
            $supply->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('supplies.index')->with('success', 'Supply updated successfully.');
    }

    public function destroy(Supply $supply)
    {
        $supply->delete();

        return redirect()->back()->with('success', 'Supply deleted successfully.');
    }

    public function restock(Request $request, Supply $supply)
    {
        $validated = $request->validate(['add_amount' => 'required|integer|min:1']);
        $supply->increment('current_stock', $validated['add_amount']);
        $supply->update(['last_restocked' => now()->toDateString()]);

        return redirect()->back()->with('success', 'Stock replenished successfully.');
    }
}
