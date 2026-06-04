import {
    CheckIcon,
    BuildingOfficeIcon,
    CurrencyDollarIcon,
    CalendarDaysIcon,
    ArrowPathIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import DetailItem from '@/Components/DetailItem';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import StatusBadge from '@/Components/StatusBadge';
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

export default function SubscriptionsShow({
    subscription: sub,
}: {
    subscription: Subscription;
}) {
    const badgeColor: Record<string, 'emerald' | 'gray' | 'red'> = {
        active: 'emerald',
        inactive: 'gray',
        cancelled: 'red',
    };

    return (
        <SuperAdminLayout
            headerTitle={sub.plan_name}
            headerSubtitle="Subscription Details"
        >
            <Head title={`${sub.plan_name} — Subscription`} />
            <div className="mx-auto max-w-6xl space-y-6 px-6">
                <PageBreadcrumb
                    items={[
                        { label: 'Home' },
                        { label: 'Subscriptions', href: '/subscriptions' },
                        { label: sub.plan_name },
                    ]}
                />

                <div className="grid gap-6 lg:grid-cols-3">
                    <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-neutral-700 dark:bg-neutral-800">
                        <h2 className="mb-4 text-lg font-bold text-gray-900 dark:text-neutral-100">
                            {sub.plan_name} Plan
                        </h2>
                        <div className="mb-6 grid gap-4 sm:grid-cols-2">
                            <DetailItem
                                label="Tenant"
                                value={sub.tenant_name}
                                icon={BuildingOfficeIcon}
                            />
                            <DetailItem
                                label="Price"
                                value={`$${sub.price}/${sub.billing_cycle}`}
                                icon={CurrencyDollarIcon}
                            />
                            <div className="rounded-xl bg-gray-50 p-4 dark:bg-neutral-700/50">
                                <span className="text-[10px] font-bold text-gray-400 uppercase dark:text-neutral-500">
                                    Status
                                </span>
                                <div className="mt-1 ml-0">
                                    <StatusBadge
                                        label={sub.status}
                                        color={badgeColor[sub.status] || 'gray'}
                                    />
                                </div>
                            </div>
                            <DetailItem
                                label="Auto Renew"
                                value={sub.auto_renew ? 'Yes' : 'No'}
                                icon={ArrowPathIcon}
                            />
                            <DetailItem
                                label="Start"
                                value={sub.start_date}
                                icon={CalendarDaysIcon}
                            />
                            <DetailItem
                                label="End"
                                value={sub.end_date || '—'}
                                icon={CalendarDaysIcon}
                            />
                        </div>
                        {sub.features_json && sub.features_json.length > 0 && (
                            <div className="border-t border-gray-100 pt-4 dark:border-neutral-700">
                                <h3 className="mb-3 text-sm font-bold text-gray-900 dark:text-neutral-100">
                                    Features
                                </h3>
                                <ul className="space-y-2">
                                    {sub.features_json.map((f, i) => (
                                        <li
                                            key={i}
                                            className={`flex items-center gap-2 text-sm ${f.is_highlighted ? 'font-semibold' : 'text-gray-600 dark:text-neutral-400'}`}
                                        >
                                            <CheckIcon
                                                className={`h-4 w-4 ${f.is_highlighted ? 'text-blue-600' : 'text-gray-300 dark:text-neutral-600'}`}
                                            />
                                            {f.feature_label}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        )}
                    </div>

                    <div className="space-y-3">
                        <Link
                            href={`/subscriptions/${sub.id}/edit`}
                            className="block w-full rounded-xl bg-blue-600 py-3 text-center text-sm font-bold text-white hover:bg-blue-700"
                        >
                            Edit Subscription
                        </Link>
                        <Link
                            href="/subscriptions"
                            className="block w-full rounded-xl bg-gray-100 py-3 text-center text-sm font-semibold text-gray-700 dark:bg-neutral-800 dark:text-neutral-300"
                        >
                            Back to List
                        </Link>
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
