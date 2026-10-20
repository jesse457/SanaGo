<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenditureRequest;
use App\Http\Requests\UpdateExpenditureRequest;
use App\Models\Expenditure;
use Illuminate\Http\RedirectResponse;
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

        return redirect()->route('finance.expenditures')
            ->with('success', 'Expenditure recorded.');
    }

    public function update(UpdateExpenditureRequest $request, Expenditure $expenditure): RedirectResponse
    {
        $expenditure->update($request->validated());

        return redirect()->route('finance.expenditures')
            ->with('success', 'Expenditure updated.');
    }

    public function destroy(Expenditure $expenditure): RedirectResponse
    {
        $expenditure->delete();

        return redirect()->route('finance.expenditures')
            ->with('success', 'Expenditure deleted.');
    }
}
