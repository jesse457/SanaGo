import {
    BanknotesIcon,
    Bars3Icon,
    BellIcon,
    BuildingOfficeIcon,
    ChevronDownIcon,
    Cog6ToothIcon,
    CreditCardIcon,
    HeartIcon,
    KeyIcon,
    MegaphoneIcon,
    MegaphoneIcon as GrowthIcon,
    QueueListIcon,
    ServerStackIcon,
    ShieldCheckIcon,
    Squares2X2Icon,
    TicketIcon,
    UserCircleIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { ChevronLeftIcon } from '@heroicons/react/24/solid';
import { Link, router, usePage } from '@inertiajs/react';
import React, {
    memo,
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';
import { AppToaster } from '@/Components/AppToaster';
import { FlashHandler } from '@/Components/FlashHandler';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { Badge } from '@/Components/ui';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';
import { BrandMark } from '@/Layouts/components/BrandMark';
import { DrawerHeader } from '@/Layouts/components/DrawerHeader';
import { NavLink } from '@/Layouts/components/NavItem';
import { NotificationsPanel } from '@/Layouts/components/NotificationsPanel';
import type { ShellNotification } from '@/Layouts/components/NotificationsPanel';
import { DrawerBackdrop } from '@/Layouts/components/ShellPrimitives';
import { ThemeToggle } from '@/Layouts/components/ThemeToggle';
import { UserChip, UserMeta } from '@/Layouts/components/UserChip';
import { UserMenuPanel } from '@/Layouts/components/UserMenuPanel';

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

/**
 * The landlord console is grouped by what an operator is trying to do, not by
 * which controller answers the route:
 *
 *   Overview — the pulse of the platform
 *   Tenants  — the hospitals themselves
 *   Billing  — money: usage, catalogue and contracts
 *   Platform — the machine room: settings, audit trail, health
 *   Growth   — demand and customer conversations
 *   Access   — who may operate the console
 */
const NAV_CONFIG: NavGroup[] = [
    {
        category: 'Overview',
        categoryKey: 'nav.category.overview',
        icon: <Squares2X2Icon className="h-4 w-4 shrink-0" />,
        items: [
            {
                label: 'Dashboard',
                labelKey: 'nav.dashboard',
                href: '/dashboard/super-admin',
                icon: <Squares2X2Icon className="h-4 w-4" />,
            },
        ],
    },
    {
        category: 'Tenants',
        categoryKey: 'nav.category.tenants',
        icon: <BuildingOfficeIcon className="h-4 w-4 shrink-0" />,
        items: [
            {
                label: 'Hospitals',
                labelKey: 'nav.tenants',
                href: '/tenants',
                icon: <BuildingOfficeIcon className="h-4 w-4" />,
            },
        ],
    },
    {
        category: 'Billing',
        categoryKey: 'nav.category.billing',
        icon: <BanknotesIcon className="h-4 w-4 shrink-0" />,
        items: [
            {
                label: 'Billing & usage',
                labelKey: 'nav.billing_usage',
                href: '/platform/billing',
                icon: <BanknotesIcon className="h-4 w-4" />,
            },
            {
                label: 'Plans',
                labelKey: 'nav.plans',
                href: '/plans',
                icon: <CreditCardIcon className="h-4 w-4" />,
            },
            {
                label: 'Subscriptions',
                labelKey: 'nav.subscriptions',
                href: '/subscriptions',
                icon: <QueueListIcon className="h-4 w-4" />,
            },
        ],
    },
    {
        category: 'Platform',
        categoryKey: 'nav.category.platform',
        icon: <ServerStackIcon className="h-4 w-4 shrink-0" />,
        items: [
            {
                label: 'Settings',
                labelKey: 'nav.platform_settings',
                href: '/platform/settings',
                icon: <Cog6ToothIcon className="h-4 w-4" />,
            },
            {
                label: 'Audit log',
                labelKey: 'nav.audit_log',
                href: '/platform/audit',
                icon: <ShieldCheckIcon className="h-4 w-4" />,
            },
            {
                label: 'Health & monitoring',
                labelKey: 'nav.health',
                href: '/platform/health',
                icon: <HeartIcon className="h-4 w-4" />,
            },
        ],
    },
    {
        category: 'Growth',
        categoryKey: 'nav.category.growth',
        icon: <GrowthIcon className="h-4 w-4 shrink-0" />,
        items: [
            {
                label: 'Demo requests',
                labelKey: 'nav.demo_requests',
                href: '/demo-requests',
                icon: <UserGroupIcon className="h-4 w-4" />,
            },
            {
                label: 'Support tickets',
                labelKey: 'nav.support_tickets',
                href: '/support-tickets',
                icon: <TicketIcon className="h-4 w-4" />,
            },
            {
                label: 'Announcements',
                labelKey: 'nav.announcements',
                href: '/announcements',
                icon: <MegaphoneIcon className="h-4 w-4" />,
            },
        ],
    },
    {
        category: 'Access',
        categoryKey: 'nav.category.access',
        icon: <KeyIcon className="h-4 w-4 shrink-0" />,
        items: [
            {
                label: 'Staff users',
                labelKey: 'nav.landlord_users',
                href: '/landlord-users',
                icon: <UserCircleIcon className="h-4 w-4" />,
            },
            {
                label: 'Roles & permissions',
                labelKey: 'nav.landlord_roles',
                href: '/landlord-roles',
                icon: <ShieldCheckIcon className="h-4 w-4" />,
            },
        ],
    },
];

// ─── Sidebar Navigation ───

const SidebarNav = memo(function SidebarNav({
    isOpen,
    isActive,
    openCategories,
    onToggleCategory,
    platformName,
    currency,
}: {
    isOpen: boolean;
    isActive: (href: string) => boolean;
    openCategories: Record<string, boolean>;
    onToggleCategory: (category: string) => void;
    platformName: string;
    currency: string;
}) {
    const { t } = useTranslation();
    const isCategoryOpen = (cat: string) => !!openCategories[cat];

    return (
        <>
            <div className="flex h-14 shrink-0 items-center border-b border-line px-3.5">
                <BrandMark
                    icon={ShieldCheckIcon}
                    caption={
                        isOpen
                            ? `${platformName} · ${t('nav.super_admin')}`
                            : undefined
                    }
                    href="/dashboard/super-admin"
                    compact={!isOpen}
                />
            </div>

            <nav
                aria-label={t('nav.super_admin')}
                className="flex-1 space-y-0.5 overflow-y-auto overscroll-contain px-2.5 py-3"
            >
                {NAV_CONFIG.map((group) => {
                    const hasActiveChild = group.items.some((item) =>
                        isActive(item.href),
                    );
                    const isExpanded = isCategoryOpen(group.category);

                    return (
                        <div key={group.category} className="space-y-0.5">
                            <button
                                type="button"
                                onClick={() => onToggleCategory(group.category)}
                                aria-expanded={isExpanded}
                                title={t(group.categoryKey)}
                                className={`group flex w-full items-center justify-between rounded-md px-2.5 py-2 text-left transition-[background-color,color] duration-200 ease-premium focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none ${
                                    hasActiveChild
                                        ? 'text-ink'
                                        : 'text-ink-muted hover:bg-surface-hover hover:text-ink'
                                }`}
                            >
                                <span className="flex min-w-0 items-center gap-2.5">
                                    <span
                                        className={`shrink-0 transition-colors ${
                                            isExpanded || hasActiveChild
                                                ? 'text-accent'
                                                : 'text-ink-subtle group-hover:text-ink-muted'
                                        }`}
                                    >
                                        {group.icon}
                                    </span>
                                    {isOpen && (
                                        <span className="truncate text-[12.5px] font-medium tracking-tight">
                                            {t(group.categoryKey)}
                                        </span>
                                    )}
                                </span>
                                {isOpen && (
                                    <ChevronDownIcon
                                        className={`h-3 w-3 shrink-0 text-ink-subtle transition-transform duration-200 ease-premium ${
                                            isExpanded ? 'rotate-180' : ''
                                        }`}
                                    />
                                )}
                            </button>

                            {isOpen && isExpanded && (
                                <div className="ml-3.5 space-y-0.5 border-l border-line py-1 pl-2.5">
                                    {group.items.map((item) => (
                                        <NavLink
                                            key={item.href}
                                            href={item.href}
                                            isActive={isActive(item.href)}
                                            variant="sidebar"
                                            linkComponent={(linkProps) => (
                                                <Link
                                                    href={linkProps.href}
                                                    prefetch
                                                    onClick={linkProps.onClick}
                                                    className={
                                                        linkProps.className
                                                    }
                                                >
                                                    {linkProps.children}
                                                </Link>
                                            )}
                                        >
                                            {() => (
                                                <span className="truncate text-[12.5px]">
                                                    {t(item.labelKey)}
                                                </span>
                                            )}
                                        </NavLink>
                                    ))}
                                </div>
                            )}
                        </div>
                    );
                })}
            </nav>

            {/* Plan / currency context: the operator always knows which
                platform they are operating and what money means here. */}
            {isOpen && (
                <div className="mx-2.5 mb-2 rounded-lg border border-line bg-surface-muted px-2.5 py-2">
                    <p className="truncate text-[11px] font-semibold tracking-[0.08em] text-ink-subtle uppercase">
                        {platformName}
                    </p>
                    <p className="mt-0.5 flex items-center gap-1.5 text-[11.5px] text-ink-muted">
                        <span className="h-1.5 w-1.5 rounded-full bg-accent" />
                        {t('nav.billing_currency')}: {currency}
                    </p>
                </div>
            )}

            <div className="space-y-0.5 border-t border-line p-2.5">
                <NavLink
                    href="/settings"
                    isActive={isActive('/settings')}
                    variant="sidebar"
                    linkComponent={(linkProps) => (
                        <Link
                            href={linkProps.href}
                            prefetch
                            className={linkProps.className}
                        >
                            {linkProps.children}
                        </Link>
                    )}
                >
                    {({ isActive: settingsActive }) => (
                        <>
                            <Cog6ToothIcon
                                className={`h-[17px] w-[17px] shrink-0 transition-colors ${
                                    settingsActive
                                        ? 'text-accent'
                                        : 'text-ink-subtle group-hover:text-ink-muted'
                                }`}
                            />
                            {isOpen && (
                                <span className="truncate text-[12.5px]">
                                    {t('nav.settings')}
                                </span>
                            )}
                        </>
                    )}
                </NavLink>
            </div>
        </>
    );
});

// ─── Notification Bell ───

/**
 * Landlord notifications are real: the platform sends them on the central
 * notifications table whenever a subscription or tenant changes state.
 *
 * The feed is fetched on mount and refreshed every 60 s; failures (offline,
 * broadcaster down) leave the honest empty state rather than an error.
 */
function useLandlordNotifications() {
    const [items, setItems] = useState<ShellNotification[]>([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [loading, setLoading] = useState(true);

    const load = useCallback(async () => {
        try {
            const response = await fetch('/platform/notifications/latest', {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const tone = (
                value?: string,
            ): 'info' | 'success' | 'warning' | 'error' =>
                value === 'error'
                    ? 'error'
                    : value === 'warning'
                      ? 'warning'
                      : value === 'success'
                        ? 'success'
                        : 'info';

            setItems(
                (data.notifications ?? []).map((notification: any) => ({
                    id: notification.id,
                    message: notification.message ?? notification.title ?? '',
                    time: notification.created_at ?? '',
                    read: false,
                    type: tone(notification.type),
                    category: notification.category,
                    href: notification.link ?? '/platform/notifications',
                })),
            );
            setUnreadCount(Number(data.unreadCount ?? 0));
        } catch {
            // Keep whatever we had; the panel shows an empty state.
        } finally {
            setLoading(false);
        }
    }, []);

    const csrfToken = () => {
        const match = document.cookie.match(/(?:^|; )XSRF-TOKEN=([^;]*)/);

        return match ? decodeURIComponent(match[1]) : '';
    };

    const markRead = useCallback(async (id: number | string) => {
        setItems((current) => current.filter((item) => item.id !== id));
        setUnreadCount((count) => Math.max(0, count - 1));

        try {
            await fetch(`/platform/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-XSRF-TOKEN': csrfToken(),
                },
                credentials: 'same-origin',
            });
        } catch {
            // The optimistic update stays; the next poll reconciles.
        }
    }, []);

    const markAllRead = useCallback(async () => {
        setItems([]);
        setUnreadCount(0);

        try {
            await fetch('/platform/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-XSRF-TOKEN': csrfToken(),
                },
                credentials: 'same-origin',
            });
        } catch {
            // Ignored, as above.
        }
    }, []);

    useEffect(() => {
        // Defer the first fetch out of the effect body: the hook writes state
        // from a queued microtask so the effect never triggers a cascade.
        queueMicrotask(() => void load());
        const timer = window.setInterval(() => void load(), 60_000);

        return () => window.clearInterval(timer);
    }, [load]);

    return {
        items,
        unreadCount,
        loading,
        markRead,
        markAllRead,
        refresh: load,
    };
}

const NotificationsDropdown = memo(function NotificationsDropdown({
    feed,
    onClose,
}: {
    feed: ReturnType<typeof useLandlordNotifications>;
    onClose: () => void;
}) {
    const { t } = useTranslation();

    const handleSelect = (notification: ShellNotification) => {
        void feed.markRead(notification.id);

        if (notification.href) {
            router.visit(notification.href);
        }

        onClose();
    };

    return (
        <NotificationsPanel
            title={t('common.notifications')}
            items={feed.items}
            unreadCount={feed.unreadCount}
            loading={feed.loading}
            emptyLabel={t('notifications.no_platform_feed')}
            footer={{
                href: '/platform/notifications',
                label: t('notifications.view_all', 'View all notifications'),
                onNavigate: onClose,
            }}
            onSelect={handleSelect}
        />
    );
});

// ─── User Menu Dropdown ───

const UserMenuDropdown = memo(function UserMenuDropdown({
    user,
    onClose,
}: {
    user: User | null;
    onClose: () => void;
}) {
    const { t } = useTranslation();

    return (
        <UserMenuPanel
            user={user}
            role="super-admin"
            roleLabel={t('nav.super_admin')}
            labels={{
                profile: t('nav.my_profile'),
                profileDescription: t('nav.profile_description'),
                settings: t('nav.settings'),
                settingsDescription: t('nav.settings_description'),
                signOut: t('nav.sign_out'),
                signOutDescription: t('nav.sign_out_description'),
            }}
            onClose={onClose}
            onSignOut={() => router.post('/logout')}
        />
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
    const user = (props.auth as any)?.user as User | null;

    // `platform` is the shared platform-context prop. It is not part of
    // HandleInertiaRequests yet, so the fallbacks below keep the shell honest
    // (the name comes from the app, never a hardcoded "$").
    const platform = (props as any).platform ?? null;
    const platformName: string = platform?.name ?? 'SanaGo';
    const currency: string = platform?.currency ?? 'XAF';

    const resolvedSubtitle =
        headerSubtitle ??
        (platform?.maintenance_mode
            ? t('nav.maintenance_mode_active')
            : t('nav.super_admin'));

    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const [notifOpen, setNotifOpen] = useState(false);
    const notificationFeed = useLandlordNotifications();

    // Dynamic ref trackers to track click-outside actions safely
    const userMenuRef = useRef<HTMLDivElement>(null);
    const notifRef = useRef<HTMLDivElement>(null);

    // Track manually toggled navigation groups
    const [manuallyToggled, setManuallyToggled] = useState<
        Record<string, boolean>
    >({});

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
        // Navigation closes the mobile drawer: this is a response to an external
        // event (a completed Inertia visit), not derived render state.
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setMobileMenuOpen(false);
    }, [url]);

    // Click outside listener logic
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                userMenuRef.current &&
                !userMenuRef.current.contains(event.target as Node)
            ) {
                setUserMenuOpen(false);
            }

            if (
                notifRef.current &&
                !notifRef.current.contains(event.target as Node)
            ) {
                setNotifOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);

        return () =>
            document.removeEventListener('mousedown', handleClickOutside);
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
            const currentPath = url
                .split('?')[0]
                .split('#')[0]
                .replace(/\/$/, '');
            const targetPath = href
                .split('?')[0]
                .split('#')[0]
                .replace(/\/$/, '');

            return (
                currentPath === targetPath ||
                currentPath.startsWith(targetPath + '/')
            );
        },
        [url],
    );

    // Calculates which category groups should actually be open (only the active child gets opened by default)
    const openCategories = useMemo(() => {
        const result: Record<string, boolean> = {};
        NAV_CONFIG.forEach((group) => {
            const hasActiveChild = group.items.some((item) =>
                isActiveLink(item.href),
            );
            const manual = manuallyToggled[group.category];
            result[group.category] =
                manual !== undefined ? manual : hasActiveChild;
        });

        return result;
    }, [manuallyToggled, isActiveLink]);

    const toggleCategory = useCallback(
        (category: string) => {
            setManuallyToggled((prev) => ({
                ...prev,
                [category]: !openCategories[category],
            }));
        },
        [openCategories],
    );

    const toggleTheme = useCallback(() => {
        setIsDark((prev) => !prev);
    }, []);

    const closeAllDropdowns = useCallback(() => {
        setUserMenuOpen(false);
        setNotifOpen(false);
    }, []);

    return (
        <TranslationProvider>
            <FlashHandler />
            <AppToaster />
            <div className="flex h-dvh overflow-hidden bg-canvas font-sans">
                {/* ─── Mobile backdrop ─── */}
                <DrawerBackdrop
                    open={mobileMenuOpen}
                    onClose={() => setMobileMenuOpen(false)}
                />

                {/* ─── Mobile sidebar ─── */}
                <aside
                    className={`fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] flex-col border-r border-line bg-surface transition-transform duration-300 ease-premium lg:hidden ${
                        mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
                    }`}
                >
                    <DrawerHeader onClose={() => setMobileMenuOpen(false)}>
                        <BrandMark
                            icon={ShieldCheckIcon}
                            caption={`${platformName} · ${t('nav.super_admin')}`}
                            href="/dashboard/super-admin"
                        />
                    </DrawerHeader>
                    <SidebarNav
                        isOpen={true}
                        isActive={isActiveLink}
                        openCategories={openCategories}
                        onToggleCategory={toggleCategory}
                        platformName={platformName}
                        currency={currency}
                    />
                </aside>

                {/* ─── Desktop sidebar ─── */}
                <aside
                    className={`relative hidden flex-col border-r border-line bg-surface transition-[width] duration-300 ease-premium lg:flex ${
                        sidebarOpen ? 'w-64' : 'w-[68px]'
                    }`}
                >
                    <SidebarNav
                        isOpen={sidebarOpen}
                        isActive={isActiveLink}
                        openCategories={openCategories}
                        onToggleCategory={toggleCategory}
                        platformName={platformName}
                        currency={currency}
                    />
                    <button
                        type="button"
                        onClick={() => setSidebarOpen((prev) => !prev)}
                        className="absolute top-20 -right-3 z-30 rounded-md border border-line bg-surface p-1.5 text-ink-subtle shadow-surface transition-[color,transform] duration-200 ease-premium hover:text-ink active:scale-95"
                        title={
                            sidebarOpen
                                ? t('common.collapse_sidebar')
                                : t('common.expand_sidebar')
                        }
                    >
                        <ChevronLeftIcon
                            className={`h-3.5 w-3.5 transition-transform duration-300 ease-premium ${
                                !sidebarOpen && 'rotate-180'
                            }`}
                        />
                    </button>
                </aside>

                {/* ─── Main content area ─── */}
                <div className="flex min-w-0 flex-1 flex-col overflow-hidden">
                    {/* ─── Top header ─── */}
                    <header className="chrome-blur sticky top-0 z-30 flex h-14 shrink-0 items-center justify-between gap-4 border-b border-line px-4 lg:px-6">
                        <div className="flex min-w-0 flex-1 items-center gap-3">
                            <button
                                type="button"
                                onClick={() => setMobileMenuOpen(true)}
                                aria-label="Open navigation"
                                className="-ml-1.5 rounded-md p-1.5 text-ink-muted transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink lg:hidden"
                            >
                                <Bars3Icon className="h-[18px] w-[18px]" />
                            </button>
                            <div className="min-w-0">
                                <h1 className="truncate text-[14px] font-semibold tracking-tight text-ink">
                                    {resolvedTitle}
                                </h1>
                                <p className="hidden truncate text-[11.5px] text-ink-subtle sm:block">
                                    {resolvedSubtitle}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center gap-1.5 lg:gap-2">
                            <Badge
                                tone="muted"
                                variant="outline"
                                className="hidden font-mono md:inline-flex"
                                title={t('nav.billing_currency')}
                            >
                                {currency}
                            </Badge>

                            {platform?.maintenance_mode && (
                                <Badge
                                    tone="warning"
                                    dot
                                    className="hidden sm:inline-flex"
                                >
                                    {t('nav.maintenance_mode')}
                                </Badge>
                            )}

                            <LocaleSwitcher variant="full" />

                            <ThemeToggle
                                isDark={isDark}
                                onToggle={toggleTheme}
                            />

                            {/* Notifications */}
                            <div className="relative" ref={notifRef}>
                                <button
                                    type="button"
                                    onClick={() => {
                                        setNotifOpen((prev) => !prev);
                                        setUserMenuOpen(false);
                                    }}
                                    aria-label={t('common.notifications')}
                                    aria-expanded={notifOpen}
                                    className="relative rounded-md p-1.5 text-ink-subtle transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink"
                                >
                                    <BellIcon className="h-[18px] w-[18px]" />
                                    {notificationFeed.unreadCount > 0 && (
                                        <span className="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-accent px-1 text-[10px] font-semibold text-accent-ink tabular-nums">
                                            {notificationFeed.unreadCount > 99
                                                ? '99+'
                                                : notificationFeed.unreadCount}
                                        </span>
                                    )}
                                </button>

                                {notifOpen && (
                                    <NotificationsDropdown
                                        feed={notificationFeed}
                                        onClose={closeAllDropdowns}
                                    />
                                )}
                            </div>

                            {/* User menu */}
                            <div
                                className="ml-1 flex items-center border-l border-line pl-2.5"
                                ref={userMenuRef}
                            >
                                <div className="relative">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setUserMenuOpen((prev) => !prev);
                                            setNotifOpen(false);
                                        }}
                                        aria-label={t('common.user_menu')}
                                        aria-expanded={userMenuOpen}
                                        className="group flex cursor-pointer items-center gap-2.5 rounded-md focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                    >
                                        <UserMeta
                                            name={
                                                user?.name ||
                                                t('common.staff_user')
                                            }
                                            role={t('nav.super_admin')}
                                        />
                                        <UserChip name={user?.name} />
                                    </button>

                                    {userMenuOpen && (
                                        <UserMenuDropdown
                                            user={user}
                                            onClose={closeAllDropdowns}
                                        />
                                    )}
                                </div>
                            </div>
                        </div>
                    </header>

                    {/* ─── Page content ─── */}
                    <main className="flex-1 overflow-y-auto bg-canvas">
                        <div className="mx-auto w-full max-w-[1560px] px-4 py-5 sm:px-6 lg:px-8">
                            {children}
                        </div>
                    </main>
                </div>
            </div>
        </TranslationProvider>
    );
}
