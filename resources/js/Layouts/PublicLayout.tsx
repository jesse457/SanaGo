import { Head, Link, usePage } from '@inertiajs/react';
import { useState, useEffect, useCallback, type PropsWithChildren } from 'react';
import {
  HeartPulse,
  Menu,
  X,
  Sun,
  Moon,
  ArrowUp,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';
import { FlashHandler } from '@/Components/FlashHandler';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';

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
    if (href === '/') return url === '/';
    return url.startsWith(href);
  };

  return (
    <TranslationProvider>
      <FlashHandler />
      <>
        <Head>
        <title>{`SanaGo — ${t('site.title')}`}</title>
        <meta
          name="description"
          content={t('site.description')}
        />
        <meta
          name="viewport"
          content="width=device-width, initial-scale=1"
        />
      </Head>

      <div className="min-h-screen bg-white dark:bg-neutral-900 font-sans text-slate-900 dark:text-neutral-100 selection:bg-blue-100 dark:selection:bg-blue-800 overflow-x-hidden transition-colors duration-300">
        {/* Navigation */}
        <header className="fixed top-0 w-full bg-white/80 dark:bg-neutral-900/80 backdrop-blur-md z-50 border-b border-slate-100 dark:border-neutral-800 transition-colors duration-300">
          <nav className="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            {/* Logo */}
            <Link
              href="/"
              className="flex items-center gap-3 hover:opacity-80 transition"
            >
              <div className="h-9 w-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30">
                <HeartPulse className="w-5 h-5" />
              </div>
              <span className="text-xl font-black tracking-tight text-slate-800 dark:text-neutral-100">
                Sana<span className="text-blue-600">Go</span>
              </span>
            </Link>

            {/* Desktop Navigation */}
            <div className="hidden md:flex items-center gap-8">
              {NAV_ITEMS.map((item) => (
                <Link
                  key={item.name}
                  href={item.href}
                  className={cn(
                    'text-sm font-semibold transition',
                    isActive(item.href)
                      ? 'text-blue-600 dark:text-blue-400'
                      : 'text-slate-600 dark:text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400',
                  )}
                >
                  {t(item.name)}
                </Link>
              ))}
              <button
                onClick={toggleDarkMode}
                className="p-2 rounded-xl text-slate-600 dark:text-neutral-400 hover:bg-slate-100 dark:hover:bg-neutral-800 transition cursor-pointer"
                aria-label="Toggle dark mode"
              >
                {isDark ? <Sun size={18} /> : <Moon size={18} />}
              </button>
              <LocaleSwitcher variant="full" />
              <Link
                href="/login"
                className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-200 dark:shadow-blue-900/30 transition transform hover:scale-105 duration-200"
              >
                {t('auth.login.submit')}
              </Link>
            </div>

            {/* Mobile Menu Button */}
            <div className="flex items-center gap-3 md:hidden">
              <button
                onClick={toggleDarkMode}
                className="p-2 rounded-xl text-slate-600 dark:text-neutral-400 hover:bg-slate-100 dark:hover:bg-neutral-800 transition cursor-pointer"
                aria-label="Toggle dark mode"
              >
                {isDark ? <Sun size={18} /> : <Moon size={18} />}
              </button>
              <button
                className="p-2 text-slate-600 dark:text-neutral-400 cursor-pointer"
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
              'absolute right-0 top-0 h-full w-80 bg-white dark:bg-neutral-900 shadow-2xl transition-transform duration-300',
              mobileMenuOpen ? 'translate-x-0' : 'translate-x-full',
            )}
          >
            <div className="flex justify-between items-center p-6 border-b border-slate-100 dark:border-neutral-800">
              <div className="flex items-center gap-3">
                <div className="h-8 w-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white">
                  <HeartPulse className="w-4 h-4" />
                </div>
                <span className="font-black text-lg text-slate-800 dark:text-neutral-100">
                  Sana<span className="text-blue-600">Go</span>
                </span>
              </div>
              <button
                onClick={() => setMobileMenuOpen(false)}
                className="p-2 text-slate-500 dark:text-neutral-400 cursor-pointer"
                aria-label="Close menu"
              >
                <X size={20} />
              </button>
            </div>
            <div className="flex flex-col p-6 gap-4">
              {NAV_ITEMS.map((item) => (
                <Link
                  key={item.name}
                  href={item.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className={cn(
                    'text-base font-semibold py-2 transition text-left',
                    isActive(item.href)
                      ? 'text-blue-600 dark:text-blue-400'
                      : 'text-slate-600 dark:text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400',
                  )}
                >
                  {t(item.name)}
                </Link>
              ))}
              <div className="pt-4">
                <Link
                  href="/login"
                  className="block w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-center font-bold transition"
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
        <footer className="bg-white dark:bg-neutral-900 pt-24 pb-12 border-t border-slate-100 dark:border-neutral-800">
          <div className="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-12">
            {/* Branding */}
            <div>
              <Link
                href="/"
                className="flex items-center gap-3 mb-6 hover:opacity-80 transition"
              >
                <div className="h-8 w-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white">
                  <HeartPulse className="w-4 h-4" />
                </div>
                <span className="font-black text-lg text-slate-800 dark:text-neutral-100">
                  Sana<span className="text-blue-600">Go</span>
                </span>
              </Link>
              <p className="text-slate-500 dark:text-neutral-500 text-sm leading-relaxed max-w-xs">
                {t('footer.description')}
              </p>
            </div>

            {/* Modules */}
            <div>
              <h4 className="text-xs font-bold uppercase tracking-widest text-slate-900 dark:text-neutral-100 mb-6">
                {t('footer.modules_heading')}
              </h4>
              <ul className="space-y-3 text-sm text-slate-500 dark:text-neutral-500 font-medium">
                {FOOTER_MODULES.map((item) => (
                  <li
                    key={item}
                    className="hover:text-blue-600 transition cursor-pointer"
                  >
                    {t(item)}
                  </li>
                ))}
              </ul>
            </div>

            {/* Platform */}
            <div>
              <h4 className="text-xs font-bold uppercase tracking-widest text-slate-900 dark:text-neutral-100 mb-6">
                {t('footer.platform_heading')}
              </h4>
              <ul className="space-y-3 text-sm text-slate-500 dark:text-neutral-500 font-medium">
                {FOOTER_PLATFORM.map((item) => (
                  <li
                    key={item}
                    className="hover:text-blue-600 transition cursor-pointer"
                  >
                    {t(item)}
                  </li>
                ))}
              </ul>
            </div>
          </div>

          <div className="max-w-7xl mx-auto px-6 mt-20 pt-8 border-t border-slate-50 dark:border-neutral-800 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-bold text-slate-400 dark:text-neutral-500 uppercase tracking-widest">
            <p>{t('footer.name')}</p>
            <p>{t('footer.builtWith')}</p>
          </div>
        </footer>

        {/* Scroll to Top Button */}
        {showScrollTop && (
          <button
            onClick={scrollToTop}
            className="fixed bottom-6 right-6 z-40 p-3 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition transform hover:scale-110 duration-200 cursor-pointer"
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
