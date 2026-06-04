import {
    BuildingOfficeIcon,
    CurrencyDollarIcon,
    TicketIcon,
    UserGroupIcon,
    ArrowTrendingUpIcon,
    PlusIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Metrics {
    total_tenants: number;
    active_tenants: number;
    active_subscriptions: number;
    monthly_revenue: number;
    yearly_revenue: number;
    pending_demo_requests: number;
    open_tickets: number;
}

interface RecentTenant {
    id: string;
    name: string;
    domain: string | null;
    is_active: boolean;
    plan_name: string | null;
    created_at: string;
}

interface Props {
    metrics: Metrics;
    recentTenants: RecentTenant[];
    recentActivity: {
        type: string;
        label: string;
        detail: string;
        time: string;
    }[];
}

export default function SuperAdminDashboard({
    metrics,
    recentTenants,
    recentActivity,
}: Props) {
    const { t } = useTranslation();

    const stats = [
        {
            label: 'Total Tenants',
            value: metrics.total_tenants,
            sub: `${metrics.active_tenants} active`,
            icon: BuildingOfficeIcon,
            color: 'blue',
        },
        {
            label: 'Active Subscriptions',
            value: metrics.active_subscriptions,
            sub: `$${metrics.monthly_revenue}/mo MRR`,
            icon: CurrencyDollarIcon,
            color: 'emerald',
        },
        {
            label: 'Demo Requests',
            value: metrics.pending_demo_requests,
            sub: 'pending review',
            icon: UserGroupIcon,
            color: 'purple',
        },
        {
            label: 'Open Tickets',
            value: metrics.open_tickets,
            sub: 'needs attention',
            icon: TicketIcon,
            color: 'amber',
        },
    ];

    const colorMap: Record<string, string> = {
        blue: 'bg-blue-50 text-blue-700',
        emerald: 'bg-emerald-50 text-emerald-700',
        purple: 'bg-purple-50 text-purple-700',
        amber: 'bg-amber-50 text-amber-700',
    };

    return (
        <SuperAdminLayout
            headerTitle={t('page.super_admin_dashboard.title')}
            headerSubtitle={t('page.super_admin_dashboard.subtitle')}
        >
            <Head title={t('page.super_admin_dashboard.page_title')} />

            {/* Stats Grid */}
            <div className="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                {stats.map((stat) => (
                    <div
                        key={stat.label}
                        className="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
                    >
                        <div className="mb-4 flex items-center justify-between">
                            <div
                                className={`rounded-xl p-2.5 ${colorMap[stat.color]}`}
                            >
                                <stat.icon className="h-6 w-6" />
                            </div>
                        </div>
                        <p className="text-3xl font-bold text-gray-900 dark:text-neutral-100">
                            {stat.value}
                        </p>
                        <p className="mt-1 text-sm font-medium text-gray-500 dark:text-neutral-400">
                            {stat.label}
                        </p>
                        <p className="mt-0.5 text-xs text-gray-400 dark:text-neutral-500">
                            {stat.sub}
                        </p>
                    </div>
                ))}
            </div>

            <div className="grid gap-6 lg:grid-cols-3">
                {/* Recent Tenants */}
                <div className="rounded-2xl border border-gray-100 bg-white shadow-sm lg:col-span-2 dark:border-neutral-800 dark:bg-neutral-900">
                    <div className="flex items-center justify-between border-b border-gray-100 p-6 dark:border-neutral-800">
                        <h2 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                            Recent Tenants
                        </h2>
                        <Link
                            href="/tenants/create"
                            className="flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" /> New Tenant
                        </Link>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-gray-100 text-left text-xs font-bold tracking-wider text-gray-400 uppercase dark:border-neutral-800">
                                    <th className="px-6 py-3">Hospital</th>
                                    <th className="px-6 py-3">Domain</th>
                                    <th className="px-6 py-3">Plan</th>
                                    <th className="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {recentTenants.map((tenant) => (
                                    <tr
                                        key={tenant.id}
                                        className="border-b border-gray-50 hover:bg-gray-50 dark:border-neutral-800/50 dark:hover:bg-neutral-800/30"
                                    >
                                        <td className="px-6 py-3.5">
                                            <Link
                                                href={`/tenants/${tenant.id}`}
                                                className="text-sm font-semibold text-gray-900 hover:text-blue-600 dark:text-neutral-100"
                                            >
                                                {tenant.name}
                                            </Link>
                                        </td>
                                        <td className="px-6 py-3.5 text-sm text-gray-500 dark:text-neutral-400">
                                            {tenant.domain || '—'}
                                        </td>
                                        <td className="px-6 py-3.5">
                                            <span className="text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                {tenant.plan_name || 'No plan'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-3.5">
                                            <span
                                                className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold ${tenant.is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'}`}
                                            >
                                                {tenant.is_active
                                                    ? 'Active'
                                                    : 'Suspended'}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Recent Activity */}
                <div className="rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div className="border-b border-gray-100 p-6 dark:border-neutral-800">
                        <h2 className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                            Recent Activity
                        </h2>
                    </div>
                    <div className="space-y-4 p-6">
                        {recentActivity.map((activity, i) => (
                            <div key={i} className="flex gap-3">
                                <div className="mt-2 h-2 w-2 shrink-0 rounded-full bg-blue-500" />
                                <div className="min-w-0 flex-1">
                                    <p className="text-sm font-medium text-gray-900 dark:text-neutral-100">
                                        {activity.label}
                                    </p>
                                    <p className="truncate text-xs text-gray-500 dark:text-neutral-400">
                                        {activity.detail}
                                    </p>
                                    <p className="mt-1 text-xs text-gray-400 dark:text-neutral-500">
                                        {activity.time}
                                    </p>
                                </div>
                            </div>
                        ))}
                        {recentActivity.length === 0 && (
                            <p className="py-4 text-center text-sm text-gray-400">
                                No recent activity
                            </p>
                        )}
                    </div>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
