import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    MegaphoneIcon,
    XMarkIcon,
} from '@heroicons/react/20/solid';
import type { PageProps } from '@inertiajs/core';
import { usePage } from '@inertiajs/react';
import { useState } from 'react';

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

/*
 * Solid tones rather than the soft ones: an announcement is a full-bleed strip
 * across the top of the shell, so it needs to read as a band, not a card. Each
 * tone pairs with `text-ink-inverse`, which flips with the theme and keeps
 * contrast on both the dark and the light ends of the palette.
 */
const TONE_BY_TYPE = {
    info: 'bg-accent',
    warning: 'bg-warning',
    success: 'bg-positive',
} as const;

const ICON_BY_TYPE = {
    info: MegaphoneIcon,
    warning: ExclamationTriangleIcon,
    success: CheckCircleIcon,
} as const;

/**
 * AnnouncementBanner — system-wide notices as a single-line strip above the
 * shell. One bar per announcement, dismissible and persisted locally.
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
            className="relative z-40 flex w-full shrink-0 flex-col"
        >
            {visible.map((announcement) => {
                const type = announcement.type ?? 'info';
                const isDismissing = dismissing.has(announcement.id);
                const Icon = ICON_BY_TYPE[type] ?? MegaphoneIcon;

                return (
                    <div
                        key={announcement.id}
                        aria-live="polite"
                        className={`origin-top overflow-hidden transition-[opacity,transform] duration-300 ease-premium ${
                            isDismissing
                                ? 'pointer-events-none max-h-0 scale-y-95 opacity-0'
                                : 'max-h-24 scale-y-100 opacity-100'
                        }`}
                    >
                        <div
                            className={`flex items-center gap-2.5 px-4 py-2 text-ink-inverse lg:px-6 ${
                                TONE_BY_TYPE[type] ?? TONE_BY_TYPE.info
                            }`}
                        >
                            <Icon
                                className="h-4 w-4 shrink-0"
                                aria-hidden="true"
                            />
                            <p className="min-w-0 flex-1 truncate text-[12.5px] leading-tight">
                                <span className="font-semibold">
                                    {announcement.title}
                                </span>
                                {announcement.body && (
                                    <>
                                        <span className="mx-2 opacity-40">
                                            ·
                                        </span>
                                        <span className="opacity-85">
                                            {announcement.body}
                                        </span>
                                    </>
                                )}
                                {announcement.link_url && (
                                    <a
                                        href={announcement.link_url}
                                        className="ml-2 rounded-sm font-semibold underline decoration-ink-inverse/50 underline-offset-2 transition-opacity duration-150 hover:opacity-80 focus-visible:ring-2 focus-visible:ring-ink-inverse/60 focus-visible:outline-none"
                                    >
                                        {announcement.link_text ?? 'Learn more'}
                                    </a>
                                )}
                            </p>
                            <button
                                type="button"
                                onClick={() => handleDismiss(announcement.id)}
                                aria-label={`Dismiss announcement: ${announcement.title}`}
                                className="shrink-0 cursor-pointer rounded-md p-1 text-ink-inverse/70 transition-[color,background-color] duration-150 ease-premium hover:bg-ink-inverse/15 hover:text-ink-inverse focus-visible:ring-2 focus-visible:ring-ink-inverse/60 focus-visible:outline-none active:scale-90"
                            >
                                <XMarkIcon
                                    className="h-4 w-4"
                                    aria-hidden="true"
                                />
                            </button>
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
