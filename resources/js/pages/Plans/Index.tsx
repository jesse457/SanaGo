import {
    CreditCardIcon,
    CurrencyDollarIcon,
    PencilSquareIcon,
    PlusIcon,
    TrashIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
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

interface PlanFeature {
    feature_label: string;
    is_highlighted: boolean;
}
interface Plan {
    id: number;
    name: string;
    slug: string;
    description: string;
    monthly_price: string | null;
    yearly_price: string | null;
    is_active: boolean;
    features_json: PlanFeature[];
    subscriptions_count: number;
}

/** The plan most tenants are on is the one worth emphasising. */
function findRecommendedPlan(plans: Plan[]): Plan | null {
    return (
        plans.reduce<Plan | null>(
            (best, plan) =>
                plan.is_active &&
                (!best || plan.subscriptions_count > best.subscriptions_count)
                    ? plan
                    : best,
            null,
        ) ?? null
    );
}

export default function PlansIndex({ plans }: { plans: Plan[] }) {
    const [search, setSearch] = useState('');
    const [delModal, setDelModal] = useState<Plan | null>(null);
    const { delete: destroy, processing } = useForm();
    const filtered = useMemo(
        () =>
            plans.filter(
                (p) =>
                    p.name.toLowerCase().includes(search.toLowerCase()) ||
                    p.slug.toLowerCase().includes(search.toLowerCase()),
            ),
        [plans, search],
    );
    const recommended = findRecommendedPlan(plans);

    const handleDelete = (e: FormEvent) => {
        e.preventDefault();

        if (delModal) {
            destroy(`/plans/${delModal.id}`, {
                onSuccess: () => setDelModal(null),
            });
        }
    };

    return (
        <SuperAdminLayout
            headerTitle="Plans"
            headerSubtitle="Manage subscription plans and pricing"
        >
            <Head title="Plans" />
            <div className="mx-auto max-w-7xl space-y-6 px-6">
                <PageHeader
                    title="Plans"
                    description="Subscription tiers, their pricing and the quotas each tenant gets."
                    icon={CreditCardIcon}
                    breadcrumbs={[{ label: 'Home' }, { label: 'Plans' }]}
                    actions={
                        <Button href="/plans/create" icon={PlusIcon}>
                            New plan
                        </Button>
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

                {filtered.length > 0 ? (
                    <ul className="space-y-2">
                        {filtered.map((plan) => {
                            const isRecommended = recommended?.id === plan.id;
                            const highlights = plan.features_json.filter(
                                (f) => f.is_highlighted,
                            );

                            return (
                                <li key={plan.id}>
                                    <Card
                                        className={cn(
                                            'flex flex-col gap-4 p-4 transition-colors duration-200 ease-premium hover:border-line-strong sm:flex-row sm:items-center sm:gap-6 sm:p-5',
                                            isRecommended &&
                                                'border-accent/40 bg-accent-soft/40',
                                        )}
                                    >
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
                                                {isRecommended && (
                                                    <Badge tone="accent" dot>
                                                        Recommended
                                                    </Badge>
                                                )}
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
                                            </div>
                                            {plan.description && (
                                                <p className="mt-1.5 line-clamp-1 max-w-[70ch] text-[13px] leading-relaxed text-ink-muted">
                                                    {plan.description}
                                                </p>
                                            )}
                                            {highlights.length > 0 && (
                                                <div className="mt-2.5 flex flex-wrap gap-1.5">
                                                    {highlights.map((f, i) => (
                                                        <Badge
                                                            key={i}
                                                            tone="accent"
                                                            variant="outline"
                                                        >
                                                            {f.feature_label}
                                                        </Badge>
                                                    ))}
                                                </div>
                                            )}
                                        </div>

                                        <div className="grid shrink-0 grid-cols-3 gap-4 border-t border-line pt-3 sm:w-72 sm:grid-cols-3 sm:border-t-0 sm:border-l sm:pt-0 sm:pl-6">
                                            <div>
                                                <p className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    Monthly
                                                </p>
                                                <p className="mt-1 text-[15px] font-semibold text-ink tabular-nums">
                                                    {plan.monthly_price
                                                        ? `$${plan.monthly_price}`
                                                        : '—'}
                                                </p>
                                                {plan.yearly_price && (
                                                    <p className="text-[11.5px] text-ink-subtle tabular-nums">
                                                        ${plan.yearly_price}/yr
                                                    </p>
                                                )}
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    Seats
                                                </p>
                                                <p className="mt-1 flex items-center gap-1 text-[15px] font-semibold text-ink tabular-nums">
                                                    <UserGroupIcon className="h-4 w-4 text-ink-subtle" />
                                                    {plan.subscriptions_count}
                                                </p>
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    Price
                                                </p>
                                                <p className="mt-1 flex items-center gap-1 text-[13px] font-medium text-ink-muted tabular-nums">
                                                    <CurrencyDollarIcon className="h-4 w-4 text-ink-subtle" />
                                                    {plan.monthly_price
                                                        ? 'Monthly'
                                                        : 'Yearly'}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex shrink-0 items-center gap-1.5">
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
                ) : (
                    <EmptyState
                        icon={CreditCardIcon}
                        title="No plans found"
                        description={
                            search
                                ? 'No plan matches that search.'
                                : 'Create your first subscription plan to start selling tiers.'
                        }
                        action={{
                            label: 'Create first plan',
                            href: '/plans/create',
                        }}
                    />
                )}
            </div>

            <Dialog
                open={delModal !== null}
                onClose={() => setDelModal(null)}
                title="Delete plan"
                description={`Delete plan ${delModal?.name ?? ''}? Tenants on this plan will lose it.`}
                size="sm"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setDelModal(null)}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="danger"
                            loading={processing}
                            icon={TrashIcon}
                            onClick={handleDelete}
                        >
                            Delete
                        </Button>
                    </>
                }
            >
                <p className="text-[13px] leading-relaxed text-ink-muted">
                    This cannot be undone. Export anything you need from the
                    plan before deleting it.
                </p>
            </Dialog>
        </SuperAdminLayout>
    );
}
