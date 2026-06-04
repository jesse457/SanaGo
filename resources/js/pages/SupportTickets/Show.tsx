import {
    PaperAirplaneIcon,
    BuildingOfficeIcon,
    CalendarDaysIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router, useForm } from '@inertiajs/react';
import DetailItem from '@/Components/DetailItem';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Message {
    id: number;
    message: string;
    is_internal: boolean;
    user_name: string;
    created_at: string;
}
interface Ticket {
    id: number;
    tenant_name: string | null;
    tenant_id: string;
    subject: string;
    status: string;
    priority: string;
    user_name: string | null;
    created_at: string;
    messages: Message[];
}

const STATUSES = ['open', 'in_progress', 'resolved', 'closed'];

export default function SupportTicketsShow({ ticket }: { ticket: Ticket }) {
    const { data, setData, post, processing, reset } = useForm({
        message: '',
        is_internal: false,
    });
    const {
        data: sData,
        setData: setS,
        patch,
    } = useForm({ status: ticket.status });

    const sendMessage = (e: React.FormEvent) => {
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
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home', href: '/' },
                        { label: 'Support Tickets', href: '/support-tickets' },
                        { label: ticket.subject },
                    ]}
                />

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <PageSection title="Conversation">
                            <div className="mb-6 max-h-96 space-y-4 overflow-y-auto p-6 pb-0">
                                {ticket.messages.map((m) => (
                                    <div
                                        key={m.id}
                                        className={`rounded-xl p-4 ${
                                            m.is_internal
                                                ? 'border border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-900/10'
                                                : 'bg-gray-50 dark:bg-neutral-800'
                                        }`}
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
                                        {m.is_internal && (
                                            <span className="mt-1 text-xs font-bold text-amber-600 dark:text-amber-400">
                                                Internal Note
                                            </span>
                                        )}
                                    </div>
                                ))}
                            </div>
                            <form
                                onSubmit={sendMessage}
                                className="space-y-3 p-6 pt-0"
                            >
                                <FormTextarea
                                    name="message"
                                    placeholder="Type your reply..."
                                    required
                                    value={data.message}
                                    onChange={(v) => setData('message', v)}
                                    rows={3}
                                />
                                <div className="flex items-center justify-between">
                                    <label className="flex items-center gap-2 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        <input
                                            type="checkbox"
                                            checked={data.is_internal}
                                            onChange={(e) =>
                                                setData(
                                                    'is_internal',
                                                    e.target.checked,
                                                )
                                            }
                                        />{' '}
                                        Internal note
                                    </label>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                                    >
                                        <PaperAirplaneIcon className="h-4 w-4" />{' '}
                                        Send
                                    </button>
                                </div>
                            </form>
                        </PageSection>
                    </div>

                    <div className="space-y-6">
                        <PageSection title="Details">
                            <div className="space-y-4 p-6">
                                <DetailItem
                                    label="Tenant"
                                    value={ticket.tenant_name || '—'}
                                    icon={BuildingOfficeIcon}
                                />
                                <DetailItem
                                    label="Priority"
                                    value={ticket.priority.toUpperCase()}
                                />
                                <DetailItem
                                    label="Created"
                                    value={new Date(
                                        ticket.created_at,
                                    ).toLocaleDateString()}
                                    icon={CalendarDaysIcon}
                                />
                                <div className="border-t border-gray-100 pt-4 dark:border-neutral-800">
                                    <label className="ml-1 text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        Status
                                    </label>
                                    <div className="mt-1.5 flex gap-2">
                                        <select
                                            value={sData.status}
                                            onChange={(e) =>
                                                setS('status', e.target.value)
                                            }
                                            className="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800"
                                        >
                                            {STATUSES.map((s) => (
                                                <option key={s} value={s}>
                                                    {s.replace('_', ' ')}
                                                </option>
                                            ))}
                                        </select>
                                        <button
                                            onClick={() =>
                                                patch(
                                                    `/support-tickets/${ticket.id}/status`,
                                                    {
                                                        preserveScroll: true,
                                                    },
                                                )
                                            }
                                            className="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700"
                                        >
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </PageSection>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
