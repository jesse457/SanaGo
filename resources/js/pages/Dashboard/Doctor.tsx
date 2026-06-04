import { UsersIcon, CalendarDaysIcon, BeakerIcon, ChevronRightIcon } from '@heroicons/react/24/outline';
import React from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

const Dashboard = ({ upcomingAppointments = [], incomingLabResults = [] }: any) => {
    const { t } = useTranslation();
    const kpi = [
        { label: t('page.doctor_dashboard.total_patients'), count: 42, icon: UsersIcon, color: 'blue' },
        { label: t('page.doctor_dashboard.appointments'), count: 8, icon: CalendarDaysIcon, color: 'emerald' },
        { label: t('page.doctor_dashboard.lab_results'), count: 3, icon: BeakerIcon, color: 'amber' },
    ];

    return (
        <div className="space-y-8">
            {/* KPI Cards */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                {kpi.map((item, idx) => (
                    <div key={idx} className="bg-white dark:bg-neutral-900 p-6 rounded-2xl border border-gray-200 dark:border-neutral-800 shadow-sm flex items-center justify-between group hover:border-blue-500 transition-colors">
                        <div>
                            <p className="text-xs font-bold text-gray-500 uppercase tracking-widest">{item.label}</p>
                            <h3 className="text-3xl font-black text-gray-900 dark:text-neutral-100 mt-1">{item.count}</h3>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-800 text-blue-600 dark:text-blue-400 rounded-2xl group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20 transition-colors">
                            <item.icon className="w-8 h-8" />
                        </div>
                    </div>
                ))}
            </div>

            <div className="grid grid-cols-1 xl:grid-cols-2 gap-8">
                {/* Upcoming Schedule */}
                <div className="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-2xl shadow-sm overflow-hidden">
                    <div className="px-6 py-5 border-b border-gray-100 dark:border-neutral-800 flex justify-between items-center bg-gray-50/30 dark:bg-neutral-800/30">
                        <h3 className="text-sm font-black text-gray-900 dark:text-neutral-100 uppercase tracking-tighter">{t('page.doctor_dashboard.upcoming_schedule')}</h3>
                        <button className="text-[10px] font-black text-blue-600 px-3 py-1 bg-blue-50 dark:bg-blue-900/30 rounded-full hover:bg-blue-600 hover:text-white transition">{t('page.doctor_dashboard.view_all')}</button>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left">
                            <thead>
                                <tr className="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-neutral-800">
                                    <th className="px-6 py-4">{t('page.doctor_dashboard.time')}</th>
                                    <th className="px-6 py-4">{t('page.doctor_dashboard.patient')}</th>
                                    <th className="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50 dark:divide-neutral-800">
                                {upcomingAppointments.length > 0 ? upcomingAppointments.map((appt: any, i: number) => (
                                    <tr key={i} className="hover:bg-gray-50 dark:hover:bg-neutral-800/40 transition group">
                                        <td className="px-6 py-4 font-bold text-blue-600">{appt.appointment_time}</td>
                                        <td className="px-6 py-4 font-medium dark:text-neutral-100">{appt.patient_name}</td>
                                        <td className="px-6 py-4 text-right">
                                            <button className="p-1 text-gray-400 group-hover:text-blue-600"><ChevronRightIcon className="w-5 h-5"/></button>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr><td colSpan={3} className="p-12 text-center text-gray-400 italic">{t('page.doctor_dashboard.no_appointments_today')}</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Lab Results */}
                <div className="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-2xl shadow-sm overflow-hidden">
                    <div className="px-6 py-5 border-b border-gray-100 dark:border-neutral-800 bg-gray-50/30 dark:bg-neutral-800/30">
                        <h3 className="text-sm font-black text-gray-900 dark:text-neutral-100 uppercase tracking-tighter">{t('page.doctor_dashboard.latest_lab_results')}</h3>
                    </div>
                    <div className="divide-y divide-gray-50 dark:divide-neutral-800">
                        {incomingLabResults.length > 0 ? incomingLabResults.map((result: any, i: number) => (
                            <div key={i} className="p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-neutral-800/40 transition">
                                <div className="flex gap-4 items-center">
                                    <div className="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-xl">
                                        <BeakerIcon className="w-5 h-5" />
                                    </div>
                                    <div>
                                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{result.test_name}</p>
                                        <p className="text-xs text-gray-500">{result.patient_name}</p>
                                    </div>
                                </div>
                                <button className="px-4 py-2 text-[10px] font-black bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-500/20 hover:scale-105 transition uppercase tracking-widest">{t('page.doctor_dashboard.review')}</button>
                            </div>
                        )) : (
                            <div className="p-12 text-center text-gray-400 italic">{t('page.doctor_dashboard.no_pending_results')}</div>
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

    return <AppLayout children={page} headerTitle={t('page.doctor_dashboard.title')} headerSubtitle={t('page.doctor_dashboard.subtitle')} />;
}
Dashboard.layout = DashboardLayout;

export default Dashboard;
