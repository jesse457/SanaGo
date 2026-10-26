<?php

namespace App\Services\Laboratory;

use App\Enums\LabRequestStatus;
use App\Models\LabRequest;

class LabRequestService
{
    /**
     * File a lab order.
     *
     * Every order gets an accession number at creation: it is the sample id the
     * tube is labelled with and the one analyzers echo back (ASTM `O` record,
     * HL7 OBR-3). Without it a machine run cannot be tied to the order that
     * asked for it, which is why results used to be typed in by hand.
     */
    public function createRequest(array $data): LabRequest
    {
        return LabRequest::create([
            ...$data,
            'accession_number' => $data['accession_number'] ?? $this->nextAccessionNumber(),
            'request_date' => now(),
            'status' => LabRequestStatus::PENDING->value,
        ]);
    }

    public function updateRequest(LabRequest $labRequest, array $data): LabRequest
    {
        $labRequest->update($data);

        return $labRequest;
    }

    /**
     * Sequential, date-scoped, human-readable: ACC-20261026-0007.
     *
     * Sequential rather than random because the number is read aloud and
     * written on tubes by hand; date-scoped so it stays short.
     */
    public function nextAccessionNumber(): string
    {
        $prefix = 'ACC-'.now()->format('Ymd').'-';

        $latest = LabRequest::query()
            ->where('accession_number', 'like', $prefix.'%')
            ->orderByDesc('accession_number')
            ->value('accession_number');

        $sequence = $latest === null ? 1 : ((int) substr((string) $latest, -4)) + 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
