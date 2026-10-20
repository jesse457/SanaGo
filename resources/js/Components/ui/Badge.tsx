import type { HTMLAttributes, ReactNode } from 'react';
import { cn } from '@/lib/utils';

export type BadgeTone =
    | 'neutral'
    | 'accent'
    | 'positive'
    | 'warning'
    | 'critical'
    | 'muted';

export type BadgeVariant = 'soft' | 'outline' | 'solid';

/*
 * Soft tones are the default: a wash of the hue plus its high-contrast ink.
 * Every pair clears WCAG AA (4.5:1) in both themes — see app.css for the
 * measured ratios.
 */
const softTones: Record<BadgeTone, string> = {
    neutral: 'bg-surface-inset text-ink-muted',
    accent: 'bg-accent-soft text-accent-ink',
    positive: 'bg-positive-soft text-positive-ink',
    warning: 'bg-warning-soft text-warning-ink',
    critical: 'bg-critical-soft text-critical-ink',
    muted: 'bg-surface-muted text-ink-subtle',
};

const outlineTones: Record<BadgeTone, string> = {
    neutral: 'border border-line-strong text-ink-muted',
    accent: 'border border-accent/30 text-accent-ink',
    positive: 'border border-positive/30 text-positive-ink',
    warning: 'border border-warning/30 text-warning-ink',
    critical: 'border border-critical/30 text-critical-ink',
    muted: 'border border-line text-ink-subtle',
};

const solidTones: Record<BadgeTone, string> = {
    neutral: 'bg-ink text-ink-inverse',
    accent: 'bg-accent text-ink-inverse',
    positive: 'bg-positive text-ink-inverse',
    warning: 'bg-warning text-ink-inverse',
    critical: 'bg-critical text-ink-inverse',
    muted: 'bg-ink-subtle text-ink-inverse',
};

export interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
    tone?: BadgeTone;
    variant?: BadgeVariant;
    size?: 'sm' | 'md';
    dot?: boolean;
    children?: ReactNode;
}

/**
 * Small uppercase marker. One size, one tracking value, tone carries meaning —
 * never decoration.
 */
export function Badge({
    tone = 'neutral',
    variant = 'soft',
    size = 'sm',
    dot = false,
    className,
    children,
    ...props
}: BadgeProps) {
    const toneClasses =
        variant === 'solid'
            ? solidTones[tone]
            : variant === 'outline'
              ? outlineTones[tone]
              : softTones[tone];

    return (
        <span
            className={cn(
                // Badges are the only pill in the system — everything else is
                // rounded-md (controls) or rounded-lg/xl (panels).
                'inline-flex items-center gap-1.5 rounded-full font-medium whitespace-nowrap',
                size === 'sm'
                    ? 'px-2 py-0.5 text-[10px] tracking-[0.1em] uppercase'
                    : 'px-2.5 py-1 text-[11px] tracking-[0.08em] uppercase',
                toneClasses,
                className,
            )}
            {...props}
        >
            {dot && (
                <span
                    className={cn(
                        'h-1.5 w-1.5 rounded-full',
                        tone === 'positive' && 'bg-positive',
                        tone === 'warning' && 'bg-warning',
                        tone === 'critical' && 'bg-critical',
                        tone === 'accent' && 'bg-accent',
                        (tone === 'neutral' || tone === 'muted') &&
                            'bg-ink-subtle',
                    )}
                />
            )}
            {children}
        </span>
    );
}
