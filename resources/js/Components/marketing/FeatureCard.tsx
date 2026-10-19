import type { ReactNode } from 'react';
import { Card } from '@/Components/ui';
import { cn } from '@/lib/utils';

export type FeatureTint = 'blue' | 'sage' | 'clay' | 'lavender' | 'sand';

export interface FeatureCardProps {
    icon: ReactNode;
    title: string;
    description: string;
    /** Muted pastel reserved for the icon well. */
    tint?: FeatureTint;
    /** Legacy alias for `tint`; unknown values fall back to the accent tint. */
    color?: string;
    /** Index rendered in the corner — reads as a numbered index, not a badge. */
    index?: string;
    className?: string;
    /** Optional footer line (e.g. the module's role scope). */
    footer?: ReactNode;
}

const tintWell: Record<FeatureTint, string> = {
    blue: 'bg-accent-soft text-accent-ink',
    sage: 'bg-positive-soft text-positive-ink',
    clay: 'bg-warning-soft text-warning-ink',
    lavender: 'bg-surface-inset text-accent-ink',
    sand: 'bg-surface-muted text-ink-muted',
};

/**
 * FeatureCard — one module of the platform.
 *
 * Built on the kit `Card` (hairline surface, no shadow), decorated with a
 * tinted icon well and a hairline footer. Used inside the bento grid, so it
 * stretches to its cell height rather than setting its own.
 */
export default function FeatureCard({
    icon,
    title,
    description,
    tint,
    color,
    index,
    className,
    footer,
}: FeatureCardProps) {
    const resolvedTint: FeatureTint =
        tint ?? (color && color in tintWell ? (color as FeatureTint) : 'blue');

    return (
        <Card
            className={cn(
                'group flex h-full flex-col p-6 transition-[border-color,background-color] duration-300 ease-premium hover:border-line-strong sm:p-7',
                className,
            )}
        >
            <div className="flex items-start justify-between gap-4">
                <span
                    className={cn(
                        'flex h-10 w-10 shrink-0 items-center justify-center rounded-md transition-transform duration-300 ease-premium group-hover:-translate-y-0.5',
                        tintWell[resolvedTint],
                    )}
                >
                    {icon}
                </span>
                {index && (
                    <span
                        data-numeric
                        className="font-mono text-[11px] tracking-[0.1em] text-ink-subtle/70 tabular-nums"
                    >
                        {index}
                    </span>
                )}
            </div>

            <h3 className="mt-6 text-[16px] leading-tight font-semibold tracking-[-0.015em] text-ink">
                {title}
            </h3>
            <p className="mt-2.5 max-w-[42ch] text-[14px] leading-relaxed text-ink-muted">
                {description}
            </p>

            {footer && (
                <div className="mt-auto border-t border-line pt-4 text-[12px] text-ink-subtle">
                    {footer}
                </div>
            )}
        </Card>
    );
}
