import {
    Squares2X2Icon,
    MoonIcon,
    SunIcon,
    BuildingOfficeIcon,
    ChevronDownIcon,
    BeakerIcon,
    UserGroupIcon,
    ClipboardDocumentCheckIcon,
    BanknotesIcon,
    AdjustmentsHorizontalIcon,
    ShieldCheckIcon,
    Bars3Icon,
    XMarkIcon,
    Cog6ToothIcon,
    HeartIcon,
    ArrowRightStartOnRectangleIcon,
    BellIcon,
    CheckIcon,
    UserCircleIcon,
    DocumentTextIcon,
    ChevronLeftIcon,
    ClockIcon,
    QuestionMarkCircleIcon,
    SignalSlashIcon,
    PresentationChartBarIcon,
    CreditCardIcon,
    SparklesIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Link, usePage, router } from '@inertiajs/react';
import React, {
    useState,
    useEffect,
    useMemo,
    useCallback,
    memo,
    useRef,
} from 'react';
import AnnouncementBanner from '@/Components/AnnouncementBanner';
import { AppToaster } from '@/Components/AppToaster';
import { FlashHandler } from '@/Components/FlashHandler';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { Button, Dialog, Skeleton } from '@/Components/ui';
import type { BreadcrumbItem } from '@/Components/ui/Breadcrumbs';
import { BreadcrumbTrailProvider } from '@/Components/ui/PageHeader';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';
import { formatCurrency, formatDateRelative } from '@/lib/format';
import { cn } from '@/lib/utils';

// ─── Types ──────────────────────────────────────────────────────────────────

interface NavItem {
    label: string;
    labelKey: string;
    href: string;
    permission?: string;
    badge?: number;
    icon?: React.ReactNode;
}

interface NavGroup {
    category: string;
    categoryKey: string;
    icon: React.ReactNode;
    items: NavItem[];
    badge?: number;
}

interface User {
    id: number;
    name: string;
    email: string;
    roles?: { name: string }[] | string[];
    permissions?: string[];
    is_active: boolean;
    department?: { name: string } | null;
}

/**
 * Shared Inertia props this layout reads.
 *
 * `headerTitle` / `headerSubtitle` are still ACCEPTED because ~100 pages pass
 * them, but they are no longer rendered as a second header: the page title is
 * owned exclusively by `<PageHeader>` inside the page body. `headerTitle` is
 * kept for the `<title>` context only (see the effect in AppLayout).
 */
interface AppLayoutProps {
    headerTitle?: string;
    headerSubtitle?: string;
    headerAction?: React.ReactNode;
}

interface TenantSubscription {
    plan_name?: string | null;
    status?: string;
    status_label?: string;
    status_tone?: string;
    /** Amount in major units; rendered with `formatCurrency` in the tooltip. */
    price?: number | null;
    currency?: string | null;
    days_remaining?: number | null;
    grants_access?: boolean;
    is_on_trial?: boolean;
}

interface SharedTenant {
    id?: string;
    name?: string;
    slug?: string;
    subscription?: TenantSubscription | null;
    limits?: Record<string, number | null>;
    features?: Record<string, boolean>;
    usage?: Record<string, number>;
}

interface SharedPlatform {
    name?: string;
    currency?: string;
    currency_symbol?: string;
    realtime_enabled?: boolean;
    supported_locales?: string[];
}

interface AppNotification {
    id: string;
    type: string;
    message: string;
    category: string;
    link?: string | null;
    read: boolean;
    created_at: string;
}

// ─── Navigation Config ──────────────────────────────────────────────────────

const NAV_CONFIG: NavGroup[] = [
    {
        category: 'Patient Management',
        categoryKey: 'nav.category.patient_mgmt',
        icon: <UserGroupIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Patient Directory',
                labelKey: 'nav.patient_directory',
                href: '/patients',
                permission: 'view-patients',
            },
            {
                label: 'Admissions',
                labelKey: 'nav.admissions',
                href: '/admissions',
                permission: 'view-patients',
            },
            // { label: 'Vitals', labelKey: 'nav.vitals', href: '/vitals', permission: 'view-patients' },
            {
                label: 'Appointments',
                labelKey: 'nav.appointments',
                href: '/appointments',
                permission: 'view-appointments',
            },
            {
                label: 'Appt. Calendar',
                labelKey: 'nav.appt_calendar',
                href: '/appointments/calendar',
                permission: 'view-appointments',
            },
        ],
    },
    {
        category: 'Clinical',
        categoryKey: 'nav.category.clinical',
        icon: <ClipboardDocumentCheckIcon className="h-4 w-4" />,
        items: [
            {
                label: 'New Consultation',
                labelKey: 'nav.new_consultation',
                href: '/consultations/create',
                permission: 'view-consultations',
            },
            {
                label: 'Sick Leave Cert.',
                labelKey: 'nav.sick_leave_cert',
                href: '/certificates/sick-leave',
                permission: 'create-consultations',
            },
            {
                label: 'Medical Certificates',
                labelKey: 'nav.medical_certs',
                href: '/certificates/medical',
                permission: 'create-consultations',
            },
            {
                label: 'Death Certificates',
                labelKey: 'nav.death_certs',
                href: '/certificates/death',
                permission: 'create-consultations',
            },
        ],
    },
    {
        category: 'Surgery & Emergency',
        categoryKey: 'nav.category.surgery_emergency',
        icon: <HeartIcon className="h-4 w-4" />,
        items: [
            {
                label: 'All Surgeries',
                labelKey: 'nav.all_surgeries',
                href: '/surgeries',
                permission: 'create-consultations',
            },
            {
                label: 'Emergency Cases',
                labelKey: 'nav.emergency_cases',
                href: '/emergencies',
                permission: 'create-admissions',
            },
        ],
    },

    {
        category: 'Lab & Diagnostics',
        categoryKey: 'nav.category.lab_diagnostics',
        icon: <BeakerIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Test Catalog',
                labelKey: 'nav.test_catalog',
                href: '/lab/tests',
                permission: 'view-lab-tests',
            },
            {
                label: 'Lab Requests',
                labelKey: 'nav.lab_requests',
                href: '/lab/requests',
                permission: 'view-lab-tests',
            },
            {
                label: 'Lab Results',
                labelKey: 'nav.lab_results',
                href: '/lab/results',
                permission: 'view-lab-results',
            },
        ],
    },
    {
        category: 'Radiology',
        categoryKey: 'nav.category.radiology',
        icon: <PresentationChartBarIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Radiology Requests',
                labelKey: 'nav.radiology_requests',
                href: '/radiology/requests',
                permission: 'view-radiology',
            },
        ],
    },
    {
        category: 'Pharmacy',
        categoryKey: 'nav.category.pharmacy',
        icon: <AdjustmentsHorizontalIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Drug Inventory',
                labelKey: 'nav.drug_inventory',
                href: '/pharmacy/drugs',
                permission: 'view-drugs',
            },
            {
                label: 'Dispensation',
                labelKey: 'nav.dispensation',
                href: '/pharmacy/dispensation',
                permission: 'dispense-drugs',
            },
            {
                // Supplies were a separate top-level module with its own
                // category; inventory is part of pharmacy operations.
                label: 'Inventory',
                labelKey: 'nav.supply_inventory',
                href: '/supplies',
                permission: 'view-supplies',
            },
        ],
    },
    {
        category: 'Infrastructure',
        categoryKey: 'nav.category.infrastructure',
        icon: <BuildingOfficeIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Wards',
                labelKey: 'nav.wards',
                href: '/wards',
                permission: 'manage-infrastructure',
            },
            {
                label: 'Beds',
                labelKey: 'nav.beds',
                href: '/beds',
                permission: 'manage-infrastructure',
            },
            {
                label: 'Departments',
                labelKey: 'nav.departments',
                href: '/departments',
                permission: 'manage-departments',
            },
        ],
    },
    {
        category: 'Finance',
        categoryKey: 'nav.category.finance',
        icon: <BanknotesIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Services Catalog',
                labelKey: 'nav.services_catalog',
                href: '/finance/services',
                permission: 'view-finance',
            },
            {
                label: 'Invoices',
                labelKey: 'nav.invoices',
                href: '/finance/invoices',
                permission: 'view-finance',
            },
            {
                label: 'Revenue Analytics',
                labelKey: 'nav.revenue_analytics',
                href: '/finance/revenue',
                permission: 'view-finance',
            },
            {
                label: 'Expenditures',
                labelKey: 'nav.expenditures',
                href: '/finance/expenditures',
                permission: 'view-finance',
            },
            {
                label: 'Payers (Insurance)',
                labelKey: 'nav.payers',
                href: '/finance/payers',
                permission: 'view-finance',
            },
            {
                label: 'Patient Insurances',
                labelKey: 'nav.patient_insurances',
                href: '/finance/patient-insurances',
                permission: 'view-finance',
            },
            {
                label: 'Claims',
                labelKey: 'nav.claims',
                href: '/finance/claims',
                permission: 'view-finance',
            },
            {
                label: 'MINSANTE Reports',
                labelKey: 'nav.minsante_reports',
                href: '/finance/reports/rpm',
                permission: 'view-finance',
            },
        ],
    },
    {
        category: 'Cash Register',
        categoryKey: 'nav.category.cash_register',
        icon: <CreditCardIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Caisse',
                labelKey: 'nav.caisse',
                href: '/caisse',
                permission: 'manage-caisse',
            },
            {
                label: 'Open Shift',
                labelKey: 'nav.open_shift',
                href: '/caisse',
                permission: 'manage-caisse',
            },
            {
                label: 'Record Payment',
                labelKey: 'nav.record_payment',
                href: '/caisse',
                permission: 'manage-caisse',
            },
        ],
    },
    {
        category: 'Administration',
        categoryKey: 'nav.category.administration',
        icon: <ShieldCheckIcon className="h-4 w-4" />,
        items: [
            {
                label: 'User Management',
                labelKey: 'nav.user_management',
                href: '/users',
                permission: 'manage-users',
            },
            {
                label: 'Add User',
                labelKey: 'nav.add_user',
                href: '/users/create',
                permission: 'manage-users',
            },
            {
                label: 'Roles & Permissions',
                labelKey: 'nav.roles_permissions',
                href: '/roles',
                permission: 'manage-roles',
            },
            {
                label: 'Shifts Roster',
                labelKey: 'nav.shifts_roster',
                href: '/shifts',
                permission: 'manage-users',
            },
            {
                label: 'Audit Logs',
                labelKey: 'nav.audit_logs',
                href: '/audit-logs',
                permission: 'view-audit-logs',
            },
            {
                label: 'Billing Settings',
                labelKey: 'nav.billing_settings',
                href: '/billing',
                permission: 'manage-users',
            },
        ],
    },
];

