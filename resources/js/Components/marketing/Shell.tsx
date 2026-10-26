import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

/**
 * Shell — the public site's single column of measure.
 *
 * One width (1240px) and two gutters (20px / 32px) for every band on every
 * page. Anything that looks full-bleed still lives inside this so the eye never
 * has to re-find the left edge.
 */
export function Shell({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'mx-auto w-full max-w-[1240px] px-5 sm:px-8',
                className,
            )}
        >
            {children}
        </div>
    );
}

/**
 * Section — vertical rhythm for a band of content.
 *
 * Rhythm is deliberately asymmetric: the top pad is tighter than the bottom, so
 * a band feels connected to the heading above it and detached from what comes
 * next.
 */
export function Section({
    children,
    className,
    id,
    tone = 'canvas',
    bordered = false,
}: {
    children: ReactNode;
    className?: string;
    id?: string;
    tone?: 'canvas' | 'muted' | 'surface';
    /** Draw the hairline above the band. */
    bordered?: boolean;
}) {
    const tones = {
        canvas: 'bg-canvas',
        muted: 'bg-surface-muted',
        surface: 'bg-surface',
    } as const;

    return (
        <section
            id={id}
            className={cn(
                'scroll-mt-24 py-16 sm:py-20 lg:py-24',
                bordered && 'border-t border-line',
                tones[tone],
                className,
            )}
        >
            <Shell>{children}</Shell>
        </section>
    );
}
