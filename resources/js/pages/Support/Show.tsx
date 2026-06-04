import {
    TicketIcon,
    PaperAirplaneIcon,
    ArrowPathIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import DetailItem from '@/Components/DetailItem';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Message {
    id: number;
    message: string;
    is_internal: boolean;
    user_name: string;
    created_at: string;
}
interface Ticket {
    id: number;
    subject: string;
    status: string;
    priority: string;
    created_at: string;
    messages: Message[];
}

const STATUS_COLORS: Record<string, 'blue' | 'amber' | 'emerald' | 'gray'> = {
    open: 'blue',
    in_progress: 'amber',
    resolved: 'emerald',
    closed: 'gray',
};

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

    return (
        <AppLayout
            headerTitle={ticket.subject}
            headerSubtitle={`Ticket #${ticket.id}`}
        >
            <Head title={ticket.subject} />
            <div className="mx-auto max-w-3xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: 'Support', href: '/support' },
                        { label: ticket.subject },
                    ]}
                />

                <PageSection title="Ticket Details" icon={TicketIcon}>
                    <div className="space-y-6 p-6">
                        {/* Status badge */}
                        <div className="flex items-center gap-3">
                            <StatusBadge
                                label={ticket.status.replace('_', ' ')}
                                color={STATUS_COLORS[ticket.status] || 'gray'}
                            />
                        </div>

                        {/* Detail grid */}
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <DetailItem
                                label="Status"
                                value={ticket.status.replace('_', ' ')}
                            />
                            <DetailItem
                                label="Priority"
                                value={ticket.priority}
                            />
                            <DetailItem
                                label="Created"
                                value={new Date(
                                    ticket.created_at,
                                ).toLocaleDateString()}
                            />
                        </div>

                        {/* Messages */}
                        <div>
                            <span className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                Messages
                            </span>
                            <div className="mt-1.5 max-h-96 space-y-4 overflow-y-auto">
                                {ticket.messages.map((m) => (
                                    <div
                                        key={m.id}
                                        className={`rounded-xl p-4 ${m.is_internal ? 'hidden' : 'bg-gray-50 dark:bg-neutral-700'}`}
                                    >
                                        <div className="mb-1 flex items-center justify-between">
                                            <span className="text-sm font-bold">
                                                {m.user_name}
                                            </span>
                                            <span className="text-xs text-gray-400">
                                                {new Date(
                                                    m.created_at,
                                                ).toLocaleString()}
                                            </span>
                                        </div>
                                        <p className="text-sm">{m.message}</p>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Reply form */}
                        {ticket.status !== 'closed' && (
                            <form
                                onSubmit={sendReply}
                                className="space-y-3 border-t border-gray-100 pt-4 dark:border-neutral-700"
                            >
                                <span className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    Reply
                                </span>
                                <textarea
                                    value={data.message}
                                    onChange={(e) =>
                                        setData('message', e.target.value)
                                    }
                                    rows={3}
                                    placeholder="Type your reply..."
                                    className="mt-1.5 w-full resize-none rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm font-semibold transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 dark:placeholder:text-neutral-500"
                                    required
                                />
                                <div className="flex justify-end">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        {processing ? (
                                            <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                        ) : (
                                            <PaperAirplaneIcon className="h-4 w-4" />
                                        )}{' '}
                                        Send
                                    </button>
                                </div>
                            </form>
                        )}
                    </div>
                </PageSection>
            </div>
        </AppLayout>
    );
}
