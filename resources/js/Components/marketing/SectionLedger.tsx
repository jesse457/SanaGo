import type { ReactNode } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

/**
 * SectionLedger — the public site's recurring section opener.
 *
 * A hairline rule, then the section's index number in the accent, then the
 * label, then an optional note set flush right. It reads like the header of a
 * page in a bound clinical file, and it is what makes the marketing pages feel
 * like one document instead of a stack of blocks.
 */
export interface SectionLedgerProps {
    /** Two-digit section number: `01`, `02`… */
    index: string;
    label: string;
    /** Optional right-hand caption (a count, a source, a date). */
    note?: string;
    className?: string;
}

export function SectionLedger({
    index,
    label,
    note,
    className,
}: SectionLedgerProps) {
    return (
        <div className={cn('border-t border-line pt-4', className)}>
            <div className="flex flex-wrap items-baseline justify-between gap-x-8 gap-y-2">
                <p className="flex items-center gap-3 font-mono text-[10.5px] font-medium tracking-[0.2em] text-ink-subtle uppercase">
                    <span data-numeric className="text-accent-ink">
                        {index}
                    </span>
                    <span aria-hidden className="h-3 w-px bg-line-strong" />
                    <span>{label}</span>
                </p>
                {note && (
                    <p className="max-w-[46ch] text-[11.5px] leading-relaxed text-ink-subtle">
                        {note}
                    </p>
                )}
            </div>
        </div>
    );
}

/**
 * DisplayTitle — the marketing display face.
 *
 * Instrument Serif carries only these lines; everything functional stays in
 * Inter. `italic` marks the turn of the sentence, the way a pull quote would.
 */
export interface DisplayTitleProps {
    children: ReactNode;
    as?: 'h1' | 'h2' | 'h3';
    size?: 'hero' | 'section' | 'panel';
    className?: string;
}

const titleSizes = {
    hero: 'text-[clamp(2.6rem,6.4vw,4.9rem)] leading-[0.94] tracking-[-0.02em]',
    section:
        'text-[clamp(1.95rem,3.4vw,2.9rem)] leading-[1.02] tracking-[-0.015em]',
    panel: 'text-[clamp(1.5rem,2.2vw,1.9rem)] leading-[1.1]',
} as const;

export function DisplayTitle({
    children,
    as: Tag = 'h2',
    size = 'section',
    className,
}: DisplayTitleProps) {
    return (
        <Tag
            className={cn(
                'font-editorial font-normal text-ink',
                titleSizes[size],
                className,
            )}
        >
            {children}
        </Tag>
    );
}

/**
 * Lede — the paragraph that follows a display title. One measure, generous
 * leading, never wider than ~58 characters.
 */
export function Lede({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <p
            className={cn(
                'max-w-[58ch] text-[15.5px] leading-[1.7] text-ink-muted',
                className,
            )}
        >
            {children}
        </p>
    );
}

/**
 * MicroLabel — small caps for field labels, table headers and photo captions.
 */
export function MicroLabel({
    children,
    className,
}: {
    children: ReactNode;
    className?: string;
}) {
    return (
        <span
            className={cn(
                'font-mono text-[10px] font-medium tracking-[0.18em] text-ink-subtle uppercase',
                className,
            )}
        >
            {children}
        </span>
    );
}

/**
 * MetaRule — a one-line, hairline-separated run of facts (photography credit,
 * data provenance, plan source). Used at the close of a section.
 */
export function MetaRule({
    items,
    className,
}: {
    items: string[];
    className?: string;
}) {
    const { t } = useTranslation();

    return (
        <p
            className={cn(
                'flex flex-wrap items-center gap-x-3 gap-y-1 font-mono text-[10px] tracking-[0.14em] text-ink-subtle uppercase',
                className,
            )}
        >
            {items.map((item, i) => (
                <span key={item} className="flex items-center gap-3">
                    {i > 0 && (
                        <span
                            aria-hidden
                            className="h-2.5 w-px bg-line-strong"
                        />
                    )}
                    {item}
                </span>
            ))}
            <span className="sr-only">{t('mk.common.meta', 'metadata')}</span>
        </p>
    );
}
