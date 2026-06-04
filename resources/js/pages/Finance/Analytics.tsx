import {
    BanknotesIcon,
    ArrowTrendingUpIcon,
    ArrowTrendingDownIcon,
    CurrencyDollarIcon,
    ChartBarIcon,
    DocumentTextIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import React from 'react';
import PageSection from '@/Components/PageSection';
import StatCard from '@/Components/StatCard';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface MonthlyData {
    month: string;
    revenue: number;
    expenditure: number;
    profit: number;
}

interface Summary {
    total_revenue: number;
    invoices: number;
    surgeries: number;
    emergencies: number;
}

interface Props {
    monthlyData: MonthlyData[];
    summary: Summary;
    expenditureBreakdown: { category: string; total: number }[];
    departmentRevenue: { item_type: string; total: number }[];
    year: number;
}

export default function FinanceAnalytics({
    monthlyData,
    summary,
    expenditureBreakdown,
    departmentRevenue,
    year,
}: Props) {
    const { t } = useTranslation();
    const maxRevenue = Math.max(...monthlyData.map((d) => d.revenue), 1);
    const maxExp = Math.max(...monthlyData.map((d) => d.expenditure), 1);

    return (
        <AppLayout
            headerTitle={t('finance.analytics.title')}
            headerSubtitle={t('finance.analytics.subtitle', { year })}
        >
            <Head title={t('finance.analytics.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        icon={CurrencyDollarIcon}
                        label={t('finance.analytics.total_revenue')}
                        value={`${summary.total_revenue.toLocaleString()} FCFA`}
                        color="emerald"
                    />
                    <StatCard
                        icon={BanknotesIcon}
                        label={t('finance.analytics.invoices')}
                        value={`${summary.invoices.toLocaleString()} FCFA`}
                        color="blue"
                    />
                    <StatCard
                        icon={ArrowTrendingUpIcon}
                        label={t('finance.analytics.surgeries')}
                        value={`${summary.surgeries.toLocaleString()} FCFA`}
                        color="purple"
                    />
                    <StatCard
                        icon={ArrowTrendingDownIcon}
                        label={t('finance.analytics.emergencies')}
                        value={`${summary.emergencies.toLocaleString()} FCFA`}
                        color="red"
                    />
                </div>

                <PageSection
                    icon={ChartBarIcon}
                    title={t(
                        'finance.analytics.monthly_revenue_vs_expenditure',
                    )}
                >
                    <div className="space-y-3 p-6">
                        {monthlyData.map((m) => (
                            <div key={m.month} className="space-y-1">
                                <div className="flex justify-between text-xs font-bold text-gray-500 dark:text-neutral-400">
                                    <span>{m.month}</span>
                                    <span
                                        className={
                                            m.profit >= 0
                                                ? 'text-emerald-600'
                                                : 'text-red-600'
                                        }
                                    >
                                        {m.profit >= 0 ? '+' : ''}
                                        {m.profit.toLocaleString()} FCFA
                                    </span>
                                </div>
                                <div className="flex h-3 overflow-hidden rounded-full bg-gray-100 dark:bg-neutral-700">
                                    <div
                                        style={{
                                            width: `${(m.revenue / maxRevenue) * 100}%`,
                                        }}
                                        className="bg-emerald-500 transition-all"
                                    />
                                    <div
                                        style={{
                                            width: `${(m.expenditure / maxExp) * 100}%`,
                                        }}
                                        className="ml-0.5 bg-red-400 transition-all"
                                    />
                                </div>
                                <div className="flex justify-between text-[10px] text-gray-400 dark:text-neutral-500">
                                    <span>
                                        {t('finance.analytics.rev')}{' '}
                                        {m.revenue.toLocaleString()}
                                    </span>
                                    <span>
                                        {t('finance.analytics.exp')}{' '}
                                        {m.expenditure.toLocaleString()}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                </PageSection>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {expenditureBreakdown.length > 0 && (
                        <PageSection
                            icon={DocumentTextIcon}
                            title={t(
                                'finance.analytics.expenditure_by_category',
                            )}
                        >
                            <div className="space-y-2 p-6">
                                {expenditureBreakdown.map((e) => (
                                    <div
                                        key={e.category}
                                        className="flex items-center justify-between border-b border-gray-100 py-2 last:border-0 dark:border-neutral-700"
                                    >
                                        <span className="text-sm font-bold text-gray-700 capitalize dark:text-neutral-300">
                                            {e.category}
                                        </span>
                                        <span className="text-sm font-bold text-red-600">
                                            {Number(e.total).toLocaleString()}{' '}
                                            FCFA
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </PageSection>
                    )}
                    {departmentRevenue.length > 0 && (
                        <PageSection
                            icon={BanknotesIcon}
                            title={t('finance.analytics.revenue_by_department')}
                        >
                            <div className="space-y-2 p-6">
                                {departmentRevenue.map((d) => (
                                    <div
                                        key={d.item_type}
                                        className="flex items-center justify-between border-b border-gray-100 py-2 last:border-0 dark:border-neutral-700"
                                    >
                                        <span className="text-sm font-bold text-gray-700 capitalize dark:text-neutral-300">
                                            {d.item_type}
                                        </span>
                                        <span className="text-sm font-bold text-emerald-600">
                                            {Number(d.total).toLocaleString()}{' '}
                                            FCFA
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </PageSection>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
