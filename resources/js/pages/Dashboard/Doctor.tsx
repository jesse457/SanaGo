import {
    UsersIcon,
    CalendarDaysIcon,
    BeakerIcon,
    ChevronRightIcon,
} from '@heroicons/react/24/outline';
import React from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

const Dashboard = ({
    upcomingAppointments = [],
    incomingLabResults = [],
}: any) => {
    const { t } = useTranslation();
    const kpi = [
        {
            label: t('page.doctor_dashboard.total_patients'),
            count: 42,
            icon: UsersIcon,
            color: 'blue',
        },
        {
            label: t('page.doctor_dashboard.appointments'),
            count: 8,
            icon: CalendarDaysIcon,
            color: 'emerald',
        },
        {
            label: t('page.doctor_dashboard.lab_results'),
            count: 3,
            icon: BeakerIcon,
            color: 'amber',
        },
    ];

    return (
        <div className="space-y-8">
            {/* KPI Cards */}
            <div className="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-3">
                {kpi.map((item, idx) => (
                    <div
                        key={idx}
                        className="group flex items-center justify-between rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-colors hover:border-blue-500 dark:border-neutral-800 dark:bg-neutral-900"
                    >
                        <div>
                            <p className="text-xs font-bold tracking-widest text-gray-500 uppercase">
                                {item.label}
                            </p>
                            <h3 className="mt-1 text-3xl font-black text-gray-900 dark:text-neutral-100">
                                {item.count}
                            </h3>
                        </div>
                        <div className="rounded-2xl bg-gray-50 p-4 text-blue-600 transition-colors group-hover:bg-blue-50 dark:bg-neutral-800 dark:text-blue-400 dark:group-hover:bg-blue-900/20">
                            <item.icon className="h-8 w-8" />
                        </div>
                    </div>
                ))}
            </div>

            <div className="grid grid-cols-1 gap-8 xl:grid-cols-2">
                {/* Upcoming Schedule */}
                <div className="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div className="flex items-center justify-between border-b border-gray-100 bg-gray-50/30 px-6 py-5 dark:border-neutral-800 dark:bg-neutral-800/30">
                        <h3 className="text-sm font-black tracking-tighter text-gray-900 uppercase dark:text-neutral-100">
                            {t('page.doctor_dashboard.upcoming_schedule')}
                        </h3>
                        <button className="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-black text-blue-600 transition hover:bg-blue-600 hover:text-white dark:bg-blue-900/30">
                            {t('page.doctor_dashboard.view_all')}
                        </button>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="border-b border-gray-100 text-[10px] font-black tracking-widest text-gray-400 uppercase dark:border-neutral-800">
                                    <th className="px-6 py-4">
                                        {t('page.doctor_dashboard.time')}
                                    </th>
                                    <th className="px-6 py-4">
                                        {t('page.doctor_dashboard.patient')}
                                    </th>
                                    <th className="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50 dark:divide-neutral-800">
                                {upcomingAppointments.length > 0 ? (
                                    upcomingAppointments.map(
                                        (appt: any, i: number) => (
                                            <tr
                                                key={i}
                                                className="group transition hover:bg-gray-50 dark:hover:bg-neutral-800/40"
                                            >
                                                <td className="px-6 py-4 font-bold text-blue-600">
                                                    {appt.appointment_time}
                                                </td>
                                                <td className="px-6 py-4 font-medium dark:text-neutral-100">
                                                    {appt.patient_name}
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    <button className="p-1 text-gray-400 group-hover:text-blue-600">
                                                        <ChevronRightIcon className="h-5 w-5" />
                                                    </button>
                                                </td>
                                            </tr>
                                        ),
                                    )
                                ) : (
                                    <tr>
                                        <td
                                            colSpan={3}
                                            className="p-12 text-center text-gray-400 italic"
                                        >
                                            {t(
                                                'page.doctor_dashboard.no_appointments_today',
                                            )}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Lab Results */}
                <div className="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <div className="border-b border-gray-100 bg-gray-50/30 px-6 py-5 dark:border-neutral-800 dark:bg-neutral-800/30">
                        <h3 className="text-sm font-black tracking-tighter text-gray-900 uppercase dark:text-neutral-100">
                            {t('page.doctor_dashboard.latest_lab_results')}
                        </h3>
                    </div>
                    <div className="divide-y divide-gray-50 dark:divide-neutral-800">
                        {incomingLabResults.length > 0 ? (
                            incomingLabResults.map((result: any, i: number) => (
                                <div
                                    key={i}
                                    className="flex items-center justify-between p-6 transition hover:bg-gray-50 dark:hover:bg-neutral-800/40"
                                >
                                    <div className="flex items-center gap-4">
                                        <div className="rounded-xl bg-amber-50 p-3 text-amber-600 dark:bg-amber-900/20">
                                            <BeakerIcon className="h-5 w-5" />
                                        </div>
                                        <div>
                                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                {result.test_name}
                                            </p>
                                            <p className="text-xs text-gray-500">
                                                {result.patient_name}
                                            </p>
                                        </div>
                                    </div>
                                    <button className="rounded-xl bg-blue-600 px-4 py-2 text-[10px] font-black tracking-widest text-white uppercase shadow-lg shadow-blue-500/20 transition hover:scale-105">
                                        {t('page.doctor_dashboard.review')}
                                    </button>
                                </div>
                            ))
                        ) : (
                            <div className="p-12 text-center text-gray-400 italic">
                                {t('page.doctor_dashboard.no_pending_results')}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

/**
 * THIS IS WHAT MAKES IT STATIC
 * By defining the layout as a property on the component,
 * Inertia will not re-mount the layout when navigating.
 */
function DashboardLayout(page: React.ReactNode) {
    const { t } = useTranslation();

    return (
        <AppLayout
            children={page}
            headerTitle={t('page.doctor_dashboard.title')}
            headerSubtitle={t('page.doctor_dashboard.subtitle')}
        />
    );
}
Dashboard.layout = DashboardLayout;

export default Dashboard;
