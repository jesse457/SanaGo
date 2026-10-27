import { PlusIcon, TicketIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { Button, EmptyState, PageHeader } from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { TicketTable } from '@/pages/Support/components/TicketTable';

interface Ticket {
    id: number;
    tenant_name: string | null;
    subject: string;
    status: string;
    priority: string;
    user_name: string | null;
    created_at: string;
}

export default function SupportTicketsIndex({
    tickets,
}: {
    tickets: Ticket[];
}) {
    return (
        <SuperAdminLayout
            headerTitle="Support Tickets"
            headerSubtitle="Manage tenant support requests"
        >
            <Head title="Support Tickets" />
            <div className="mx-auto w-full max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    title="Support tickets"
                    eyebrow="Landlord"
                    description="Support requests raised by every tenant, with their current status and priority."
                    breadcrumbs={[{ label: 'Support tickets' }]}
                    actions={
                        <Button href="/support-tickets/create" icon={PlusIcon}>
                            New ticket
                        </Button>
                    }
                />

                {tickets.length === 0 ? (
                    <EmptyState
                        icon={TicketIcon}
                        title="No support tickets"
                        description="Tenant support requests will appear here as they come in."
                        action={{
                            label: 'Create a ticket',
                            href: '/support-tickets/create',
                            icon: PlusIcon,
                        }}
                    />
                ) : (
                    <TicketTable
                        tickets={tickets}
                        showTenant
                        href={(ticket) => `/support-tickets/${ticket.id}`}
                    />
                )}
            </div>
        </SuperAdminLayout>
    );
}
