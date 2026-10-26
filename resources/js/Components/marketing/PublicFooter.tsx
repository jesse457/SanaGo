import { ArrowUpRightIcon } from '@heroicons/react/24/outline';
import { Link } from '@inertiajs/react';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { MicroLabel } from '@/Components/marketing/SectionLedger';
import { useMarketing } from '@/hooks/useMarketing';
import { useTranslation } from '@/hooks/useTranslation';
import { BrandMark } from '@/Layouts/components/BrandMark';
import { ThemeToggle } from '@/Layouts/components/ThemeToggle';
import { cn } from '@/lib/utils';

interface FooterLink {
    label: string;
    href: string;
}

interface PublicFooterProps {
    isDark: boolean;
    onToggleTheme: () => void;
}

/**
 * PublicFooter — the closing document of the site.
 *
 * Three regions, not a link farm: the brand and the two ways to reach a human,
 * the four routes that matter, and a ledger of facts read live from the
 * platform console. The legal links the footer is actually obligated to carry
 * sit in the last column of that ledger.
 */
export function PublicFooter({ isDark, onToggleTheme }: PublicFooterProps) {
    const { platform, stats, capabilities } = useMarketing();
    const { t } = useTranslation();
    const year = new Date().getFullYear();

    const productLinks: FooterLink[] = [
        { label: t('nav.features', 'Features'), href: '/features' },
        { label: t('nav.pricing', 'Pricing'), href: '/pricing' },
        {
            label: t('mk.footer.workflow', 'Patient journey'),
            href: '/features#workflow',
        },
        {
            label: t('mk.footer.roles', 'Role consoles'),
            href: '/features#roles',
        },
    ];

    const companyLinks: FooterLink[] = [
        { label: t('nav.about', 'About'), href: '/about' },
        { label: t('nav.contact', 'Contact'), href: '/contact' },
        { label: t('mk.footer.privacy', 'Privacy'), href: '/privacy' },
        { label: t('mk.footer.terms', 'Terms'), href: '/terms' },
    ];

    const facts: { label: string; value: string }[] = [
        {
            label: t('mk.footer.fact_hospitals', 'Hospitals live'),
            value: String(stats.hospitals),
        },
        {
            label: t('mk.footer.fact_modules', 'Gated modules'),
            value: String(stats.capabilities || capabilities.length),
        },
        {
            label: t('mk.footer.fact_currency', 'Billing currency'),
            value: platform.currency_symbol || platform.currency,
        },
        {
            label: t('mk.footer.fact_locales', 'Desk languages'),
            value: platform.supported_locales
                .map((locale) => locale.toUpperCase())
                .join(' / '),
        },
    ];

    return (
        <footer className="border-t border-line bg-surface-muted">
            <div className="mx-auto max-w-[1240px] px-5 py-14 sm:px-8 sm:py-16">
                <div className="grid gap-12 lg:grid-cols-12 lg:gap-8">
                    {/* Identity + the two ways to reach a human */}
                    <div className="lg:col-span-5">
                        <BrandMark
                            caption={t(
                                'layout.hms_platform',
                                'Hospital platform',
                            )}
                            href="/"
                            size="sm"
                        />
                        <p className="mt-5 max-w-[38ch] text-[13px] leading-relaxed text-ink-muted">
                            {platform.tagline ||
                                t(
                                    'footer.description',
                                    'A hospital management platform for admissions, diagnostics, pharmacy, billing and staff — on one record.',
                                )}
                        </p>

                        <div className="mt-7 flex flex-col gap-1.5">
                            {platform.support_email && (
                                <a
                                    href={`mailto:${platform.support_email}`}
                                    className="group inline-flex w-fit items-center gap-1.5 text-[13px] font-medium text-ink transition-colors duration-150 hover:text-accent-ink"
                                >
                                    {platform.support_email}
                                    <ArrowUpRightIcon className="h-3 w-3 text-ink-subtle transition-transform duration-200 ease-premium group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                                </a>
                            )}
                            {platform.support_phone && (
                                <a
                                    href={`tel:${platform.support_phone.replace(/\s+/g, '')}`}
                                    className="w-fit text-[13px] text-ink-muted transition-colors duration-150 hover:text-ink"
                                >
                                    <span data-numeric>
                                        {platform.support_phone}
                                    </span>
                                </a>
                            )}
                        </div>

                        <div className="mt-7 flex items-center gap-3">
                            <LocaleSwitcher variant="full" />
                            <ThemeToggle
                                isDark={isDark}
                                onToggle={onToggleTheme}
                                label={t(
                                    'mk.footer.toggle_theme',
                                    'Toggle dark mode',
                                )}
                            />
                        </div>
                    </div>

                    {/* Routes */}
                    <nav
                        aria-label={t('mk.footer.nav', 'Footer')}
                        className="grid grid-cols-2 gap-8 lg:col-span-4 lg:pl-8"
                    >
                        <div>
                            <MicroLabel>
                                {t('mk.footer.product', 'Product')}
                            </MicroLabel>
                            <ul className="mt-4 space-y-2.5">
                                {productLinks.map((link) => (
                                    <li key={link.href}>
                                        <Link
                                            href={link.href}
                                            className="text-[13px] text-ink-muted transition-colors duration-150 hover:text-ink"
                                        >
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div>
                            <MicroLabel>
                                {t('mk.footer.company', 'Company')}
                            </MicroLabel>
                            <ul className="mt-4 space-y-2.5">
                                {companyLinks.map((link) => (
                                    <li key={link.href}>
                                        <Link
                                            href={link.href}
                                            className="text-[13px] text-ink-muted transition-colors duration-150 hover:text-ink"
                                        >
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </nav>

                    {/* Live facts, read from the console */}
                    <div className="lg:col-span-3">
                        <MicroLabel>
                            {t('mk.footer.from_console', 'From the console')}
                        </MicroLabel>
                        <dl className="mt-4 grid grid-cols-2 gap-px overflow-hidden rounded-md border border-line bg-line">
                            {facts.map((fact) => (
                                <div
                                    key={fact.label}
                                    className="bg-surface px-3 py-2.5"
                                >
                                    <dt className="font-mono text-[8.5px] tracking-[0.12em] text-ink-subtle uppercase">
                                        {fact.label}
                                    </dt>
                                    <dd
                                        data-numeric
                                        className="mt-1 truncate text-[13px] font-semibold text-ink tabular-nums"
                                    >
                                        {fact.value}
                                    </dd>
                                </div>
                            ))}
                        </dl>
                        <p className="mt-3 text-[10.5px] leading-relaxed text-ink-subtle">
                            {t(
                                'mk.footer.facts_note',
                                'Plans, contacts and counts on this site are read live from the platform console.',
                            )}
                        </p>
                    </div>
                </div>

                <div className="mt-14 flex flex-col gap-3 border-t border-line pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p className="font-mono text-[10px] tracking-[0.12em] text-ink-subtle uppercase">
                        <span data-numeric>© {year}</span> {platform.name}
                        <span aria-hidden className="mx-2 opacity-40">
                            /
                        </span>
                        {t('mk.footer.rights', 'All rights reserved')}
                    </p>
                    <p
                        className={cn(
                            'font-mono text-[10px] tracking-[0.12em] text-ink-subtle uppercase',
                            'sm:text-right',
                        )}
                    >
                        {t('mk.footer.photography', 'Photography via Unsplash')}
                        <span aria-hidden className="mx-2 opacity-40">
                            /
                        </span>
                        {t('mk.footer.built', 'Built for FCFA billing')}
                    </p>
                </div>
            </div>
        </footer>
    );
}
