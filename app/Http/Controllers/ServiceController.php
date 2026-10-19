<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('Finance/Services/Index', [
            'services' => Service::orderBy('category')
                ->orderBy('name')
                ->paginate(30),
        ]);
    }

    public function create()
    {
        return Inertia::render('Finance/Services/Create');
    }

    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated());

        return redirect()->route('finance.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Finance/Services/Edit', [
            'service' => $service,
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());

        return redirect()->route('finance.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return back()->with('success', 'Service deleted.');
    }
}
