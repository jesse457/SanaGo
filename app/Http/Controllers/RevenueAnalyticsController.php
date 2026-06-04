<?php

namespace App\Http\Controllers;

use App\Services\Billing\RevenueService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RevenueAnalyticsController extends Controller
{
    public function index(Request $request, RevenueService $revenue): Response
    {
        $year = $request->input('year', date('Y'));
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));

        return Inertia::render('Finance/Analytics', [
            'monthlyData' => $revenue->getMonthlyRevenue((int) $year),
            'summary' => $revenue->getRevenueSummary($startDate, $endDate),
            'expenditureBreakdown' => $revenue->getExpenditureSummary($startDate, $endDate),
            'departmentRevenue' => $revenue->getDepartmentRevenue($startDate, $endDate),
            'year' => $year,
        ]);
    }
}
