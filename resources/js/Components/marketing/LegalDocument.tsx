import { MicroLabel } from '@/Components/marketing/SectionLedger';
import { cn } from '@/lib/utils';

export interface LegalSection {
    id: string;
    title: string;
    paragraphs?: string[];
    bullets?: string[];
}

/**
 * LegalDocument — long-form text that stays navigable.
 *
 * A sticky contents rail, numbered headings and a measure that holds at ~68
 * characters. These pages exist because a footer that links to a 404 is worse
 * than no footer at all: the notice has to be readable on a phone.
 */
export function LegalDocument({
    sections,
    intro,
    updatedLabel,
    className,
}: {
    sections: LegalSection[];
    intro: string;
    updatedLabel: string;
    className?: string;
}) {
    return (
        <div className={cn('grid gap-12 lg:grid-cols-12 lg:gap-10', className)}>
            <nav aria-label={intro} className="lg:col-span-3">
                <div className="lg:sticky lg:top-24">
                    <MicroLabel>{intro}</MicroLabel>
                    <ol className="mt-4">
                        {sections.map((section, index) => (
                            <li key={section.id}>
                                <a
                                    href={`#${section.id}`}
                                    className="flex items-baseline gap-3 border-t border-line py-2.5 text-[12px] text-ink-muted transition-colors duration-150 hover:text-ink"
                                >
                                    <span
                                        data-numeric
                                        className="font-mono text-[9px] tracking-[0.14em] text-ink-subtle tabular-nums"
                                    >
                                        {String(index + 1).padStart(2, '0')}
                                    </span>
                                    {section.title}
                                </a>
                            </li>
                        ))}
                    </ol>
                    <p className="mt-4 font-mono text-[9px] tracking-[0.14em] text-ink-subtle uppercase">
                        {updatedLabel}
                    </p>
                </div>
            </nav>

            <article className="lg:col-span-9 lg:max-w-[68ch]">
                {sections.map((section, index) => (
                    <section
                        key={section.id}
                        id={section.id}
                        className="scroll-mt-24 border-t border-line py-8 first:border-t-0 first:pt-0"
                    >
                        <div className="flex items-baseline gap-3">
                            <span
                                data-numeric
                                className="font-mono text-[10px] tracking-[0.16em] text-accent-ink tabular-nums"
                            >
                                {String(index + 1).padStart(2, '0')}
                            </span>
                            <h2 className="text-[16px] leading-tight font-semibold tracking-[-0.01em] text-ink">
                                {section.title}
                            </h2>
                        </div>

                        {section.paragraphs?.map((paragraph) => (
                            <p
                                key={paragraph.slice(0, 40)}
                                className="mt-3 text-[13.5px] leading-[1.75] text-ink-muted"
                            >
                                {paragraph}
                            </p>
                        ))}

                        {section.bullets && (
                            <ul className="mt-4 space-y-2">
                                {section.bullets.map((bullet) => (
                                    <li
                                        key={bullet.slice(0, 40)}
                                        className="flex gap-2.5 text-[13px] leading-relaxed text-ink-muted"
                                    >
                                        <span
                                            aria-hidden
                                            className="mt-[9px] h-1 w-1 shrink-0 rounded-full bg-accent"
                                        />
                                        {bullet}
                                    </li>
                                ))}
                            </ul>
                        )}
                    </section>
                ))}
            </article>
        </div>
    );
}
