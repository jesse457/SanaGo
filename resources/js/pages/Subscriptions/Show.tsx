import {
    ArrowPathIcon,
    BuildingOfficeIcon,
    CalendarDaysIcon,
    CheckIcon,
    CurrencyDollarIcon,
    PencilSquareIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import {
    Badge,
    Button,
    Card,
    KeyValue,
    KeyValueGrid,
    PageHeader,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Subscription {
    id: number;
    plan_name: string;
    tenant_name: string;
    billing_cycle: string;
    price: string;
    start_date: string;
    end_date: string;
    status: string;
    auto_renew: boolean;
    features_json: { feature_label: string; is_highlighted: boolean }[];
}

const statusTone: Record<string, BadgeTone> = {
    active: 'positive',
    inactive: 'neutral',
    cancelled: 'critical',
};

export default function SubscriptionsShow({
    subscription: sub,
}: {
    subscription: Subscription;
}) {
    return (
        <SuperAdminLayout
            headerTitle={sub.plan_name}
            headerSubtitle="Subscription details"
        >
            <Head title={`${sub.plan_name} — Subscription`} />
            <div className="mx-auto max-w-6xl space-y-5 pb-12">
                <PageHeader
                    eyebrow="Subscription"
                    title={`${sub.plan_name} plan`}
                    description={sub.tenant_name}
                    breadcrumbs={[
                        { label: 'Subscriptions', href: '/subscriptions' },
                        { label: sub.plan_name },
                    ]}
                    actions={
                        <>
                            <Button href="/subscriptions" variant="secondary">
                                Back to list
                            </Button>
                            <Button
                                href={`/subscriptions/${sub.id}/edit`}
                                icon={PencilSquareIcon}
                            >
                                Edit subscription
                            </Button>
                        </>
                    }
                    meta={
                        <Badge tone={statusTone[sub.status] ?? 'neutral'} dot>
                            {sub.status}
                        </Badge>
                    }
                />

                <div className="grid gap-5 lg:grid-cols-3">
                    <div className="space-y-5 lg:col-span-2">
                        <Card className="p-5 sm:p-6">
                            <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                                Plan details
                            </h2>
                            <KeyValueGrid columns={2} className="mt-4">
                                <KeyValue
                                    label="Tenant"
                                    value={sub.tenant_name}
                                    icon={BuildingOfficeIcon}
                                />
                                <KeyValue
                                    label="Price"
                                    value={`$${sub.price}/${sub.billing_cycle}`}
                                    icon={CurrencyDollarIcon}
                                />
                                <KeyValue
                                    label="Status"
                                    value={sub.status}
                                    icon={ArrowPathIcon}
                                />
                                <KeyValue
                                    label="Auto renew"
                                    value={sub.auto_renew ? 'Yes' : 'No'}
                                    icon={ArrowPathIcon}
                                />
                                <KeyValue
                                    label="Start"
                                    value={sub.start_date}
                                    icon={CalendarDaysIcon}
                                />
                                <KeyValue
                                    label="End"
                                    value={sub.end_date || '—'}
                                    icon={CalendarDaysIcon}
                                />
                            </KeyValueGrid>
                        </Card>

                        {sub.features_json && sub.features_json.length > 0 && (
                            <Card>
                                <div className="border-b border-line px-5 py-4 sm:px-6">
                                    <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                                        Features
                                    </h2>
                                </div>
                                <ul className="divide-y divide-line">
                                    {sub.features_json.map((feature, index) => (
                                        <li
                                            key={index}
                                            className="flex items-center gap-2.5 px-5 py-3 sm:px-6"
                                        >
                                            <CheckIcon
                                                className={
                                                    feature.is_highlighted
                                                        ? 'h-4 w-4 shrink-0 text-accent'
                                                        : 'h-4 w-4 shrink-0 text-ink-subtle'
                                                }
                                            />
                                            <span
                                                className={
                                                    feature.is_highlighted
                                                        ? 'text-[13px] font-semibold text-ink'
                                                        : 'text-[13px] text-ink-muted'
                                                }
                                            >
                                                {feature.feature_label}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            </Card>
                        )}
                    </div>

                    <Card className="h-fit p-5 sm:p-6">
                        <p className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            Billing
                        </p>
                        <p className="mt-2 text-[26px] leading-none font-semibold tracking-[-0.02em] text-ink tabular-nums">
                            ${sub.price}
                        </p>
                        <p className="mt-1 text-[12.5px] text-ink-subtle">
                            per {sub.billing_cycle}
                        </p>
                        <dl className="mt-4 space-y-2 border-t border-line pt-4 text-[12.5px]">
                            <div className="flex items-center justify-between">
                                <dt className="text-ink-subtle">Start</dt>
                                <dd className="font-medium text-ink tabular-nums">
                                    {sub.start_date}
                                </dd>
                            </div>
                            <div className="flex items-center justify-between">
                                <dt className="text-ink-subtle">End</dt>
                                <dd className="font-medium text-ink tabular-nums">
                                    {sub.end_date || '—'}
                                </dd>
                            </div>
                            <div className="flex items-center justify-between">
                                <dt className="text-ink-subtle">Auto renew</dt>
                                <dd className="font-medium text-ink">
                                    {sub.auto_renew ? 'Yes' : 'No'}
                                </dd>
                            </div>
                        </dl>
                    </Card>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
