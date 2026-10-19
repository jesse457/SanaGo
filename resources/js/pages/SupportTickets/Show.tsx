import {
    BuildingOfficeIcon,
    CalendarDaysIcon,
    TicketIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Badge,
    Button,
    Checkbox,
    Field,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
    Select,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { TicketComposer } from '@/pages/Support/components/TicketComposer';
import { TicketMessageList } from '@/pages/Support/components/TicketMessageList';
import type { TicketMessage } from '@/pages/Support/components/TicketMessageList';
import {
    priorityTone,
    statusLabel,
    statusTone,
    TICKET_STATUSES,
} from '@/pages/Support/components/ticketStatus';

interface Ticket {
    id: number;
    tenant_name: string | null;
    tenant_id: string;
    subject: string;
    status: string;
    priority: string;
    user_name: string | null;
    created_at: string;
    messages: TicketMessage[];
}

export default function SupportTicketsShow({ ticket }: { ticket: Ticket }) {
    const { data, setData, post, processing, reset } = useForm({
        message: '',
        is_internal: false,
    });
    const {
        data: sData,
        setData: setS,
        patch,
    } = useForm({
        status: ticket.status,
    });

    const sendMessage = (e: FormEvent) => {
        e.preventDefault();
        post(`/support-tickets/${ticket.id}/messages`, {
            preserveScroll: true,
            onSuccess: () => reset('message'),
        });
    };

    return (
        <SuperAdminLayout
            headerTitle={ticket.subject}
            headerSubtitle={`Ticket #${ticket.id}`}
        >
            <Head title={`${ticket.subject} — Ticket`} />
            <div className="mx-auto w-full max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    title={ticket.subject}
                    eyebrow={`Ticket #${ticket.id}`}
                    icon={TicketIcon}
                    breadcrumbs={[
                        { label: 'Home', href: '/' },
                        { label: 'Support tickets', href: '/support-tickets' },
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
                            {ticket.tenant_name && (
                                <span>{ticket.tenant_name}</span>
                            )}
                        </>
                    }
                    actions={
                        <Button
                            href="/support-tickets"
                            variant="secondary"
                            size="sm"
                        >
                            Back to list
                        </Button>
                    }
                />

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <SectionCard
                            title="Conversation"
                            description="Replies from the tenant and the support team."
                        >
                            <div className="space-y-5">
                                <TicketMessageList
                                    messages={ticket.messages}
                                    showInternalNotes
                                />
                                <div className="border-t border-line pt-4">
                                    <TicketComposer
                                        value={data.message}
                                        onChange={(value) =>
                                            setData('message', value)
                                        }
                                        onSubmit={sendMessage}
                                        processing={processing}
                                        placeholder="Write a reply…"
                                        required
                                        options={
                                            <Checkbox
                                                label="Internal note"
                                                description="Hidden from the tenant."
                                                checked={data.is_internal}
                                                onChange={(e) =>
                                                    setData(
                                                        'is_internal',
                                                        e.target.checked,
                                                    )
                                                }
                                            />
                                        }
                                    />
                                </div>
                            </div>
                        </SectionCard>
                    </div>

                    <div className="space-y-6">
                        <SectionCard title="Details">
                            <div className="space-y-5">
                                <KeyValueGrid columns={1} tile={false}>
                                    <KeyValue
                                        label="Tenant"
                                        value={ticket.tenant_name || '—'}
                                        icon={BuildingOfficeIcon}
                                        tile
                                    />
                                    <KeyValue
                                        label="Priority"
                                        value={
                                            <Badge
                                                tone={priorityTone(
                                                    ticket.priority,
                                                )}
                                            >
                                                {ticket.priority}
                                            </Badge>
                                        }
                                        tile
                                    />
                                    <KeyValue
                                        label="Created"
                                        value={new Date(
                                            ticket.created_at,
                                        ).toLocaleDateString()}
                                        icon={CalendarDaysIcon}
                                        tile
                                    />
                                </KeyValueGrid>

                                <div className="border-t border-line pt-4">
                                    <Field
                                        label="Status"
                                        htmlFor="status"
                                        hint="Applies immediately once updated."
                                    >
                                        <div className="flex items-end gap-2">
                                            <Select
                                                id="status"
                                                value={sData.status}
                                                onChange={(e) =>
                                                    setS(
                                                        'status',
                                                        e.target.value,
                                                    )
                                                }
                                                className="flex-1"
                                            >
                                                {TICKET_STATUSES.map(
                                                    (status) => (
                                                        <option
                                                            key={status}
                                                            value={status}
                                                        >
                                                            {statusLabel(
                                                                status,
                                                            )}
                                                        </option>
                                                    ),
                                                )}
                                            </Select>
                                            <Button
                                                variant="secondary"
                                                onClick={() =>
                                                    patch(
                                                        `/support-tickets/${ticket.id}/status`,
                                                        {
                                                            preserveScroll: true,
                                                        },
                                                    )
                                                }
                                            >
                                                Update
                                            </Button>
                                        </div>
                                    </Field>
                                </div>
                            </div>
                        </SectionCard>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
