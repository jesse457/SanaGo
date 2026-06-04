import { ChatBubbleLeftIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import DetailItem from '@/Components/DetailItem';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Note {
    id: number;
    note: string;
    user_name: string;
    created_at: string;
}
interface DemoRequest {
    id: number;
    name: string;
    email: string;
    company: string | null;
    message: string;
    pipeline_status: string;
    assigned_to: { id: number; name: string } | null;
    follow_up_date: string | null;
    created_at: string;
    notes: Note[];
}

const STAGES = [
    'new',
    'contacted',
    'demo_scheduled',
    'negotiating',
    'converted',
    'rejected',
];
const STAGE_LABELS: Record<string, string> = {
    new: 'New',
    contacted: 'Contacted',
    demo_scheduled: 'Demo Scheduled',
    negotiating: 'Negotiating',
    converted: 'Converted',
    rejected: 'Rejected',
};
const STAGE_COLORS: Record<
    string,
    'blue' | 'purple' | 'amber' | 'emerald' | 'red'
> = {
    new: 'blue',
    contacted: 'purple',
    demo_scheduled: 'amber',
    negotiating: 'amber',
    converted: 'emerald',
    rejected: 'red',
};

export default function DemoRequestsShow({
    demoRequest: req,
}: {
    demoRequest: DemoRequest;
}) {
    const { data, setData, post, processing } = useForm({ note: '' });
    const {
        data: statusForm,
        setData: setStatus,
        patch: patchStatus,
    } = useForm({
        pipeline_status: req.pipeline_status,
        assigned_to: req.assigned_to?.id ?? '',
        follow_up_date: req.follow_up_date ?? '',
    });

    return (
        <SuperAdminLayout
            headerTitle={req.name}
            headerSubtitle="Demo Request Detail"
        >
            <Head title={`${req.name} — Demo Request`} />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Demo Requests', href: '/demo-requests' },
                        { label: req.name },
                    ]}
                />

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="space-y-6 lg:col-span-2">
                        <PageSection title="Contact Information">
                            <div className="grid gap-4 p-6 sm:grid-cols-2">
                                <DetailItem label="Name" value={req.name} />
                                <DetailItem label="Email" value={req.email} />
                                <DetailItem
                                    label="Company"
                                    value={req.company}
                                />
                                <DetailItem
                                    label="Received"
                                    value={new Date(
                                        req.created_at,
                                    ).toLocaleDateString()}
                                />
                            </div>
                            {req.message && (
                                <div className="mx-6 mb-6 rounded-lg bg-gray-50 p-4 dark:bg-neutral-800">
                                    <p className="text-sm text-gray-700 dark:text-neutral-300">
                                        {req.message}
                                    </p>
                                </div>
                            )}
                        </PageSection>

                        <PageSection title="Notes">
                            <div className="space-y-3 p-6">
                                {req.notes.length === 0 ? (
                                    <EmptyState
                                        icon={ChatBubbleLeftIcon}
                                        title="No notes yet"
                                    />
                                ) : (
                                    req.notes.map((n) => (
                                        <div
                                            key={n.id}
                                            className="rounded-lg bg-gray-50 p-3 dark:bg-neutral-800"
                                        >
                                            <p className="text-sm">{n.note}</p>
                                            <p className="mt-1 text-xs text-gray-400">
                                                {n.user_name} —{' '}
                                                {new Date(
                                                    n.created_at,
                                                ).toLocaleString()}
                                            </p>
                                        </div>
                                    ))
                                )}
                            </div>
                            <form
                                onSubmit={(e) => {
                                    e.preventDefault();
                                    post(`/demo-requests/${req.id}/notes`, {
                                        preserveScroll: true,
                                    });
                                }}
                                className="flex items-end gap-2 border-t border-gray-100 px-6 pt-4 pb-6 dark:border-neutral-700"
                            >
                                <FormTextarea
                                    name="note"
                                    placeholder="Add a note..."
                                    value={data.note}
                                    onChange={(v) => setData('note', v)}
                                    rows={2}
                                    required
                                    className="flex-1"
                                />
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="shrink-0 rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-50"
                                >
                                    Add
                                </button>
                            </form>
                        </PageSection>
                    </div>

                    <div className="space-y-6">
                        <PageSection title="Pipeline">
                            <div className="space-y-4 p-6">
                                <div className="flex items-center justify-between">
                                    <span className="text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                        Current Stage
                                    </span>
                                    <StatusBadge
                                        label={
                                            STAGE_LABELS[req.pipeline_status]
                                        }
                                        color={
                                            STAGE_COLORS[req.pipeline_status]
                                        }
                                    />
                                </div>
                                <FormSelect
                                    name="pipeline_status"
                                    label="Stage"
                                    value={statusForm.pipeline_status}
                                    onChange={(v) =>
                                        setStatus('pipeline_status', v)
                                    }
                                    options={STAGES.map((s) => ({
                                        value: s,
                                        label: STAGE_LABELS[s],
                                    }))}
                                />
                                <FormInput
                                    name="follow_up_date"
                                    label="Follow-up Date"
                                    type="date"
                                    value={statusForm.follow_up_date}
                                    onChange={(v) =>
                                        setStatus('follow_up_date', v)
                                    }
                                />
                                <button
                                    onClick={() =>
                                        patchStatus(
                                            `/demo-requests/${req.id}/status`,
                                            { preserveScroll: true },
                                        )
                                    }
                                    className="w-full rounded-lg bg-blue-600 py-3 text-sm font-bold text-white hover:bg-blue-700"
                                >
                                    Update Status
                                </button>
                            </div>
                        </PageSection>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