const DASHBOARDS: Record<string, string> = {
    admin: '/dashboard/admin',
    doctor: '/dashboard',
    reception: '/dashboard',
    lab_tech: '/dashboard',
    pharma: '/dashboard',
    nurse: '/dashboard',
};

// Roles that can access the financial/admin dashboard

// ─── Helpers ────────────────────────────────────────────────────────────────

function getRoleName(role: any): string {
    return typeof role === 'string' ? role : (role?.name ?? '');
}

function getUserDashboard(user: User | null): string {
    if (!user?.roles?.length) {
        return '/dashboard/admin';
    }

    return DASHBOARDS[getRoleName(user.roles[0])] || '/dashboard/admin';
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
        admin: 'bg-accent-soft text-accent-ink border-accent/30 ',
        doctor: 'bg-accent-soft text-accent-ink border-accent/30 ',
        reception: 'bg-positive-soft text-positive-ink border-positive/40 ',
        lab_tech: 'bg-warning-soft text-warning-ink border-warning/40 ',
        pharma: 'bg-accent-soft text-accent-ink border-accent/30 ',
        nurse: 'bg-accent-soft text-accent-ink border-accent/30 ',
    };

    return map[role] || 'bg-surface-muted text-ink-muted border-line ';
}

// ─── Sidebar Navigation ─────────────────────────────────────────────────────

