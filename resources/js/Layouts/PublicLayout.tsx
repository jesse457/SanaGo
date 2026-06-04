import { Head, Link, usePage } from '@inertiajs/react';
import { HeartPulse, Menu, X, Sun, Moon, ArrowUp } from 'lucide-react';
import { useState, useEffect, useCallback } from 'react';
import type { PropsWithChildren } from 'react';
import { FlashHandler } from '@/Components/FlashHandler';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';
import { cn } from '@/lib/utils';

interface NavItem {
    name: string;
    href: string;
    isRoute: boolean;
}

const NAV_ITEMS: NavItem[] = [
    { name: 'nav.home', href: '/', isRoute: true },
    { name: 'nav.features', href: '/features', isRoute: true },
    { name: 'nav.pricing', href: '/pricing', isRoute: true },
    { name: 'nav.about', href: '/about', isRoute: true },
    { name: 'nav.contact', href: '/contact', isRoute: true },
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

    // Initialize dark mode
    useEffect(() => {
        const storedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia(
            '(prefers-color-scheme: dark)',
        ).matches;
        const shouldBeDark =
            storedTheme === 'dark' || (!storedTheme && prefersDark);
        setIsDark(shouldBeDark);

        if (shouldBeDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }, []);

    // Toggle dark mode
    const toggleDarkMode = () => {
        const newDark = !isDark;
        setIsDark(newDark);

        if (newDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
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
        if (mobileMenuOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }

        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [mobileMenuOpen]);

    const isActive = (href: string) => {
        if (href === '/') {
            return url === '/';
        }

        return url.startsWith(href);
    };

    return (
        <TranslationProvider>
            <FlashHandler />
            <>
                <Head>
                    <title>{`SanaGo — ${t('site.title')}`}</title>
                    <meta name="description" content={t('site.description')} />
                    <meta
                        name="viewport"
                        content="width=device-width, initial-scale=1"
                    />
                </Head>

                <div className="min-h-screen overflow-x-hidden bg-white font-sans text-slate-900 transition-colors duration-300 selection:bg-blue-100 dark:bg-neutral-900 dark:text-neutral-100 dark:selection:bg-blue-800">
                    {/* Navigation */}
                    <header className="fixed top-0 z-50 w-full border-b border-slate-100 bg-white/80 backdrop-blur-md transition-colors duration-300 dark:border-neutral-800 dark:bg-neutral-900/80">
                        <nav className="mx-auto flex h-20 max-w-7xl items-center justify-between px-6">
                            {/* Logo */}
                            <Link
                                href="/"
                                className="flex items-center gap-3 transition hover:opacity-80"
                            >
                                <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30">
                                    <HeartPulse className="h-5 w-5" />
                                </div>
                                <span className="text-xl font-black tracking-tight text-slate-800 dark:text-neutral-100">
                                    Sana
                                    <span className="text-blue-600">Go</span>
                                </span>
                            </Link>

                            {/* Desktop Navigation */}
                            <div className="hidden items-center gap-8 md:flex">
                                {NAV_ITEMS.map((item) => (
                                    <Link
                                        key={item.name}
                                        href={item.href}
                                        className={cn(
                                            'text-sm font-semibold transition',
                                            isActive(item.href)
                                                ? 'text-blue-600 dark:text-blue-400'
                                                : 'text-slate-600 hover:text-blue-600 dark:text-neutral-400 dark:hover:text-blue-400',
                                        )}
                                    >
                                        {t(item.name)}
                                    </Link>
                                ))}
                                <button
                                    onClick={toggleDarkMode}
                                    className="cursor-pointer rounded-xl p-2 text-slate-600 transition hover:bg-slate-100 dark:text-neutral-400 dark:hover:bg-neutral-800"
                                    aria-label="Toggle dark mode"
                                >
                                    {isDark ? (
                                        <Sun size={18} />
                                    ) : (
                                        <Moon size={18} />
                                    )}
                                </button>
                                <LocaleSwitcher variant="full" />
                                <Link
                                    href="/login"
                                    className="transform rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-200 transition duration-200 hover:scale-105 hover:bg-blue-700 dark:shadow-blue-900/30"
                                >
                                    {t('auth.login.submit')}
                                </Link>
                            </div>

                            {/* Mobile Menu Button */}
                            <div className="flex items-center gap-3 md:hidden">
                                <button
                                    onClick={toggleDarkMode}
                                    className="cursor-pointer rounded-xl p-2 text-slate-600 transition hover:bg-slate-100 dark:text-neutral-400 dark:hover:bg-neutral-800"
                                    aria-label="Toggle dark mode"
                                >
                                    {isDark ? (
                                        <Sun size={18} />
                                    ) : (
                                        <Moon size={18} />
                                    )}
                                </button>
                                <button
                                    className="cursor-pointer p-2 text-slate-600 dark:text-neutral-400"
                                    onClick={() => setMobileMenuOpen(true)}
                                    aria-label="Open menu"
                                >
                                    <Menu />
                                </button>
                            </div>
                        </nav>
                    </header>

                    {/* Mobile Sidebar */}
                    <div
                        className={cn(
                            'fixed inset-0 z-50 transition-all duration-300 md:hidden',
                            mobileMenuOpen ? 'visible' : 'invisible',
                        )}
                    >
                        <div
                            className={cn(
                                'absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300',
                                mobileMenuOpen ? 'opacity-100' : 'opacity-0',
                            )}
                            onClick={() => setMobileMenuOpen(false)}
                        />
                        <div
                            className={cn(
                                'absolute top-0 right-0 h-full w-80 bg-white shadow-2xl transition-transform duration-300 dark:bg-neutral-900',
                                mobileMenuOpen
                                    ? 'translate-x-0'
                                    : 'translate-x-full',
                            )}
                        >
                            <div className="flex items-center justify-between border-b border-slate-100 p-6 dark:border-neutral-800">
                                <div className="flex items-center gap-3">
                                    <div className="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white">
                                        <HeartPulse className="h-4 w-4" />
                                    </div>
                                    <span className="text-lg font-black text-slate-800 dark:text-neutral-100">
                                        Sana
                                        <span className="text-blue-600">
                                            Go
                                        </span>
                                    </span>
                                </div>
                                <button
                                    onClick={() => setMobileMenuOpen(false)}
                                    className="cursor-pointer p-2 text-slate-500 dark:text-neutral-400"
                                    aria-label="Close menu"
                                >
                                    <X size={20} />
                                </button>
                            </div>
                            <div className="flex flex-col gap-4 p-6">
                                {NAV_ITEMS.map((item) => (
                                    <Link
                                        key={item.name}
                                        href={item.href}
                                        onClick={() => setMobileMenuOpen(false)}
                                        className={cn(
                                            'py-2 text-left text-base font-semibold transition',
                                            isActive(item.href)
                                                ? 'text-blue-600 dark:text-blue-400'
                                                : 'text-slate-600 hover:text-blue-600 dark:text-neutral-400 dark:hover:text-blue-400',
                                        )}
                                    >
                                        {t(item.name)}
                                    </Link>
                                ))}
                                <div className="pt-4">
                                    <Link
                                        href="/login"
                                        className="block w-full rounded-xl bg-blue-600 px-6 py-3 text-center font-bold text-white transition hover:bg-blue-700"
                                        onClick={() => setMobileMenuOpen(false)}
                                    >
                                        {t('auth.login.submit')}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Main Content */}
                    <main>{children}</main>

                    {/* Footer */}
                    <footer className="border-t border-slate-100 bg-white pt-24 pb-12 dark:border-neutral-800 dark:bg-neutral-900">
                        <div className="mx-auto grid max-w-7xl gap-12 px-6 md:grid-cols-3">
                            {/* Branding */}
                            <div>
                                <Link
                                    href="/"
                                    className="mb-6 flex items-center gap-3 transition hover:opacity-80"
                                >
                                    <div className="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white">
                                        <HeartPulse className="h-4 w-4" />
                                    </div>
                                    <span className="text-lg font-black text-slate-800 dark:text-neutral-100">
                                        Sana
                                        <span className="text-blue-600">
                                            Go
                                        </span>
                                    </span>
                                </Link>
                                <p className="max-w-xs text-sm leading-relaxed text-slate-500 dark:text-neutral-500">
                                    {t('footer.description')}
                                </p>
                            </div>

                            {/* Modules */}
                            <div>
                                <h4 className="mb-6 text-xs font-bold tracking-widest text-slate-900 uppercase dark:text-neutral-100">
                                    {t('footer.modules_heading')}
                                </h4>
                                <ul className="space-y-3 text-sm font-medium text-slate-500 dark:text-neutral-500">
                                    {FOOTER_MODULES.map((item) => (
                                        <li
                                            key={item}
                                            className="cursor-pointer transition hover:text-blue-600"
                                        >
                                            {t(item)}
                                        </li>
                                    ))}
                                </ul>
                            </div>

                            {/* Platform */}
                            <div>
                                <h4 className="mb-6 text-xs font-bold tracking-widest text-slate-900 uppercase dark:text-neutral-100">
                                    {t('footer.platform_heading')}
                                </h4>
                                <ul className="space-y-3 text-sm font-medium text-slate-500 dark:text-neutral-500">
                                    {FOOTER_PLATFORM.map((item) => (
                                        <li
                                            key={item}
                                            className="cursor-pointer transition hover:text-blue-600"
                                        >
                                            {t(item)}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>

                        <div className="mx-auto mt-20 flex max-w-7xl flex-col items-center justify-between gap-4 border-t border-slate-50 px-6 pt-8 text-xs font-bold tracking-widest text-slate-400 uppercase md:flex-row dark:border-neutral-800 dark:text-neutral-500">
                            <p>{t('footer.name')}</p>
                            <p>{t('footer.builtWith')}</p>
                        </div>
                    </footer>

                    {/* Scroll to Top Button */}
                    {showScrollTop && (
                        <button
                            onClick={scrollToTop}
                            className="fixed right-6 bottom-6 z-40 transform cursor-pointer rounded-full bg-blue-600 p-3 text-white shadow-lg transition duration-200 hover:scale-110 hover:bg-blue-700"
                            aria-label="Scroll to top"
                        >
                            <ArrowUp size={20} />
                        </button>
                    )}
                </div>
            </>
        </TranslationProvider>
    );
}
