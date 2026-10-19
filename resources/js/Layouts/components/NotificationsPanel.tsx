import {
    BeakerIcon,
    BellIcon,
    BanknotesIcon,
    ClockIcon,
    DocumentTextIcon,
    HeartIcon,
} from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { Badge, SegmentedControl } from '@/Components/ui';
import { cn } from '@/lib/utils';

export type NotificationTone = 'info' | 'success' | 'warning' | 'error';

export interface ShellNotification {
    id: number | string;
    message: string;
    time: string;
    read: boolean;
    type?: NotificationTone;
    category?: string;
    href?: string;
}

const toneStyles: Record<NotificationTone, string> = {
    info: 'bg-accent-soft text-accent-ink',
    success: 'bg-positive-soft text-positive-ink',
    warning: 'bg-warning-soft text-warning-ink',
    error: 'bg-critical-soft text-critical-ink',
};

const categoryIcons: Record<string, ReactNode> = {
    clinical: <DocumentTextIcon className="h-4 w-4" />,
    lab: <BeakerIcon className="h-4 w-4" />,
    radiology: <HeartIcon className="h-4 w-4" />,
    pharmacy: <ClockIcon className="h-4 w-4" />,
    finance: <BanknotesIcon className="h-4 w-4" />,
    general: <BellIcon className="h-4 w-4" />,
};

export interface NotificationsPanelProps {
    title: string;
    items: ShellNotification[];
    unreadCount: number;
    /** Omit to hide the all/unread filter. */
    filter?: 'all' | 'unread';
    onFilterChange?: (filter: 'all' | 'unread') => void;
    filterLabels?: { all: string; unread: string };
    loading?: boolean;
    loadingLabel?: string;
    emptyLabel: string;
    footer?: { href: string; label: string; onNavigate?: () => void };
    onSelect: (notification: ShellNotification) => void;
    className?: string;
}

/**
 * NotificationsPanel — the bell panel used by the admin and super-admin shells.
 * Hairline panel, float shadow, `Badge` for the unread count, token-toned icon
 * wells instead of palette steps.
 */
export function NotificationsPanel({
    title,
    items,
    unreadCount,
    filter,
    onFilterChange,
    filterLabels,
    loading = false,
    loadingLabel = 'Loading…',
    emptyLabel,
    footer,
    onSelect,
    className,
}: NotificationsPanelProps) {
    return (
        <div
            className={cn(
                'animate-fade-in absolute top-full right-0 mt-2 w-[22rem] max-w-[calc(100vw-2rem)] overflow-hidden rounded-lg border border-line bg-surface shadow-float',
                className,
            )}
        >
            <div className="flex items-center justify-between gap-3 border-b border-line px-3.5 py-2.5">
                <div className="flex min-w-0 items-center gap-2">
                    <p className="truncate text-[13px] font-semibold tracking-tight text-ink">
                        {title}
                    </p>
                    {unreadCount > 0 && (
                        <Badge tone="accent" variant="solid" size="sm">
                            {unreadCount}
                        </Badge>
                    )}
                </div>

                {filter && onFilterChange && filterLabels && (
                    <SegmentedControl
                        size="sm"
                        className="shrink-0"
                        value={filter}
                        onChange={(value) => onFilterChange(value)}
                        options={[
                            { value: 'all', label: filterLabels.all },
                            { value: 'unread', label: filterLabels.unread },
                        ]}
                    />
                )}
            </div>

            <div className="max-h-80 overflow-y-auto overscroll-contain">
                {loading ? (
                    <p className="px-3.5 py-8 text-center text-[12.5px] text-ink-subtle">
                        {loadingLabel}
                    </p>
                ) : items.length > 0 ? (
                    items.map((item) => {
                        const tone = item.type ?? 'info';
                        const icon =
                            categoryIcons[item.category ?? 'general'] ??
                            categoryIcons.general;

                        return (
                            <button
                                key={item.id}
                                type="button"
                                onClick={() => onSelect(item)}
                                className={cn(
                                    'flex w-full gap-2.5 border-b border-line/60 px-3.5 py-2.5 text-left transition-colors duration-150 ease-premium last:border-b-0 hover:bg-surface-hover focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none',
                                    !item.read && 'bg-accent-soft/50',
                                )}
                            >
                                <span
                                    className={cn(
                                        'flex h-8 w-8 shrink-0 items-center justify-center rounded-md',
                                        toneStyles[tone],
                                    )}
                                >
                                    {icon}
                                </span>
                                <span className="min-w-0 flex-1">
                                    <span className="flex items-start justify-between gap-2">
                                        <span
                                            className={cn(
                                                'truncate text-[12.5px]',
                                                item.read
                                                    ? 'text-ink-muted'
                                                    : 'font-semibold text-ink',
                                            )}
                                        >
                                            {item.message}
                                        </span>
                                        {!item.read && (
                                            <span className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-accent" />
                                        )}
                                    </span>
                                    <span className="mt-1 block text-[10.5px] font-medium tracking-[0.06em] text-ink-subtle tabular-nums">
                                        {item.time}
                                    </span>
                                </span>
                            </button>
                        );
                    })
                ) : (
                    <div className="px-3.5 py-10 text-center">
                        <BellIcon className="mx-auto mb-2 h-5 w-5 text-ink-subtle" />
                        <p className="text-[12.5px] text-ink-subtle">
                            {emptyLabel}
                        </p>
                    </div>
                )}
            </div>

            {footer && (
                <div className="border-t border-line bg-surface-muted/60 px-3 py-2 text-center">
                    <Link
                        href={footer.href}
                        onClick={footer.onNavigate}
                        className="text-[12.5px] font-medium text-accent-ink transition-colors duration-150 ease-premium hover:text-accent focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                    >
                        {footer.label}
                    </Link>
                </div>
            )}
        </div>
    );
}
