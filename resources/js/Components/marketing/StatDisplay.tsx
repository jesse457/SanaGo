import type { ComponentType, ReactNode, SVGProps } from 'react';
import type { PastelTint } from '@/Components/marketing/SectionHeading';
import { cn } from '@/lib/utils';

export interface StatDisplayProps {
    value: string;
    label: string;
    /** Only the leading stat of a row carries the accent. */
    feature?: boolean;
    className?: string;
}

/**
 * StatDisplay — a figure from the product fact sheet, set typographically.
 *
 * Value in tabular figures with a hairline above it, label in quiet uppercase.
 * No card, no color block: it should read like a caption in a printed report.
 */
export default function StatDisplay({
    value,
    label,
    feature = false,
    className,
}: StatDisplayProps) {
    return (
        <div className={cn('border-t border-line pt-4', className)}>
            <p
                data-numeric
                className={cn(
                    'text-[30px] leading-none font-semibold tracking-[-0.03em] tabular-nums sm:text-[34px]',
                    feature ? 'text-accent' : 'text-ink',
                )}
            >
                {value}
            </p>
            <p className="mt-2.5 text-[10.5px] leading-snug font-semibold tracking-[0.14em] text-ink-subtle uppercase">
                {label}
            </p>
        </div>
    );
}

export interface TintedStat extends StatDisplayProps {
    icon?: ComponentType<SVGProps<SVGSVGElement>>;
}

/**
 * StatRow — the stat strip used inside tinted panels. Values share the
 * condensed display scale, dividers are hairlines, the tint stays in the panel.
 */
export function StatRow({
    items,
    className,
}: {
    items: TintedStat[];
    className?: string;
}) {
    return (
        <div
            className={cn(
                'grid grid-cols-2 gap-px overflow-hidden rounded-xl border border-line bg-line sm:grid-cols-4',
                className,
            )}
        >
            {items.map((item) => (
                <div key={item.label} className="bg-surface px-4 py-5">
                    {item.icon && (
                        <item.icon className="mb-3 h-4 w-4 text-accent" />
                    )}
                    <p
                        data-numeric
                        className="text-[24px] leading-none font-semibold tracking-[-0.025em] text-ink tabular-nums"
                    >
                        {item.value}
                    </p>
                    <p className="mt-2 text-[10.5px] font-semibold tracking-[0.13em] text-ink-subtle uppercase">
                        {item.label}
                    </p>
                </div>
            ))}
        </div>
    );
}

/** Re-exported for pages that tint a block per module. */
export type StatTint = PastelTint;

export type StatIcon = ReactNode;
