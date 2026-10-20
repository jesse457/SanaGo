<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PlatformAuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Central audit trail of everything the landlord side does.
 */
class PlatformAuditController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $action = (string) $request->query('action', 'all');
        $tenantId = $request->query('tenant');

        $logs = PlatformAuditLog::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('description', 'like', "%{$search}%")
                        ->orWhere('actor_name', 'like', "%{$search}%")
                        ->orWhere('actor_email', 'like', "%{$search}%")
                        ->orWhere('tenant_id', 'like', "%{$search}%");
                });
            })
            ->when($action !== 'all', fn ($query) => $query->where('action', 'like', $action.'%'))
            ->when($tenantId !== null && $tenantId !== '', fn ($query) => $query->where('tenant_id', $tenantId))
            ->latest()
            ->paginate(50)
            ->withQueryString()
            ->through(fn (PlatformAuditLog $log) => [
                'id' => $log->id,
                'action' => $log->action,
                'description' => $log->description,
                'actor_name' => $log->actor_name,
                'actor_email' => $log->actor_email,
                'tenant_id' => $log->tenant_id,
                'subject_type' => $log->subject_type,
                'subject_id' => $log->subject_id,
                'properties' => $log->properties_json,
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Platform/AuditLog', [
            'logs' => $logs,
            'filters' => ['search' => $search, 'action' => $action, 'tenant' => $tenantId],
            'actions' => PlatformAuditLog::query()
                ->select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action'),
        ]);
    }
}
