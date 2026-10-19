import { cn } from '@/lib/utils';

export interface ProgressBarProps {
    value: number;
    max?: number;
    tone?: 'accent' | 'positive' | 'warning' | 'critical';
    size?: 'sm' | 'md';
    className?: string;
    label?: string;
}

const tones = {
    accent: 'bg-accent',
    positive: 'bg-positive',
    warning: 'bg-warning',
    critical: 'bg-critical',
};

/** ProgressBar — occupancy, stock levels, treatment progress. */
export function ProgressBar({
    value,
    max = 100,
    tone = 'accent',
    size = 'md',
    className,
    label,
}: ProgressBarProps) {
    const pct = Math.max(0, Math.min(100, (value / Math.max(max, 1)) * 100));

    return (
        <div
            role="progressbar"
            aria-valuenow={Math.round(pct)}
            aria-valuemin={0}
            aria-valuemax={100}
            aria-label={label}
            className={cn(
                'w-full overflow-hidden rounded-full bg-surface-inset',
                size === 'sm' ? 'h-1' : 'h-1.5',
                className,
            )}
        >
            <div
                className={cn(
                    'h-full rounded-full transition-[width] duration-500 ease-premium',
                    tones[tone],
                )}
                style={{ width: `${pct}%` }}
            />
        </div>
    );
}