const SidebarNav = memo(function SidebarNav({
    isOpen,
    filteredNav,
    openCategories,
    onToggleCategory,
    dashboardUrl,
    isActive,
}: {
    isOpen: boolean;
    filteredNav: NavGroup[];
    openCategories: Record<string, boolean>;
    onToggleCategory: (category: string) => void;
    dashboardUrl: string;
    isActive: (href: string) => boolean;
}) {
    const { t } = useTranslation();
    const isDashboardActive = isActive(dashboardUrl);

    return (
        <>
            {/* Logo Area */}
            <div className="flex h-14 shrink-0 items-center border-b border-line px-3.5">
                <Link
                    href={dashboardUrl}
                    className="flex w-full items-center gap-2.5 overflow-hidden"
                >
                    <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-ink text-ink-inverse">
                        <HeartIcon className="h-4 w-4" />
                    </span>
                    {isOpen && (
                        <div className="min-w-0 overflow-hidden">
                            <span className="block truncate text-[14px] font-semibold tracking-tight whitespace-nowrap text-ink">
                                SanaGo
                            </span>
                            <span className="-mt-0.5 block truncate text-[9.5px] font-medium tracking-[0.18em] text-ink-subtle uppercase">
                                {t('layout.hms_platform')}
                            </span>
                        </div>
                    )}
                </Link>
            </div>

            {/* Navigation */}
            <nav className="flex-1 space-y-0.5 overflow-y-auto overscroll-contain px-2.5 py-3">
                {/* Dashboard Link */}
                <Link
                    href={dashboardUrl}
                    prefetch
                    aria-current={isDashboardActive ? 'page' : undefined}
                    className={`group relative flex items-center gap-2.5 rounded-md px-2.5 py-2 transition-[background-color,color] duration-200 ease-premium ${
                        isDashboardActive
                            ? 'bg-accent-soft font-semibold text-accent-ink'
                            : 'text-ink-muted hover:bg-surface-hover hover:text-ink'
                    }`}
                >
                    {/* 2px accent rail on the active row — the only edge marker
                        in the system, so "where am I" never depends on color
                        alone (it survives grayscale and color-blindness). */}
                    <span
                        aria-hidden="true"
                        className={`absolute top-1.5 bottom-1.5 left-0 w-0.5 rounded-full transition-opacity duration-200 ease-premium ${
                            isDashboardActive ? 'bg-accent' : 'opacity-0'
                        }`}
                    />
                    <Squares2X2Icon
                        className={`h-[17px] w-[17px] shrink-0 transition-colors ${isDashboardActive ? 'text-accent' : 'text-ink-subtle group-hover:text-ink-muted'}`}
                    />
                    {isOpen && (
                        <span className="truncate text-[13px] font-medium">
                            {t('nav.dashboard')}
                        </span>
                    )}
                </Link>

                {/* Nav Groups */}
                {filteredNav.map((group) => {
                    const hasActiveChild = group.items.some((item) =>
                        isActive(item.href),
                    );
                    const isExpanded = openCategories[group.category];

                    return (
                        <div key={group.category} className="space-y-0.5">
                            <button
                                type="button"
                                onClick={() => onToggleCategory(group.category)}
                                aria-expanded={Boolean(isExpanded)}
                                aria-controls={`nav-group-${group.category.replace(/\s+/g, '-')}`}
                                className={`group flex w-full items-center justify-between rounded-md px-2.5 py-2 text-left transition-[background-color,color] duration-200 ease-premium ${
                                    hasActiveChild
                                        ? 'text-ink'
                                        : 'text-ink-muted hover:bg-surface-hover hover:text-ink'
                                }`}
                            >
                                <div className="flex min-w-0 items-center gap-2.5">
                                    <div
                                        className={`shrink-0 transition-colors ${isExpanded || hasActiveChild ? 'text-accent' : 'text-ink-subtle group-hover:text-ink-muted'}`}
                                    >
                                        {group.icon}
                                    </div>
                                    {isOpen && (
                                        <span className="truncate text-[12.5px] font-medium tracking-tight">
                                            {t(group.categoryKey)}
                                        </span>
                                    )}
                                </div>
                                {isOpen && (
                                    <div className="flex shrink-0 items-center gap-1.5">
                                        {group.badge && (
                                            <span className="rounded-full bg-accent-soft px-1.5 py-0.5 text-[9px] font-semibold text-accent-ink tabular-nums">
                                                {group.badge}
                                            </span>
                                        )}
                                        <ChevronDownIcon
                                            className={`h-3 w-3 text-ink-subtle transition-transform duration-200 ease-premium ${isExpanded ? 'rotate-180' : ''}`}
                                        />
                                    </div>
                                )}
                            </button>

                            {isOpen && isExpanded && (
                                <div
                                    id={`nav-group-${group.category.replace(/\s+/g, '-')}`}
                                    className="relative ml-3.5 space-y-0.5 border-l border-line py-1 pl-2.5"
                                >
                                    {group.items.map((item) => {
                                        const isItemActive = isActive(
                                            item.href,
                                        );

                                        return (
                                            <Link
                                                key={item.href}
                                                href={item.href}
                                                prefetch
                                                aria-current={
                                                    isItemActive
                                                        ? 'page'
                                                        : undefined
                                                }
                                                className={`group/item relative flex items-center justify-between rounded-md py-1.5 pr-2 pl-2.5 text-[12.5px] transition-[background-color,color] duration-150 ease-premium ${
                                                    isItemActive
                                                        ? 'bg-accent-soft font-semibold text-accent-ink'
                                                        : 'text-ink-muted hover:bg-surface-hover hover:text-ink'
                                                }`}
                                            >
                                                {isItemActive && (
                                                    <span
                                                        aria-hidden="true"
                                                        className="absolute top-1.5 bottom-1.5 left-0 w-0.5 rounded-full bg-accent"
                                                    />
                                                )}
                                                <span className="truncate">
                                                    {t(item.labelKey)}
                                                </span>
                                                {item.badge && (
                                                    <span
                                                        className={`rounded-full px-1.5 py-0.5 text-[9px] font-semibold tabular-nums ${isItemActive ? 'bg-accent/12 text-accent-ink' : 'bg-surface-inset text-ink-subtle'}`}
                                                    >
                                                        {item.badge}
                                                    </span>
                                                )}
                                            </Link>
                                        );
                                    })}
                                </div>
                            )}
                        </div>
                    );
                })}
            </nav>

            {/* Footer Links */}
            <div className="space-y-0.5 border-t border-line p-2.5">
                <Link
                    href="/support"
                    prefetch
                    className="group flex items-center gap-2.5 rounded-md px-2.5 py-2 text-ink-muted transition-[background-color,color] duration-150 ease-premium hover:bg-surface-hover hover:text-ink"
                >
                    <QuestionMarkCircleIcon className="h-[17px] w-[17px] shrink-0 text-ink-subtle transition-colors group-hover:text-ink-muted" />
                    {isOpen && (
                        <span className="truncate text-[12.5px] font-medium">
                            {t('nav.category.support')}
                        </span>
                    )}
                </Link>
                <Link
                    href="/settings"
                    prefetch
                    className="group flex items-center gap-2.5 rounded-md px-2.5 py-2 text-ink-muted transition-[background-color,color] duration-150 ease-premium hover:bg-surface-hover hover:text-ink"
                >
                    <Cog6ToothIcon className="h-[17px] w-[17px] shrink-0 text-ink-subtle transition-colors group-hover:text-ink-muted" />
                    {isOpen && (
                        <span className="truncate text-[12.5px] font-medium">
                            {t('nav.settings')}
                        </span>
                    )}
                </Link>
            </div>
        </>
    );
});

// ─── Notification centre ────────────────────────────────────────────────────

/** XSRF cookie name Laravel sets, used for the non-Inertia mark-read call. */
function xsrfToken(): string {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : '';
}

function fetchJson(url: string, init: RequestInit = {}) {
    return fetch(url, {
        credentials: 'same-origin',
        ...init,
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': xsrfToken(),
            ...(init.headers ?? {}),
        },
    });
}

/**
 * Real-time notification state for the bell.
 *
 * Transport: Laravel Echo on the user's private channel
 * (`App.Models.User.{id}`, the channel `User::receivesBroadcastNotificationsOn`
 * declares) listening for `.Illuminate\Notifications\Events\BroadcastNotificationCreated`.
 *
 * Fallback: when `window.Echo` is absent — no VITE_REVERB_APP_KEY, the server
 * disabled realtime, or the socket errored/disconnected — the hook polls
 * `/notifications/latest` every 60 s instead. Listeners and timers are always
 * torn down on unmount.
 */
