<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class SupportTicketController extends Controller
{
    public function index(Request $request): Response
    {
        $tickets = SupportTicket::with(['tenant', 'user'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->priority, fn ($q, $p) => $q->where('priority', $p))
            ->latest()
            ->get()
            ->map(fn (SupportTicket $t) => [
                'id' => $t->id,
                'tenant_name' => $t->tenant?->name,
                'subject' => $t->subject,
                'status' => $t->status,
                'priority' => $t->priority,
                'user_name' => $t->user?->name,
                'created_at' => $t->created_at?->toDateTimeString(),
                'updated_at' => $t->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('SupportTickets/Index', ['tickets' => $tickets]);
    }

    public function create(): Response
    {
        $tenants = Tenant::where('is_active', true)->get()->map(fn (Tenant $t) => [
            'id' => $t->id,
            'name' => $t->name,
        ]);

        return Inertia::render('SupportTickets/Create', ['tenants' => $tenants]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'string', 'exists:tenants,id'],
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'tenant_id' => $validated['tenant_id'],
            'user_id' => $request->user()?->id,
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()?->id,
            'message' => $validated['message'],
        ]);

        return Redirect::route('support-tickets.show', $ticket)->with('flash', 'Ticket created.');
    }

    public function show(SupportTicket $supportTicket): Response
    {
        $supportTicket->load(['tenant', 'user', 'messages.user']);

        return Inertia::render('SupportTickets/Show', [
            'ticket' => [
                'id' => $supportTicket->id,
                'tenant_name' => $supportTicket->tenant?->name,
                'tenant_id' => $supportTicket->tenant_id,
                'subject' => $supportTicket->subject,
                'status' => $supportTicket->status,
                'priority' => $supportTicket->priority,
                'user_name' => $supportTicket->user?->name,
                'created_at' => $supportTicket->created_at?->toDateTimeString(),
                'messages' => $supportTicket->messages->map(fn ($m) => [
                    'id' => $m->id,
                    'message' => $m->message,
                    'user_name' => $m->user?->name ?? 'System',
                    'created_at' => $m->created_at?->toDateTimeString(),
                ]),
            ],
        ]);
    }

    public function addMessage(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'is_internal' => ['boolean'],
        ]);

        $supportTicket->messages()->create([
            'user_id' => $request->user()?->id,
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        return Redirect::back()->with('flash', 'Message sent.');
    }

    public function updateStatus(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:open,in_progress,resolved,closed'],
        ]);

        $supportTicket->update(['status' => $validated['status']]);

        return Redirect::back()->with('flash', 'Ticket status updated.');
    }

    // ─── Tenant-Facing Methods ───

    public function tenantIndex(Request $request): Response
    {
        $tenantId = tenant()?->id;
        $tickets = SupportTicket::with('user')
            ->where('tenant_id', $tenantId)
            ->latest()
            ->get()
            ->map(fn (SupportTicket $t) => [
                'id' => $t->id,
                'subject' => $t->subject,
                'status' => $t->status,
                'priority' => $t->priority,
                'created_at' => $t->created_at?->toDateTimeString(),
                'updated_at' => $t->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('Support/Index', ['tickets' => $tickets]);
    }

    public function tenantCreate(): Response
    {
        return Inertia::render('Support/Create');
    }

    public function tenantStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'tenant_id' => tenant()?->id,
            'user_id' => $request->user()?->id,
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()?->id,
            'message' => $validated['message'],
        ]);

        return Redirect::route('tenant.tickets.show', $ticket)->with('success', 'Ticket submitted.');
    }

    public function tenantShow(SupportTicket $supportTicket): Response
    {
        // Tickets live on the central connection shared by every tenant, so
        // the id alone must never be trusted: a hospital may only read its
        // own ticket (and never the internal notes on it).
        $this->ensureTicketBelongsToTenant($supportTicket);

        $supportTicket->load(['messages' => fn ($query) => $query->where('is_internal', false), 'messages.user']);

        return Inertia::render('Support/Show', [
            'ticket' => [
                'id' => $supportTicket->id,
                'subject' => $supportTicket->subject,
                'status' => $supportTicket->status,
                'priority' => $supportTicket->priority,
                'created_at' => $supportTicket->created_at?->toDateTimeString(),
                'messages' => $supportTicket->messages->map(fn ($m) => [
                    'id' => $m->id,
                    'message' => $m->message,
                    'user_name' => $m->user?->name ?? 'System',
                    'created_at' => $m->created_at?->toDateTimeString(),
                ]),
            ],
        ]);
    }

    public function tenantReply(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        $this->ensureTicketBelongsToTenant($supportTicket);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $supportTicket->messages()->create([
            'user_id' => $request->user()?->id,
            'message' => $validated['message'],
        ]);

        return Redirect::back()->with('success', 'Reply sent.');
    }

    /**
     * Abort unless the ticket belongs to the active tenant.
     */
    private function ensureTicketBelongsToTenant(SupportTicket $supportTicket): void
    {
        $tenantId = tenant()?->getTenantKey();

        if ($tenantId === null || (string) $supportTicket->tenant_id !== (string) $tenantId) {
            abort(404);
        }
    }
}
