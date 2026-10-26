import type { ReactNode } from 'react';
import { DisplayTitle, Lede } from '@/Components/marketing/SectionLedger';
import { Shell } from '@/Components/marketing/Shell';
import { cn } from '@/lib/utils';

interface PageHeroProps {
    /** Two-digit index, in the same ledger language as the sections. */
    index: string;
    eyebrow: string;
    title: ReactNode;
    lede?: string;
    actions?: ReactNode;
    /** Right-hand column: a photograph, a product shot, or a fact ledger. */
    aside?: ReactNode;
    /** Short run of facts printed under the whole hero. */
    footnotes?: { label: string; value: string }[];
    className?: string;
}

/**
 * PageHero — the opener shared by Features, Pricing, About, Contact and the
 * legal pages.
 *
 * The text column is wider than the aside on purpose: the headline is the
 * argument, the aside is evidence. It sits below a fixed strip of footnotes so
 * an inner page answers "what is this and what is it for" before the fold.
 */
export function PageHero({
    index,
    eyebrow,
    title,
    lede,
    actions,
    aside,
    footnotes,
    className,
}: PageHeroProps) {
    return (
        <div
            className={cn(
                'relative overflow-hidden pt-12 pb-14 sm:pt-16',
                className,
            )}
        >
            <Shell>
                <div className="flex items-center gap-3 font-mono text-[10.5px] tracking-[0.2em] text-ink-subtle uppercase">
                    <span data-numeric className="text-accent-ink">
                        {index}
                    </span>
                    <span aria-hidden className="h-3 w-px bg-line-strong" />
                    <span>{eyebrow}</span>
                </div>

                <div className="mt-8 grid items-start gap-10 lg:grid-cols-12 lg:gap-12">
                    <div
                        className={cn(
                            aside ? 'lg:col-span-7' : 'lg:col-span-9',
                        )}
                    >
                        <DisplayTitle
                            as="h1"
                            size="hero"
                            className="max-w-[24ch]"
                        >
                            {title}
                        </DisplayTitle>
                        {lede && <Lede className="mt-6">{lede}</Lede>}
                        {actions && (
                            <div className="mt-8 flex flex-wrap items-center gap-3">
                                {actions}
                            </div>
                        )}
                    </div>

                    {aside && (
                        <div className="lg:col-span-5 lg:pt-2">{aside}</div>
                    )}
                </div>

                {footnotes && footnotes.length > 0 && (
                    <dl className="mt-14 grid grid-cols-2 gap-px overflow-hidden rounded-lg border border-line bg-line sm:grid-cols-4">
                        {footnotes.map((fact) => (
                            <div
                                key={fact.label}
                                className="bg-surface px-4 py-4"
                            >
                                <dt className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                                    {fact.label}
                                </dt>
                                <dd
                                    data-numeric
                                    className="mt-1.5 text-[15px] font-semibold tracking-[-0.01em] text-ink tabular-nums"
                                >
                                    {fact.value}
                                </dd>
                            </div>
                        ))}
                    </dl>
                )}
            </Shell>
        </div>
    );
}
