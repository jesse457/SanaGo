import {
    ArrowUpIcon,
    ArrowRightIcon,
    Bars3Icon,
    HeartIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useState } from 'react';
import type { PropsWithChildren } from 'react';
import { AppToaster } from '@/Components/AppToaster';
import { FlashHandler } from '@/Components/FlashHandler';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { Button } from '@/Components/ui';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';
import { BrandMark } from '@/Layouts/components/BrandMark';
import { DrawerHeader } from '@/Layouts/components/DrawerHeader';
import { NavLink } from '@/Layouts/components/NavItem';
import { SectionLabel } from '@/Layouts/components/ShellPrimitives';
import { IconThemeToggle } from '@/Layouts/components/ThemeToggle';
import { cn } from '@/lib/utils';

interface NavItem {
    name: string;
    href: string;
}

const NAV_ITEMS: NavItem[] = [
    { name: 'nav.home', href: '/' },
    { name: 'nav.features', href: '/features' },
    { name: 'nav.pricing', href: '/pricing' },
    { name: 'nav.about', href: '/about' },
    { name: 'nav.contact', href: '/contact' },
];

const FOOTER_MODULES = [
    'footer.modules.patientManagement',
    'footer.modules.appointmentsScheduling',
    'footer.modules.labDiagnostics',
    'footer.modules.pharmacyInventory',
    'footer.modules.billingRevenue',
    'footer.modules.infrastructureWards',
];

const FOOTER_PLATFORM = [
    'footer.platform.rbac',
    'footer.platform.multiTenant',
    'footer.platform.auditLogging',
    'footer.platform.subscriptionManagement',
    'footer.platform.darkMode',
];

