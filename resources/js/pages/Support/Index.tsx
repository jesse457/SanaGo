import { PlusIcon, TicketIcon } from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Ticket {
    id: number;
    subject: string;
    status: string;
    priority: string;
    created_at: string;
    updated_at: string;
}

const STATUS_COLORS: Record<string, 'blue' | 'amber' | 'emerald' | 'gray'> = {
    open: 'blue',
    in_progress: 'amber',
    resolved: 'emerald',
    closed: 'gray',
};

export default function SupportIndex({ tickets }: { tickets: Ticket[] }) {
    const { t } = useTranslation();

    return (
        <AppLayout
            headerTitle="Support"
            headerSubtitle="View your support tickets"
        >
            <Head title="Support" />
            <div className="mx-auto max-w-5xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[{ label: t('common.home') }, { label: 'Support' }]}
                />

                <div className="flex justify-end">
                    <Link
                        href="/support/create"
                        className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                    >
                        <PlusIcon className="h-4 w-4" /> New Ticket
                    </Link>
                </div>

                {tickets.length === 0 ? (
                    <EmptyState
                        icon={TicketIcon}
                        title="No support tickets yet"
                        action={{
                            label: 'Create your first ticket',
                            href: '/support/create',
                        }}
                    />
                ) : (
                    <div className="space-y-4">
                        {tickets.map((ticket) => (
                            <Link
                                key={ticket.id}
                                href={`/support/${ticket.id}`}
                                className="block rounded-xl border border-gray-100 bg-white p-6 transition-all hover:border-blue-400 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
                            >
                                <div className="flex items-start justify-between">
                                    <div className="flex-1">
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                                            {ticket.subject}
                                        </h3>
                                        <div className="mt-2 flex items-center gap-3">
                                            <StatusBadge
                                                label={ticket.status.replace(
                                                    '_',
                                                    ' ',
                                                )}
                                                color={
                                                    STATUS_COLORS[
                                                        ticket.status
                                                    ] || 'gray'
                                                }
                                            />
                                            <span className="text-xs font-bold text-gray-400 uppercase">
                                                {ticket.priority}
                                            </span>
                                            <span className="text-xs text-gray-400">
                                                {new Date(
                                                    ticket.created_at,
                                                ).toLocaleDateString()}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
