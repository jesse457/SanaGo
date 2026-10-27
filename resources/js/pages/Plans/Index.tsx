import {
    CheckCircleIcon,
    CreditCardIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import {
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
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { cn } from '@/lib/utils';
import {
    formatCurrency,
    formatNumber,
    humaniseKey,
} from '@/pages/Platform/components/format';
import type {
    FeatureOption,
    PlanRecord,
} from '@/pages/Platform/components/types';

interface PlansIndexProps {
    plans: PlanRecord[];
    featureOptions: FeatureOption[];
}

/** The plan most tenants are on is the one worth emphasising. */
function findRecommendedPlan(plans: PlanRecord[]): PlanRecord | null {
    return (
        plans.reduce<PlanRecord | null>(
            (best, plan) =>
                plan.is_active &&
                (!best ||
                    (plan.subscriptions_count ?? 0) >
                        (best.subscriptions_count ?? 0))
                    ? plan
                    : best,
            null,
        ) ?? null
    );
}

export default function PlansIndex({ plans, featureOptions }: PlansIndexProps) {
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<PlanRecord | null>(null);
    const { delete: destroy, processing } = useForm();

    const labelFor = useMemo(() => {
        const map = new Map<string, string>();

        for (const option of featureOptions ?? []) {
            map.set(option.value, option.label);
        }

        return map;
    }, [featureOptions]);

    const filtered = useMemo(() => {
        const term = search.trim().toLowerCase();

        if (term === '') {
            return plans;
        }

        return plans.filter(
            (plan) =>
                plan.name.toLowerCase().includes(term) ||
                plan.slug.toLowerCase().includes(term) ||
                (plan.description ?? '').toLowerCase().includes(term),
        );
    }, [plans, search]);

    const recommended = findRecommendedPlan(plans);

    const confirmDelete = () => {
        if (!delModal) {
            return;
        }

        destroy(`/plans/${delModal.id}`, {
            preserveScroll: true,
            onSuccess: () => setDelModal(null),
        });
    };

    const totalSubscriptions = plans.reduce(
        (total, plan) => total + (plan.subscriptions_count ?? 0),
        0,
    );

    return (
        <SuperAdminLayout
            headerTitle="Plans"
            headerSubtitle="Subscription catalogue"
        >
            <Head title="Plans" />
            <div className="space-y-5 pb-12">
                <PageHeader
                    title="Plans"
                    description="Subscription tiers: pricing, the quotas each tenant gets and the modules each plan unlocks."
                    breadcrumbs={[{ label: 'Billing' }, { label: 'Plans' }]}
                    actions={
                        <Button href="/plans/create" icon={PlusIcon}>
                            New plan
                        </Button>
                    }
                    meta={
                        <>
                            <span className="tabular-nums">
                                {plans.length} plan
                                {plans.length === 1 ? '' : 's'}
                            </span>
                            <span className="tabular-nums">
                                {totalSubscriptions} subscription
                                {totalSubscriptions === 1 ? '' : 's'}
                            </span>
                        </>
                    }
                />

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder="Search plans…"
                    />
                    <ToolbarSpacer />
                    <span className="text-[12px] text-ink-subtle tabular-nums">
                        {filtered.length} of {plans.length}
                    </span>
                </Toolbar>

                {filtered.length === 0 ? (
                    plans.length === 0 ? (
                        <EmptyState
                            icon={CreditCardIcon}
                            title="No plans yet"
                            description="Create your first tier to start selling subscriptions."
                            action={{
                                label: 'Create first plan',
                                href: '/plans/create',
                            }}
                        />
                    ) : (
                        <EmptyState
                            icon={CreditCardIcon}
                            title="No plan matches that search"
                            description="Try a different name or slug."
                            secondaryAction={
                                <Button
                                    variant="secondary"
                                    onClick={() => setSearch('')}
                                >
                                    Clear search
                                </Button>
                            }
                        />
                    )
                ) : (
                    <ul className="space-y-2">
                        {filtered.map((plan) => {
                            const isRecommended = recommended?.id === plan.id;
                            const highlights = (
                                plan.features_json ?? []
                            ).filter((feature) => feature.is_highlighted);
                            const enabledFlags = Object.entries(
                                plan.feature_flags ?? {},
                            )
                                .filter(([, enabled]) => enabled)
                                .map(([key]) => key);

                            return (
                                <li key={plan.id}>
                                    <Card
                                        className={cn(
                                            'flex flex-col gap-4 p-4 transition-colors duration-200 ease-premium hover:border-line-strong sm:p-5 xl:flex-row xl:items-start xl:gap-6',
                                            isRecommended &&
                                                'border-accent/40 bg-accent-soft/30',
                                        )}
                                    >
                                        {/* Identity + capabilities */}
                                        <div className="min-w-0 flex-1">
                                            <div className="flex flex-wrap items-center gap-2">
                                                <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                                                    {plan.name}
                                                </h2>
                                                <Badge
                                                    tone="muted"
                                                    variant="outline"
                                                    className="font-mono"
                                                >
                                                    {plan.slug}
                                                </Badge>
                                                <Badge
                                                    tone={
                                                        plan.is_active
                                                            ? 'positive'
                                                            : 'muted'
                                                    }
                                                    dot
                                                >
                                                    {plan.is_active
                                                        ? 'Active'
                                                        : 'Inactive'}
                                                </Badge>
                                                {plan.is_featured && (
                                                    <Badge tone="accent">
                                                        Featured
                                                    </Badge>
                                                )}
                                                {isRecommended && (
                                                    <Badge
                                                        tone="accent"
                                                        variant="outline"
                                                    >
                                                        Most used
                                                    </Badge>
                                                )}
                                            </div>

                                            {plan.description && (
                                                <p className="mt-1.5 line-clamp-2 max-w-[70ch] text-[13px] leading-relaxed text-ink-muted">
                                                    {plan.description}
                                                </p>
                                            )}

                                            <div className="mt-2.5 flex flex-wrap gap-1.5">
                                                {enabledFlags.length === 0 ? (
                                                    <span className="text-[12px] text-ink-subtle">
                                                        No capability flags
                                                        enabled
                                                    </span>
                                                ) : (
                                                    enabledFlags.map((key) => (
                                                        <Badge
                                                            key={key}
                                                            tone="positive"
                                                            variant="outline"
                                                        >
                                                            <CheckCircleIcon className="h-3 w-3" />
                                                            {labelFor.get(
                                                                key,
                                                            ) ??
                                                                humaniseKey(
                                                                    key,
                                                                )}
                                                        </Badge>
                                                    ))
                                                )}
                                            </div>

                                            {highlights.length > 0 && (
                                                <div className="mt-2 flex flex-wrap gap-1.5">
                                                    {highlights.map(
                                                        (feature, index) => (
                                                            <Badge
                                                                key={index}
                                                                tone="accent"
                                                                variant="outline"
                                                            >
                                                                {
                                                                    feature.feature_label
                                                                }
                                                            </Badge>
                                                        ),
                                                    )}
                                                </div>
                                            )}
                                        </div>

                                        {/* Quotas */}
                                        <dl className="grid shrink-0 grid-cols-3 gap-3 border-t border-line pt-3 xl:w-56 xl:border-t-0 xl:border-l xl:pt-0 xl:pl-5">
                                            <div>
                                                <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    Users
                                                </dt>
                                                <dd className="mt-1 text-[12.5px] font-medium text-ink tabular-nums">
                                                    {plan.max_users_per_tenant
                                                        ? formatNumber(
                                                              plan.max_users_per_tenant,
                                                          )
                                                        : '∞'}
                                                </dd>
                                            </div>
                                            <div>
                                                <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    Patients
                                                </dt>
                                                <dd className="mt-1 text-[12.5px] font-medium text-ink tabular-nums">
                                                    {plan.max_patients_per_tenant
                                                        ? formatNumber(
                                                              plan.max_patients_per_tenant,
                                                          )
                                                        : '∞'}
                                                </dd>
                                            </div>
                                            <div>
                                                <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    Storage
                                                </dt>
                                                <dd className="mt-1 text-[12.5px] font-medium text-ink tabular-nums">
                                                    {plan.storage_gb
                                                        ? `${formatNumber(plan.storage_gb)} GB`
                                                        : '∞'}
                                                </dd>
                                            </div>
                                        </dl>

                                        {/* Pricing + subscribers */}
                                        <div className="grid shrink-0 grid-cols-2 gap-3 border-t border-line pt-3 xl:w-60 xl:border-t-0 xl:border-l xl:pt-0 xl:pl-5">
                                            <div>
                                                <p className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    Monthly
                                                </p>
                                                <p className="mt-1 text-[13.5px] font-semibold text-ink tabular-nums">
                                                    {formatCurrency(
                                                        plan.monthly_price,
                                                        plan.currency,
                                                    )}
                                                </p>
                                                <p className="text-[11.5px] text-ink-subtle tabular-nums">
                                                    {formatCurrency(
                                                        plan.yearly_price,
                                                        plan.currency,
                                                    )}
                                                    /yr
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    Subscribers
                                                </p>
                                                <p className="mt-1 flex items-center gap-1 text-[13.5px] font-semibold text-ink tabular-nums">
                                                    <UserGroupIcon className="h-4 w-4 text-ink-subtle" />
                                                    {formatNumber(
                                                        plan.subscriptions_count ??
                                                            0,
                                                    )}
                                                </p>
                                                <p className="text-[11.5px] text-ink-subtle">
                                                    {plan.trial_days
                                                        ? `${plan.trial_days}-day trial`
                                                        : 'No trial'}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex shrink-0 items-center gap-1.5 border-t border-line pt-3 xl:border-t-0 xl:pt-0">
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                icon={PencilSquareIcon}
                                                href={`/plans/${plan.id}/edit`}
                                            >
                                                Edit
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                icon={TrashIcon}
                                                aria-label={`Delete ${plan.name}`}
                                                onClick={() =>
                                                    setDelModal(plan)
                                                }
                                            />
                                        </div>
                                    </Card>
                                </li>
                            );
                        })}
                    </ul>
                )}
            </div>

            <Dialog
                open={delModal !== null}
                onClose={() => setDelModal(null)}
                title={`Delete ${delModal?.name ?? 'plan'}?`}
                description="Plans with active subscribers cannot be deleted — move those tenants to another plan first."
                size="sm"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setDelModal(null)}
                            disabled={processing}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="danger"
                            icon={TrashIcon}
                            loading={processing}
                            onClick={confirmDelete}
                        >
                            Delete plan
                        </Button>
                    </>
                }
            >
                <p className="text-[13px] leading-relaxed text-ink-muted">
                    This cannot be undone. A plan that still has active
                    subscribers is refused by the server rather than orphaned —
                    check the subscriber count on the card first.
                </p>
            </Dialog>
        </SuperAdminLayout>
    );
}
