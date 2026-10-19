<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * MINSANTE RPM (Recettes-Prestations-Maladies) Report.
     * Legally required financial report for Cameroon's Ministry of Health.
     */
    public function rpm(): Response
    {
        return Inertia::render('Finance/Reports/Rpm');
    }
}
