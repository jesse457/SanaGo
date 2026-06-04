import {
    PlusIcon,
    TicketIcon,
    MagnifyingGlassIcon,
    BuildingOfficeIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Ticket {
    id: number;
    tenant_name: string | null;
    subject: string;
    status: string;
    priority: string;
    user_name: string | null;
    created_at: string;
}

const STATUS_BADGE_COLORS: Record<string, string> = {
    open: 'blue',
    in_progress: 'amber',
    resolved: 'emerald',
    closed: 'gray',
};

export default function SupportTicketsIndex({
    tickets,
}: {
    tickets: Ticket[];
}) {
    const [search, setSearch] = useState('');
    const filtered = useMemo(
        () =>
            tickets.filter(
                (t) =>
                    t.subject.toLowerCase().includes(search.toLowerCase()) ||
                    (t.tenant_name || '')
                        .toLowerCase()
                        .includes(search.toLowerCase()),
            ),
        [tickets, search],
    );

    return (
        <SuperAdminLayout
            headerTitle="Support Tickets"
            headerSubtitle="Manage tenant support requests"
        >
            <Head title="Support Tickets" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Support Tickets' },
                    ]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="relative w-full sm:w-96">
                        <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Search tickets..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800"
                        />
                    </div>
                    <Link
                        href="/support-tickets/create"
                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" /> New Ticket
                    </Link>
                </div>

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {filtered.map((ticket) => (
                        <Link
                            key={ticket.id}
                            href={`/support-tickets/${ticket.id}`}
                            className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all duration-200 hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <div className="mb-4 flex items-start justify-between">
                                <div className="rounded-lg bg-blue-50 p-3 text-blue-600">
                                    <TicketIcon className="h-6 w-6" />
                                </div>
                                <StatusBadge
                                    label={ticket.status.replace('_', ' ')}
                                    color={
                                        (STATUS_BADGE_COLORS[
                                            ticket.status
                                        ] as any) || 'gray'
                                    }
                                />
                            </div>
                            <h3 className="mb-2 text-lg font-bold text-gray-900 group-hover:text-blue-600 dark:text-neutral-100">
                                {ticket.subject}
                            </h3>
                            <div className="flex items-center gap-1.5 text-xs text-gray-500">
                                <BuildingOfficeIcon className="h-3.5 w-3.5" />
                                {ticket.tenant_name || 'No tenant'}
                            </div>
                            <div className="mt-3 flex items-center gap-3 text-xs text-gray-400">
                                <span className="font-bold uppercase">
                                    {ticket.priority}
                                </span>
                                <span>
                                    {new Date(
                                        ticket.created_at,
                                    ).toLocaleDateString()}
                                </span>
                            </div>
                        </Link>
                    ))}
                </div>
                {filtered.length === 0 && (
                    <EmptyState icon={TicketIcon} title="No tickets found" />
                )}
            </div>
        </SuperAdminLayout>
    );
}
