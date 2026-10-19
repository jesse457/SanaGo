import { Badge } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { cn } from '@/lib/utils';

export interface EntityCardProps {
    /** Mono-ish identifier shown above the title (ward code, type capacity…). */
    eyebrow?: string;
    title: string;
    subtitle?: string;
    actions?: React.ReactNode;
    children?: React.ReactNode;
    className?: string;
}

/**
 * EntityCard — the shared shell for the tiles on the Utilities page (wards,
 * beds, bed types). One surface, one hairline, reveal-on-hover row actions.
 */
export function EntityCard({
    eyebrow,
    title,
    subtitle,
    actions,
    children,
    className,
}: EntityCardProps) {
    return (
        <div
            className={cn(
                'group relative rounded-xl border border-line bg-surface p-4 transition-[border-color,box-shadow] duration-300 ease-premium hover:border-line-strong hover:shadow-surface',
                className,
            )}
        >
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    {eyebrow && (
                        <Badge tone="accent" variant="soft">
                            {eyebrow}
                        </Badge>
                    )}
                    <p className="mt-2 truncate text-[14px] font-semibold tracking-tight text-ink">
                        {title}
                    </p>
                    {subtitle && (
                        <p className="mt-0.5 truncate text-[12px] text-ink-subtle">
                            {subtitle}
                        </p>
                    )}
                </div>
                {actions && (
                    <div className="flex shrink-0 items-center gap-1 opacity-0 transition-opacity duration-200 ease-premium group-hover:opacity-100 focus-within:opacity-100">
                        {actions}
                    </div>
                )}
            </div>
            {children}
        </div>
    );
}

export function entityStatusTone(status?: string | null): BadgeTone {
    if (status === 'Available') {
        return 'positive';
    }

    if (status === 'Occupied') {
        return 'critical';
    }

    if (status === 'Maintenance') {
        return 'warning';
    }

    return 'muted';
}
