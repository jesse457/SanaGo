import {
    ArrowRightIcon,
    Bars2Icon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useState } from 'react';
import type { PropsWithChildren } from 'react';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { AnnouncementStrip } from '@/Components/marketing/AnnouncementStrip';
import { PublicFooter } from '@/Components/marketing/PublicFooter';
import { NotificationHandler } from '@/Components/NotificationHandler';
import { Button } from '@/Components/ui';
import { useMarketing } from '@/hooks/useMarketing';
import { TranslationProvider, useTranslation } from '@/hooks/useTranslation';
import { BrandMark } from '@/Layouts/components/BrandMark';
import { ThemeToggle } from '@/Layouts/components/ThemeToggle';
import { cn } from '@/lib/utils';

interface NavItem {
    key: string;
    href: string;
}

const NAV_ITEMS: NavItem[] = [
    { key: 'nav.home', href: '/' },
    { key: 'nav.features', href: '/features' },
    { key: 'nav.pricing', href: '/pricing' },
    { key: 'nav.about', href: '/about' },
    { key: 'nav.contact', href: '/contact' },
];

/**
 * PublicLayout — the marketing shell.
 *
 * A sticky, hairline header with an underline that marks the current page, an
 * operator-controlled announcement strip above it, and a document-like footer
 * below. Dark mode is owned here (one state, one switch, persisted in
 * localStorage) because the theme toggle lives in the header on mobile and in
 * the footer on every breakpoint.
 */
