<?php

namespace App\Services\Clinical;

use App\Models\NurseCareReport;

class NurseCareService
{
    public function createReport(array $data): NurseCareReport
    {
        return NurseCareReport::create($data);
    }

    public function updateReport(NurseCareReport $report, array $data): NurseCareReport
    {
        $report->update($data);

        return $report;
    }
}
