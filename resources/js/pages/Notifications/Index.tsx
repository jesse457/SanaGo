import {
    BanknotesIcon,
    BeakerIcon,
    BellIcon,
    ClockIcon,
    DocumentTextIcon,
    PhotoIcon,
} from '@heroicons/react/24/outline';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import type { ComponentType, SVGProps } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    PageHeader,
    Tabs,
    Toolbar,
    ToolbarSpacer,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import ContextLayout from '@/Layouts/ContextLayout';

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
    notifications: {
        data: NotificationItem[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number;
        to: number;
        total: number;
    };
    unreadCount: number;
}

const CATEGORY_ICONS: Record<string, ComponentType<SVGProps<SVGSVGElement>>> = {
    clinical: DocumentTextIcon,
    lab: BeakerIcon,
    pharmacy: ClockIcon,
    radiology: PhotoIcon,
    finance: BanknotesIcon,
    general: BellIcon,
};

const TYPE_TONES: Record<string, BadgeTone> = {
    info: 'accent',
    success: 'positive',
    warning: 'warning',
    error: 'critical',
};

const TYPE_LABELS: Record<string, string> = {
    info: 'Info',
    success: 'Success',
    warning: 'Warning',
    error: 'Error',
};

export default function NotificationsIndex({
    notifications,
    unreadCount,
}: Props) {
    const { t } = useTranslation();
    const { props } = usePage();
    const isLandlord = Boolean((props as { isLandlord?: boolean }).isLandlord);
    const [filter, setFilter] = useState<'all' | 'unread'>('all');

    const markReadRoute = (id: string) =>
        isLandlord
            ? route('landlord.notifications.read', id)
            : route('notifications.read', id);

    const markAllReadRoute = () =>
        isLandlord
            ? route('landlord.notifications.mark-all-read')
            : route('notifications.mark-all-read');

    const filtered =
        filter === 'unread'
            ? notifications.data.filter((notification) => !notification.read)
            : notifications.data;

    const handleMarkRead = (id: string) => {
        router.post(
            markReadRoute(id),
            {},
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    };

    const handleMarkAllRead = () => {
        router.post(
            markAllReadRoute(),
            {},
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <ContextLayout
            headerTitle={t('Notifications')}
            headerSubtitle={t(
                'Stay updated on patient care, lab results, and system events',
            )}
        >
            <Head title={t('Notifications')} />

            <div className="mx-auto max-w-3xl space-y-5 pb-12">
                <PageHeader
                    title={t('Notifications')}
                    description={t(
                        'Stay updated on patient care, lab results, and system events',
                    )}
                    breadcrumbs={[{ label: t('Notifications') }]}
                />

                <Toolbar>
                    <Tabs
                        variant="pill"
                        value={filter}
                        onChange={(value) =>
                            setFilter(value as 'all' | 'unread')
                        }
                        items={[
                            {
                                value: 'all',
                                label: t('All'),
                                count: notifications.total,
                            },
                            {
                                value: 'unread',
                                label: t('Unread'),
                                count: unreadCount,
                            },
                        ]}
                    />
                    <ToolbarSpacer />
                    {unreadCount > 0 && (
                        <Button
                            size="sm"
                            variant="ghost"
                            onClick={handleMarkAllRead}
                        >
                            {t('Mark all as read')}
                        </Button>
                    )}
                </Toolbar>

                {filtered.length === 0 ? (
                    <EmptyState
                        icon={BellIcon}
                        title={
                            filter === 'unread'
                                ? t('No unread notifications')
                                : t('No notifications yet')
                        }
                        description={t(
                            'Notifications about patient care and system events will appear here.',
                        )}
                    />
                ) : (
                    <div className="flex flex-col gap-2">
                        {filtered.map((notification) => {
                            const Icon =
                                CATEGORY_ICONS[notification.category] ??
                                BellIcon;

                            return (
                                <Card
                                    key={notification.id}
                                    role="button"
                                    tabIndex={0}
                                    onClick={() => {
                                        if (!notification.read) {
                                            handleMarkRead(notification.id);
                                        }

                                        if (notification.link) {
                                            router.visit(notification.link);
                                        }
                                    }}
                                    onKeyDown={(event) => {
                                        if (event.key === 'Enter') {
                                            if (!notification.read) {
                                                handleMarkRead(notification.id);
                                            }

                                            if (notification.link) {
                                                router.visit(notification.link);
                                            }
                                        }
                                    }}
                                    className={`cursor-pointer p-4 transition-colors duration-200 ease-premium hover:border-line-strong ${
                                        notification.read
                                            ? 'bg-surface'
                                            : 'bg-accent-soft/60'
                                    }`}
                                >
                                    <div className="flex items-start gap-3">
                                        <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-line bg-surface text-ink-muted">
                                            <Icon className="h-4 w-4" />
                                        </span>

                                        <div className="min-w-0 flex-1">
                                            <p
                                                className={`text-[13px] leading-relaxed ${
                                                    notification.read
                                                        ? 'text-ink-muted'
                                                        : 'font-medium text-ink'
                                                }`}
                                            >
                                                {notification.message}
                                            </p>
                                            <div className="mt-1.5 flex flex-wrap items-center gap-2">
                                                <Badge
                                                    tone={
                                                        TYPE_TONES[
                                                            notification.type
                                                        ] ?? 'neutral'
                                                    }
                                                    variant="soft"
                                                >
                                                    {TYPE_LABELS[
                                                        notification.type
                                                    ] ?? notification.type}
                                                </Badge>
                                                <span className="text-[11.5px] text-ink-subtle tabular-nums">
                                                    {notification.created_at}
                                                </span>
                                            </div>
                                        </div>

                                        {!notification.read && (
                                            <span
                                                aria-hidden="true"
                                                className="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent"
                                            />
                                        )}
                                    </div>
                                </Card>
                            );
                        })}
                    </div>
                )}

                <Pagination
                    links={notifications.links}
                    meta={{
                        from: notifications.from,
                        to: notifications.to,
                        total: notifications.total,
                    }}
                />
            </div>
        </ContextLayout>
    );
}
