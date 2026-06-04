<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBedRequest;
use App\Http\Requests\StoreBedTypeRequest;
use App\Http\Requests\StoreWardRequest;
use App\Http\Requests\UpdateBedRequest;
use App\Http\Requests\UpdateBedTypeRequest;
use App\Http\Requests\UpdateWardRequest;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\Ward;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InfastructureController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Utilities', [
            'wards' => Ward::withCount('beds')->get(),
            'beds' => Bed::with(['ward', 'bedType'])->get(),
            'bedTypes' => BedType::all(),
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->input('entity_type');

        if ($type === 'ward') {
            $data = app(StoreWardRequest::class)->validated();
            Ward::create($data);
        } elseif ($type === 'bed') {
            $data = app(StoreBedRequest::class)->validated();
            Bed::create($data);
        } elseif ($type === 'type') {
            $data = app(StoreBedTypeRequest::class)->validated();
            BedType::create($data);
        }

        return back()->with('success', 'Created successfully');
    }

    public function update(Request $request, $id)
    {
        $type = $request->input('entity_type');

        if ($type === 'ward') {
            $ward = Ward::findOrFail($id);
            $data = app(UpdateWardRequest::class)->validated();
            $ward->update($data);
        } elseif ($type === 'bed') {
            $bed = Bed::findOrFail($id);
            $data = app(UpdateBedRequest::class)->validated();
            $bed->update($data);
        } elseif ($type === 'type') {
            $bedType = BedType::findOrFail($id);
            $data = app(UpdateBedTypeRequest::class)->validated();
            $bedType->update($data);
        }

        return back()->with('success', 'Updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $type = $request->input('entity_type');

        if ($type === 'ward') {
            $ward = Ward::findOrFail($id);
            if ($ward->beds()->count() > 0) {
                return back()->withErrors(['error' => 'Ward is not empty']);
            }
            $ward->delete();
        } elseif ($type === 'bed') {
            Bed::findOrFail($id)->delete();
        } elseif ($type === 'type') {
            BedType::findOrFail($id)->delete();
        }

        return back()->with('success', 'Deleted successfully');
    }
}