export default function PublicLayout({ children }: PropsWithChildren) {
    const { platform } = useMarketing();
    const { url } = usePage();
    const { t } = useTranslation();

    const [isDark, setIsDark] = useState<boolean>(() => {
        if (typeof document === 'undefined') {
            return false;
        }

        return document.documentElement.classList.contains('dark');
    });
    const [mobileMenuOpen, setMobileMenuOpen] = useState<boolean>(false);
    const [showScrollTop, setShowScrollTop] = useState<boolean>(false);

    useEffect(() => {
        document.documentElement.classList.toggle('dark', isDark);
    }, [isDark]);

    const toggleDarkMode = useCallback(() => {
        setIsDark((previous) => {
            const next = !previous;
            localStorage.setItem('theme', next ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', next);

            return next;
        });
    }, []);

    useEffect(() => {
        const handleScroll = () => {
            setShowScrollTop(window.scrollY > 640);
        };

        window.addEventListener('scroll', handleScroll, { passive: true });

        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    useEffect(() => {
        document.body.style.overflow = mobileMenuOpen ? 'hidden' : '';

        return () => {
            document.body.style.overflow = '';
        };
    }, [mobileMenuOpen]);

    useEffect(() => {
        if (!mobileMenuOpen) {
            return;
        }

        const handleEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') {
                setMobileMenuOpen(false);
            }
        };

        document.addEventListener('keydown', handleEscape);

        return () => document.removeEventListener('keydown', handleEscape);
    }, [mobileMenuOpen]);

    const isActive = useCallback(
        (href: string) => {
            if (href === '/') {
                return url === '/' || url === '';
            }

            return url.startsWith(href);
        },
        [url],
    );

    return (
        <TranslationProvider>
            <NotificationHandler />
            <Head>
                <title>{`${platform.name} — ${t('site.title', 'Hospital management, on one record')}`}</title>
                <meta
                    name="description"
                    content={t(
                        'site.description',
                        'SanaGo runs admissions, consultations, laboratory, radiology, pharmacy, wards and billing on one patient record — built for hospitals billing in FCFA.',
                    )}
                />
                <meta name="theme-color" content="#1c6d70" />
                <meta property="og:type" content="website" />
                <meta
                    property="og:title"
                    content={`${platform.name} — ${t('site.title', 'Hospital management, on one record')}`}
                />
                <meta
                    property="og:description"
                    content={t(
                        'site.description',
                        'SanaGo runs admissions, consultations, laboratory, radiology, pharmacy, wards and billing on one patient record — built for hospitals billing in FCFA.',
                    )}
                />
                <meta
                    property="og:image"
                    content="/images/marketing/og-image.png"
                />
                <meta property="og:image:width" content="1200" />
                <meta property="og:image:height" content="630" />
                <meta name="twitter:card" content="summary_large_image" />
            </Head>

            <a href="#content" className="sg-skip-link">
                {t('mk.nav.skip', 'Skip to content')}
            </a>

            <div className="flex min-h-screen flex-col bg-canvas text-ink">
                <AnnouncementStrip />

                <header className="chrome-blur sticky top-0 z-40 border-b border-line">
                    <div className="mx-auto flex h-16 w-full max-w-[1240px] items-center justify-between gap-6 px-5 sm:px-8">
                        <BrandMark
                            caption={t(
                                'layout.hms_platform',
                                'Hospital platform',
                            )}
                            href="/"
                            size="sm"
                        />

                        <nav
                            aria-label={t('mk.nav.primary', 'Primary')}
                            className="hidden items-center gap-7 md:flex"
                        >
                            {NAV_ITEMS.map((item) => {
                                const active = isActive(item.href);

                                return (
                                    <Link
                                        key={item.href}
                                        href={item.href}
                                        aria-current={
                                            active ? 'page' : undefined
                                        }
                                        className={cn(
                                            'relative py-1.5 text-[13px] transition-colors duration-200 ease-premium',
                                            active
                                                ? 'font-medium text-ink'
                                                : 'text-ink-muted hover:text-ink',
                                        )}
                                    >
                                        {t(item.key)}
                                        <span
                                            aria-hidden
                                            className={cn(
                                                'absolute -bottom-[21px] left-0 h-px w-full origin-left bg-accent transition-transform duration-300 ease-premium',
                                                active
                                                    ? 'scale-x-100'
                                                    : 'scale-x-0',
                                            )}
                                        />
                                    </Link>
                                );
                            })}
                        </nav>

                        <div className="hidden items-center gap-2.5 md:flex">
                            <ThemeToggle
                                isDark={isDark}
                                onToggle={toggleDarkMode}
                                label={t(
                                    'mk.nav.toggle_theme',
                                    'Toggle dark mode',
                                )}
                            />
                            <LocaleSwitcher variant="full" />
                            <span
                                aria-hidden
                                className="mx-1 h-4 w-px bg-line-strong"
                            />
                            <Link
                                href="/login"
                                className="text-[13px] font-medium text-ink-muted transition-colors duration-150 hover:text-ink"
                            >
                                {t('auth.login.submit', 'Sign in')}
                            </Link>
                            <Button
                                href="/contact"
                                size="sm"
                                trailingIcon={ArrowRightIcon}
                            >
                                {t('mk.nav.demo', 'Book a demo')}
                            </Button>
                        </div>

                        <div className="flex items-center gap-1.5 md:hidden">
                            <ThemeToggle
                                isDark={isDark}
                                onToggle={toggleDarkMode}
                                label={t(
                                    'mk.nav.toggle_theme',
                                    'Toggle dark mode',
                                )}
                            />
                            <button
                                type="button"
                                onClick={() => setMobileMenuOpen(true)}
                                aria-label={t('mk.nav.open', 'Open menu')}
                                aria-expanded={mobileMenuOpen}
                                className="cursor-pointer rounded-md border border-line bg-surface p-2 text-ink-muted transition-colors duration-150 ease-premium hover:border-line-strong hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                            >
                                <Bars2Icon className="h-[18px] w-[18px]" />
                            </button>
                        </div>
                    </div>
                </header>

                {/* Mobile drawer */}
                <div
                    className={cn(
                        'fixed inset-0 z-50 md:hidden',
                        mobileMenuOpen
                            ? 'visible'
                            : 'pointer-events-none invisible',
                    )}
                >
                    <div
                        className={cn(
                            'absolute inset-0 bg-ink/30 backdrop-blur-[2px] transition-opacity duration-300 ease-premium',
                            mobileMenuOpen ? 'opacity-100' : 'opacity-0',
                        )}
                        onClick={() => setMobileMenuOpen(false)}
                    />
                    <div
                        role="dialog"
                        aria-modal="true"
                        aria-label={t('mk.nav.primary', 'Primary')}
                        className={cn(
                            'absolute top-0 right-0 flex h-full w-[86vw] max-w-sm flex-col border-l border-line bg-surface transition-transform duration-300 ease-premium',
                            mobileMenuOpen
                                ? 'translate-x-0'
                                : 'translate-x-full',
                        )}
                    >
                        <div className="flex h-16 items-center justify-between border-b border-line px-4">
                            <BrandMark href="/" size="sm" />
                            <button
                                type="button"
                                onClick={() => setMobileMenuOpen(false)}
                                aria-label={t('mk.nav.close', 'Close menu')}
                                className="cursor-pointer rounded-md border border-line bg-surface p-2 text-ink-muted transition-colors duration-150 hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                            >
                                <XMarkIcon className="h-[18px] w-[18px]" />
                            </button>
                        </div>

                        <nav className="flex-1 overflow-y-auto p-3">
                            {NAV_ITEMS.map((item) => {
                                const active = isActive(item.href);

                                return (
                                    <Link
                                        key={item.href}
                                        href={item.href}
                                        onClick={() => setMobileMenuOpen(false)}
                                        aria-current={
                                            active ? 'page' : undefined
                                        }
                                        className={cn(
                                            'flex items-center justify-between rounded-md px-3 py-3 text-[14px] transition-colors duration-150',
                                            active
                                                ? 'bg-accent-soft font-medium text-accent-ink'
                                                : 'text-ink-muted hover:bg-surface-hover hover:text-ink',
                                        )}
                                    >
                                        {t(item.key)}
                                        <ArrowRightIcon className="h-3.5 w-3.5 opacity-40" />
                                    </Link>
                                );
                            })}

                            <div className="mt-4 border-t border-line pt-4 pl-1">
                                <LocaleSwitcher variant="full" />
                            </div>
                        </nav>

                        <div className="space-y-2 border-t border-line p-3">
                            <Button
                                href="/contact"
                                size="md"
                                fullWidth
                                trailingIcon={ArrowRightIcon}
                                onClick={() => setMobileMenuOpen(false)}
                            >
                                {t('mk.nav.demo', 'Book a demo')}
                            </Button>
                            <Button
                                href="/login"
                                variant="secondary"
                                size="md"
                                fullWidth
                            >
                                {t('auth.login.submit', 'Sign in')}
                            </Button>
                        </div>
                    </div>
                </div>

                <main id="content" className="flex-1">
                    {children}
                </main>

                <PublicFooter isDark={isDark} onToggleTheme={toggleDarkMode} />

                {showScrollTop && (
                    <button
                        type="button"
                        onClick={() =>
                            window.scrollTo({ top: 0, behavior: 'smooth' })
                        }
                        aria-label={t('mk.nav.top', 'Back to top')}
                        className="animate-fade-in fixed right-5 bottom-5 z-30 inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-md border border-line bg-surface text-ink-muted shadow-raised transition-[color,border-color,background-color,transform] duration-200 ease-premium hover:border-line-strong hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none active:scale-95"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="1.8"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            className="h-4 w-4"
                            aria-hidden
                        >
                            <path d="M12 19V5M5 12l7-7 7 7" />
                        </svg>
                    </button>
                )}
            </div>
        </TranslationProvider>
    );
}
