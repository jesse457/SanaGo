import { Head, Link, usePage } from '@inertiajs/react';
import {
    Beaker,
    Bell,
    ChevronDown,
    ClipboardCheck,
    Clock,
    WifiOff,
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
    pendingLabRequests,
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
            case 'Urgent':
                return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
            case 'High':
                return 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400';
            default:
                return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        }
    };

    return (
        <AppLayout
            headerTitle={t('page.lab_dashboard.title')}
            headerSubtitle={t('page.lab_dashboard.subtitle')}
        >
            <Head title={t('page.lab_dashboard.title')} />

            <div className="h-full flex-1 overflow-y-auto bg-gray-50 font-sans dark:bg-neutral-900">
                {/* Network Alert */}
                {isOffline && (
                    <div className="animate-in slide-in-from-top sticky top-0 z-50 bg-rose-600 py-2 text-center text-xs font-bold text-white">
                        <WifiOff size={14} className="mr-2 inline" />
                        {t('page.lab_dashboard.offline_message')}
                    </div>
                )}

                {/* Sticky Header */}
                <header className="sticky top-0 z-20 border-b border-gray-200 bg-white px-6 py-4 dark:border-neutral-800 dark:bg-neutral-900">
                    <div className="flex items-center justify-between">
                        {/* Left: Title */}
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-3">
                                <div className="hidden rounded-lg bg-purple-50 p-2 sm:block dark:bg-purple-900/30">
                                    <Beaker className="h-5 w-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <h1 className="text-xl leading-tight font-bold text-gray-900 dark:text-neutral-100">
                                        {t('page.lab_dashboard.title')}
                                    </h1>
                                    <p className="text-xs font-medium text-gray-500 dark:text-neutral-400">
                                        <span
                                            className={
                                                isOffline
                                                    ? 'text-gray-400'
                                                    : 'text-green-600 dark:text-green-400'
                                            }
                                        >
                                            ●{' '}
                                            {isOffline
                                                ? t(
                                                      'page.lab_dashboard.offline',
                                                  )
                                                : t(
                                                      'page.lab_dashboard.online',
                                                  )}
                                        </span>{' '}
                                        &bull;{' '}
                                        {auth?.user?.name ||
                                            t('page.lab_dashboard.guest')}
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
                                    className="relative rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 focus:outline-none dark:hover:bg-neutral-800"
                                >
                                    <Bell className="h-6 w-6" />
                                    {unreadCount > 0 && (
                                        <span className="absolute top-1.5 right-1.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white dark:ring-neutral-900" />
                                    )}
                                </button>

                                {notifOpen && (
                                    <>
                                        <div
                                            className="fixed inset-0 z-40"
                                            onClick={() => setNotifOpen(false)}
                                        />
                                        <div className="absolute right-0 z-50 mt-3 w-80 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                                            <div className="flex items-center justify-between border-b border-gray-100 bg-gray-50 px-4 py-3 dark:border-neutral-700 dark:bg-neutral-800">
                                                <h3 className="text-xs font-bold text-gray-500 uppercase">
                                                    {t(
                                                        'page.lab_dashboard.notifications',
                                                    )}
                                                </h3>
                                                <button
                                                    onClick={markAllRead}
                                                    className="text-xs text-purple-600 hover:underline"
                                                >
                                                    {t(
                                                        'page.lab_dashboard.mark_read',
                                                    )}
                                                </button>
                                            </div>
                                            <div className="max-h-64 overflow-y-auto">
                                                {notifications.length === 0 ? (
                                                    <div className="p-6 text-center text-xs text-gray-400">
                                                        {t(
                                                            'page.lab_dashboard.no_notifications',
                                                        )}
                                                    </div>
                                                ) : (
                                                    notifications.map(
                                                        (note) => (
                                                            <div
                                                                key={note.id}
                                                                className="flex cursor-pointer gap-3 border-b border-gray-50 px-4 py-3 hover:bg-gray-50 dark:border-neutral-700/50 dark:hover:bg-neutral-700/30"
                                                            >
                                                                <div className="mt-1.5">
                                                                    <div
                                                                        className={`h-2 w-2 rounded-full ${note.read ? 'bg-gray-300' : 'bg-purple-500'}`}
                                                                    />
                                                                </div>
                                                                <div>
                                                                    <p className="text-sm font-medium text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            note.message
                                                                        }
                                                                    </p>
                                                                    <p className="mt-1 text-[10px] text-gray-500">
                                                                        {new Date(
                                                                            note.created_at,
                                                                        ).toLocaleTimeString()}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        ),
                                                    )
                                                )}
                                            </div>
                                        </div>
                                    </>
                                )}
                            </div>

                            <div className="mx-1 h-6 w-px bg-gray-200 dark:bg-neutral-700" />

                            {/* Profile Dropdown */}
                            <div className="relative">
                                <button
                                    onClick={() => setProfileOpen(!profileOpen)}
                                    className="group flex items-center gap-2 focus:outline-none"
                                >
                                    <img
                                        src={`https://ui-avatars.com/api/?name=${encodeURIComponent(auth?.user?.name)}&background=9333ea&color=fff`}
                                        className="h-8 w-8 rounded-full border border-gray-200 shadow-sm ring-purple-100 transition group-hover:ring-2 dark:border-neutral-700"
                                        alt="avatar"
                                    />
                                    <div className="hidden text-left md:block">
                                        <p className="text-xs font-semibold text-gray-700 dark:text-neutral-200">
                                            {auth?.user?.name}
                                        </p>
                                        <p className="text-[10px] tracking-wider text-gray-500 uppercase">
                                            {t('page.lab_dashboard.technician')}
                                        </p>
                                    </div>
                                    <ChevronDown
                                        size={14}
                                        className={`text-gray-400 transition-transform ${profileOpen ? 'rotate-180' : ''}`}
                                    />
                                </button>

                                {profileOpen && (
                                    <>
                                        <div
                                            className="fixed inset-0 z-40"
                                            onClick={() =>
                                                setProfileOpen(false)
                                            }
                                        />
                                        <div className="absolute right-0 z-50 mt-2 w-48 overflow-hidden rounded-lg border border-gray-100 bg-white py-1 shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                                            <Link
                                                href={route('lab.profile')}
                                                className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-neutral-200 dark:hover:bg-neutral-700"
                                            >
                                                {t(
                                                    'page.lab_dashboard.profile_settings',
                                                )}
                                            </Link>
                                            <Link
                                                href={route('logout')}
                                                method="post"
                                                as="button"
                                                className="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                            >
                                                {t(
                                                    'page.lab_dashboard.sign_out',
                                                )}
                                            </Link>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <div className="space-y-6 p-6">
                    {/* Stats Grid */}
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-3">
                        {/* Completed */}
                        <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <div className="flex items-start justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">
                                        {t(
                                            'page.lab_dashboard.tests_completed',
                                        )}
                                    </p>
                                    <h3 className="mt-2 text-3xl font-bold text-gray-900 dark:text-neutral-100">
                                        {completedTestsToday.length}
                                    </h3>
                                    <p className="mt-1 text-xs font-medium text-green-600 dark:text-green-400">
                                        {t('page.lab_dashboard.today')}
                                    </p>
                                </div>
                                <span className="rounded-lg bg-green-50 p-2 text-green-600 dark:bg-green-900/20 dark:text-green-400">
                                    <ClipboardCheck size={24} />
                                </span>
                            </div>
                        </div>

                        {/* In Progress */}
                        <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <div className="flex items-start justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">
                                        {t('page.lab_dashboard.processing')}
                                    </p>
                                    <h3 className="mt-2 text-3xl font-bold text-gray-900 dark:text-neutral-100">
                                        {inProgressTest.length}
                                    </h3>
                                    <p className="mt-1 text-xs font-medium text-blue-600 dark:text-blue-400">
                                        {t(
                                            'page.lab_dashboard.currently_active',
                                        )}
                                    </p>
                                </div>
                                <span className="rounded-lg bg-blue-50 p-2 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                                    <Beaker
                                        size={24}
                                        className="animate-pulse"
                                    />
                                </span>
                            </div>
                        </div>

                        {/* Pending */}
                        <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <div className="flex items-start justify-between">
                                <div>
                                    <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">
                                        {t(
                                            'page.lab_dashboard.pending_requests',
                                        )}
                                    </p>
                                    <h3 className="mt-2 text-3xl font-bold text-gray-900 dark:text-neutral-100">
                                        {pendingLabRequests.length}
                                    </h3>
                                    <p className="mt-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                                        {t(
                                            'page.lab_dashboard.needs_attention',
                                        )}
                                    </p>
                                </div>
                                <span className="rounded-lg bg-amber-50 p-2 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">
                                    <Clock size={24} />
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Pending Table */}
                    <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="flex items-center justify-between border-b border-gray-200 px-6 py-5 dark:border-neutral-700">
                            <div>
                                <h3 className="text-base font-semibold text-gray-900 dark:text-neutral-100">
                                    {t(
                                        'page.lab_dashboard.pending_lab_requests',
                                    )}
                                </h3>
                                <p className="text-sm text-gray-500 dark:text-neutral-400">
                                    {t('page.lab_dashboard.queue_management')}
                                </p>
                            </div>
                            <Link
                                href={route('lab.test-requests')}
                                className="text-sm font-medium text-purple-600 hover:text-purple-700 hover:underline"
                            >
                                {t('page.lab_dashboard.view_all')}
                            </Link>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 dark:bg-neutral-800/50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.lab_dashboard.patient')}
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.lab_dashboard.test')}
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.lab_dashboard.doctor')}
                                        </th>
                                        <th className="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                            {t('page.lab_dashboard.urgency')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                    {pendingLabRequests.length === 0 ? (
                                        <tr>
                                            <td
                                                colSpan={4}
                                                className="px-6 py-12 text-center text-sm text-gray-500 italic"
                                            >
                                                {t(
                                                    'page.lab_dashboard.no_pending_requests',
                                                )}
                                            </td>
                                        </tr>
                                    ) : (
                                        pendingLabRequests.map((request) => (
                                            <tr
                                                key={request.id}
                                                className="group cursor-pointer transition hover:bg-gray-50 dark:hover:bg-neutral-700/25"
                                            >
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="flex items-center">
                                                        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 text-xs font-bold text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                                                            {request.patient?.first_name.charAt(
                                                                0,
                                                            ) || '?'}
                                                        </div>
                                                        <div className="ml-3">
                                                            <div className="text-sm font-medium text-gray-900 dark:text-neutral-100">
                                                                {
                                                                    request
                                                                        .patient
                                                                        ?.first_name
                                                                }{' '}
                                                                {
                                                                    request
                                                                        .patient
                                                                        ?.last_name
                                                                }
                                                            </div>
                                                            <div className="text-[10px] text-gray-500">
                                                                ID:{' '}
                                                                {
                                                                    request
                                                                        .patient
                                                                        ?.patient_uid
                                                                }
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-600 dark:text-neutral-300">
                                                    {
                                                        request.test_definition
                                                            ?.test_name
                                                    }
                                                </td>
                                                <td className="px-6 py-4 text-sm whitespace-nowrap text-gray-600 dark:text-neutral-300">
                                                    Dr. {request.doctor?.name}
                                                </td>
                                                <td className="px-6 py-4 text-center whitespace-nowrap">
                                                    <span
                                                        className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold tracking-wider uppercase ${getUrgencyStyles(request.urgency_level)}`}
                                                    >
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
