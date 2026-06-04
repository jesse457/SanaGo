import {
    Squares2X2Icon,
    MoonIcon,
    SunIcon,
    BuildingOfficeIcon,
    CreditCardIcon,
    BanknotesIcon,
    Bars3Icon,
    XMarkIcon,
    Cog6ToothIcon,
    ChevronDownIcon,
    ShieldCheckIcon,
    ArrowRightStartOnRectangleIcon,
    BellIcon,
    UserCircleIcon,
    UserGroupIcon,
    TicketIcon,
    MegaphoneIcon,
    ChartBarIcon,
} from '@heroicons/react/24/outline';
import { ChevronLeftIcon } from '@heroicons/react/24/solid';
import { Link, usePage, router } from '@inertiajs/react';
import React, { useState, useEffect, useMemo, useCallback, memo, useRef } from 'react';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';
import { FlashHandler } from '@/Components/FlashHandler';

interface User {
    id: number;
    name: string;
    email: string;
    roles?: { name: string }[];
    is_active: boolean;
}

interface SuperAdminLayoutProps {
    headerTitle?: string;
    headerSubtitle?: string;
}

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((p) => p.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

function getRoleBadge(role: string): string {
    const map: Record<string, string> = {
        'super-admin':
            'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
    };

    return (
        map[role] ||
        'bg-slate-50 dark:bg-neutral-700 text-slate-700 dark:text-neutral-300 border-slate-100 dark:border-neutral-600'
    );
}

// ─── Nav Config ───

interface NavItem {
    label: string;
    labelKey: string;
    href: string;
    icon: React.ReactNode;
}

interface NavGroup {
    category: string;
    categoryKey: string;
    icon: React.ReactNode;
    items: NavItem[];
}

const NAV_CONFIG: NavGroup[] = [
    {
        category: 'Overview',
        categoryKey: 'nav.category.overview',
        icon: <Squares2X2Icon className="h-5 w-5 shrink-0" />,
        items: [
            { label: 'Dashboard', labelKey: 'nav.dashboard', href: '/dashboard/super-admin', icon: <Squares2X2Icon className="w-4 h-4" /> },
        ],
    },
    {
        category: 'Tenant Management',
        categoryKey: 'nav.category.tenant_management',
        icon: <BuildingOfficeIcon className="h-5 w-5 shrink-0" />,
        items: [
            { label: 'Tenants', labelKey: 'nav.tenants', href: '/tenants', icon: <BuildingOfficeIcon className="w-4 h-4" /> },
            { label: 'Plans', labelKey: 'nav.plans', href: '/plans', icon: <CreditCardIcon className="w-4 h-4" /> },
            { label: 'Subscriptions', labelKey: 'nav.subscriptions', href: '/subscriptions', icon: <BanknotesIcon className="w-4 h-4" /> },
        ],
    },
    {
        category: 'Sales',
        categoryKey: 'nav.category.sales',
        icon: <UserGroupIcon className="h-5 w-5 shrink-0" />,
        items: [
            { label: 'Demo Requests', labelKey: 'nav.demo_requests', href: '/demo-requests', icon: <UserGroupIcon className="w-4 h-4" /> },
        ],
    },
    {
        category: 'Support',
        categoryKey: 'nav.category.support',
        icon: <TicketIcon className="h-5 w-5 shrink-0" />,
        items: [
            { label: 'Support Tickets', labelKey: 'nav.support_tickets', href: '/support-tickets', icon: <TicketIcon className="w-4 h-4" /> },
        ],
    },
    {
        category: 'Administration',
        categoryKey: 'nav.category.administration',
        icon: <ShieldCheckIcon className="h-5 w-5 shrink-0" />,
        items: [
            { label: 'Staff Users', labelKey: 'nav.landlord_users', href: '/landlord-users', icon: <UserCircleIcon className="w-4 h-4" /> },
            { label: 'Roles & Permissions', labelKey: 'nav.landlord_roles', href: '/landlord-roles', icon: <ShieldCheckIcon className="w-4 h-4" /> },
            { label: 'Announcements', labelKey: 'nav.announcements', href: '/announcements', icon: <MegaphoneIcon className="w-4 h-4" /> },
        ],
    },
    {
        category: 'Insights',
        categoryKey: 'nav.category.insights',
        icon: <ChartBarIcon className="h-5 w-5 shrink-0" />,
        items: [
            { label: 'Analytics', labelKey: 'nav.analytics', href: '/analytics', icon: <ChartBarIcon className="w-4 h-4" /> },
        ],
    },
];

// ─── Sidebar Navigation ───

const SidebarNav = memo(function SidebarNav({
    isOpen,
    isActive,
    openCategories,
    onToggleCategory,
}: {
    isOpen: boolean;
    isActive: (href: string) => boolean;
    openCategories: Record<string, boolean>;
    onToggleCategory: (category: string) => void;
}) {
    const { t } = useTranslation();
    const isCategoryOpen = (cat: string) => !!openCategories[cat];

    return (
        <>
            <div className="flex h-16 shrink-0 items-center border-b border-gray-100 px-4 dark:border-neutral-800">
                <Link href="/dashboard/super-admin" className="flex items-center gap-3 overflow-hidden w-full">
                    <div className="h-10 w-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200 dark:shadow-none shrink-0">
                        <ShieldCheckIcon className="w-6 h-6" />
                    </div>
                    {isOpen && (
                        <div className="overflow-hidden">
                            <span className="font-black text-lg text-gray-900 dark:text-neutral-100 tracking-tight whitespace-nowrap">Sana<span className="text-blue-600">Go</span></span>
                            <span className="block text-[10px] text-gray-400 font-medium -mt-1">Super Admin</span>
                        </div>
                    )}
                </Link>
            </div>

            <nav className="flex-1 overflow-y-auto py-2 px-3 space-y-1 overscroll-contain">
                {NAV_CONFIG.map(group => (
                    <div key={group.category} className="mb-1">
                        <button
                            onClick={() => onToggleCategory(group.category)}
                            className={`w-full flex items-center justify-between px-3 py-2.5 rounded-lg transition-all duration-200 group ${isCategoryOpen(group.category) ? 'text-blue-600 bg-blue-50/80 dark:bg-blue-900/20 shadow-sm' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-neutral-800 hover:text-gray-700 dark:hover:text-neutral-300'}`}
                        >
                            <div className="flex items-center gap-3">
                                <div className={`p-1 rounded-lg transition-colors ${isCategoryOpen(group.category) ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-neutral-400'}`}>{group.icon}</div>
                                {isOpen && <span className="text-[12px] font-semibold tracking-wide">{t(group.categoryKey)}</span>}
                            </div>
                            {isOpen && <ChevronDownIcon className={`w-4 h-4 transition-transform duration-300 ${isCategoryOpen(group.category) ? 'rotate-180' : ''}`} />}
                        </button>
                        {isOpen && isCategoryOpen(group.category) && (
                            <div className="mt-1 ml-7 border-l-2 border-gray-100 dark:border-neutral-800 space-y-1 animate-slideDown">
                                {group.items.map(item => (
                                    <Link key={item.href} href={item.href}
                                        className={`group/item flex items-center justify-between pl-4 pr-3 py-2 text-sm rounded-r-lg transition-all duration-200 ${isActive(item.href) ? 'text-blue-600 font-semibold bg-blue-50/60 dark:bg-blue-900/20 border-l-2 border-blue-600 -ml-[2px]' : 'text-gray-500 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800'}`}
                                    >
                                        <span className="truncate">{t(item.labelKey)}</span>
                                    </Link>
                                ))}
                            </div>
                        )}
                    </div>
                ))}
            </nav>

            <div className="p-3 border-t border-gray-100 dark:border-neutral-800 space-y-1">
                <Link href="/settings" className="flex items-center gap-3 px-3 py-2.5 text-gray-500 hover:text-blue-600 transition-all duration-200 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-800 group">
                    <div className="p-1 rounded-lg bg-gray-100 dark:bg-neutral-800 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors"><Cog6ToothIcon className="w-4 h-4" /></div>
                    {isOpen && <span className="text-sm font-medium">{t('nav.settings')}</span>}
                </Link>
            </div>
        </>
    );
});

// ─── Notification Bell ───

const NotificationsDropdown = memo(function NotificationsDropdown({
    onClose,
}: {
    onClose: () => void;
}) {
    const { t } = useTranslation();
    const notifications = [
        {
            id: 1,
            message: 'New tenant registration',
            time: '2 days ago',
            read: false,
        },
        {
            id: 2,
            message: 'System update available',
            time: '1 week ago',
            read: true,
        },
    ];
    const unreadCount = notifications.filter((n) => !n.read).length;

    return (
        <div className="absolute top-full right-0 z-20 mt-2 w-80 rounded-xl border border-gray-200 bg-white py-2 shadow-xl dark:border-neutral-700 dark:bg-neutral-800">
            <div className="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-neutral-700">
                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                    {t('common.notifications')}
                </p>
                {unreadCount > 0 && (
                    <span className="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                        {unreadCount} {t('common.new')}
                    </span>
                )}
            </div>
            <div className="max-h-64 overflow-y-auto overscroll-contain">
                {notifications.map((n) => (
                    <div
                        key={n.id}
                        className={`border-b border-gray-50 px-4 py-3 transition-colors hover:bg-gray-50 dark:border-neutral-700/50 dark:hover:bg-neutral-700/30 ${!n.read ? 'bg-blue-50/30 dark:bg-blue-900/5' : ''}`}
                    >
                        <p className="text-sm text-gray-700 dark:text-neutral-300">
                            {n.message}
                        </p>
                        <p className="mt-1 text-[10px] font-medium text-gray-400">
                            {n.time}
                        </p>
                    </div>
                ))}
            </div>
        </div>
    );
});

// ─── User Menu Dropdown ───

const UserMenuDropdown = memo(function UserMenuDropdown({
    user,
    userRole,
    onClose,
}: {
    user: User | null;
    userRole: string;
    onClose: () => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="absolute top-full right-0 z-20 mt-2 w-56 rounded-xl border border-gray-200 bg-white py-2 shadow-xl dark:border-neutral-700 dark:bg-neutral-800">
            <div className="border-b border-gray-100 px-4 py-3 dark:border-neutral-700">
                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                    {user?.name || t('common.staff_user')}
                </p>
                <p className="text-xs text-gray-500 dark:text-neutral-400">
                    {user?.email}
                </p>
                <span
                    className={`mt-1.5 inline-block rounded border px-2 py-0.5 text-[9px] font-bold uppercase ${getRoleBadge(userRole)}`}
                >
                    {t('nav.super_admin')}
                </span>
            </div>
            <div className="py-1">
                <Link
                    href="/profile"
                    className="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition-colors duration-150 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    onClick={onClose}
                >
                    <UserCircleIcon className="h-4 w-4" />
                    {t('nav.my_profile')}
                </Link>
                <Link
                    href="/settings"
                    className="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition-colors duration-150 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    onClick={onClose}
                >
                    <Cog6ToothIcon className="h-4 w-4" />
                    {t('nav.settings')}
                </Link>
            </div>
            <div className="border-t border-gray-100 pt-1 dark:border-neutral-700">
                <button
                    onClick={() => {
                        router.post('/logout');
                    }}
                    className="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 transition-colors duration-150 hover:bg-red-50 dark:hover:bg-red-900/20"
                >
                    <ArrowRightStartOnRectangleIcon className="h-4 w-4" />
                    {t('nav.sign_out')}
                </button>
            </div>
        </div>
    );
});

// ─── Main Layout ───

export default function SuperAdminLayout({
    children,
    headerTitle,
    headerSubtitle,
}: React.PropsWithChildren<SuperAdminLayoutProps>) {
    const { url, props } = usePage();
    const { t } = useTranslation();
    const resolvedTitle = headerTitle ?? t('nav.dashboard');
    const resolvedSubtitle = headerSubtitle ?? t('nav.super_admin');
    const user = (props.auth as any)?.user as User | null;

    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const [notifOpen, setNotifOpen] = useState(false);

    // Dynamic ref trackers to track click-outside actions safely
    const userMenuRef = useRef<HTMLDivElement>(null);
    const notifRef = useRef<HTMLDivElement>(null);

    // Track manually toggled navigation groups
    const [manuallyToggled, setManuallyToggled] = useState<Record<string, boolean>>({});

    const [isDark, setIsDark] = useState(() => {
        if (typeof window === 'undefined') {
            return false;
        }

        const stored = localStorage.getItem('theme');

        if (stored) {
            return stored === 'dark';
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    });

    useEffect(() => {
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }, [isDark]);

    useEffect(() => {
        setMobileMenuOpen(false);
    }, [url]);

    // Click outside listener logic
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (userMenuRef.current && !userMenuRef.current.contains(event.target as Node)) {
                setUserMenuOpen(false);
            }
            if (notifRef.current && !notifRef.current.contains(event.target as Node)) {
                setNotifOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // Escape listener logic
    useEffect(() => {
        const handleKeyPress = (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                setUserMenuOpen(false);
                setNotifOpen(false);
            }
        };

        document.addEventListener('keydown', handleKeyPress);
        return () => document.removeEventListener('keydown', handleKeyPress);
    }, []);

    // Sanitized parameter matching to prevent routes with query strings from breaking active styles
    const isActiveLink = useCallback(
        (href: string) => {
            const currentPath = url.split('?')[0].split('#')[0].replace(/\/$/, '');
            const targetPath = href.split('?')[0].split('#')[0].replace(/\/$/, '');

            return currentPath === targetPath || currentPath.startsWith(targetPath + '/');
        },
        [url],
    );

    // Calculates which category groups should actually be open (only the active child gets opened by default)
    const openCategories = useMemo(() => {
        const result: Record<string, boolean> = {};
        NAV_CONFIG.forEach(group => {
            const hasActiveChild = group.items.some(item => isActiveLink(item.href));
            const manual = manuallyToggled[group.category];
            result[group.category] = manual !== undefined ? manual : hasActiveChild;
        });
        return result;
    }, [manuallyToggled, isActiveLink]);

    const toggleCategory = useCallback((category: string) => {
        setManuallyToggled(prev => ({ ...prev, [category]: !openCategories[category] }));
    }, [openCategories]);

    const toggleTheme = useCallback(() => {
        setIsDark((prev) => !prev);
    }, []);

    const closeAllDropdowns = useCallback(() => {
        setUserMenuOpen(false);
        setNotifOpen(false);
    }, []);

    const userRole = useMemo(() => 'super-admin', []);
    const userInitials = useMemo(
        () => (user ? getInitials(user.name) : 'SA'),
        [user],
    );
    const unreadCount = 1;

    return (
        <TranslationProvider>
            <FlashHandler />
            <div className="flex h-screen overflow-hidden bg-gray-50 font-sans dark:bg-neutral-950">
            {/* ─── Mobile backdrop ─── */}
            {mobileMenuOpen && (
                <div
                    className="fixed inset-0 z-[60] bg-gray-900/50 backdrop-blur-sm lg:hidden"
                    onClick={() => setMobileMenuOpen(false)}
                />
            )}

            {/* ─── Mobile sidebar ─── */}
            <aside
                className={`fixed inset-y-0 left-0 z-[70] flex w-72 transform flex-col border-r bg-white transition-transform duration-300 ease-out lg:hidden dark:border-neutral-800 dark:bg-neutral-900 ${
                    mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <div className="absolute top-4 right-4">
                    <button
                        onClick={() => setMobileMenuOpen(false)}
                        className="p-2 text-gray-500"
                    >
                        <XMarkIcon className="h-6 w-6" />
                    </button>
                </div>
                <SidebarNav
                    isOpen={true}
                    isActive={isActiveLink}
                    openCategories={openCategories}
                    onToggleCategory={toggleCategory}
                />
            </aside>

            {/* ─── Desktop sidebar ─── */}
            <aside
                className={`fixed inset-y-0 left-0 z-50 hidden flex-col border-r border-gray-200 bg-white transition-[width] duration-300 ease-out lg:flex dark:border-neutral-800 dark:bg-neutral-900 ${
                    sidebarOpen ? 'w-64' : 'w-20'
                }`}
            >
                <SidebarNav
                    isOpen={sidebarOpen}
                    isActive={isActiveLink}
                    openCategories={openCategories}
                    onToggleCategory={toggleCategory}
                />
                <button
                    onClick={() => setSidebarOpen((prev) => !prev)}
                    className="absolute top-20 -right-3 z-50 rounded-full border border-gray-200 bg-white p-1 text-gray-400 shadow-sm transition-colors duration-150 hover:text-blue-600 dark:border-neutral-800 dark:bg-neutral-900"
                    title={
                        sidebarOpen
                            ? t('common.collapse_sidebar')
                            : t('common.expand_sidebar')
                    }
                >
                    <ChevronLeftIcon
                        className={`h-4 w-4 transition-transform duration-300 ${!sidebarOpen && 'rotate-180'}`}
                    />
                </button>
            </aside>

            {/* ─── Main content area ─── */}
            <div
                className={`flex min-w-0 flex-1 flex-col transition-[margin] duration-300 ease-out ${
                    sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'
                }`}
            >
                {/* ─── Top header ─── */}
                <header className="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-gray-200 bg-white/80 px-4 backdrop-blur-md lg:px-8 dark:border-neutral-800 dark:bg-neutral-900/80">
                    <div className="flex items-center gap-4">
                        <button
                            onClick={() => setMobileMenuOpen(true)}
                            className="-ml-2 rounded-lg p-2 text-gray-500 transition-colors duration-150 hover:bg-gray-100 lg:hidden dark:hover:bg-neutral-800"
                        >
                            <Bars3Icon className="h-6 w-6" />
                        </button>
                        <div>
                            <h1 className="max-w-[200px] truncate text-lg font-black tracking-tight text-gray-900 lg:max-w-none lg:text-xl dark:text-neutral-100">
                                {resolvedTitle}
                            </h1>
                            <p className="hidden truncate text-[10px] font-bold tracking-widest text-gray-400 uppercase sm:block">
                                {resolvedSubtitle}
                            </p>
                        </div>
                    </div>

                    <div className="flex items-center gap-2 lg:gap-4">
                        {/* Locale Switcher */}
                        <LocaleSwitcher variant="full" />

                        {/* Theme toggle */}
                        <button
                            onClick={toggleTheme}
                            role="switch"
                            aria-checked={isDark}
                            aria-label={t('common.toggle_dark_mode')}
                            className={`relative inline-flex h-7 w-12 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors duration-300 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-neutral-900 ${
                                isDark
                                    ? 'bg-blue-600'
                                    : 'bg-gray-200 dark:bg-neutral-700'
                            }`}
                        >
                            <span
                                className={`flex h-5 w-5 items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300 ${
                                    isDark ? 'translate-x-6' : 'translate-x-0.5'
                                }`}
                            >
                                {isDark ? (
                                    <MoonIcon className="h-3.5 w-3.5 text-blue-600" />
                                ) : (
                                    <SunIcon className="h-3.5 w-3.5 text-yellow-500" />
                                )}
                            </span>
                        </button>

                        {/* Notifications */}
                        <div className="relative" ref={notifRef}>
                            <button
                                onClick={() => {
                                    setNotifOpen((prev) => !prev);
                                    setUserMenuOpen(false);
                                }}
                                className="relative rounded-lg p-2 text-gray-400 transition-colors duration-150 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-neutral-800"
                                aria-label={t('common.notifications')}
                            >
                                <BellIcon className="h-5 w-5" />
                                {unreadCount > 0 && (
                                    <span className="absolute top-1.5 right-1.5 h-2 w-2 rounded-full border-2 border-white bg-red-500 dark:border-neutral-900" />
                                )}
                            </button>

                            {notifOpen && (
                                <NotificationsDropdown
                                    onClose={closeAllDropdowns}
                                />
                            )}
                        </div>

                        {/* User menu */}
                        <div className="relative flex items-center gap-3 border-l border-gray-100 pl-2 dark:border-neutral-800" ref={userMenuRef}>
                            <button
                                onClick={() => {
                                    setUserMenuOpen((prev) => !prev);
                                    setNotifOpen(false);
                                }}
                                className="flex cursor-pointer items-center gap-3"
                                aria-label={t('common.user_menu')}
                            >
                                <div className="hidden text-right md:block">
                                    <p className="text-xs font-bold text-gray-900 dark:text-neutral-100">
                                        {user?.name || t('common.staff_user')}
                                    </p>
                                    <span
                                        className={`rounded border px-2 py-0.5 text-[9px] font-bold uppercase ${getRoleBadge('super-admin')}`}
                                    >
                                        {t('nav.super_admin')}
                                    </span>
                                </div>
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-sm font-bold text-white shadow-md">
                                    {userInitials}
                                </div>
                            </button>

                            {userMenuOpen && (
                                <UserMenuDropdown
                                    user={user}
                                    userRole="super-admin"
                                    onClose={closeAllDropdowns}
                                />
                            )}
                        </div>
                    </div>
                </header>

                {/* ─── Page content ─── */}
                <main className="flex-1 overflow-y-auto bg-gray-50/50 p-4 lg:p-8 dark:bg-neutral-950">
                    <div className="mx-auto max-w-7xl">
                        {children}
                    </div>
                </main>
            </div>
        </div>
        </TranslationProvider>
    );
}
