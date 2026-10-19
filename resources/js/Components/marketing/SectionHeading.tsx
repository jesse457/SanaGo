import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

export type PastelTint = 'blue' | 'sage' | 'clay' | 'lavender' | 'sand';

export interface SectionHeadingProps {
    title: string;
    subtitle?: string;
    /** Small uppercase label above the title. */
    eyebrow?: string;
    align?: 'center' | 'left';
    className?: string;
    /** Rendered opposite the title on wide screens (links, CTAs, counts). */
    aside?: ReactNode;
}

/**
 * SectionHeading — the editorial section opener.
 *
 * A left-aligned row carries the label, the title and an optional aside;
 * `align="center"` is kept for the short closing blocks. No decorative rule
 * under the text: type weight and the section's own hairline do that work.
 */
export default function SectionHeading({
    title,
    subtitle,
    eyebrow,
    align = 'left',
    className,
    aside,
}: SectionHeadingProps) {
    const centered = align === 'center';

    return (
        <div
            className={cn(
                'flex flex-col gap-6',
                centered
                    ? 'items-center text-center'
                    : 'sm:flex-row sm:items-end sm:justify-between',
                className,
            )}
        >
            <div
                className={cn(
                    'max-w-[46ch]',
                    centered && 'mx-auto max-w-[54ch]',
                )}
            >
                {eyebrow && (
                    <p className="mb-3 text-[10.5px] font-semibold tracking-[0.2em] text-ink-subtle uppercase">
                        {eyebrow}
                    </p>
                )}
                <h2 className="text-[26px] leading-[1.1] font-semibold tracking-[-0.025em] text-ink sm:text-[32px] lg:text-[36px]">
                    {title}
                </h2>
                {subtitle && (
                    <p className="mt-4 text-[15px] leading-relaxed text-ink-muted">
                        {subtitle}
                    </p>
                )}
            </div>

            {aside && (
                <div className="flex shrink-0 flex-wrap items-center gap-2">
                    {aside}
                </div>
            )}
        </div>
    );
}

/**
 * Muted pastel chips. They belong inside illustrative blocks and tags only —
 * never as a section or panel fill.
 */
export const PASTEL_CHIP: Record<PastelTint, string> = {
    blue: 'bg-accent-soft text-accent-ink',
    sage: 'bg-positive-soft text-positive-ink',
    clay: 'bg-warning-soft text-warning-ink',
    lavender: 'bg-surface-inset text-accent-ink',
    sand: 'bg-surface-muted text-ink-muted',
};
