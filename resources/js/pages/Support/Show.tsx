import { TicketIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Badge,
    Button,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { TicketComposer } from './components/TicketComposer';
import { TicketMessageList } from './components/TicketMessageList';
import type { TicketMessage } from './components/TicketMessageList';
import {
    priorityTone,
    statusLabel,
    statusTone,
} from './components/ticketStatus';

interface Ticket {
    id: number;
    subject: string;
    status: string;
    priority: string;
    created_at: string;
    messages: TicketMessage[];
}

export default function SupportShow({ ticket }: { ticket: Ticket }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, reset } = useForm({ message: '' });

    const sendReply = (e: FormEvent) => {
        e.preventDefault();
        post(`/support/${ticket.id}/reply`, {
            preserveScroll: true,
            onSuccess: () => reset('message'),
        });
    };

    const isClosed = ticket.status === 'closed';

    return (
        <AppLayout
            headerTitle={ticket.subject}
            headerSubtitle={`Ticket #${ticket.id}`}
        >
            <Head title={ticket.subject} />

            <div className="mx-auto w-full max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    title={ticket.subject}
                    eyebrow={`Ticket #${ticket.id}`}
                    icon={TicketIcon}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/dashboard' },
                        { label: 'Support', href: '/support' },
                        { label: ticket.subject },
                    ]}
                    meta={
                        <>
                            <Badge tone={statusTone(ticket.status)} dot>
                                {statusLabel(ticket.status)}
                            </Badge>
                            <Badge tone={priorityTone(ticket.priority)}>
                                {ticket.priority}
                            </Badge>
                            <span>
                                Opened{' '}
                                {new Date(
                                    ticket.created_at,
                                ).toLocaleDateString()}
                            </span>
                        </>
                    }
                    actions={
                        <Button href="/support" variant="secondary" size="sm">
                            {t('common.back_to_list')}
                        </Button>
                    }
                />

                <KeyValueGrid columns={3}>
                    <KeyValue
                        label="Status"
                        value={
                            <Badge tone={statusTone(ticket.status)} dot>
                                {statusLabel(ticket.status)}
                            </Badge>
                        }
                    />
                    <KeyValue
                        label="Priority"
                        value={
                            <Badge tone={priorityTone(ticket.priority)}>
                                {ticket.priority}
                            </Badge>
                        }
                    />
                    <KeyValue
                        label="Created"
                        value={new Date(ticket.created_at).toLocaleDateString()}
                    />
                </KeyValueGrid>

                <SectionCard
                    title="Conversation"
                    description="Replies from you and the support team, newest last."
                >
                    <div className="space-y-5">
                        <TicketMessageList messages={ticket.messages} />

                        {isClosed ? (
                            <p className="border-t border-line pt-4 text-[13px] text-ink-subtle">
                                This ticket is closed. Replies are disabled.
                            </p>
                        ) : (
                            <div className="border-t border-line pt-4">
                                <TicketComposer
                                    value={data.message}
                                    onChange={(value) =>
                                        setData('message', value)
                                    }
                                    onSubmit={sendReply}
                                    processing={processing}
                                    placeholder="Write a reply…"
                                    required
                                />
                            </div>
                        )}
                    </div>
                </SectionCard>
            </div>
        </AppLayout>
    );
}
