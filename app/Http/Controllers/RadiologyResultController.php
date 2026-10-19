<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRadiologyResultRequest;
use App\Models\RadiologyResult;
use Illuminate\Http\RedirectResponse;

class RadiologyResultController extends Controller
{
    public function validate(StoreRadiologyResultRequest $request, RadiologyResult $result): RedirectResponse
    {
        $result->update([
            ...$request->validated(),
            'validated_by' => auth()->id(),
            'validated_at' => now(),
            'status' => 'validated',
        ]);

        return back()->with('success', 'Radiology result validated successfully.');
    }
}
