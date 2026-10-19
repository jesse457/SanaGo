<?php

namespace App\Http\Controllers;

use App\Enums\CertificateType;
use App\Http\Requests\StoreCertificateRequest;
use App\Models\Certificate;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CertificateController extends Controller
{
    public function createSickLeave(): Response
    {
        return $this->createForm(CertificateType::SickLeave);
    }

    public function storeSickLeave(StoreCertificateRequest $request): RedirectResponse
    {
        return $this->storeCertificate($request, CertificateType::SickLeave);
    }

    public function createMedical(): Response
    {
        return $this->createForm(CertificateType::MedicalFitness);
    }

    public function storeMedical(StoreCertificateRequest $request): RedirectResponse
    {
        return $this->storeCertificate($request, CertificateType::MedicalFitness);
    }

    public function createDeath(): Response
    {
        return $this->createForm(CertificateType::Death);
    }

    public function storeDeath(StoreCertificateRequest $request): RedirectResponse
    {
        return $this->storeCertificate($request, CertificateType::Death);
    }

    public function print(Certificate $certificate): Response
    {
        return Inertia::render('Certificates/Print', [
            'certificate' => $certificate->load(['patient', 'doctor']),
        ]);
    }

    private function createForm(CertificateType $type): Response
    {
        return Inertia::render('Certificates/Create', [
            'type' => $type->value,
            'patients' => Patient::select('id', 'first_name', 'last_name', 'patient_uid', 'date_of_birth', 'gender', 'address')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    private function storeCertificate(StoreCertificateRequest $request, CertificateType $type): RedirectResponse
    {
        $data = $request->validated();
        $data['type'] = $type->value;
        $data['issued_by'] = auth()->id();
        $data['status'] = 'issued';

        $certificate = Certificate::create($data);

        return redirect()->route('certificates.print', $certificate)
            ->with('success', 'Certificate issued successfully.');
    }
}
