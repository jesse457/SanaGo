<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurgeryRequest;
use App\Http\Requests\UpdateSurgeryRequest;
use App\Models\Patient;
use App\Models\Surgery;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SurgeryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Surgeries/Index', [
            'surgeries' => Surgery::with(['patient', 'surgeon', 'anesthetist', 'bed'])
                ->orderBy('scheduled_at', 'desc')
                ->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Surgeries/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    public function store(StoreSurgeryRequest $request): RedirectResponse
    {
        Surgery::create($request->validated());

        return redirect()->route('surgeries.index')
            ->with('success', 'Surgery scheduled successfully.');
    }

    public function show(Surgery $surgery): Response
    {
        return Inertia::render('Surgeries/Show', [
            'surgery' => $surgery->load(['patient', 'surgeon', 'anesthetist', 'bed']),
        ]);
    }

    public function edit(Surgery $surgery): Response
    {
        return Inertia::render('Surgeries/Edit', [
            'surgery' => $surgery->load(['patient', 'surgeon', 'anesthetist']),
        ]);
    }

    public function update(UpdateSurgeryRequest $request, Surgery $surgery): RedirectResponse
    {
        $surgery->update($request->validated());

        return redirect()->route('surgeries.index')
            ->with('success', 'Surgery updated successfully.');
    }

    public function destroy(Surgery $surgery): RedirectResponse
    {
        $surgery->delete();

        return redirect()->route('surgeries.index')
            ->with('success', 'Surgery cancelled.');
    }
}
