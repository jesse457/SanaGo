import { Head } from '@inertiajs/react';
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { BanknotesIcon, ArrowTrendingUpIcon, ArrowTrendingDownIcon, CurrencyDollarIcon } from '@heroicons/react/24/outline';
import { useTranslation } from '@/hooks/useTranslation';

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

export default function FinanceAnalytics({ monthlyData, summary, expenditureBreakdown, departmentRevenue, year }: Props) {
    const { t } = useTranslation();
    const maxRevenue = Math.max(...monthlyData.map(d => d.revenue), 1);
    const maxExp = Math.max(...monthlyData.map(d => d.expenditure), 1);

    return (
        <AppLayout headerTitle={t('finance.analytics.title')} headerSubtitle={t('finance.analytics.subtitle', { year })}>
            <Head title={t('finance.analytics.head_title')} />

            <div className="max-w-7xl mx-auto space-y-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                        <div className="flex items-center gap-3 mb-3">
                            <div className="h-10 w-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center"><CurrencyDollarIcon className="w-5 h-5 text-emerald-600" /></div>
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('finance.analytics.total_revenue')}</p>
                        </div>
                        <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">{summary.total_revenue.toLocaleString()} FCFA</p>
                    </div>
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                        <div className="flex items-center gap-3 mb-3">
                            <div className="h-10 w-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center"><BanknotesIcon className="w-5 h-5 text-blue-600" /></div>
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('finance.analytics.invoices')}</p>
                        </div>
                        <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">{summary.invoices.toLocaleString()} FCFA</p>
                    </div>
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                        <div className="flex items-center gap-3 mb-3">
                            <div className="h-10 w-10 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center"><ArrowTrendingUpIcon className="w-5 h-5 text-purple-600" /></div>
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('finance.analytics.surgeries')}</p>
                        </div>
                        <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">{summary.surgeries.toLocaleString()} FCFA</p>
                    </div>
                    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                        <div className="flex items-center gap-3 mb-3">
                            <div className="h-10 w-10 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center"><ArrowTrendingDownIcon className="w-5 h-5 text-red-600" /></div>
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('finance.analytics.emergencies')}</p>
                        </div>
                        <p className="text-2xl font-black text-gray-900 dark:text-neutral-100">{summary.emergencies.toLocaleString()} FCFA</p>
                    </div>
                </div>

                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                    <h3 className="text-sm font-black text-gray-900 dark:text-neutral-100 uppercase mb-6">{t('finance.analytics.monthly_revenue_vs_expenditure')}</h3>
                    <div className="space-y-3">
                        {monthlyData.map(m => (
                            <div key={m.month} className="space-y-1">
                                <div className="flex justify-between text-xs font-bold text-gray-500">
                                    <span>{m.month}</span>
                                    <span className={m.profit >= 0 ? 'text-emerald-600' : 'text-red-600'}>{m.profit >= 0 ? '+' : ''}{m.profit.toLocaleString()} FCFA</span>
                                </div>
                                <div className="flex h-3 rounded-full overflow-hidden bg-gray-100 dark:bg-neutral-700">
                                    <div style={{ width: `${(m.revenue / maxRevenue) * 100}%` }} className="bg-emerald-500 transition-all" />
                                    <div style={{ width: `${(m.expenditure / maxExp) * 100}%` }} className="bg-red-400 transition-all ml-0.5" />
                                </div>
                                <div className="flex justify-between text-[10px] text-gray-400">
                                    <span>{t('finance.analytics.rev')} {m.revenue.toLocaleString()}</span>
                                    <span>{t('finance.analytics.exp')} {m.expenditure.toLocaleString()}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {expenditureBreakdown.length > 0 && (
                        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                            <h3 className="text-sm font-black text-gray-900 dark:text-neutral-100 uppercase mb-4">{t('finance.analytics.expenditure_by_category')}</h3>
                            <div className="space-y-2">
                                {expenditureBreakdown.map(e => (
                                    <div key={e.category} className="flex justify-between items-center py-2 border-b border-gray-100 dark:border-neutral-700 last:border-0">
                                        <span className="text-sm font-bold text-gray-700 dark:text-neutral-300 capitalize">{e.category}</span>
                                        <span className="text-sm font-bold text-red-600">{Number(e.total).toLocaleString()} FCFA</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                    {departmentRevenue.length > 0 && (
                        <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                            <h3 className="text-sm font-black text-gray-900 dark:text-neutral-100 uppercase mb-4">{t('finance.analytics.revenue_by_department')}</h3>
                            <div className="space-y-2">
                                {departmentRevenue.map(d => (
                                    <div key={d.item_type} className="flex justify-between items-center py-2 border-b border-gray-100 dark:border-neutral-700 last:border-0">
                                        <span className="text-sm font-bold text-gray-700 dark:text-neutral-300 capitalize">{d.item_type}</span>
                                        <span className="text-sm font-bold text-emerald-600">{Number(d.total).toLocaleString()} FCFA</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
