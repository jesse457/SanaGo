import { BeakerIcon, ExclamationTriangleIcon, ShoppingCartIcon } from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import React from 'react';
import { route } from 'ziggy-js';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

const Dashboard = () => {
    const { t } = useTranslation();
    const stats = [
        { label: t('page.pharmacy_dashboard.drugs_in_stock'), count: 156, icon: BeakerIcon, color: 'text-emerald-600', bg: 'bg-emerald-50 dark:bg-emerald-900/20' },
        { label: t('page.pharmacy_dashboard.low_stock_alerts'), count: 3, icon: ExclamationTriangleIcon, color: 'text-rose-600', bg: 'bg-rose-50 dark:bg-rose-900/20' },
        { label: t('page.pharmacy_dashboard.pending_dispensations'), count: 7, icon: ShoppingCartIcon, color: 'text-amber-600', bg: 'bg-amber-50 dark:bg-amber-900/20' },
    ];

    const lowStockItems = [
        { name: 'Amoxicillin 500mg', stock: 12, min: 50, unit: 'tablets' },
        { name: 'Paracetamol IV', stock: 8, min: 30, unit: 'vials' },
        { name: 'Insulin Regular', stock: 3, min: 20, unit: 'vials' },
    ];

    return (
        <AppLayout headerTitle={t('page.pharmacy_dashboard.title')} headerSubtitle={t('page.pharmacy_dashboard.subtitle')}>
            <Head title={t('page.pharmacy_dashboard.title')} />

            <div className="space-y-6">
                {/* KPI Cards */}
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    {stats.map((stat, idx) => (
                        <div key={idx} className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm hover:shadow-md transition-shadow">
                            <div className="flex justify-between items-start">
                                <div>
                                    <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">{stat.label}</p>
                                    <h3 className="text-3xl font-bold text-gray-900 dark:text-neutral-100 mt-2">{stat.count}</h3>
                                </div>
                                <span className={`p-2 rounded-lg ${stat.bg} ${stat.color}`}>
                                    <stat.icon className="w-6 h-6" />
                                </span>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Quick Actions */}
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm p-6">
                    <h3 className="text-base font-bold text-gray-900 dark:text-neutral-100 mb-4">{t('page.pharmacy_dashboard.quick_actions')}</h3>
                    <div className="flex flex-wrap gap-3">
                        <Link
                            href={route('pharmacy.drugs.index')}
                            className="px-4 py-2 text-sm font-semibold bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        >
                            {t('page.pharmacy_dashboard.manage_drugs')}
                        </Link>
                        <Link
                            href={route('pharmacy.dispensation.index')}
                            className="px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition"
                        >
                            {t('page.pharmacy_dashboard.view_dispensations')}
                        </Link>
                    </div>
                </div>

                {/* Low Stock Alerts */}
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                        <h3 className="font-bold text-gray-900 dark:text-neutral-100">{t('page.pharmacy_dashboard.low_stock_alerts_title')}</h3>
                    </div>
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead className="bg-gray-50 dark:bg-neutral-800">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.pharmacy_dashboard.drug')}</th>
                                <th className="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{t('page.pharmacy_dashboard.current')}</th>
                                <th className="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{t('page.pharmacy_dashboard.min')}</th>
                                <th className="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{t('page.pharmacy_dashboard.unit')}</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {lowStockItems.map((item, idx) => (
                                <tr key={idx} className="hover:bg-gray-50 dark:hover:bg-neutral-700/30">
                                    <td className="px-6 py-3 text-sm font-medium text-gray-900 dark:text-neutral-100">{item.name}</td>
                                    <td className="px-6 py-3 text-right text-sm font-bold text-rose-600">{item.stock}</td>
                                    <td className="px-6 py-3 text-right text-sm text-gray-500">{item.min}</td>
                                    <td className="px-6 py-3 text-right text-sm text-gray-500">{item.unit}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
};

export default Dashboard;