export default function PublicLayout({ children }: PropsWithChildren) {
    const [isDark, setIsDark] = useState<boolean>(false);
    const [mobileMenuOpen, setMobileMenuOpen] = useState<boolean>(false);
    const [showScrollTop, setShowScrollTop] = useState<boolean>(false);
    const { url } = usePage();
    const { t } = useTranslation();

    // Dark mode is resolved once during the initial render: the inline script in
    // the Blade shell has already applied the class, so no effect is needed.
    useEffect(() => {
        document.documentElement.classList.toggle('dark', isDark);
    }, [isDark]);

    const toggleDarkMode = () => {
        const newDark = !isDark;
        setIsDark(newDark);
        document.documentElement.classList.toggle('dark', newDark);
        localStorage.setItem('theme', newDark ? 'dark' : 'light');
    };

    // Show scroll to top button
    useEffect(() => {
        const handleScroll = () => {
            setShowScrollTop(window.scrollY > 500);
        };
        window.addEventListener('scroll', handleScroll, { passive: true });

        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Close mobile menu on resize if open
    useEffect(() => {
        const handleResize = () => {
            if (window.innerWidth >= 768) {
                setMobileMenuOpen(false);
            }
        };
        window.addEventListener('resize', handleResize);

        return () => window.removeEventListener('resize', handleResize);
    }, []);

    // Prevent body scroll when mobile menu is open
    useEffect(() => {
        document.body.style.overflow = mobileMenuOpen ? 'hidden' : 'unset';

        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [mobileMenuOpen]);

    const isActive = useCallback(
        (href: string) => {
            if (href === '/') {
                return url === '/';
            }

            return url.startsWith(href);
        },
        [url],
    );

    return (
        <TranslationProvider>
            <FlashHandler />
            <AppToaster />
            <>
                <Head>
                    <title>{`SanaGo — ${t('site.title')}`}</title>
                    <meta name="description" content={t('site.description')} />
                    <meta
                        name="viewport"
                        content="width=device-width, initial-scale=1"
                    />
                </Head>

                <div className="min-h-screen overflow-x-hidden bg-canvas font-sans text-ink">
                    {/* Navigation */}
                    <header className="chrome-blur fixed top-0 z-30 w-full border-b border-line">
                        <nav className="mx-auto flex h-16 max-w-6xl items-center justify-between gap-6 px-5 sm:px-8">
                            <BrandMark
                                icon={HeartIcon}
                                caption={t('layout.hms_platform')}
                                href="/"
                                size="sm"
                            />

                            {/* Desktop Navigation */}
                            <div className="hidden items-center gap-1 md:flex">
                                {NAV_ITEMS.map((item) => (
                                    <NavLink
                                        key={item.name}
                                        href={item.href}
                                        isActive={isActive(item.href)}
                                        variant="inline"
                                        linkComponent={(linkProps) => (
                                            <Link {...linkProps}>
                                                {linkProps.children}
                                            </Link>
                                        )}
                                    >
                                        {() => (
                                            <span className="text-[12.5px] font-medium">
                                                {t(item.name)}
                                            </span>
                                        )}
                                    </NavLink>
                                ))}
                            </div>

                            <div className="hidden items-center gap-2 md:flex">
                                <IconThemeToggle
                                    isDark={isDark}
                                    onToggle={toggleDarkMode}
                                />
                                <LocaleSwitcher variant="full" />
                                <Button
                                    href="/login"
                                    size="sm"
                                    className="ml-1"
                                >
                                    {t('auth.login.submit')}
                                </Button>
                            </div>

                            {/* Mobile Menu Button */}
                            <div className="flex items-center gap-2 md:hidden">
                                <IconThemeToggle
                                    isDark={isDark}
                                    onToggle={toggleDarkMode}
                                />
                                <button
                                    type="button"
                                    onClick={() => setMobileMenuOpen(true)}
                                    aria-label="Open menu"
                                    aria-expanded={mobileMenuOpen}
                                    className="cursor-pointer rounded-md border border-line bg-surface p-1.5 text-ink-muted transition-colors duration-150 ease-premium hover:border-line-strong hover:bg-surface-hover hover:text-ink"
                                >
                                    <Bars3Icon className="h-[18px] w-[18px]" />
                                </button>
                            </div>
                        </nav>
                    </header>

                    {/* Mobile Sidebar */}
                    <div
                        className={cn(
                            'fixed inset-0 z-50 md:hidden',
                            mobileMenuOpen ? 'visible' : 'invisible',
                        )}
                    >
                        <div
                            className={cn(
                                'absolute inset-0 bg-ink/25 backdrop-blur-[2px] transition-opacity duration-300 ease-premium',
                                mobileMenuOpen ? 'opacity-100' : 'opacity-0',
                            )}
                            onClick={() => setMobileMenuOpen(false)}
                        />
                        <div
                            className={cn(
                                'absolute top-0 right-0 flex h-full w-80 max-w-[85vw] flex-col border-l border-line bg-surface transition-transform duration-300 ease-premium',
                                mobileMenuOpen
                                    ? 'translate-x-0'
                                    : 'translate-x-full',
                            )}
                        >
                            <DrawerHeader
                                onClose={() => setMobileMenuOpen(false)}
                                closeLabel="Close menu"
                            >
                                <BrandMark
                                    icon={HeartIcon}
                                    href="/"
                                    size="sm"
                                />
                            </DrawerHeader>

                            <div className="flex flex-1 flex-col gap-0.5 overflow-y-auto p-2.5">
                                {NAV_ITEMS.map((item) => (
                                    <NavLink
                                        key={item.name}
                                        href={item.href}
                                        isActive={isActive(item.href)}
                                        variant="inline"
                                        onNavigate={() =>
                                            setMobileMenuOpen(false)
                                        }
                                        linkComponent={(linkProps) => (
                                            <Link {...linkProps}>
                                                {linkProps.children}
                                            </Link>
                                        )}
                                    >
                                        {() => (
                                            <span className="text-[13px] font-medium">
                                                {t(item.name)}
                                            </span>
                                        )}
                                    </NavLink>
                                ))}
                            </div>

                            <div className="border-t border-line p-3">
                                <Button
                                    href="/login"
                                    size="md"
                                    fullWidth
                                    trailingIcon={ArrowRightIcon}
                                    onClick={() => setMobileMenuOpen(false)}
                                >
                                    {t('auth.login.submit')}
                                </Button>
                            </div>
                        </div>
                    </div>

                    {/* Main Content */}
                    <main className="pt-16">{children}</main>

                    {/* Footer */}
                    <footer className="border-t border-line bg-surface-muted">
                        <div className="mx-auto max-w-6xl px-5 py-16 sm:px-8">
                            <div className="grid gap-12 md:grid-cols-3">
                                {/* Branding */}
                                <div>
                                    <BrandMark
                                        icon={HeartIcon}
                                        caption={t('layout.hms_platform')}
                                        href="/"
                                        size="sm"
                                    />
                                    <p className="mt-5 max-w-xs text-[13px] leading-relaxed text-ink-muted">
                                        {t('footer.description')}
                                    </p>
                                </div>

                                {/* Modules */}
                                <div>
                                    <SectionLabel className="mb-4">
                                        {t('footer.modules_heading')}
                                    </SectionLabel>
                                    <ul className="space-y-2.5">
                                        {FOOTER_MODULES.map((item) => (
                                            <li
                                                key={item}
                                                className="cursor-pointer text-[13px] text-ink-muted transition-colors duration-150 ease-premium hover:text-ink"
                                            >
                                                {t(item)}
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                {/* Platform */}
                                <div>
                                    <SectionLabel className="mb-4">
                                        {t('footer.platform_heading')}
                                    </SectionLabel>
                                    <ul className="space-y-2.5">
                                        {FOOTER_PLATFORM.map((item) => (
                                            <li
                                                key={item}
                                                className="cursor-pointer text-[13px] text-ink-muted transition-colors duration-150 ease-premium hover:text-ink"
                                            >
                                                {t(item)}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>

                            <div className="mt-14 flex flex-col items-center justify-between gap-3 border-t border-line pt-6 text-[11px] font-medium tracking-[0.06em] text-ink-subtle sm:flex-row">
                                <p>{t('footer.name')}</p>
                                <p>{t('footer.builtWith')}</p>
                            </div>
                        </div>
                    </footer>

                    {/* Scroll to Top Button */}
                    {showScrollTop && (
                        <button
                            type="button"
                            onClick={scrollToTop}
                            aria-label="Scroll to top"
                            className={cn(
                                'animate-fade-in fixed right-6 bottom-6 z-30 inline-flex h-10 w-10 items-center justify-center rounded-full',
                                'border border-line bg-surface text-ink-muted shadow-raised',
                                'transition-[transform,color,background-color,border-color] duration-200 ease-premium hover:border-line-strong hover:bg-surface-hover hover:text-ink active:scale-95',
                            )}
                        >
                            <ArrowUpIcon className="h-[18px] w-[18px]" />
                        </button>
                    )}
                </div>
            </>
        </TranslationProvider>
    );
}
