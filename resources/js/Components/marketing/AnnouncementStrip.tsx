import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    MegaphoneIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { useState } from 'react';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

const STORAGE_KEY = 'dismissed_platform_announcements';

const toneByType: Record<string, string> = {
    info: 'bg-accent-soft text-accent-ink border-accent/20',
    success: 'bg-positive-soft text-positive-ink border-positive/20',
    warning: 'bg-warning-soft text-warning-ink border-warning/20',
};

const iconByType = {
    info: MegaphoneIcon,
    success: CheckCircleIcon,
    warning: ExclamationTriangleIcon,
} as const;

/**
 * AnnouncementStrip — the operator's megaphone, above the header.
 *
 * Writes its dismissals to localStorage by announcement id, so a notice an
 * operator publishes in the console appears on the site without a deploy and
 * a visitor who has read it is not shown it again.
 */
export function AnnouncementStrip() {
    const { announcements } = useMarketing();
    const { t } = useTranslation();

    const [dismissed, setDismissed] = useState<number[]>(() => {
        if (typeof window === 'undefined') {
            return [];
        }

        try {
            const stored = localStorage.getItem(STORAGE_KEY);

            return stored ? (JSON.parse(stored) as number[]) : [];
        } catch {
            return [];
        }
    });

    const visible = announcements.filter(
        (announcement) => !dismissed.includes(announcement.id),
    );

    if (visible.length === 0) {
        return null;
    }

    const announcement = visible[0];
    const type = announcement.type in iconByType ? announcement.type : 'info';
    const Icon = iconByType[type as keyof typeof iconByType];

    const dismiss = () => {
        const next = [...dismissed, announcement.id];
        setDismissed(next);

        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
        } catch {
            // Private mode: dismissal simply does not persist.
        }
    };

    return (
        <div className={cn('border-b', toneByType[type] ?? toneByType.info)}>
            <div className="mx-auto flex max-w-[1240px] items-center gap-3 px-5 py-2 sm:px-8">
                <Icon className="h-3.5 w-3.5 shrink-0" strokeWidth={2} />
                <p className="min-w-0 flex-1 truncate text-[12px] leading-tight">
                    <span className="font-semibold">{announcement.title}</span>
                    <span className="mx-2 opacity-40">·</span>
                    <span className="opacity-85">{announcement.body}</span>
                </p>
                <button
                    type="button"
                    onClick={dismiss}
                    aria-label={t('mk.announcement.dismiss', 'Dismiss notice')}
                    className="shrink-0 cursor-pointer rounded-sm p-1 opacity-60 transition-opacity duration-150 hover:opacity-100 focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                >
                    <XMarkIcon className="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
    );
}
