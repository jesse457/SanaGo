import { UserGroupIcon, CalendarDaysIcon, BuildingOfficeIcon, PlusCircleIcon } from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import React from 'react';
import { route } from 'ziggy-js';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

const Dashboard = () => {
    const { t } = useTranslation();
    const stats = [
        { label: t('page.reception_dashboard.patients_today'), count: 24, icon: UserGroupIcon, color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-900/20' },
        { label: t('page.reception_dashboard.appointments'), count: 18, icon: CalendarDaysIcon, color: 'text-emerald-600', bg: 'bg-emerald-50 dark:bg-emerald-900/20' },
        { label: t('page.reception_dashboard.admissions_active'), count: 42, icon: BuildingOfficeIcon, color: 'text-amber-600', bg: 'bg-amber-50 dark:bg-amber-900/20' },
    ];

    const quickActions = [
        { label: t('page.reception_dashboard.register_patient'), href: route('patients.create'), icon: PlusCircleIcon },
        { label: t('page.reception_dashboard.book_appointment'), href: route('appointments.book'), icon: CalendarDaysIcon },
        { label: t('page.reception_dashboard.patient_checkin'), href: route('patients.checkin'), icon: UserGroupIcon },
    ];

    return (
        <AppLayout headerTitle={t('page.reception_dashboard.title')} headerSubtitle={t('page.reception_dashboard.subtitle')}>
            <Head title={t('page.reception_dashboard.title')} />

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
                    <h3 className="text-base font-bold text-gray-900 dark:text-neutral-100 mb-4">{t('page.reception_dashboard.quick_actions')}</h3>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {quickActions.map((action, idx) => (
                            <Link
                                key={idx}
                                href={action.href}
                                className="flex items-center gap-3 p-4 bg-gray-50 dark:bg-neutral-700/30 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors group"
                            >
                                <action.icon className="w-6 h-6 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform" />
                                <span className="text-sm font-semibold text-gray-700 dark:text-neutral-200">{action.label}</span>
                            </Link>
                        ))}
                    </div>
                </div>

                {/* Waiting Queue */}
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                        <h3 className="font-bold text-gray-900 dark:text-neutral-100">{t('page.reception_dashboard.waiting_queue')}</h3>
                    </div>
                    <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead className="bg-gray-50 dark:bg-neutral-800">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.reception_dashboard.patient')}</th>
                                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.reception_dashboard.doctor')}</th>
                                <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.reception_dashboard.time')}</th>
                                <th className="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{t('page.reception_dashboard.position')}</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            <tr>
                                <td colSpan={4} className="px-6 py-12 text-center text-sm text-gray-500 italic">
                                    {t('page.reception_dashboard.waiting_queue_empty')}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
};

export default Dashboard;
