import {
    XMarkIcon,
    MegaphoneIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
} from '@heroicons/react/20/solid';
import type { PageProps } from '@inertiajs/core';
import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

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

const STYLES: Record<
    string,
    {
        bg: string;
        border: string;
        icon: React.ReactNode;
        linkClass: string;
    }
> = {
    info: {
        bg: 'bg-blue-50/60 dark:bg-blue-950/20 backdrop-blur-md',
        border: 'border-b border-blue-100 dark:border-blue-900/30',
        icon: (
            <MegaphoneIcon className="h-4 w-4 shrink-0 text-blue-600 dark:text-blue-400" />
        ),
        linkClass: 'text-blue-700 dark:text-blue-400 hover:underline',
    },
    warning: {
        bg: 'bg-amber-50/60 dark:bg-amber-950/20 backdrop-blur-md',
        border: 'border-b border-amber-100 dark:border-amber-900/30',
        icon: (
            <ExclamationTriangleIcon className="h-4 w-4 shrink-0 text-amber-600 dark:text-amber-400" />
        ),
        linkClass: 'text-amber-700 dark:text-amber-400 hover:underline',
    },
    success: {
        bg: 'bg-emerald-50/60 dark:bg-emerald-950/20 backdrop-blur-md',
        border: 'border-b border-emerald-100 dark:border-emerald-900/30',
        icon: (
            <CheckCircleIcon className="h-4 w-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
        ),
        linkClass: 'text-emerald-700 dark:text-emerald-400 hover:underline',
    },
};

export default function AnnouncementBanner() {
    const { props } = usePage<CustomPageProps>();
    const announcements = props.announcements;

    const [dismissed, setDismissed] = useState<Set<number>>(new Set());
    const [dismissing, setDismissing] = useState<Set<number>>(new Set());

    useEffect(() => {
        try {
            const stored = sessionStorage.getItem('dismissed_announcements');

            if (stored) {
                setDismissed(new Set(JSON.parse(stored)));
            }
        } catch {}
    }, []);

    if (!announcements || announcements.length === 0) {
        return null;
    }

    const visible = announcements.filter((a) => !dismissed.has(a.id));

    if (visible.length === 0) {
        return null;
    }

    const handleDismiss = (id: number) => {
        setDismissing((prev) => new Set([...prev, id]));
        setTimeout(() => {
            const next = new Set([...dismissed, id]);
            setDismissed(next);
            sessionStorage.setItem(
                'dismissed_announcements',
                JSON.stringify([...next]),
            );
            setDismissing((prev) => {
                const updated = new Set(prev);
                updated.delete(id);

                return updated;
            });
        }, 200); // 200ms snappy page layout shift
    };

    return (
        <div className="flex w-full shrink-0 flex-col transition-all duration-200">
            {visible.map((a) => {
                const s = STYLES[a.type] || STYLES.info;
                const isDismissing = dismissing.has(a.id);

                return (
                    <div
                        key={a.id}
                        className={`flex w-full origin-top items-center justify-between gap-3 px-4 py-2 transition-all duration-200 ease-out lg:px-6 ${s.bg} ${s.border} ${
                            isDismissing
                                ? 'pointer-events-none max-h-0 overflow-hidden border-b-0 py-0 opacity-0'
                                : 'max-h-12 opacity-100'
                        } `}
                        style={{
                            ...(isDismissing && {
                                paddingTop: 0,
                                paddingBottom: 0,
                                height: 0,
                                borderBottomWidth: 0,
                            }),
                        }}
                    >
                        {/* Inline message alignment */}
                        <div className="flex min-w-0 flex-1 items-center gap-2.5 text-xs sm:text-sm">
                            {s.icon}
                            <div className="flex flex-col gap-x-2 gap-y-0.5 truncate sm:flex-row sm:items-center">
                                <span className="shrink-0 font-semibold text-zinc-900 dark:text-zinc-100">
                                    {a.title}
                                </span>
                                <span className="truncate text-zinc-600 dark:text-zinc-300">
                                    {a.body}
                                </span>
                            </div>
                            {a.link_url && (
                                <a
                                    href={a.link_url}
                                    className={`ml-2 shrink-0 text-xs font-semibold tracking-wider uppercase underline underline-offset-4 ${s.linkClass}`}
                                >
                                    {a.link_text || 'Learn more'}
                                </a>
                            )}
                        </div>

                        {/* Tight dismissal trigger */}
                        <button
                            onClick={() => handleDismiss(a.id)}
                            aria-label="Dismiss banner"
                            className="rounded-md p-1 text-zinc-400 transition-all duration-150 hover:bg-black/5 hover:text-zinc-600 active:scale-95 dark:text-zinc-500 dark:hover:bg-white/5 dark:hover:text-zinc-300"
                        >
                            <XMarkIcon className="h-4 w-4" />
                        </button>
                    </div>
                );
            })}
        </div>
    );
}
