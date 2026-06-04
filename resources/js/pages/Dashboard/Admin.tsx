import {
    BanknotesIcon,
    UserGroupIcon,
    CalendarIcon,
    BeakerIcon,
    HeartIcon,
    ExclamationCircleIcon,
    BuildingOfficeIcon,
    ClockIcon,
    ArrowTrendingUpIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend,
} from 'chart.js';
import React from 'react';
import { Line } from 'react-chartjs-2';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { AdminDashboardMetrics } from '@/types/dashboard';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend,
);

interface Props {
    metrics: AdminDashboardMetrics;
}

export default function Dashboard({ metrics }: Props) {
    const { t } = useTranslation();
    const MONTHS = [
        t('common.months.jan'),
        t('common.months.feb'),
        t('common.months.mar'),
        t('common.months.apr'),
        t('common.months.may'),
        t('common.months.jun'),
        t('common.months.jul'),
        t('common.months.aug'),
        t('common.months.sep'),
        t('common.months.oct'),
        t('common.months.nov'),
        t('common.months.dec'),
    ];
    const {
        patient_stats,
        appointment_stats,
        clinical_stats,
        emergency_stats,
        revenue_summary,
        outstanding_invoices,
        outstanding_invoices_count,
        bed_occupancy,
        monthly_data,
        recent_activity,
    } = metrics;

    const chartData = {
        labels: monthly_data.map(
            (d) => MONTHS[parseInt(d.month) - 1] || d.month,
        ),
        datasets: [
            {
                label: t('page.admin_dashboard.revenue'),
                data: monthly_data.map((d) => d.revenue),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
            },
            {
                label: t('page.admin_dashboard.expenditure'),
                data: monthly_data.map((d) => d.expenditure),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                fill: true,
                tension: 0.4,
            },
        ],
    };

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: true, position: 'bottom' as const } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { callback: (v: any) => `${(v / 1000).toFixed(0)}k` },
            },
            x: { grid: { display: false as const } },
        },
    };

    const maxProfit = Math.max(
        ...monthly_data.map((d) => Math.abs(d.profit)),
        1,
    );

    return (
        <AppLayout
            headerTitle={t('page.admin_dashboard.title')}
            headerSubtitle={t('page.admin_dashboard.subtitle')}
        >
            <Head title={t('page.admin_dashboard.title')} />

            <div className="space-y-6">
                {/* Section: Key Metrics */}
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label={t('page.admin_dashboard.total_patients')}
                        value={patient_stats.total.toLocaleString()}
                        subtitle={t('page.admin_dashboard.new_today', {
                            count: patient_stats.new_today,
                        })}
                        icon={UserGroupIcon}
                        color="text-blue-600"
                        bg="bg-blue-50"
                    />
                    <StatCard
                        label={t('page.admin_dashboard.active_admissions')}
                        value={patient_stats.active_admissions.toLocaleString()}
                        subtitle={t('page.admin_dashboard.bed_occupancy', {
                            occupied: bed_occupancy.occupied,
                            total: bed_occupancy.total,
                            percentage: bed_occupancy.percentage,
                        })}
                        icon={BuildingOfficeIcon}
                        color="text-indigo-600"
                        bg="bg-indigo-50"
                    />
                    <StatCard
                        label={t('page.admin_dashboard.todays_appointments')}
                        value={appointment_stats.total.toLocaleString()}
                        subtitle={t('page.admin_dashboard.appointment_status', {
                            completed: appointment_stats.completed,
                            waiting: appointment_stats.waiting,
                        })}
                        icon={CalendarIcon}
                        color="text-amber-600"
                        bg="bg-amber-50"
                    />
                    <StatCard
                        label={t('page.admin_dashboard.revenue_today')}
                        value={`${(revenue_summary?.total_revenue || 0).toLocaleString()} FCFA`}
                        subtitle={t(
                            'page.admin_dashboard.outstanding_invoices',
                            { count: outstanding_invoices_count },
                        )}
                        icon={BanknotesIcon}
                        color="text-emerald-600"
                        bg="bg-emerald-50"
                    />
                </div>

                {/* Section: Clinical & Emergency Stats */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <MiniStat
                        icon={HeartIcon}
                        label={t('page.admin_dashboard.active_surgeries')}
                        value={clinical_stats.active_surgeries}
                        color="text-rose-600"
                        bg="bg-rose-50"
                    />
                    <MiniStat
                        icon={BeakerIcon}
                        label={t('page.admin_dashboard.pending_lab_requests')}
                        value={clinical_stats.pending_lab_requests}
                        color="text-purple-600"
                        bg="bg-purple-50"
                    />
                    <MiniStat
                        icon={ClockIcon}
                        label={t('page.admin_dashboard.pending_prescriptions')}
                        value={clinical_stats.pending_prescriptions}
                        color="text-teal-600"
                        bg="bg-teal-50"
                    />
                    <MiniStat
                        icon={ExclamationCircleIcon}
                        label={t('page.admin_dashboard.active_emergencies')}
                        value={emergency_stats.active_count}
                        color="text-orange-600"
                        bg="bg-orange-50"
                    />
                    <Link
                        href="/finance/revenue"
                        className="group flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 transition-all hover:border-blue-300 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-blue-700"
                    >
                        <div>
                            <p className="text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                {t('page.admin_dashboard.revenue_analytics')}
                            </p>
                            <p className="mt-1 text-sm font-bold text-blue-600 group-hover:text-blue-700">
                                {t('page.admin_dashboard.view_full_report')}
                            </p>
                        </div>
                        <ArrowTrendingUpIcon className="h-5 w-5 text-blue-500 transition-transform group-hover:translate-x-0.5" />
                    </Link>
                </div>

                {/* Section: Revenue vs Expenditure Chart + Outstanding */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-neutral-700 dark:bg-neutral-800">
                        <h3 className="mb-4 text-base font-bold text-gray-900 dark:text-neutral-100">
                            {t('page.admin_dashboard.revenue_vs_expenditure')}
                        </h3>
                        <div className="h-72 w-full">
                            {monthly_data.length > 0 ? (
                                <Line data={chartData} options={chartOptions} />
                            ) : (
                                <div className="flex h-full items-center justify-center text-sm text-gray-400 dark:text-neutral-500">
                                    {t(
                                        'page.admin_dashboard.no_data_available',
                                    )}
                                </div>
                            )}
                        </div>
                    </div>

                    <div className="space-y-6">
                        <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <h3 className="mb-4 text-base font-bold text-gray-900 dark:text-neutral-100">
                                {t('page.admin_dashboard.monthly_profit_loss')}
                            </h3>
                            <div className="space-y-4">
                                {monthly_data.length > 0 ? (
                                    monthly_data
                                        .slice(-6)
                                        .reverse()
                                        .map((d) => (
                                            <div key={d.month}>
                                                <div className="mb-1 flex justify-between text-xs">
                                                    <span className="font-medium text-gray-600 dark:text-neutral-300">
                                                        {MONTHS[
                                                            parseInt(d.month) -
                                                                1
                                                        ] || d.month}
                                                    </span>
                                                    <span
                                                        className={`font-bold ${d.profit >= 0 ? 'text-emerald-600' : 'text-red-600'}`}
                                                    >
                                                        {d.profit >= 0
                                                            ? '+'
                                                            : ''}
                                                        {d.profit.toLocaleString()}{' '}
                                                        FCFA
                                                    </span>
                                                </div>
                                                <div className="h-1.5 w-full rounded-full bg-gray-100 dark:bg-neutral-700">
                                                    <div
                                                        className={`h-1.5 rounded-full ${d.profit >= 0 ? 'bg-emerald-500' : 'bg-red-500'}`}
                                                        style={{
                                                            width: `${Math.min((Math.abs(d.profit) / maxProfit) * 100, 100)}%`,
                                                        }}
                                                    />
                                                </div>
                                            </div>
                                        ))
                                ) : (
                                    <p className="py-8 text-center text-sm text-gray-400 dark:text-neutral-500">
                                        {t('page.admin_dashboard.no_data_yet')}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="rounded-xl border border-red-200 bg-white p-6 shadow-sm dark:border-red-900/50 dark:bg-neutral-800">
                            <h3 className="mb-4 text-base font-bold text-gray-900 dark:text-neutral-100">
                                {t(
                                    'page.admin_dashboard.outstanding_invoices_title',
                                )}
                            </h3>
                            <div className="text-center">
                                <p className="text-3xl font-black text-red-600">
                                    {outstanding_invoices_count}
                                </p>
                                <p className="mt-1 text-sm text-gray-500">
                                    {t('page.admin_dashboard.unpaid_amount', {
                                        amount: outstanding_invoices.toLocaleString(),
                                    })}
                                </p>
                                <Link
                                    href="/finance/invoices"
                                    className="mt-3 inline-block text-xs font-semibold text-blue-600 hover:underline"
                                >
                                    {t(
                                        'page.admin_dashboard.view_all_invoices',
                                    )}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Section: Revenue Breakdown + Recent Activity */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <h3 className="mb-4 text-base font-bold text-gray-900 dark:text-neutral-100">
                            {t('page.admin_dashboard.revenue_breakdown')}
                        </h3>
                        <div className="space-y-3">
                            <RevenueRow
                                label={t('page.admin_dashboard.paid_invoices')}
                                value={revenue_summary?.invoices || 0}
                                count={revenue_summary?.invoice_count || 0}
                            />
                            <RevenueRow
                                label={t('page.admin_dashboard.surgeries')}
                                value={revenue_summary?.surgeries || 0}
                                count={revenue_summary?.surgery_count || 0}
                            />
                            <RevenueRow
                                label={t('page.admin_dashboard.emergencies')}
                                value={revenue_summary?.emergencies || 0}
                                count={revenue_summary?.emergency_count || 0}
                            />
                            <RevenueRow
                                label={t('page.admin_dashboard.admissions')}
                                value={revenue_summary?.admissions || 0}
                            />
                        </div>
                    </div>

                    <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <h3 className="mb-4 text-base font-bold text-gray-900 dark:text-neutral-100">
                            {t('page.admin_dashboard.recent_activity')}
                        </h3>
                        {recent_activity.length > 0 ? (
                            <div className="space-y-3">
                                {recent_activity.map((activity) => (
                                    <div
                                        key={activity.id}
                                        className="flex items-start gap-3 rounded-lg bg-gray-50 p-3 dark:bg-neutral-700/30"
                                    >
                                        <div
                                            className={`shrink-0 rounded-lg p-1.5 ${
                                                activity.event === 'created'
                                                    ? 'bg-emerald-100 text-emerald-600'
                                                    : activity.event ===
                                                        'updated'
                                                      ? 'bg-blue-100 text-blue-600'
                                                      : activity.event ===
                                                          'deleted'
                                                        ? 'bg-red-100 text-red-600'
                                                        : 'bg-gray-100 text-gray-600'
                                            }`}
                                        >
                                            {activity.event === 'created' ? (
                                                <CheckCircleIcon className="h-4 w-4" />
                                            ) : activity.event === 'deleted' ? (
                                                <XCircleIcon className="h-4 w-4" />
                                            ) : (
                                                <ClockIcon className="h-4 w-4" />
                                            )}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm text-gray-700 dark:text-neutral-300">
                                                {activity.description}
                                            </p>
                                            <p className="mt-0.5 text-xs text-gray-400">
                                                {activity.causer_name} &middot;{' '}
                                                {activity.created_at}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="py-8 text-center text-sm text-gray-400 dark:text-neutral-500">
                                {t('page.admin_dashboard.no_activity')}
                            </p>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

function StatCard({
    label,
    value,
    subtitle,
    icon: Icon,
    color,
    bg,
}: {
    label: string;
    value: string;
    subtitle: string;
    icon: React.ComponentType<{ className?: string }>;
    color: string;
    bg: string;
}) {
    return (
        <div className="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800">
            <div className="flex items-start justify-between">
                <div>
                    <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">
                        {label}
                    </p>
                    <h3 className="mt-2 truncate text-2xl font-bold text-gray-900 dark:text-neutral-100">
                        {value}
                    </h3>
                    <p className="mt-1 text-xs text-gray-400 dark:text-neutral-500">
                        {subtitle}
                    </p>
                </div>
                <span
                    className={`rounded-lg p-2 ${bg} ${color} dark:bg-opacity-20`}
                >
                    <Icon className="h-6 w-6" />
                </span>
            </div>
        </div>
    );
}

function MiniStat({
    icon: Icon,
    label,
    value,
    color,
    bg,
}: {
    icon: React.ComponentType<{ className?: string }>;
    label: string;
    value: number;
    color: string;
    bg: string;
}) {
    return (
        <div className="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-neutral-700 dark:bg-neutral-800">
            <span
                className={`rounded-lg p-2 ${bg} ${color} dark:bg-opacity-20`}
            >
                <Icon className="h-5 w-5" />
            </span>
            <div>
                <p className="text-lg font-bold text-gray-900 dark:text-neutral-100">
                    {value}
                </p>
                <p className="text-xs text-gray-500 dark:text-neutral-400">
                    {label}
                </p>
            </div>
        </div>
    );
}

function RevenueRow({
    label,
    value,
    count,
}: {
    label: string;
    value: number;
    count?: number;
}) {
    const { t } = useTranslation();

    return (
        <div className="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-neutral-700/30">
            <div>
                <span className="text-sm font-medium text-gray-700 dark:text-neutral-300">
                    {label}
                </span>
                {count !== undefined && (
                    <span className="ml-2 text-xs text-gray-400">
                        ({t('page.admin_dashboard.records_count', { count })})
                    </span>
                )}
            </div>
            <span className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                {value.toLocaleString()} FCFA
            </span>
        </div>
    );
}
