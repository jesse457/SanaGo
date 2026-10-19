import {
    CreditCardIcon,
    CurrencyDollarIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ArchiveBoxIcon,
    ClockIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import {
    EmptyState,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    ProgressBar,
    SectionCard,
    StatCard,
    StatGrid,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Subscription {
    plan_name: string;
    billing_cycle: string;
    price: string;
    start_date: string;
    end_date: string;
    status: string;
    auto_renew: boolean;
    features_json: { feature_label: string; is_highlighted: boolean }[];
}
interface BillingProps {
    subscription: Subscription | null;
    tenant_name: string;
}

export default function BillingIndex({
    subscription: sub,
    tenant_name,
}: BillingProps) {
    const { t } = useTranslation();
    // "Days remaining" is inherently time-dependent: the clock is read once per
    // render on purpose, so the lint rule is silenced with that intent.
    // eslint-disable-next-line react-hooks/purity
    const now = Date.now();
    const daysRemaining = sub
        ? Math.max(
              0,
              Math.ceil((new Date(sub.end_date).getTime() - now) / 86400000),
          )
        : 0;
    const usagePercent = daysRemaining
        ? Math.round(((365 - daysRemaining) / 365) * 100)
        : 0;

    return (
        <AppLayout
            headerTitle="Billing & Subscription"
            headerSubtitle={tenant_name}
        >
            <Head title="Billing" />

            <div className="mx-auto flex max-w-5xl flex-col gap-6">
                <PageHeader
                    title="Billing & Subscription"
                    eyebrow={tenant_name}
                    description="Your current plan, billing period and included features."
                    breadcrumbs={[
                        { label: t('common.home'), href: route('dashboard') },
                        { label: 'Billing' },
                    ]}
                />

                {!sub ? (
                    <EmptyState
                        icon={CreditCardIcon}
                        title="No active subscription"
                        description="Contact your administrator for assistance."
                    />
                ) : (
                    <>
                        <StatGrid columns={4}>
                            <StatCard
                                icon={ArchiveBoxIcon}
                                label="Plan"
                                value={sub.plan_name}
                                tone="accent"
                            />
                            <StatCard
                                icon={CurrencyDollarIcon}
                                label="Price"
                                value={`${sub.price}/${sub.billing_cycle}`}
                                tone="accent"
                            />
                            <StatCard
                                icon={CheckCircleIcon}
                                label="Status"
                                value={sub.status}
                                tone={
                                    sub.status === 'active'
                                        ? 'positive'
                                        : 'warning'
                                }
                            />
                            <StatCard
                                icon={ClockIcon}
                                label="Auto Renew"
                                value={sub.auto_renew ? 'Yes' : 'No'}
                                tone={sub.auto_renew ? 'positive' : 'critical'}
                            />
                        </StatGrid>

                        <div className="grid gap-6 lg:grid-cols-3">
                            <div className="lg:col-span-2">
                                <SectionCard
                                    title="Plan Details"
                                    icon={CreditCardIcon}
                                >
                                    <div className="space-y-4">
                                        <KeyValueGrid columns={2}>
                                            <KeyValue
                                                label="Start Date"
                                                value={sub.start_date}
                                                icon={CalendarDaysIcon}
                                            />
                                            <KeyValue
                                                label="End Date"
                                                value={sub.end_date}
                                                icon={CalendarDaysIcon}
                                            />
                                            <KeyValue
                                                label="Billing Cycle"
                                                value={sub.billing_cycle}
                                            />
                                            <div className="min-w-0">
                                                <dt className="flex items-center gap-1.5 text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                    <ClockIcon className="h-3.5 w-3.5" />
                                                    Days Remaining
                                                </dt>
                                                <dd className="mt-1 flex items-center gap-2">
                                                    <span
                                                        className={`text-[13px] font-semibold tabular-nums ${
                                                            daysRemaining < 30
                                                                ? 'text-critical-ink'
                                                                : 'text-positive-ink'
                                                        }`}
                                                    >
                                                        {daysRemaining} days
                                                    </span>
                                                </dd>
                                            </div>
                                        </KeyValueGrid>

                                        <div>
                                            <div className="mb-1.5 flex justify-between text-[12px] text-ink-subtle tabular-nums">
                                                <span>
                                                    Period usage ({usagePercent}
                                                    %)
                                                </span>
                                                <span>
                                                    {365 - daysRemaining} of 365
                                                    days
                                                </span>
                                            </div>
                                            <ProgressBar
                                                value={Math.min(
                                                    usagePercent,
                                                    100,
                                                )}
                                                tone={
                                                    usagePercent > 90
                                                        ? 'critical'
                                                        : 'accent'
                                                }
                                                label="Period usage"
                                            />
                                        </div>
                                    </div>
                                </SectionCard>
                            </div>

                            <SectionCard title="Your Plan Features">
                                {sub.features_json &&
                                sub.features_json.length > 0 ? (
                                    <ul className="space-y-2.5">
                                        {sub.features_json.map((f, i) => (
                                            <li
                                                key={i}
                                                className={`flex items-center gap-2 text-[13px] ${
                                                    f.is_highlighted
                                                        ? 'font-semibold text-ink'
                                                        : 'text-ink-subtle'
                                                }`}
                                            >
                                                <CheckCircleIcon
                                                    className={`h-4 w-4 shrink-0 ${
                                                        f.is_highlighted
                                                            ? 'text-accent'
                                                            : 'text-ink-subtle'
                                                    }`}
                                                />
                                                {f.feature_label}
                                            </li>
                                        ))}
                                    </ul>
                                ) : (
                                    <p className="text-[13px] text-ink-subtle">
                                        No feature details available.
                                    </p>
                                )}
                            </SectionCard>
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
