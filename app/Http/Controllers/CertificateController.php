<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCertificateRequest;
use App\Models\Certificate;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CertificateController extends Controller
{
    /**
     * One page issues every certificate type: the type is picked inside the
     * form instead of being baked into the URL.
     */
    public function create(): Response
    {
        return Inertia::render('Certificates/Create', [
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender', 'address')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    public function store(StoreCertificateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['issued_by'] = auth()->id();
        $data['status'] = 'issued';

        $certificate = Certificate::create($data);

        return redirect()->route('certificates.print', $certificate)
            ->with('success', 'Certificate issued successfully.');
    }

    public function print(Certificate $certificate): Response
    {
        return Inertia::render('Certificates/Print', [
            'certificate' => $certificate->load(['patient', 'doctor']),
        ]);
    }
}
