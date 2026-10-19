import { Link } from '@inertiajs/react';
import type { ComponentType, ReactNode, SVGProps } from 'react';
import { cn } from '@/lib/utils';

export type StatTileTone =
    | 'accent'
    | 'positive'
    | 'warning'
    | 'critical'
    | 'neutral';

export interface StatTileLinkProps {
    href: string;
    label: string;
    value: ReactNode;
    icon: ComponentType<SVGProps<SVGSVGElement>>;
    /** Supporting line under the value. */
    hint?: ReactNode;
    tone?: StatTileTone;
    /** Renders a small pulsing marker in the icon well. */
    pulse?: boolean;
    className?: string;
}

const toneClasses: Record<StatTileTone, string> = {
    accent: 'bg-accent-soft text-accent-ink',
    positive: 'bg-positive-soft text-positive-ink',
    warning: 'bg-warning-soft text-warning-ink',
    critical: 'bg-critical-soft text-critical-ink',
    neutral: 'bg-surface-inset text-ink-muted',
};

/**
 * StatTileLink — a KPI-shaped tile that navigates. Used by the dashboard quick
 * actions where the count itself is the call to action.
 */
export function StatTileLink({
    href,
    label,
    value,
    icon: Icon,
    hint,
    tone = 'accent',
    pulse = false,
    className,
}: StatTileLinkProps) {
    return (
        <Link
            href={href}
            className={cn(
                'group flex items-center gap-3 rounded-xl border border-line bg-surface p-3.5 transition-[border-color,background-color,transform] duration-200 ease-premium',
                'hover:border-line-strong hover:bg-surface-hover active:scale-[0.985]',
                'focus-visible:ring-2 focus-visible:ring-accent/45 focus-visible:outline-none',
                className,
            )}
        >
            <span
                className={cn(
                    'relative flex h-9 w-9 shrink-0 items-center justify-center rounded-lg',
                    toneClasses[tone],
                )}
            >
                {pulse && (
                    <span className="absolute -top-0.5 -right-0.5 h-2 w-2 rounded-full bg-critical" />
                )}
                <Icon className="h-[17px] w-[17px]" />
            </span>
            <span className="min-w-0">
                <span className="block text-[17px] leading-none font-semibold tracking-[-0.01em] text-ink tabular-nums">
                    {value}
                </span>
                <span className="mt-1 block truncate text-[11px] font-medium text-ink-subtle">
                    {hint ?? label}
                </span>
            </span>
        </Link>
    );
}
