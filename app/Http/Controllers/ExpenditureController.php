<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenditureRequest;
use App\Models\Expenditure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenditureController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Finance/Expenditures', [
            'expenditures' => Expenditure::with('recordedBy')
                ->orderBy('expenditure_date', 'desc')
                ->paginate(20),
        ]);
    }

    public function store(StoreExpenditureRequest $request): RedirectResponse
    {
        Expenditure::create($request->validated());

        return redirect()->route('expenditures.index')
            ->with('success', 'Expenditure recorded.');
    }

    public function update(Request $request, Expenditure $expenditure): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expenditure_date' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $expenditure->update($validated);

        return redirect()->route('expenditures.index')
            ->with('success', 'Expenditure updated.');
    }

    public function destroy(Expenditure $expenditure): RedirectResponse
    {
        $expenditure->delete();

        return redirect()->route('expenditures.index')
            ->with('success', 'Expenditure deleted.');
    }
}
