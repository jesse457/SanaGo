import {
    ArrowRightIcon,
    EnvelopeIcon,
    PhoneIcon,
} from '@heroicons/react/24/outline';
import Button from '@/Components/marketing/Button';
import { DisplayTitle } from '@/Components/marketing/SectionLedger';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

interface CtaBannerProps {
    title?: string;
    description?: string;
    eyebrow?: string;
    className?: string;
    /** Hide the secondary link, e.g. on the contact page itself. */
    secondaryLabel?: string | null;
    secondaryHref?: string;
}

/**
 * CtaBanner — the close of every page.
 *
 * One inverted panel, the support contacts the operator set in the console,
 * and exactly one primary action. The email and phone are real anchors: a
 * hospital administrator on a phone can call from here.
 */
export function CtaBanner({
    title,
    description,
    eyebrow,
    className,
    secondaryLabel,
    secondaryHref = '/pricing',
}: CtaBannerProps) {
    const { t } = useTranslation();
    const { platform } = useMarketing();

    return (
        <section className={cn('px-5 sm:px-8', className)}>
            <div className="relative mx-auto max-w-[1240px] overflow-hidden rounded-2xl bg-ink px-6 py-12 text-ink-inverse sm:px-10 sm:py-14 lg:px-14">
                <div
                    aria-hidden
                    className="pointer-events-none absolute inset-0 opacity-[0.06]"
                    style={{
                        backgroundImage:
                            "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='28'%3E%3Cpath d='M0 27.5h28M27.5 0v28' stroke='%23ffffff' stroke-width='0.5' stroke-opacity='0.6'/%3E%3C/svg%3E\")",
                    }}
                />

                <div className="relative grid items-end gap-10 lg:grid-cols-12">
                    <div className="lg:col-span-7">
                        <p className="font-mono text-[10px] tracking-[0.2em] text-ink-inverse/55 uppercase">
                            {eyebrow ?? t('mk.cta.eyebrow', 'Next step')}
                        </p>
                        <DisplayTitle
                            size="section"
                            className="mt-4 max-w-[22ch] text-ink-inverse"
                        >
                            {title ??
                                t(
                                    'mk.cta.title',
                                    'Put the whole hospital on one record.',
                                )}
                        </DisplayTitle>
                        <p className="mt-4 max-w-[52ch] text-[14.5px] leading-relaxed text-ink-inverse/70">
                            {description ??
                                t(
                                    'mk.cta.body',
                                    'Book a working session and we will show you the desks you care about — with your own units, tariffs and ward names in the room.',
                                )}
                        </p>

                        <div className="mt-8 flex flex-wrap items-center gap-3">
                            <Button
                                href="/contact"
                                variant="secondary"
                                size="lg"
                                trailingIcon={ArrowRightIcon}
                            >
                                {t('mk.cta.primary', 'Book a working session')}
                            </Button>
                            {secondaryLabel !== null && (
                                <Button
                                    href={secondaryHref}
                                    variant="ghost"
                                    size="lg"
                                    className="text-ink-inverse/80 hover:bg-ink-inverse/10 hover:text-ink-inverse"
                                >
                                    {secondaryLabel ??
                                        t('mk.cta.secondary', 'See the plans')}
                                </Button>
                            )}
                        </div>
                    </div>

                    <div className="lg:col-span-5">
                        <p className="font-mono text-[10px] tracking-[0.2em] text-ink-inverse/55 uppercase">
                            {t('mk.cta.talk', 'Talk to a human')}
                        </p>
                        <ul className="mt-4 space-y-3">
                            {platform.support_email && (
                                <li>
                                    <a
                                        href={`mailto:${platform.support_email}`}
                                        className="group flex items-center gap-3 rounded-md border border-ink-inverse/15 px-3.5 py-3 transition-colors duration-200 ease-premium hover:border-ink-inverse/30 hover:bg-ink-inverse/5"
                                    >
                                        <EnvelopeIcon className="h-4 w-4 shrink-0 text-ink-inverse/50" />
                                        <span className="min-w-0 flex-1 truncate text-[13px]">
                                            {platform.support_email}
                                        </span>
                                        <ArrowRightIcon className="h-3.5 w-3.5 shrink-0 text-ink-inverse/40 transition-transform duration-200 ease-premium group-hover:translate-x-0.5" />
                                    </a>
                                </li>
                            )}
                            {platform.support_phone && (
                                <li>
                                    <a
                                        href={`tel:${platform.support_phone.replace(/\s+/g, '')}`}
                                        className="group flex items-center gap-3 rounded-md border border-ink-inverse/15 px-3.5 py-3 transition-colors duration-200 ease-premium hover:border-ink-inverse/30 hover:bg-ink-inverse/5"
                                    >
                                        <PhoneIcon className="h-4 w-4 shrink-0 text-ink-inverse/50" />
                                        <span className="min-w-0 flex-1 truncate text-[13px]">
                                            {platform.support_phone}
                                        </span>
                                        <ArrowRightIcon className="h-3.5 w-3.5 shrink-0 text-ink-inverse/40 transition-transform duration-200 ease-premium group-hover:translate-x-0.5" />
                                    </a>
                                </li>
                            )}
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    );
}
