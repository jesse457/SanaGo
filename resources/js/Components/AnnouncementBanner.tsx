import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    MegaphoneIcon,
} from '@heroicons/react/20/solid';
import type { PageProps } from '@inertiajs/core';
import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import { Alert } from '@/Components/ui';

interface Announcement {
    id: number;
    title: string;
    body: string;
    type: 'info' | 'warning' | 'success';
    published_at: string;
    expires_at: string | null;
    link_url?: string;
    link_text?: string;
}

interface CustomPageProps extends PageProps {
    announcements?: Announcement[];
}

const TONE_BY_TYPE = {
    info: 'info',
    warning: 'warning',
    success: 'positive',
} as const;

const ICON_BY_TYPE = {
    info: MegaphoneIcon,
    warning: ExclamationTriangleIcon,
    success: CheckCircleIcon,
} as const;

/**
 * AnnouncementBanner — system-wide notices above the shell. Uses the shared
 * Alert tones, one row per announcement, dismissible and persisted locally.
 */
export default function AnnouncementBanner() {
    const { props } = usePage<CustomPageProps>();
    const announcements = props.announcements;

    const [dismissed, setDismissed] = useState<Set<number>>(() => {
        if (typeof window === 'undefined') {
            return new Set();
        }

        try {
            const stored = localStorage.getItem('dismissed_announcements');

            return stored ? new Set<number>(JSON.parse(stored)) : new Set();
        } catch {
            return new Set();
        }
    });
    const [dismissing, setDismissing] = useState<Set<number>>(new Set());

    if (!announcements || announcements.length === 0) {
        return null;
    }

    const visible = announcements.filter((item) => !dismissed.has(item.id));

    if (visible.length === 0) {
        return null;
    }

    const handleDismiss = (id: number) => {
        setDismissing((previous) => new Set([...previous, id]));

        setTimeout(() => {
            const next = new Set([...dismissed, id]);
            setDismissed(next);
            localStorage.setItem(
                'dismissed_announcements',
                JSON.stringify([...next]),
            );
            setDismissing((previous) => {
                const updated = new Set(previous);
                updated.delete(id);

                return updated;
            });
        }, 300);
    };

    return (
        <div
            role="region"
            aria-label="System announcements"
            className="relative z-40 flex w-full shrink-0 flex-col gap-2 border-b border-line bg-canvas px-4 py-2.5 lg:px-6"
        >
            {visible.map((announcement) => {
                const type = announcement.type ?? 'info';
                const isDismissing = dismissing.has(announcement.id);
                const Icon = ICON_BY_TYPE[type] ?? MegaphoneIcon;

                return (
                    <div
                        key={announcement.id}
                        aria-live="polite"
                        className={`origin-top transition-[opacity,transform] duration-300 ease-premium ${
                            isDismissing
                                ? 'pointer-events-none scale-y-95 opacity-0'
                                : 'scale-y-100 opacity-100'
                        }`}
                    >
                        <Alert
                            tone={TONE_BY_TYPE[type] ?? 'info'}
                            title={announcement.title}
                            action={
                                <div className="flex items-center gap-2">
                                    {announcement.link_url && (
                                        <a
                                            href={announcement.link_url}
                                            className="inline-flex items-center rounded-md border border-line-strong bg-surface px-2 py-1 text-[11.5px] font-semibold text-ink transition-colors duration-150 hover:bg-surface-hover focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                        >
                                            {announcement.link_text ??
                                                'Learn more'}
                                        </a>
                                    )}
                                    <button
                                        onClick={() =>
                                            handleDismiss(announcement.id)
                                        }
                                        aria-label={`Dismiss announcement: ${announcement.title}`}
                                        className="rounded-md p-1 text-ink-subtle transition-colors duration-150 hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none active:scale-90"
                                    >
                                        <Icon className="h-4 w-4" />
                                    </button>
                                </div>
                            }
                        >
                            {announcement.body}
                        </Alert>
                    </div>
                );
            })}
        </div>
    );
}
