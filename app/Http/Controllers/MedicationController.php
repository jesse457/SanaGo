<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\UpdateMedicationRequest;
use App\Models\Batch;
use App\Models\Medication;
use App\Services\Pharmacy\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Medication::with(['batches' => function ($q) {
            $q->orderBy('current_quantity', 'desc')->orderBy('expiry_date', 'asc');
        }]);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $tab = $request->tab ?? 'all';
        if ($tab === 'low-stock') {
            $query->whereColumn('total_stock', '<=', 'min_stock_level');
        } elseif ($tab === 'expiring') {
            $query->whereHas('batches', function ($q) {
                $q->where('expiry_date', '<=', Carbon::now()->addDays(30))
                    ->where('expiry_date', '>=', Carbon::now())
                    ->where('current_quantity', '>', 0);
            });
        }

        return Inertia::render('Drugs/Index', [
            'drugs' => $query->latest()->get(),
            'filters' => $request->only(['search', 'tab']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Drugs/Create');
    }

    public function store(StoreMedicationRequest $request, InventoryService $service)
    {
        $image = $request->hasFile('image') ? $request->file('image') : null;
        $service->createMedication($request->validated(), $image);

        return redirect()->route('drugs.index')->with('success', 'Medication and batches created.');
    }

    public function update(UpdateMedicationRequest $request, Medication $drug)
    {
        if ($request->has('batch_number')) {
            $request->validate(['batch_number' => 'required', 'expiry_date' => 'required', 'quantity' => 'required|integer']);
            $service = app(InventoryService::class);
            $service->addBatch($drug, $request->only(['batch_number', 'expiry_date', 'quantity', 'purchase_price']));

            return back();
        }

        $drug->update($request->validated());

        if ($request->hasFile('image')) {
            $drug->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return back();
    }

    public function deplete(Batch $batch, InventoryService $service)
    {
        $service->depleteBatch($batch);

        return back()->with('success', 'Batch marked as depleted.');
    }

    public function destroy(Medication $drug)
    {
        $drug->batches()->delete();
        $drug->delete();

        return back();
    }
}
