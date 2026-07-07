import { Head, router } from '@inertiajs/react';
import { BellIcon, CheckCircleIcon, BeakerIcon, HeartIcon, DocumentTextIcon, BanknotesIcon, ExclamationCircleIcon, ClockIcon } from '@heroicons/react/24/outline';
import { useState } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface NotificationItem {
    id: string;
    type: string;
    message: string;
    category: string;
    link: string | null;
    read: boolean;
    created_at: string;
}

interface Props {
    notifications: { data: NotificationItem[]; links: any[]; from: number; to: number; total: number };
    unreadCount: number;
}

const CATEGORY_ICONS: Record<string, React.ComponentType<{ className?: string }>> = {
    clinical: DocumentTextIcon,
    lab: BeakerIcon,
    pharmacy: ClockIcon,
    radiology: CameraIcon,
    finance: BanknotesIcon,
    general: BellIcon,
};

const TYPE_COLORS: Record<string, string> = {
    info: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    success: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    warning: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    error: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
};

function CameraIcon(props: any) {
    return (
        <svg {...props} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
            <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0z" />
        </svg>
    );
}

export default function NotificationsIndex({ notifications, unreadCount }: Props) {
    const { t } = useTranslation();
    const [filter, setFilter] = useState<'all' | 'unread'>('all');

    const filtered = filter === 'unread'
        ? notifications.data.filter((n) => !n.read)
        : notifications.data;

    const handleMarkRead = (id: string) => {
        router.post(route('notifications.read', id), {}, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    const handleMarkAllRead = () => {
        router.post(route('notifications.mark-all-read'), {}, {
            preserveScroll: true,
        });
    };

    return (
        <AppLayout headerTitle={t('Notifications')} headerSubtitle={t('Stay updated on patient care, lab results, and system events')}>
            <Head title={t('Notifications')} />
            <PageBreadcrumb items={[{ label: t('Notifications') }]} />

            <div className="max-w-3xl mx-auto space-y-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <button
                            onClick={() => setFilter('all')}
                            className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition ${filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-600'} cursor-pointer`}
                        >
                            {t('All')} ({notifications.total})
                        </button>
                        <button
                            onClick={() => setFilter('unread')}
                            className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition ${filter === 'unread' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-600'} cursor-pointer`}
                        >
                            {t('Unread')} ({unreadCount})
                        </button>
                    </div>
                    {unreadCount > 0 && (
                        <button onClick={handleMarkAllRead} className="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium cursor-pointer">
                            {t('Mark all as read')}
                        </button>
                    )}
                </div>

                {filtered.length === 0 ? (
                    <EmptyState
                        icon={BellIcon}
                        title={filter === 'unread' ? t('No unread notifications') : t('No notifications yet')}
                        description={t('Notifications about patient care and system events will appear here.')}
                    />
                ) : (
                    <div className="space-y-2">
                        {filtered.map((notif) => {
                            const IconComp = CATEGORY_ICONS[notif.category] || BellIcon;
                            const typeColor = TYPE_COLORS[notif.type] || TYPE_COLORS.info;

                            return (
                                <div
                                    key={notif.id}
                                    className={`flex items-start gap-4 p-4 rounded-xl border transition cursor-pointer ${
                                        notif.read
                                            ? 'bg-white dark:bg-neutral-800 border-gray-100 dark:border-neutral-700'
                                            : 'bg-blue-50/50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800'
                                    } hover:shadow-sm`}
                                    onClick={() => {
                                        if (!notif.read) handleMarkRead(notif.id);
                                        if (notif.link) router.visit(notif.link);
                                    }}
                                >
                                    <span className={`shrink-0 rounded-lg p-2 ${typeColor}`}>
                                        <IconComp className="h-4 w-4" />
                                    </span>
                                    <div className="min-w-0 flex-1">
                                        <p className={`text-sm ${notif.read ? 'text-gray-600 dark:text-neutral-400' : 'text-gray-900 dark:text-white font-semibold'}`}>
                                            {notif.message}
                                        </p>
                                        <p className="mt-1 text-xs text-gray-400 dark:text-neutral-500">{notif.created_at}</p>
                                    </div>
                                    {!notif.read && (
                                        <span className="shrink-0 h-2 w-2 mt-2 rounded-full bg-blue-600 dark:bg-blue-400" />
                                    )}
                                </div>
                            );
                        })}
                    </div>
                )}

                <Pagination links={notifications.links} meta={{ from: notifications.from, to: notifications.to, total: notifications.total }} />
            </div>
        </AppLayout>
    );
}
