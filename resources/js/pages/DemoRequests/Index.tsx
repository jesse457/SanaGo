import { EnvelopeIcon, BuildingOfficeIcon } from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface DemoRequest {
    id: number;
    name: string;
    email: string;
    company: string | null;
    pipeline_status: string;
    assigned_to: string | null;
    follow_up_date: string | null;
    created_at: string;
}

const STAGES = [
    'new',
    'contacted',
    'demo_scheduled',
    'negotiating',
    'converted',
    'rejected',
] as const;
const STAGE_LABELS: Record<string, string> = {
    new: 'New',
    contacted: 'Contacted',
    demo_scheduled: 'Demo',
    negotiating: 'Negotiating',
    converted: 'Won',
    rejected: 'Lost',
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

export default function DemoRequestsIndex({
    demoRequests,
}: {
    demoRequests: DemoRequest[];
}) {
    return (
        <SuperAdminLayout
            headerTitle="Demo Requests"
            headerSubtitle="Sales pipeline"
        >
            <Head title="Demo Requests" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[{ label: 'Home' }, { label: 'Demo Requests' }]}
                />

                <div className="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    {STAGES.map((stage) => {
                        const items = demoRequests.filter(
                            (d) => d.pipeline_status === stage,
                        );

                        return (
                            <div
                                key={stage}
                                className="rounded-xl bg-surface-muted p-3"
                            >
                                <div className="mb-3 flex items-center justify-between">
                                    <StatusBadge
                                        label={STAGE_LABELS[stage]}
                                        color={STAGE_COLORS[stage]}
                                    />
                                    <span className="rounded-full bg-surface px-2 py-0.5 text-xs font-bold text-ink-subtle">
                                        {items.length}
                                    </span>
                                </div>
                                <div className="space-y-2">
                                    {items.map((req) => (
                                        <Link
                                            key={req.id}
                                            href={`/demo-requests/${req.id}`}
                                            className="block rounded-lg border border-line bg-surface p-3 transition-all hover:border-accent/40 hover:shadow-surface"
                                        >
                                            <p className="truncate text-sm font-semibold text-ink">
                                                {req.name}
                                            </p>
                                            <p className="mt-0.5 flex items-center gap-1 truncate text-xs text-ink-subtle">
                                                <EnvelopeIcon className="h-3 w-3" />
                                                {req.email}
                                            </p>
                                            {req.company && (
                                                <p className="mt-0.5 flex items-center gap-1 text-xs text-ink-subtle">
                                                    <BuildingOfficeIcon className="h-3 w-3" />
                                                    {req.company}
                                                </p>
                                            )}
                                            {req.assigned_to && (
                                                <p className="mt-1 text-xs font-medium text-ink-subtle">
                                                    {req.assigned_to}
                                                </p>
                                            )}
                                        </Link>
                                    ))}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </SuperAdminLayout>
    );
}
