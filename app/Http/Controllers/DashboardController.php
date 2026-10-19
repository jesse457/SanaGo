<?php

namespace App\Http\Controllers;

use App\Services\System\DashboardService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Redirect or render the user's dashboard based on permissions.
     */
    public function index(): Response|RedirectResponse
    {
        $user = auth()->user();

        // Admin — keep the predefined full dashboard
        if ($user->can('view-finance')) {
            return redirect()->route('dashboard.admin');
        }

        // All other roles — render the role-agnostic dashboard
        return Inertia::render('Dashboard/Admin', [
            ...$this->dashboardService->getRoleDashboard($user),
            'isRoleAgnostic' => true,
        ]);
    }

    /**
     * Admin dashboard — predefined, full metrics, unchanged.
     */
    public function admin(): Response
    {
        return Inertia::render('Dashboard/Admin', [
            'metrics' => $this->dashboardService->getAllMetrics(),
            'isRoleAgnostic' => false,
        ]);
    }
}
