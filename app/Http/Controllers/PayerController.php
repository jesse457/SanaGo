<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayerRequest;
use App\Models\Payer;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PayerController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Finance/Payers/Index', [
            'payers' => Payer::withCount(['patientInsurances', 'claims'])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StorePayerRequest $request): RedirectResponse
    {
        Payer::create($request->validated());

        return back()->with('success', 'Payer created successfully.');
    }

    public function update(StorePayerRequest $request, Payer $payer): RedirectResponse
    {
        $payer->update($request->validated());

        return back()->with('success', 'Payer updated successfully.');
    }
}
