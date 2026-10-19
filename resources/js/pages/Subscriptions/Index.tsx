import {
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    CreditCardIcon,
    CalendarDaysIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import {
    Alert,
    Badge,
    Button,
    Card,
    Dialog,
    EmptyState,
    PageHeader,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface SubscriptionItem {
    id: number;
    plan_name: string;
    tenant_name: string;
    billing_cycle: string;
    price: string;
    start_date: string;
    end_date: string;
    status: string;
    features_json: { feature_label: string }[];
}

const statusTone: Record<string, BadgeTone> = {
    active: 'positive',
    inactive: 'neutral',
    cancelled: 'critical',
};

export default function SubscriptionsIndex({
    subscriptions,
}: {
    subscriptions: SubscriptionItem[];
}) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<SubscriptionItem | null>(null);
    const { delete: destroy, processing } = useForm();

    const filtered = useMemo(
        () =>
            subscriptions.filter(
                (s) =>
                    s.tenant_name
                        ?.toLowerCase()
                        .includes(search.toLowerCase()) ||
                    s.plan_name.toLowerCase().includes(search.toLowerCase()),
            ),
        [subscriptions, search],
    );

    const confirmDelete = () => {
        if (delModal) {
            destroy(`/subscriptions/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    return (
        <SuperAdminLayout
            headerTitle="Subscriptions"
            headerSubtitle="Manage tenant subscriptions"
        >
            <Head title="Subscriptions" />
            <div className="mx-auto max-w-7xl space-y-5 pb-12">
                <PageHeader
                    title="Subscriptions"
                    description="Manage tenant subscriptions"
                    breadcrumbs={[{ label: 'Subscriptions' }]}
                    actions={
                        <Button href="/subscriptions/create" icon={PlusIcon}>
                            New subscription
                        </Button>
                    }
                />

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder="Search subscriptions…"
                    />
                    <ToolbarSpacer />
                    <Button
                        href="/subscriptions/create"
                        variant="secondary"
                        icon={PlusIcon}
                    >
                        New subscription
                    </Button>
                </Toolbar>

                {filtered.length > 0 ? (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {filtered.map((sub) => (
                            <Card
                                key={sub.id}
                                className="group flex h-full flex-col transition-colors duration-200 ease-premium hover:border-line-strong"
                            >
                                <div className="flex items-start justify-between gap-3 border-b border-line px-5 py-4">
                                    <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-accent">
                                        <CreditCardIcon className="h-[18px] w-[18px]" />
                                    </span>
                                    <Badge
                                        tone={
                                            statusTone[sub.status] ?? 'neutral'
                                        }
                                        dot
                                    >
                                        {sub.status}
                                    </Badge>
                                </div>

                                <div className="flex-1 px-5 py-4">
                                    <Link
                                        href={`/subscriptions/${sub.id}`}
                                        className="block truncate text-[15px] font-semibold tracking-tight text-ink transition-colors duration-150 hover:text-accent"
                                    >
                                        {sub.plan_name}
                                    </Link>
                                    <p className="mt-0.5 truncate text-[12.5px] text-ink-muted">
                                        {sub.tenant_name}
                                    </p>
                                    <p className="mt-3 text-[20px] leading-none font-semibold tracking-[-0.02em] text-ink tabular-nums">
                                        ${sub.price}
                                        <span className="ml-0.5 text-[12px] font-normal text-ink-subtle">
                                            /{sub.billing_cycle}
                                        </span>
                                    </p>
                                    <p className="mt-3 flex items-center gap-1.5 text-[11.5px] text-ink-subtle tabular-nums">
                                        <CalendarDaysIcon className="h-3.5 w-3.5" />
                                        {sub.start_date} — {sub.end_date}
                                    </p>
                                </div>

                                <div className="flex items-center justify-end gap-1.5 border-t border-line px-4 py-3">
                                    <Button
                                        href={`/subscriptions/${sub.id}/edit`}
                                        variant="ghost"
                                        size="sm"
                                        icon={PencilSquareIcon}
                                    >
                                        Edit
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        icon={TrashIcon}
                                        aria-label={`Delete ${sub.plan_name}`}
                                        onClick={() => setDelModal(sub)}
                                        className="text-critical-ink hover:bg-critical-soft"
                                    />
                                </div>
                            </Card>
                        ))}
                    </div>
                ) : (
                    <EmptyState
                        icon={CreditCardIcon}
                        title="No subscriptions found"
                        description={
                            search
                                ? 'Try adjusting your search terms.'
                                : undefined
                        }
                    />
                )}
            </div>

            <Dialog
                open={delModal !== null}
                onClose={() => setDelModal(null)}
                size="md"
                title="Delete subscription"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setDelModal(null)}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button
                            variant="danger"
                            loading={processing}
                            onClick={confirmDelete}
                        >
                            {processing ? 'Deleting…' : 'Delete'}
                        </Button>
                    </>
                }
            >
                <Alert tone="critical" title="This cannot be undone">
                    Delete <strong>{delModal?.plan_name}</strong> subscription?
                </Alert>
            </Dialog>
        </SuperAdminLayout>
    );
}
