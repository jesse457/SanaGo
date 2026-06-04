import { Head, Link, usePage } from '@inertiajs/react';
import {
    Beaker,
    Bell,
    ChevronDown,
    ClipboardCheck,
    Clock,
    WifiOff
} from 'lucide-react';
import React, { useState, useEffect } from 'react';
import { route } from 'ziggy-js';
import { useNotifications } from '@/hooks/useNotifications';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { PageProps, DashboardProps } from '@/types';

export default function Dashboard({
    completedTestsToday,
    inProgressTest,
    pendingLabRequests
}: DashboardProps) {
    const { t } = useTranslation();
    const { auth } = usePage<PageProps>().props;
    const { notifications, unreadCount, markAllRead } = useNotifications();

    // UI States
    const [notifOpen, setNotifOpen] = useState(false);
    const [profileOpen, setProfileOpen] = useState(false);
    const [isOffline, setIsOffline] = useState(!navigator.onLine);

    // Online/Offline Listener
    useEffect(() => {
        const handleStatus = () => setIsOffline(!navigator.onLine);
        window.addEventListener('online', handleStatus);
        window.addEventListener('offline', handleStatus);

        return () => {
            window.removeEventListener('online', handleStatus);
            window.removeEventListener('offline', handleStatus);
        };
    }, []);

    // Helper for Urgency Badge Colors
    const getUrgencyStyles = (level: string) => {
        switch (level) {
            case 'Urgent': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
            case 'High': return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';
            default: return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        }
    };

    return (
        <AppLayout headerTitle={t('page.lab_dashboard.title')} headerSubtitle={t('page.lab_dashboard.subtitle')}>
            <Head title={t('page.lab_dashboard.title')} />

            <div className="flex-1 bg-gray-50 h-full overflow-y-auto dark:bg-neutral-900 font-sans">

                {/* Network Alert */}
                {isOffline && (
                    <div className="bg-rose-600 text-white text-xs font-bold text-center py-2 sticky top-0 z-50 animate-in slide-in-from-top">
                        <WifiOff size={14} className="inline mr-2" />
                        {t('page.lab_dashboard.offline_message')}
                    </div>
                )}

                {/* Sticky Header */}
                <header className="sticky top-0 z-20 bg-white dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-800 px-6 py-4">
                    <div className="flex items-center justify-between">

                        {/* Left: Title */}
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg hidden sm:block">
                                    <Beaker className="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <h1 className="text-xl font-bold text-gray-900 dark:text-neutral-100 leading-tight">
                                        {t('page.lab_dashboard.title')}
                                    </h1>
                                    <p className="text-xs text-gray-500 dark:text-neutral-400 font-medium">
                                        <span className={isOffline ? 'text-gray-400' : 'text-green-600 dark:text-green-400'}>
                                            ● {isOffline ? t('page.lab_dashboard.offline') : t('page.lab_dashboard.online')}
                                        </span> &bull; {auth?.user?.name || t('page.lab_dashboard.guest')}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Right: Actions */}
                        <div className="flex items-center gap-4">

                            {/* Notification Dropdown */}
                            <div className="relative">
                                <button
                                    onClick={() => setNotifOpen(!notifOpen)}
                                    className="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-neutral-800 rounded-lg transition focus:outline-none"
                                >
                                    <Bell className="w-6 h-6" />
                                    {unreadCount > 0 && (
                                        <span className="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white dark:ring-neutral-900" />
                                    )}
                                </button>

                                {notifOpen && (
                                    <>
                                        <div className="fixed inset-0 z-40" onClick={() => setNotifOpen(false)} />
                                        <div className="absolute right-0 mt-3 w-80 bg-white dark:bg-neutral-800 rounded-xl shadow-lg border border-gray-100 dark:border-neutral-700 z-50 overflow-hidden">
                                            <div className="px-4 py-3 border-b border-gray-100 dark:border-neutral-700 flex justify-between items-center bg-gray-50 dark:bg-neutral-800">
                                                <h3 className="text-xs font-bold text-gray-500 uppercase">{t('page.lab_dashboard.notifications')}</h3>
                                                <button onClick={markAllRead} className="text-xs text-purple-600 hover:underline">{t('page.lab_dashboard.mark_read')}</button>
                                            </div>
                                            <div className="max-h-64 overflow-y-auto">
                                                {notifications.length === 0 ? (
                                                    <div className="p-6 text-center text-gray-400 text-xs">{t('page.lab_dashboard.no_notifications')}</div>
                                                ) : (
                                                    notifications.map(note => (
                                                        <div key={note.id} className="px-4 py-3 border-b border-gray-50 dark:border-neutral-700/50 hover:bg-gray-50 dark:hover:bg-neutral-700/30 flex gap-3 cursor-pointer">
                                                            <div className="mt-1.5">
                                                                <div className={`w-2 h-2 rounded-full ${note.read ? 'bg-gray-300' : 'bg-purple-500'}`} />
                                                            </div>
                                                            <div>
                                                                <p className="text-sm font-medium text-gray-900 dark:text-neutral-100">{note.message}</p>
                                                                <p className="text-[10px] text-gray-500 mt-1">{new Date(note.created_at).toLocaleTimeString()}</p>
                                                            </div>
                                                        </div>
                                                    ))
                                                )}
                                            </div>
                                        </div>
                                    </>
                                )}
                            </div>

                            <div className="h-6 w-px bg-gray-200 dark:bg-neutral-700 mx-1" />

                            {/* Profile Dropdown */}
                            <div className="relative">
                                <button
                                    onClick={() => setProfileOpen(!profileOpen)}
                                    className="flex items-center gap-2 group focus:outline-none"
                                >
                                    <img
                                        src={`https://ui-avatars.com/api/?name=${encodeURIComponent(auth?.user?.name)}&background=9333ea&color=fff`}
                                        className="w-8 h-8 rounded-full border border-gray-200 dark:border-neutral-700 shadow-sm group-hover:ring-2 ring-purple-100 transition"
                                        alt="avatar"
                                    />
                                    <div className="hidden md:block text-left">
                                        <p className="text-xs font-semibold text-gray-700 dark:text-neutral-200">{auth?.user?.name}</p>
                                        <p className="text-[10px] text-gray-500 uppercase tracking-wider">{t('page.lab_dashboard.technician')}</p>
                                    </div>
                                    <ChevronDown size={14} className={`text-gray-400 transition-transform ${profileOpen ? 'rotate-180' : ''}`} />
                                </button>

                                {profileOpen && (
                                    <>
                                        <div className="fixed inset-0 z-40" onClick={() => setProfileOpen(false)} />
                                        <div className="absolute right-0 mt-2 w-48 bg-white dark:bg-neutral-800 rounded-lg shadow-lg border border-gray-100 dark:border-neutral-700 z-50 py-1 overflow-hidden">
                                            <Link href={route('lab.profile')} className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-neutral-200 dark:hover:bg-neutral-700">{t('page.lab_dashboard.profile_settings')}</Link>
                                            <Link href={route('logout')} method="post" as="button" className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">{t('page.lab_dashboard.sign_out')}</Link>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <div className="p-6 space-y-6">

                    {/* Stats Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {/* Completed */}
                        <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm">
                            <div className="flex justify-between items-start">
                                <div>
                                    <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">{t('page.lab_dashboard.tests_completed')}</p>
                                    <h3 className="text-3xl font-bold text-gray-900 dark:text-neutral-100 mt-2">{completedTestsToday.length}</h3>
                                    <p className="text-xs text-green-600 dark:text-green-400 mt-1 font-medium">{t('page.lab_dashboard.today')}</p>
                                </div>
                                <span className="p-2 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 rounded-lg">
                                    <ClipboardCheck size={24} />
                                </span>
                            </div>
                        </div>

                        {/* In Progress */}
                        <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm">
                            <div className="flex justify-between items-start">
                                <div>
                                    <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">{t('page.lab_dashboard.processing')}</p>
                                    <h3 className="text-3xl font-bold text-gray-900 dark:text-neutral-100 mt-2">{inProgressTest.length}</h3>
                                    <p className="text-xs text-blue-600 dark:text-blue-400 mt-1 font-medium">{t('page.lab_dashboard.currently_active')}</p>
                                </div>
                                <span className="p-2 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg">
                                    <Beaker size={24} className="animate-pulse" />
                                </span>
                            </div>
                        </div>

                        {/* Pending */}
                        <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm">
                            <div className="flex justify-between items-start">
                                <div>
                                    <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">{t('page.lab_dashboard.pending_requests')}</p>
                                    <h3 className="text-3xl font-bold text-gray-900 dark:text-neutral-100 mt-2">{pendingLabRequests.length}</h3>
                                    <p className="text-xs text-amber-600 dark:text-amber-400 mt-1 font-medium">{t('page.lab_dashboard.needs_attention')}</p>
                                </div>
                                <span className="p-2 bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 rounded-lg">
                                    <Clock size={24} />
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Pending Table */}
                    <div className="bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden">
                        <div className="px-6 py-5 border-b border-gray-200 dark:border-neutral-700 flex justify-between items-center">
                            <div>
                                <h3 className="text-base font-semibold text-gray-900 dark:text-neutral-100">{t('page.lab_dashboard.pending_lab_requests')}</h3>
                                <p className="text-sm text-gray-500 dark:text-neutral-400">{t('page.lab_dashboard.queue_management')}</p>
                            </div>
                            <Link href={route('lab.test-requests')} className="text-sm font-medium text-purple-600 hover:text-purple-700 hover:underline">
                                {t('page.lab_dashboard.view_all')}
                            </Link>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 dark:bg-neutral-800/50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.lab_dashboard.patient')}</th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.lab_dashboard.test')}</th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{t('page.lab_dashboard.doctor')}</th>
                                        <th className="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{t('page.lab_dashboard.urgency')}</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                    {pendingLabRequests.length === 0 ? (
                                        <tr>
                                            <td colSpan={4} className="px-6 py-12 text-center text-sm text-gray-500 italic">{t('page.lab_dashboard.no_pending_requests')}</td>
                                        </tr>
                                    ) : (
                                        pendingLabRequests.map((request) => (
                                            <tr key={request.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700/25 transition group cursor-pointer">
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <div className="h-8 w-8 rounded-full bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-xs font-bold text-purple-700 dark:text-purple-300">
                                                            {request.patient?.first_name.charAt(0) || '?'}
                                                        </div>
                                                        <div className="ml-3">
                                                            <div className="text-sm font-medium text-gray-900 dark:text-neutral-100">
                                                                {request.patient?.first_name} {request.patient?.last_name}
                                                            </div>
                                                            <div className="text-[10px] text-gray-500">ID: {request.patient?.patient_uid}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-neutral-300">
                                                    {request.test_definition?.test_name}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-neutral-300">
                                                    Dr. {request.doctor?.name}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-center">
                                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${getUrgencyStyles(request.urgency_level)}`}>
                                                        {request.urgency_level}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
