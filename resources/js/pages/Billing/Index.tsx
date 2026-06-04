import {
    CreditCardIcon,
    CurrencyDollarIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ArchiveBoxIcon,
    ClockIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import DetailItem from '@/Components/DetailItem';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatCard from '@/Components/StatCard';
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
    const daysRemaining = sub
        ? Math.max(
              0,
              Math.ceil(
                  (new Date(sub.end_date).getTime() - Date.now()) / 86400000,
              ),
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
            <div className="mx-auto max-w-5xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[{ label: t('common.home') }, { label: 'Billing' }]}
                />

                {!sub ? (
                    <EmptyState
                        icon={CreditCardIcon}
                        title="No active subscription"
                        description="Contact your administrator for assistance."
                    />
                ) : (
                    <>
                        {/* Stats Row */}
                        <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                            <StatCard
                                icon={ArchiveBoxIcon}
                                label="Plan"
                                value={sub.plan_name}
                                color="blue"
                            />
                            <StatCard
                                icon={CurrencyDollarIcon}
                                label="Price"
                                value={`${sub.price}/${sub.billing_cycle}`}
                                color="purple"
                            />
                            <StatCard
                                icon={CheckCircleIcon}
                                label="Status"
                                value={sub.status}
                                color={
                                    sub.status === 'active'
                                        ? 'emerald'
                                        : 'amber'
                                }
                            />
                            <StatCard
                                icon={ClockIcon}
                                label="Auto Renew"
                                value={sub.auto_renew ? 'Yes' : 'No'}
                                color={sub.auto_renew ? 'emerald' : 'red'}
                            />
                        </div>

                        <div className="grid gap-6 lg:grid-cols-3">
                            {/* Plan Details */}
                            <div className="lg:col-span-2">
                                <PageSection
                                    title="Plan Details"
                                    icon={CreditCardIcon}
                                >
                                    <div className="p-6">
                                        <div className="grid gap-4 sm:grid-cols-2">
                                            <DetailItem
                                                label="Start Date"
                                                value={sub.start_date}
                                                icon={CalendarDaysIcon}
                                            />
                                            <DetailItem
                                                label="End Date"
                                                value={sub.end_date}
                                                icon={CalendarDaysIcon}
                                            />
                                            <div className="rounded-xl bg-gray-50 p-4 dark:bg-neutral-700/50">
                                                <div className="flex items-center gap-2">
                                                    <ClockIcon className="h-4 w-4 text-gray-400 dark:text-neutral-500" />
                                                    <span className="text-[10px] font-bold text-gray-400 uppercase dark:text-neutral-500">
                                                        Days Remaining
                                                    </span>
                                                </div>
                                                <p
                                                    className={`mt-1 ml-6 text-sm font-bold ${
                                                        daysRemaining < 30
                                                            ? 'text-red-500'
                                                            : 'text-emerald-600'
                                                    }`}
                                                >
                                                    {daysRemaining} days
                                                </p>
                                            </div>
                                            <DetailItem
                                                label="Billing Cycle"
                                                value={sub.billing_cycle}
                                            />
                                        </div>
                                        <div className="mt-4">
                                            <div className="mb-1 flex justify-between text-xs text-gray-400">
                                                <span>
                                                    Period Usage ({usagePercent}
                                                    %)
                                                </span>
                                                <span>
                                                    {365 - daysRemaining} of 365
                                                    days
                                                </span>
                                            </div>
                                            <div className="h-2.5 w-full rounded-full bg-gray-100 dark:bg-neutral-700">
                                                <div
                                                    className={`h-2.5 rounded-full transition-all ${
                                                        usagePercent > 90
                                                            ? 'bg-red-500'
                                                            : 'bg-blue-600'
                                                    }`}
                                                    style={{
                                                        width: `${Math.min(usagePercent, 100)}%`,
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </PageSection>
                            </div>

                            {/* Features */}
                            <div>
                                <PageSection title="Your Plan Features">
                                    <div className="p-6">
                                        {sub.features_json &&
                                        sub.features_json.length > 0 ? (
                                            <ul className="space-y-2.5">
                                                {sub.features_json.map(
                                                    (f, i) => (
                                                        <li
                                                            key={i}
                                                            className={`flex items-center gap-2 text-sm ${
                                                                f.is_highlighted
                                                                    ? 'font-semibold text-gray-900 dark:text-neutral-100'
                                                                    : 'text-gray-500 dark:text-neutral-400'
                                                            }`}
                                                        >
                                                            <CheckCircleIcon
                                                                className={`h-4 w-4 ${
                                                                    f.is_highlighted
                                                                        ? 'text-blue-600'
                                                                        : 'text-gray-300 dark:text-neutral-600'
                                                                }`}
                                                            />
                                                            {f.feature_label}
                                                        </li>
                                                    ),
                                                )}
                                            </ul>
                                        ) : (
                                            <p className="text-sm text-gray-400">
                                                No feature details available.
                                            </p>
                                        )}
                                    </div>
                                </PageSection>
                            </div>
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
