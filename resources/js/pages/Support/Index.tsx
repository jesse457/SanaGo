import { PlusIcon, TicketIcon } from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { Button, EmptyState, PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { TicketTable } from './components/TicketTable';

interface Ticket {
    id: number;
    subject: string;
    status: string;
    priority: string;
    created_at: string;
    updated_at: string;
}

export default function SupportIndex({ tickets }: { tickets: Ticket[] }) {
    const { t } = useTranslation();

    return (
        <AppLayout
            headerTitle="Support"
            headerSubtitle="View your support tickets"
        >
            <Head title="Support" />
            <div className="mx-auto w-full max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    title="Support"
                    eyebrow="Help desk"
                    description="Every ticket you have raised, and the conversation attached to it."
                    icon={TicketIcon}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/dashboard' },
                        { label: 'Support' },
                    ]}
                    actions={
                        <Button href="/support/create" icon={PlusIcon}>
                            New ticket
                        </Button>
                    }
                />

                {tickets.length === 0 ? (
                    <EmptyState
                        icon={TicketIcon}
                        title="No support tickets yet"
                        description="Raise a ticket and the support team will pick it up from here."
                        action={{
                            label: 'Create your first ticket',
                            href: '/support/create',
                            icon: PlusIcon,
                        }}
                    />
                ) : (
                    <TicketTable
                        tickets={tickets}
                        href={(ticket) => `/support/${ticket.id}`}
                    />
                )}
            </div>
        </AppLayout>
    );
}
