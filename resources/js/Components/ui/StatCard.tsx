import {
    ArrowDownRightIcon,
    ArrowUpRightIcon,
} from '@heroicons/react/24/outline';
import type { ComponentType, ReactNode, SVGProps } from 'react';
import { cn } from '@/lib/utils';

export type StatTone =
    | 'neutral'
    | 'accent'
    | 'positive'
    | 'warning'
    | 'critical';

export interface StatTrend {
    value: string | number;
    direction?: 'up' | 'down' | 'flat';
    /** Set when a rise is bad news (e.g. infection rate). */
    invert?: boolean;
    label?: string;
}

export interface StatCardProps {
    label: string;
    value: ReactNode;
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
    tone?: StatTone;
    trend?: StatTrend;
    hint?: ReactNode;
    footer?: ReactNode;
    className?: string;
}

const toneIcon: Record<StatTone, string> = {
    neutral: 'bg-surface-inset text-ink-muted',
    accent: 'bg-accent-soft text-accent-ink',
    positive: 'bg-positive-soft text-positive-ink',
    warning: 'bg-warning-soft text-warning-ink',
    critical: 'bg-critical-soft text-critical-ink',
};

/**
 * StatCard — one KPI. Value in tabular figures, label in quiet uppercase,
 * trend tinted by whether the movement is good news.
 */
export function StatCard({
    label,
    value,
    icon: Icon,
    tone = 'neutral',
    trend,
    hint,
    footer,
    className,
}: StatCardProps) {
    const trendPositive =
        trend?.direction === 'up'
            ? !trend.invert
            : trend?.direction === 'down'
              ? Boolean(trend.invert)
              : true;

    const TrendIcon =
        trend?.direction === 'down' ? ArrowDownRightIcon : ArrowUpRightIcon;

    return (
        <div
            className={cn(
                'group relative flex flex-col justify-between gap-3 rounded-xl border border-line bg-surface p-4 transition-[border-color,box-shadow] duration-300 ease-premium hover:border-line-strong',
                className,
            )}
        >
            <div className="flex items-start justify-between gap-3">
                <p className="text-[10.5px] font-semibold tracking-[0.13em] text-ink-subtle uppercase">
                    {label}
                </p>
                {Icon && (
                    <span
                        className={cn(
                            'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                            toneIcon[tone],
                        )}
                    >
                        <Icon className="h-4 w-4" />
                    </span>
                )}
            </div>

            <div className="flex items-end gap-2">
                <span
                    data-numeric
                    className="text-[26px] leading-none font-semibold tracking-[-0.02em] text-ink tabular-nums"
                >
                    {value}
                </span>
                {trend && (
                    <span
                        className={cn(
                            'mb-0.5 inline-flex items-center gap-0.5 rounded-full px-1.5 py-0.5 text-[11px] font-semibold tabular-nums',
                            trendPositive
                                ? 'bg-positive-soft text-positive-ink'
                                : 'bg-critical-soft text-critical-ink',
                        )}
                    >
                        {trend.direction !== 'flat' && (
                            <TrendIcon className="h-3 w-3" />
                        )}
                        {trend.value}
                    </span>
                )}
            </div>

            {(hint || footer) && (
                <div className="text-[11.5px] leading-snug text-ink-subtle">
                    {hint ?? footer}
                </div>
            )}
        </div>
    );
}

export function StatGrid({
    children,
    columns = 4,
    className,
}: {
    children: ReactNode;
    columns?: 2 | 3 | 4;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'grid grid-cols-1 gap-3 sm:grid-cols-2',
                columns === 3 ? 'xl:grid-cols-3' : 'xl:grid-cols-4',
                className,
            )}
        >
            {children}
        </div>
    );
}
