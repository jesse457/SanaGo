import { Badge, ProgressBar } from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { cn } from '@/lib/utils';
import {
    formatNumber,
    toneFromBackend,
    usagePercentage,
    usageTone,
} from '@/pages/Platform/components/format';

export interface UsageMeterProps {
    label: string;
    used: number;
    limit: number | null | undefined;
    /** Shown after the value, e.g. `GB`, `users`. */
    unit?: string;
    className?: string;
    /** Hide the "used / limit" line and only render the bar. */
    compact?: boolean;
}

/**
 * UsageMeter — one quota as a value, its limit and a meter. `null` limits mean
 * "unlimited" in the platform's plan model and must read as such, never as 0.
 */
export function UsageMeter({
    label,
    used,
    limit,
    unit,
    className,
    compact = false,
}: UsageMeterProps) {
    const percentage = usagePercentage(used, limit);
    const unlimited = limit === null || limit === undefined || limit <= 0;
    const tone = usageTone(percentage);

    return (
        <div className={cn('min-w-0', className)}>
            <div className="flex items-baseline justify-between gap-2">
                <dt className="truncate text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {label}
                </dt>
                <dd className="shrink-0 text-[12px] font-medium text-ink tabular-nums">
                    {formatNumber(used)}
                    {unit ? ` ${unit}` : ''}
                    <span className="text-ink-subtle">
                        {' / '}
                        {unlimited
                            ? '∞'
                            : `${formatNumber(limit)}${unit ? ` ${unit}` : ''}`}
                    </span>
                </dd>
            </div>
            {!compact && (
                <ProgressBar
                    className="mt-1.5"
                    size="sm"
                    value={unlimited ? 4 : used}
                    max={unlimited ? 100 : (limit ?? 100)}
                    tone={tone}
                    label={`${label}: ${formatNumber(used)} of ${unlimited ? 'unlimited' : formatNumber(limit)}`}
                />
            )}
            {compact && (
                <ProgressBar
                    className="mt-1"
                    size="sm"
                    value={unlimited ? 4 : used}
                    max={unlimited ? 100 : (limit ?? 100)}
                    tone={tone}
                    label={`${label}: ${formatNumber(used)} of ${unlimited ? 'unlimited' : formatNumber(limit)}`}
                />
            )}
        </div>
    );
}

/**
 * SubscriptionStatusBadge — renders a backend `status_label` with its
 * `status_tone`. The tone is the contract; the label is already translated by
 * the domain enum, so nothing is re-derived in JS.
 */
export function SubscriptionStatusBadge({
    label,
    tone,
    daysRemaining,
    className,
}: {
    label: string;
    tone?: string | null;
    daysRemaining?: number | null;
    className?: string;
}) {
    const resolved: BadgeTone = toneFromBackend(tone);

    return (
        <span className={cn('inline-flex items-center gap-1.5', className)}>
            <Badge tone={resolved} dot>
                {label}
            </Badge>
            {daysRemaining !== null && daysRemaining !== undefined && (
                <span
                    className={cn(
                        'text-[11.5px] font-medium tabular-nums',
                        daysRemaining < 0
                            ? 'text-critical-ink'
                            : daysRemaining <= 7
                              ? 'text-warning-ink'
                              : 'text-ink-subtle',
                    )}
                    title={`${daysRemaining} days remaining`}
                >
                    {daysRemaining < 0
                        ? `${formatNumber(Math.abs(daysRemaining))}d late`
                        : `${formatNumber(daysRemaining)}d left`}
                </span>
            )}
        </span>
    );
}