function useNotificationFeed(userId?: number, realtimeEnabled = true) {
    const [notifications, setNotifications] = useState<AppNotification[]>([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [loading, setLoading] = useState(true);
    // Optimistic: if Echo exists and we subscribe successfully, real-time is
    // assumed live. Transport errors flip it off and start the polling timer.
    const [connected, setConnected] = useState(
        () => typeof window !== 'undefined' && Boolean(window.Echo),
    );

    const load = useCallback(() => {
        return fetchJson('/notifications/latest')
            .then((r) => (r.ok ? r.json() : Promise.reject(r)))
            .then((data) => {
                setNotifications(
                    (data.notifications ?? []).map(
                        (n: Record<string, unknown>) => ({
                            id: String(n.id),
                            type: String(n.type ?? 'info'),
                            message: String(n.message ?? ''),
                            category: String(n.category ?? 'general'),
                            link: (n.link as string | null) ?? null,
                            read: n.read !== false,
                            created_at: String(n.created_at ?? ''),
                        }),
                    ),
                );
                setUnreadCount(Number(data.unreadCount ?? 0));
            })
            .catch(() => {
                /* offline or unauthenticated — the bell degrades quietly */
            })
            .finally(() => setLoading(false));
    }, []);

    useEffect(() => {
        load();
    }, [load]);

    // Prepend a live notification and bump the badge without a round-trip.
    const pushNotification = useCallback((payload: Record<string, any>) => {
        const id = String(payload.id ?? `${Date.now()}`);
        const data = (payload.data ?? payload) as Record<string, any>;

        const incoming: AppNotification = {
            id,
            type: String(data.type_label ?? data.type ?? 'info'),
            message: String(data.message ?? data.title ?? ''),
            category: String(data.category ?? 'general'),
            link: (data.link as string | null) ?? null,
            read: false,
            created_at: String(payload.created_at ?? new Date().toISOString()),
        };

        setNotifications((prev) => {
            if (prev.some((n) => n.id === incoming.id)) {
                return prev;
            }

            return [incoming, ...prev].slice(0, 12);
        });
        setUnreadCount((prev) => prev + 1);
    }, []);

    // ── Real-time transport ──
    useEffect(() => {
        if (!userId || !realtimeEnabled || typeof window === 'undefined') {
            return;
        }

        const echo = window.Echo;

        if (!echo) {
            // No Echo instance: polling below keeps the bell fresh.
            return;
        }

        const channelName = `App.Models.User.${userId}`;
        let channel: ReturnType<typeof echo.private> | undefined;

        try {
            channel = echo.private(channelName);
            channel.notification((payload: Record<string, any>) =>
                pushNotification(payload),
            );
        } catch {
            // Subscribing threw: stay on polling. Deferring the state write to a
            // microtask keeps it out of the effect body (no cascading render).
            queueMicrotask(() => setConnected(false));

            return;
        }

        // Pusher surfaces transport failures on the connector.
        const connector = (
            echo as unknown as {
                connector?: {
                    pusher?: {
                        connection?: {
                            bind?: (event: string, cb: () => void) => void;
                            unbind?: (event: string, cb: () => void) => void;
                        };
                    };
                };
            }
        ).connector;

        const pusherConnection = connector?.pusher?.connection;
        // Bound to the socket, not to render: these are exactly the
        // "subscribe to an external system" callbacks effects are for.
        const handleDisconnect = () => setConnected(false);
        const handleConnect = () => setConnected(true);

        pusherConnection?.bind?.('error', handleDisconnect);
        pusherConnection?.bind?.('unavailable', handleDisconnect);
        pusherConnection?.bind?.('disconnected', handleDisconnect);
        pusherConnection?.bind?.('connected', handleConnect);

        return () => {
            pusherConnection?.unbind?.('error', handleDisconnect);
            pusherConnection?.unbind?.('unavailable', handleDisconnect);
            pusherConnection?.unbind?.('disconnected', handleDisconnect);
            pusherConnection?.unbind?.('connected', handleConnect);

            try {
                channel?.stopListening(
                    '.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
                );
                echo.leave(channelName);
            } catch {
                /* already gone */
            }

            queueMicrotask(() => setConnected(false));
        };
    }, [userId, realtimeEnabled, pushNotification]);

    // ── Polling fallback: only while the socket is not delivering ──
    useEffect(() => {
        if (connected) {
            return;
        }

        const timer = window.setInterval(load, 60_000);

        return () => window.clearInterval(timer);
    }, [connected, load]);

    const markRead = useCallback(
        (id: string) => {
            setNotifications((prev) =>
                prev.map((n) => (n.id === id ? { ...n, read: true } : n)),
            );
            setUnreadCount((prev) => Math.max(0, prev - 1));

            fetchJson(`/notifications/${id}/read`, { method: 'POST' }).catch(
                () => load(),
            );
        },
        [load],
    );

    const markAllRead = useCallback(() => {
        setNotifications((prev) => prev.map((n) => ({ ...n, read: true })));
        setUnreadCount(0);

        router.post(
            '/notifications/mark-all-read',
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onError: () => load(),
            },
        );
    }, [load]);

    return {
        notifications,
        unreadCount,
        loading,
        connected,
        markRead,
        markAllRead,
        reload: load,
    };
}

const CATEGORY_ICONS: Record<string, React.ReactNode> = {
    clinical: <DocumentTextIcon className="h-4 w-4" />,
    lab: <BeakerIcon className="h-4 w-4" />,
    radiology: <PresentationChartBarIcon className="h-4 w-4" />,
    pharmacy: <ClockIcon className="h-4 w-4" />,
    finance: <BanknotesIcon className="h-4 w-4" />,
    billing: <CreditCardIcon className="h-4 w-4" />,
    general: <BellIcon className="h-4 w-4" />,
};

const TYPE_TONE: Record<string, string> = {
    info: 'bg-accent-soft text-accent-ink',
    success: 'bg-positive-soft text-positive-ink',
    warning: 'bg-warning-soft text-warning-ink',
    error: 'bg-critical-soft text-critical-ink',
};

/**
 * NotificationsDropdown — the bell panel. Unread count, per-item mark-as-read,
 * mark-all-read, an empty state and a link to the full inbox. Escape is handled
 * by the layout; `aria-expanded` / `aria-haspopup` live on the trigger.
 */
const NotificationsDropdown = memo(function NotificationsDropdown({
    feed,
    locale,
    onClose,
}: {
    feed: ReturnType<typeof useNotificationFeed>;
    locale: string;
    onClose: () => void;
}) {
    const { t } = useTranslation();
    const { notifications, unreadCount, loading, markRead, markAllRead } = feed;
    const [filter, setFilter] = useState<'all' | 'unread'>('all');

    const visible =
        filter === 'unread'
            ? notifications.filter((n) => !n.read)
            : notifications;

    const openNotification = (notification: AppNotification) => {
        if (!notification.read) {
            markRead(notification.id);
        }

        onClose();

        if (notification.link) {
            router.visit(notification.link);
        }
    };

    return (
        <div
            role="dialog"
            aria-label={t('common.notifications')}
            className="animate-pop-in absolute top-full right-0 z-50 mt-2 w-[min(24rem,calc(100vw-1.5rem))] overflow-hidden rounded-xl border border-line bg-surface shadow-float"
        >
            <div className="flex items-center justify-between gap-2 border-b border-line px-3.5 py-2.5">
                <div className="flex items-center gap-2">
                    <span className="text-[12.5px] font-semibold text-ink">
                        {t('common.notifications')}
                    </span>
                    {unreadCount > 0 && (
                        <span className="rounded-full bg-accent px-1.5 py-0.5 text-[10px] font-semibold text-ink-inverse tabular-nums">
                            {unreadCount}
                        </span>
                    )}
                </div>

                <div className="flex items-center gap-1.5">
                    <div className="flex rounded-md bg-surface-inset p-0.5">
                        {(['all', 'unread'] as const).map((value) => (
                            <button
                                key={value}
                                type="button"
                                onClick={() => setFilter(value)}
                                aria-pressed={filter === value}
                                className={cn(
                                    'rounded-sm px-2 py-0.5 text-[11px] font-medium transition-colors duration-150 ease-premium',
                                    filter === value
                                        ? 'bg-surface text-ink shadow-surface'
                                        : 'text-ink-subtle hover:text-ink',
                                )}
                            >
                                {t(`common.${value}`)}
                            </button>
                        ))}
                    </div>
                    {unreadCount > 0 && (
                        <button
                            type="button"
                            onClick={markAllRead}
                            title={t('common.mark_all_read')}
                            className="rounded-md px-1.5 py-1 text-[11px] font-medium text-accent-ink transition-colors duration-150 hover:bg-accent-soft"
                        >
                            <CheckIcon className="h-3.5 w-3.5" />
                            <span className="sr-only">
                                {t('common.mark_all_read')}
                            </span>
                        </button>
                    )}
                </div>
            </div>

            <div className="max-h-[22rem] overflow-y-auto overscroll-contain">
                {loading ? (
                    <div className="space-y-2 p-3">
                        {[0, 1, 2].map((i) => (
                            <div key={i} className="flex gap-2.5">
                                <Skeleton className="h-7 w-7" rounded="lg" />
                                <div className="flex-1 space-y-1.5">
                                    <Skeleton className="h-3 w-4/5" />
                                    <Skeleton className="h-2.5 w-1/3" />
                                </div>
                            </div>
                        ))}
                    </div>
                ) : visible.length > 0 ? (
                    <ul className="divide-y divide-line">
                        {visible.map((notification) => (
                            <li key={notification.id}>
                                <div
                                    className={cn(
                                        'group flex items-start gap-2.5 px-3.5 py-2.5 transition-colors duration-150',
                                        notification.read
                                            ? 'bg-surface hover:bg-surface-hover'
                                            : 'bg-accent-soft/60 hover:bg-accent-soft',
                                    )}
                                >
                                    <button
                                        type="button"
                                        onClick={() =>
                                            openNotification(notification)
                                        }
                                        className="flex min-w-0 flex-1 items-start gap-2.5 text-left"
                                    >
                                        <span
                                            className={cn(
                                                'mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg',
                                                TYPE_TONE[notification.type] ??
                                                    TYPE_TONE.info,
                                            )}
                                        >
                                            {CATEGORY_ICONS[
                                                notification.category
                                            ] ?? CATEGORY_ICONS.general}
                                        </span>
                                        <span className="min-w-0 flex-1">
                                            <span
                                                className={cn(
                                                    'block truncate text-[12.5px] text-ink',
                                                    !notification.read &&
                                                        'font-semibold',
                                                )}
                                            >
                                                {notification.message}
                                            </span>
                                            <span className="mt-0.5 block text-[11px] text-ink-subtle tabular-nums">
                                                {formatDateRelative(
                                                    notification.created_at,
                                                    { locale },
                                                )}
                                            </span>
                                        </span>
                                    </button>

                                    {!notification.read && (
                                        <button
                                            type="button"
                                            onClick={() =>
                                                markRead(notification.id)
                                            }
                                            title={t('common.mark_as_read')}
                                            className="mt-0.5 shrink-0 rounded-md p-1 text-ink-subtle opacity-0 transition-[opacity,color,background-color] duration-150 group-hover:opacity-100 hover:bg-surface-inset hover:text-accent-ink focus-visible:opacity-100"
                                        >
                                            <CheckIcon className="h-3.5 w-3.5" />
                                            <span className="sr-only">
                                                {t('common.mark_as_read')}
                                            </span>
                                        </button>
                                    )}
                                </div>
                            </li>
                        ))}
                    </ul>
                ) : (
                    <div className="px-4 py-9 text-center">
                        <CheckIcon className="mx-auto mb-2 h-6 w-6 text-ink-subtle" />
                        <p className="text-[12.5px] font-medium text-ink-muted">
                            {t('common.all_caught_up')}
                        </p>
                        <p className="mt-0.5 text-[11px] text-ink-subtle">
                            {t('common.no_notifications')}
                        </p>
                    </div>
                )}
            </div>

            <div className="border-t border-line bg-surface-muted px-3 py-2">
                <Link
                    href="/notifications"
                    onClick={onClose}
                    className="flex items-center justify-center gap-1.5 rounded-md py-1 text-[12px] font-semibold text-accent-ink transition-colors duration-150 hover:text-accent"
                >
                    {t('common.view_all_notifications')}
                </Link>
            </div>
        </div>
    );
});

/**
 * SubscriptionChip — quiet plan/status pill in the top bar. Warning tone when
 * the subscription is inside its last week or already lapsed; links to billing.
 */
const SubscriptionChip = memo(function SubscriptionChip({
    subscription,
    currency,
    currencySymbol,
}: {
    subscription?: TenantSubscription | null;
    currency?: string;
    currencySymbol?: string;
}) {
    const { t } = useTranslation();

    if (!subscription?.plan_name) {
        return null;
    }

    const days = subscription.days_remaining ?? null;
    const expiring = days !== null && days <= 7;
    const expired = days !== null && days <= 0;
    const tone =
        expired || !subscription.grants_access
            ? 'critical'
            : expiring
              ? 'warning'
              : 'neutral';

    const toneClasses: Record<string, string> = {
        critical: 'border-critical/30 bg-critical-soft text-critical-ink',
        warning: 'border-warning/30 bg-warning-soft text-warning-ink',
        neutral: 'border-line bg-surface-muted text-ink-muted',
    };

    const suffix =
        days === null
            ? null
            : days <= 0
              ? t('layout.subscription_ended', 'ended')
              : t('layout.subscription_days_left', ':days days left').replace(
                    ':days',
                    String(days),
                );

    // Money is formatted once, here, so the chip's tooltip matches every other
    // amount in the app (`1 500 000 FCFA`, never `$1,500,000.00`).
    const priceLabel =
        typeof subscription.price === 'number' && subscription.price > 0
            ? formatCurrency(
                  subscription.price,
                  subscription.currency ?? currency ?? 'XAF',
                  currencySymbol,
              )
            : null;

    const tooltip = [
        t('layout.subscription', 'Subscription'),
        subscription.plan_name,
        priceLabel,
    ]
        .filter(Boolean)
        .join(' · ');

    return (
        <Link
            href="/billing"
            className={cn(
                'hidden items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11px] font-medium whitespace-nowrap transition-colors duration-200 ease-premium hover:opacity-85 xl:inline-flex',
                toneClasses[tone],
            )}
            title={tooltip}
            aria-label={tooltip}
        >
            {subscription.is_on_trial && (
                <SparklesIcon className="h-3 w-3 shrink-0" />
            )}
            {(expiring || expired) && !subscription.is_on_trial && (
                <ExclamationTriangleIcon className="h-3 w-3 shrink-0" />
            )}
            <span className="truncate">{subscription.plan_name}</span>
            {suffix && (
                <>
                    <span aria-hidden="true" className="opacity-40">
                        ·
                    </span>
                    <span className="tabular-nums">{suffix}</span>
                </>
            )}
        </Link>
    );
});

// ─── User Menu Dropdown ─────────────────────────────────────────────────────

const UserMenuDropdown = memo(function UserMenuDropdown({
    user,
    userRole,
    onClose,
    onLogoutClick,
}: {
    user: User | null;
    userRole: string;
    onClose: () => void;
    onLogoutClick: () => void;
}) {
    const { t } = useTranslation();

    return (
        <div className="absolute top-full right-0 z-50 mt-2.5 w-72 overflow-hidden rounded-2xl border border-line bg-surface shadow-float">
            <div className="border-b border-line bg-surface-muted p-4">
                <div className="flex items-center gap-3">
                    <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-accent text-base font-bold text-ink-inverse shadow-raised">
                        {user ? getInitials(user.name) : '??'}
                    </div>
                    <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-bold text-ink">
                            {user?.name}
                        </p>
                        <p className="truncate text-xs text-ink-subtle">
                            {user?.email}
                        </p>
                        <div className="mt-1.5 flex items-center gap-2">
                            <span
                                className={`rounded-md border px-2 py-0.5 text-[9px] font-bold uppercase ${getRoleBadge(userRole)}`}
                            >
                                {userRole.replace('_', ' ')}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div className="py-1.5">
                {[
                    {
                        href: '/profile',
                        icon: UserCircleIcon,
                        label: t('nav.my_profile'),
                        desc: t('nav.profile_description'),
                    },
                    {
                        href: '/settings',
                        icon: Cog6ToothIcon,
                        label: t('nav.settings'),
                        desc: t('nav.settings_description'),
                    },
                ].map((item) => (
                    <Link
                        key={item.href}
                        href={item.href}
                        className="group flex items-center gap-3 px-4 py-2.5 text-sm text-ink-muted transition-colors hover:bg-surface-hover"
                        onClick={onClose}
                    >
                        <div className="rounded-lg bg-surface-inset p-1.5 transition-colors group-hover:bg-accent-soft">
                            <item.icon className="h-4 w-4 text-ink-subtle transition-colors group-hover:text-accent" />
                        </div>
                        <div className="flex-1">
                            <p className="font-medium">{item.label}</p>
                            <p className="text-[10px] text-ink-subtle">
                                {item.desc}
                            </p>
                        </div>
                    </Link>
                ))}
            </div>

            <div className="border-t border-line pt-1.5">
                <button
                    onClick={() => {
                        onClose();
                        onLogoutClick();
                    }}
                    className="group flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-critical-ink transition-colors hover:bg-critical-soft"
                >
                    <div className="rounded-lg bg-critical-soft p-1.5 transition-colors group-hover:bg-critical-soft">
                        <ArrowRightStartOnRectangleIcon className="h-4 w-4" />
                    </div>
                    <div className="flex-1">
                        <p className="font-medium">{t('nav.sign_out')}</p>
                        <p className="text-[10px] text-red-400">
                            {t('nav.sign_out_description')}
                        </p>
                    </div>
                </button>
            </div>
        </div>
    );
});

// ─── Breadcrumb derivation ──────────────────────────────────────────────────

const SEGMENT_LABELS: Record<string, string> = {
    create: 'New',
    edit: 'Edit',
    show: 'Details',
    calendar: 'Calendar',
    requests: 'Requests',
    results: 'Results',
    index: 'Overview',
    analytics: 'Analytics',
    settings: 'Settings',
};

function titleCase(segment: string): string {
    return segment
        .split('-')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
}

/**
 * Derives a breadcrumb trail from the current URL, using NAV_CONFIG as the
 * source of truth for module labels.
 *
 * This is the fallback for pages that never pass `breadcrumbs` to
 * `<PageHeader>`: the layout publishes the result through
 * `BreadcrumbTrailProvider`, so every screen gets a trail (and a way back)
 * without touching page markup. Trailing segments that are numeric ids or
 * UUIDs are dropped; `create` / `edit` are translated into a readable label.
 */
function deriveBreadcrumbs(
    url: string,
    t: (key: string, fallback?: string) => string,
): BreadcrumbItem[] {
    const path = url.split('?')[0].split('#')[0];
    const segments = path.split('/').filter(Boolean);

    if (segments.length === 0) {
        return [];
    }

    // Match the longest NAV_CONFIG href that prefixes the current path.
    const trail: BreadcrumbItem[] = [];
    const navItem = NAV_CONFIG.flatMap((group) =>
        group.items.map((item) => ({ ...item, category: group.categoryKey })),
    )
        .filter(
            (item) => path === item.href || path.startsWith(item.href + '/'),
        )
        .sort((a, b) => b.href.length - a.href.length)[0];

    if (navItem) {
        const parent = navItem.href.split('/').filter(Boolean)[0];
        trail.push({
            label: t(
                `nav.category.${navItem.category.split('.').pop()}`,
                titleCase(parent ?? ''),
            ),
            href: `/${parent}`,
        });
        trail.push({
            label: t(navItem.labelKey, navItem.label),
            href: navItem.href,
        });

        // Only the "sub-page" remainder is appended (e.g. /patients/42/notes).
        const rest = segments.slice(
            navItem.href.split('/').filter(Boolean).length,
        );

        rest.forEach((segment, i) => {
            if (/^[0-9a-f-]{8,}$/i.test(segment) || /^\d+$/.test(segment)) {
                return;
            }

            const label = SEGMENT_LABELS[segment] ?? titleCase(segment);

            trail.push({
                label,
                href:
                    '/' +
                    segments
                        .slice(0, segments.length - rest.length + i + 1)
                        .join('/'),
            });
        });

        return trail;
    }

    // No nav entry: build module → page from the raw segments.
    const moduleSegment = segments[0];

    trail.push({
        label: titleCase(moduleSegment),
        href: `/${moduleSegment}`,
    });

    segments.slice(1).forEach((segment) => {
        if (/^\d+$/.test(segment)) {
            return;
        }

        trail.push({
            label: SEGMENT_LABELS[segment] ?? titleCase(segment),
        });
    });

    // Suppress a single-segment trail; the title already says it.
    return trail.length > 1 ? trail : [];
}

// ─── Main App Layout ────────────────────────────────────────────────────────

export default function AppLayout({
    children,
    headerTitle,
    // `headerSubtitle` is still accepted for the ~100 pages that pass it, but
    // it is deliberately NOT rendered as a second header — the page title and
    // its supporting copy belong to <PageHeader> inside the page body.
    headerSubtitle: _headerSubtitle,
    headerAction,
}: React.PropsWithChildren<AppLayoutProps>) {
    void _headerSubtitle;
    const { url, props } = usePage();
    const { t, locale } = useTranslation();
    const user = (props.auth as any)?.user as User | null;
    const tenant = (props as any).tenant as SharedTenant | null | undefined;
    const platform = (props as any).platform as SharedPlatform | undefined;
    const isAdmin =
        user?.roles?.some((r: any) => r === 'admin' || r?.name === 'admin') ??
        false;

    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [manuallyToggled, setManuallyToggled] = useState<
        Record<string, boolean>
    >({});
    const [userMenuOpen, setUserMenuOpen] = useState(false);
    const [notifOpen, setNotifOpen] = useState(false);
    const [logoutModalOpen, setLogoutModalOpen] = useState(false);

    const [isDark, setIsDark] = useState(() => {
        if (typeof window === 'undefined') {
            return false;
        }

        const stored = localStorage.getItem('theme');

        return stored
            ? stored === 'dark'
            : window.matchMedia('(prefers-color-scheme: dark)').matches;
    });

    const userMenuRef = useRef<HTMLDivElement>(null);
    const notifRef = useRef<HTMLDivElement>(null);
    const notifButtonRef = useRef<HTMLButtonElement>(null);

    const realtimeEnabled = platform?.realtime_enabled !== false;
    const feed = useNotificationFeed(user?.id, realtimeEnabled);

    // ── Effects ──
    useEffect(() => {
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }, [isDark]);

    // `headerTitle` is no longer rendered in the chrome, but it is the most
    // accurate description of the current screen, so it drives the document
    // title when a page does not mount its own <Head>.
    useEffect(() => {
        if (!headerTitle) {
            return;
        }

        const appName = (props as any).name ?? 'SanaGo';
        const suffix = ` - ${appName}`;

        if (!document.title.endsWith(suffix)) {
            document.title = `${headerTitle}${suffix}`;
        }
    }, [headerTitle, props]);

    useEffect(() => {
        // Navigation closes the mobile drawer: this is a response to an external
        // event (a completed Inertia visit), not derived render state.
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setMobileMenuOpen(false);
    }, [url]);

    useEffect(() => {
        const handleClickOutside = (e: MouseEvent) => {
            if (
                userMenuRef.current &&
                !userMenuRef.current.contains(e.target as Node)
            ) {
                setUserMenuOpen(false);
            }

            if (
                notifRef.current &&
                !notifRef.current.contains(e.target as Node)
            ) {
                setNotifOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);

        return () =>
            document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    useEffect(() => {
        const handleKey = (e: KeyboardEvent) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                setSidebarOpen((p) => !p);
            }

            if (e.key === 'Escape') {
                if (notifOpen) {
                    // Return focus to the bell so the keyboard user is not lost.
                    notifButtonRef.current?.focus();
                }

                setUserMenuOpen(false);
                setNotifOpen(false);
                setLogoutModalOpen(false);
            }
        };
        document.addEventListener('keydown', handleKey);

        return () => document.removeEventListener('keydown', handleKey);
    }, [notifOpen]);

    // ── Logic ──
    const userHasPermission = useCallback(
        (perm: string) =>
            isAdmin ||
            (user?.permissions?.some(
                (p: any) => p === perm || p?.name === perm,
            ) ??
                false),
        [isAdmin, user?.permissions],
    );

    const filteredNav = useMemo(() => {
        if (isAdmin) {
            return NAV_CONFIG;
        }

        return NAV_CONFIG.map((g) => ({
            ...g,
            items: g.items.filter(
                (i) => !i.permission || userHasPermission(i.permission),
            ),
        })).filter((g) => g.items.length > 0);
    }, [isAdmin, userHasPermission]);

    const isActiveLink = useCallback(
        (href: string) => {
            const clean = (s: string) =>
                s.split('?')[0].split('#')[0].replace(/\/$/, '');
            const current = clean(url);
            const target = clean(href);

            return current === target;
        },
        [url],
    );

    const isPathUnder = useCallback(
        (href: string) => {
            const clean = (s: string) =>
                s.split('?')[0].split('#')[0].replace(/\/$/, '');
            const current = clean(url);
            const target = clean(href);

            return current === target || current.startsWith(target + '/');
        },
        [url],
    );

    const openCategories = useMemo(() => {
        const r: Record<string, boolean> = {};
        filteredNav.forEach((g) => {
            const hasActive = g.items.some((i) => isPathUnder(i.href));
            const manual = manuallyToggled[g.category];
            r[g.category] = manual !== undefined ? manual : hasActive;
        });

        return r;
    }, [filteredNav, manuallyToggled, isPathUnder]);

    const toggleCategory = useCallback(
        (c: string) =>
            setManuallyToggled((p) => ({ ...p, [c]: !openCategories[c] })),
        [openCategories],
    );
    const closeAllDropdowns = useCallback(() => {
        setUserMenuOpen(false);
        setNotifOpen(false);
    }, []);

    const dashboardUrl = useMemo(() => getUserDashboard(user), [user]);
    const userRole = useMemo(
        () => getRoleName(user?.roles?.[0]) || 'admin',
        [user],
    );
    const userInitials = useMemo(
        () => (user ? getInitials(user.name) : '??'),
        [user],
    );

    /**
     * Trail shown by `<PageHeader>` on pages that do not pass `breadcrumbs`
     * themselves — see `BreadcrumbTrailProvider` in ui/PageHeader.
     */
    const derivedBreadcrumbs = useMemo(
        () => deriveBreadcrumbs(url, t),
        [url, t],
    );

    const sidebarProps = useMemo(
        () => ({
            filteredNav,
            openCategories,
            onToggleCategory: toggleCategory,
            dashboardUrl,
            isActive: isActiveLink,
        }),
        [
            filteredNav,
            openCategories,
            toggleCategory,
            dashboardUrl,
            isActiveLink,
        ],
    );

    return (
        <TranslationProvider>
            <BreadcrumbTrailProvider value={derivedBreadcrumbs}>
                <FlashHandler />
                <AppToaster />

                {/* Skip link — first stop for keyboard and screen-reader users. */}
                <a href="#main-content" className="sg-skip-link">
                    {t('common.skip_to_content', 'Skip to content')}
                </a>

                <div className="flex h-dvh overflow-hidden bg-canvas font-sans">
                    <AnnouncementBanner />

                    {/* ── Mobile Backdrop ── */}
                    {mobileMenuOpen && (
                        <div
                            className="fixed inset-0 z-40 bg-ink/25 backdrop-blur-[2px] lg:hidden"
                            onClick={() => setMobileMenuOpen(false)}
                        />
                    )}

                    {/* ── Mobile Sidebar ── */}
                    <aside
                        className={`fixed inset-y-0 left-0 z-50 flex w-[268px] transform flex-col border-r border-line bg-surface transition-transform duration-300 ease-premium lg:hidden ${mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'}`}
                    >
                        <div className="absolute top-3.5 right-3 z-10">
                            <button
                                onClick={() => setMobileMenuOpen(false)}
                                aria-label={t(
                                    'common.close_navigation',
                                    'Close navigation',
                                )}
                                className="rounded-md p-1.5 text-ink-subtle transition-colors duration-150 hover:bg-surface-hover hover:text-ink"
                            >
                                <XMarkIcon className="h-[18px] w-[18px]" />
                            </button>
                        </div>
                        <SidebarNav isOpen={true} {...sidebarProps} />
                    </aside>

                    {/* ── Desktop Sidebar ── */}
                    <aside
                        className={`relative hidden flex-col border-r border-line bg-surface transition-[width] duration-300 ease-premium lg:flex ${sidebarOpen ? 'w-[268px]' : 'w-[68px]'}`}
                    >
                        <SidebarNav isOpen={sidebarOpen} {...sidebarProps} />
                        <button
                            onClick={() => setSidebarOpen((p) => !p)}
                            aria-label={
                                sidebarOpen
                                    ? t('common.collapse_sidebar')
                                    : t('common.expand_sidebar')
                            }
                            className="absolute top-20 -right-3 z-50 rounded-md border border-line bg-surface p-1.5 text-ink-subtle shadow-surface transition-[color,transform] duration-200 ease-premium hover:text-ink active:scale-95"
                            title={
                                sidebarOpen
                                    ? t('common.collapse_sidebar')
                                    : t('common.expand_sidebar')
                            }
                        >
                            <ChevronLeftIcon
                                className={`h-3.5 w-3.5 transition-transform duration-300 ease-premium ${!sidebarOpen ? 'rotate-180' : ''}`}
                            />
                        </button>
                    </aside>

                    {/* ── Main Content Area ── */}
                    <div className="flex min-w-0 flex-1 flex-col overflow-hidden">
                        {/*
                         * Utility bar only — NO page title/subtitle. The page
                         * heading lives in <PageHeader> inside <main>, so each
                         * screen states its title exactly once.
                         */}
                        <header className="chrome-blur sticky top-0 z-30 flex h-14 shrink-0 items-center justify-between gap-3 border-b border-line px-3 lg:px-5">
                            <div className="flex min-w-0 flex-1 items-center gap-2">
                                <button
                                    onClick={() => setMobileMenuOpen(true)}
                                    aria-label={t(
                                        'common.open_navigation',
                                        'Open navigation',
                                    )}
                                    className="-ml-1 rounded-md p-1.5 text-ink-muted transition-colors duration-150 hover:bg-surface-hover hover:text-ink lg:hidden"
                                >
                                    <Bars3Icon className="h-[18px] w-[18px]" />
                                </button>
                                <SubscriptionChip
                                    subscription={tenant?.subscription}
                                    currency={platform?.currency}
                                    currencySymbol={platform?.currency_symbol}
                                />
                            </div>

                            <div className="flex items-center gap-1 lg:gap-1.5">
                                {headerAction && (
                                    <div className="hidden md:block">
                                        {headerAction}
                                    </div>
                                )}
                                <LocaleSwitcher variant="full" />

                                {/* Theme toggle */}
                                <button
                                    onClick={() => setIsDark((p) => !p)}
                                    role="switch"
                                    aria-checked={isDark}
                                    aria-label={t(
                                        'common.toggle_dark_mode',
                                        'Toggle dark mode',
                                    )}
                                    title={t(
                                        'common.toggle_theme',
                                        'Toggle theme',
                                    )}
                                    className="relative inline-flex h-[22px] w-[38px] shrink-0 cursor-pointer items-center rounded-full border border-line-strong bg-surface-inset transition-colors duration-200 ease-premium focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:ring-offset-2 focus-visible:ring-offset-canvas focus-visible:outline-none data-[dark=true]:border-accent data-[dark=true]:bg-accent"
                                    data-dark={isDark}
                                >
                                    <span
                                        className={`pointer-events-none absolute left-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-surface shadow-surface transition-transform duration-200 ease-spring ${isDark ? 'translate-x-[16px]' : 'translate-x-0'}`}
                                    >
                                        {isDark ? (
                                            <MoonIcon className="h-2.5 w-2.5 text-accent-ink" />
                                        ) : (
                                            <SunIcon className="h-2.5 w-2.5 text-warning-ink" />
                                        )}
                                    </span>
                                </button>

                                {/* Notifications */}
                                <div className="relative" ref={notifRef}>
                                    <button
                                        ref={notifButtonRef}
                                        onClick={() => {
                                            setNotifOpen((p) => !p);
                                            setUserMenuOpen(false);
                                        }}
                                        aria-label={t('common.notifications')}
                                        aria-haspopup="dialog"
                                        aria-expanded={notifOpen}
                                        className={cn(
                                            'relative rounded-md p-1.5 transition-colors duration-150',
                                            notifOpen
                                                ? 'bg-surface-hover text-ink'
                                                : 'text-ink-subtle hover:bg-surface-hover hover:text-ink',
                                        )}
                                    >
                                        <BellIcon className="h-[18px] w-[18px]" />
                                        {feed.unreadCount > 0 && (
                                            <span className="absolute top-0.5 right-0.5 flex h-3.5 min-w-[14px] items-center justify-center rounded-full bg-critical px-1 text-[8.5px] font-bold text-ink-inverse tabular-nums">
                                                {feed.unreadCount > 99
                                                    ? '99+'
                                                    : feed.unreadCount}
                                            </span>
                                        )}
                                        {/* Real-time is live but quiet. */}
                                        {!feed.connected &&
                                            realtimeEnabled === false && (
                                                <SignalSlashIcon className="sr-only" />
                                            )}
                                    </button>
                                    {notifOpen && (
                                        <NotificationsDropdown
                                            feed={feed}
                                            locale={locale}
                                            onClose={closeAllDropdowns}
                                        />
                                    )}
                                </div>

                                {/* User menu */}
                                <div className="relative" ref={userMenuRef}>
                                    <button
                                        onClick={() => {
                                            setUserMenuOpen((p) => !p);
                                            setNotifOpen(false);
                                        }}
                                        aria-haspopup="menu"
                                        aria-expanded={userMenuOpen}
                                        aria-label={t('common.user_menu')}
                                        className="flex items-center gap-2 rounded-md border border-line bg-surface py-1 pr-2 pl-1 transition-colors duration-200 hover:border-line-strong hover:bg-surface-hover"
                                    >
                                        <span className="flex h-6 w-6 items-center justify-center rounded-md bg-ink text-[10px] font-bold tracking-tight text-ink-inverse">
                                            {userInitials}
                                        </span>
                                        <span className="hidden text-[12.5px] font-medium text-ink md:block">
                                            {user?.name?.split(' ')[0]}
                                        </span>
                                        <ChevronDownIcon className="hidden h-3.5 w-3.5 text-ink-subtle md:block" />
                                    </button>
                                    {userMenuOpen && (
                                        <UserMenuDropdown
                                            user={user}
                                            userRole={userRole}
                                            onClose={closeAllDropdowns}
                                            onLogoutClick={() =>
                                                setLogoutModalOpen(true)
                                            }
                                        />
                                    )}
                                </div>
                            </div>
                        </header>

                        {/* ── Page Content ── */}
                        <main
                            id="main-content"
                            tabIndex={-1}
                            className="flex-1 overflow-y-auto bg-canvas focus-visible:outline-none"
                        >
                            <div className="mx-auto w-full max-w-[1560px] px-4 py-5 sm:px-6 lg:px-8">
                                {children}
                            </div>
                        </main>
                    </div>

                    {/* ── Logout Confirmation ── */}
                    <Dialog
                        open={logoutModalOpen}
                        onClose={() => setLogoutModalOpen(false)}
                        size="sm"
                        hideClose
                        title={t('nav.sign_out')}
                        description={t('nav.sign_out_description')}
                        footer={
                            <>
                                <Button
                                    variant="secondary"
                                    size="sm"
                                    onClick={() => setLogoutModalOpen(false)}
                                >
                                    {t('common.cancel')}
                                </Button>
                                <Button
                                    variant="danger"
                                    size="sm"
                                    icon={ArrowRightStartOnRectangleIcon}
                                    onClick={() => router.post('/logout')}
                                >
                                    {t('nav.sign_out')}
                                </Button>
                            </>
                        }
                    >
                        <p className="text-[13px] leading-relaxed text-ink-muted">
                            {t('nav.sign_out_description')}
                        </p>
                    </Dialog>
                </div>
            </BreadcrumbTrailProvider>
        </TranslationProvider>
    );
}
