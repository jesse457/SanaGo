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
    UserCircleIcon,
    ArrowTrendingUpIcon,
    DocumentTextIcon,
    ChevronLeftIcon,
    ClockIcon,
    QuestionMarkCircleIcon,
    TruckIcon,
    PresentationChartBarIcon,
    CreditCardIcon,
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
import { Button, Dialog } from '@/Components/ui';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';

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

interface AppLayoutProps {
    headerTitle?: string;
    headerSubtitle?: string;
    headerAction?: React.ReactNode;
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
                label: 'Register Patient',
                labelKey: 'nav.register_patient',
                href: '/patients/create',
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
                label: 'Schedule Surgery',
                labelKey: 'nav.schedule_surgery',
                href: '/surgeries/create',
                permission: 'create-consultations',
            },
            {
                label: 'Emergency Cases',
                labelKey: 'nav.emergency_cases',
                href: '/emergencies',
                permission: 'create-admissions',
            },
            {
                label: 'New Emergency',
                labelKey: 'nav.new_emergency',
                href: '/emergencies/create',
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
            {
                label: 'New Request',
                labelKey: 'nav.new_rad_request',
                href: '/radiology/requests/create',
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
                label: 'Add Medication',
                labelKey: 'nav.add_medication',
                href: '/pharmacy/drugs/create',
                permission: 'view-drugs',
            },
            {
                label: 'Dispensation',
                labelKey: 'nav.dispensation',
                href: '/pharmacy/dispensation',
                permission: 'dispense-drugs',
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
                label: 'Bed Types',
                labelKey: 'nav.bed_types',
                href: '/bed-types',
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
        category: 'Supplies',
        categoryKey: 'nav.category.supplies',
        icon: <TruckIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Supply Inventory',
                labelKey: 'nav.supply_inventory',
                href: '/supplies',
                permission: 'view-supplies',
            },
            {
                label: 'Add Supply',
                labelKey: 'nav.add_supply',
                href: '/supplies/create',
                permission: 'view-supplies',
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
                label: 'New Service',
                labelKey: 'nav.new_service',
                href: '/finance/services/create',
                permission: 'view-finance',
            },
            {
                label: 'Invoices',
                labelKey: 'nav.invoices',
                href: '/finance/invoices',
                permission: 'view-finance',
            },
            {
                label: 'Create Invoice',
                labelKey: 'nav.create_invoice',
                href: '/finance/invoices/create',
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
                    className={`group flex items-center gap-2.5 rounded-md px-2.5 py-2 transition-[background-color,color] duration-200 ease-premium ${
                        isDashboardActive
                            ? 'bg-accent-soft font-semibold text-accent-ink'
                            : 'text-ink-muted hover:bg-surface-hover hover:text-ink'
                    }`}
                >
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
                                onClick={() => onToggleCategory(group.category)}
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
                                <div className="relative ml-3.5 space-y-0.5 border-l border-line py-1 pl-2.5">
                                    {group.items.map((item) => {
                                        const isItemActive = isActive(
                                            item.href,
                                        );

                                        return (
                                            <Link
                                                key={item.href}
                                                href={item.href}
                                                prefetch
                                                className={`group/item flex items-center justify-between rounded-md py-1.5 pr-2 pl-2.5 text-[12.5px] transition-[background-color,color] duration-150 ease-premium ${
                                                    isItemActive
                                                        ? 'bg-accent-soft font-semibold text-accent-ink'
                                                        : 'text-ink-muted hover:bg-surface-hover hover:text-ink'
                                                }`}
                                            >
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

// ─── Notifications Dropdown ─────────────────────────────────────────────────

const NotificationsDropdown = memo(function NotificationsDropdown({
    onClose,
}: {
    onClose: () => void;
}) {
    const { t } = useTranslation();
    const [notifications, setNotifications] = useState<any[]>([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState<'all' | 'unread'>('all');

    useEffect(() => {
        fetch('/notifications/latest', {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((r) => r.json())
            .then((data) => {
                setNotifications(data.notifications || []);
                setUnreadCount(data.unreadCount || 0);
            })
            .catch(() => {})
            .finally(() => setLoading(false));
    }, []);

    const filtered = filter === 'unread' ? notifications : notifications;

    const typeStyles: Record<string, string> = {
        info: 'bg-accent-soft border-accent/30 ',
        success: 'bg-positive-soft border-positive/40 ',
        warning: 'bg-warning-soft border-warning/40 ',
        error: 'bg-critical-soft border-critical/40 ',
    };

    const categoryIcons: Record<string, React.ReactNode> = {
        clinical: <DocumentTextIcon className="h-5 w-5" />,
        lab: <BeakerIcon className="h-5 w-5" />,
        radiology: <HeartIcon className="h-5 w-5" />,
        pharmacy: <ClockIcon className="h-5 w-5" />,
        finance: <BanknotesIcon className="h-5 w-5" />,
        general: <BellIcon className="h-5 w-5" />,
    };

    const handleClick = (notif: any) => {
        // Mark as read via API
        fetch(`/notifications/${notif.id}/read`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':
                    (
                        document.querySelector(
                            'meta[name="csrf-token"]',
                        ) as HTMLMetaElement
                    )?.content || '',
            },
        }).catch(() => {});

        if (notif.link) {
            router.visit(notif.link);
        }
    };

    return (
        <div className="absolute top-full right-0 z-50 mt-2.5 w-96 overflow-hidden rounded-2xl border border-line bg-surface shadow-float">
            <div className="border-b border-line p-4">
                <div className="mb-3 flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <h3 className="text-sm font-bold text-ink">
                            {t('common.notifications')}
                        </h3>
                        {unreadCount > 0 && (
                            <span className="rounded-full bg-accent px-2 py-0.5 text-[10px] font-bold text-ink-inverse">
                                {unreadCount}
                            </span>
                        )}
                    </div>
                    <div className="flex gap-1 rounded-lg bg-surface-inset p-0.5">
                        {(['all', 'unread'] as const).map((f) => (
                            <button
                                key={f}
                                onClick={() => setFilter(f)}
                                className={`rounded-md px-2.5 py-1 text-[11px] font-medium transition-colors ${filter === f ? 'bg-surface text-ink shadow-surface' : 'text-ink-subtle hover:text-ink-muted'} cursor-pointer`}
                            >
                                {t(`common.${f}`)}
                            </button>
                        ))}
                    </div>
                </div>
            </div>

            <div className="scrollbar-thin max-h-80 overflow-y-auto overscroll-contain">
                {loading ? (
                    <div className="px-4 py-8 text-center text-sm text-ink-subtle">
                        {t('Loading...')}
                    </div>
                ) : filtered.length > 0 ? (
                    filtered.map((n) => (
                        <div
                            key={n.id}
                            onClick={() => handleClick(n)}
                            className={`flex cursor-pointer gap-3 border-b border-line px-4 py-3 transition-colors hover:bg-surface-hover ${n.read === false ? 'bg-accent-soft' : ''}`}
                        >
                            <div
                                className={`rounded-xl border p-2 ${typeStyles[n.type] || typeStyles.info} shrink-0`}
                            >
                                {categoryIcons[n.category] ||
                                    categoryIcons.general}
                            </div>
                            <div className="min-w-0 flex-1">
                                <div className="flex items-start justify-between gap-2">
                                    <p className="truncate text-sm font-semibold text-ink">
                                        {n.message}
                                    </p>
                                    {n.read === false && (
                                        <span className="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent" />
                                    )}
                                </div>
                                <p className="mt-1.5 text-[10px] font-medium text-ink-subtle">
                                    {n.created_at}
                                </p>
                            </div>
                        </div>
                    ))
                ) : (
                    <div className="px-4 py-8 text-center">
                        <BellIcon className="mx-auto mb-2 h-8 w-8 text-ink-subtle" />
                        <p className="text-sm font-medium text-ink-subtle">
                            {t('common.no_notifications')}
                        </p>
                    </div>
                )}
            </div>

            <div className="border-t border-line bg-surface-muted p-3">
                <Link
                    href="/notifications"
                    className="flex items-center justify-center gap-1.5 text-sm font-semibold text-accent hover:text-accent-ink"
                    onClick={onClose}
                >
                    {t('common.view_all_notifications')}{' '}
                    <ArrowTrendingUpIcon className="h-4 w-4" />
                </Link>
            </div>
        </div>
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

// ─── Main App Layout ────────────────────────────────────────────────────────

export default function AppLayout({
    children,
    headerTitle = 'Dashboard',
    headerSubtitle,
    headerAction,
}: React.PropsWithChildren<AppLayoutProps>) {
    const { url, props } = usePage();
    const { t } = useTranslation();
    const user = (props.auth as any)?.user as User | null;
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

    // ── Effects ──
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
                setUserMenuOpen(false);
                setNotifOpen(false);
                setLogoutModalOpen(false);
            }
        };
        document.addEventListener('keydown', handleKey);

        return () => document.removeEventListener('keydown', handleKey);
    }, []);

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
            <FlashHandler />
            <AppToaster />
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
                            aria-label="Close navigation"
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
                    {/* ── Top Header ── */}
                    <header className="chrome-blur sticky top-0 z-30 flex h-14 shrink-0 items-center justify-between border-b border-line px-4 lg:px-6">
                        <div className="flex min-w-0 flex-1 items-center gap-3">
                            <button
                                onClick={() => setMobileMenuOpen(true)}
                                aria-label="Open navigation"
                                className="-ml-1.5 rounded-md p-1.5 text-ink-muted transition-colors duration-150 hover:bg-surface-hover hover:text-ink lg:hidden"
                            >
                                <Bars3Icon className="h-[18px] w-[18px]" />
                            </button>
                            <div className="min-w-0">
                                <h1 className="truncate text-[14px] font-semibold tracking-tight text-ink">
                                    {headerTitle}
                                </h1>
                                {headerSubtitle && (
                                    <p className="hidden truncate text-[11.5px] text-ink-subtle sm:block">
                                        {headerSubtitle}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="ml-4 flex items-center gap-1.5 lg:gap-2">
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
                                aria-label="Toggle dark mode"
                                className="relative inline-flex h-[22px] w-[38px] shrink-0 cursor-pointer items-center rounded-full border border-line-strong bg-surface-inset transition-colors duration-200 ease-premium focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:ring-offset-2 focus-visible:ring-offset-canvas focus-visible:outline-none data-[dark=true]:border-accent data-[dark=true]:bg-accent"
                                data-dark={isDark}
                            >
                                <span
                                    className={`pointer-events-none absolute left-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-white shadow-[0_1px_2px_rgba(0,0,0,0.2)] transition-transform duration-200 ease-spring ${isDark ? 'translate-x-[16px]' : 'translate-x-0'}`}
                                >
                                    {isDark ? (
                                        <MoonIcon className="h-2.5 w-2.5 text-accent" />
                                    ) : (
                                        <SunIcon className="h-2.5 w-2.5 text-warning" />
                                    )}
                                </span>
                            </button>

                            {/* Notifications */}
                            <div className="relative" ref={notifRef}>
                                <button
                                    onClick={() => {
                                        setNotifOpen((p) => !p);
                                        setUserMenuOpen(false);
                                    }}
                                    aria-label="Notifications"
                                    className="relative rounded-md p-1.5 text-ink-subtle transition-colors duration-150 hover:bg-surface-hover hover:text-ink"
                                >
                                    <BellIcon className="h-[18px] w-[18px]" />
                                    <span className="absolute top-1 right-1 flex h-3.5 min-w-[14px] items-center justify-center rounded-full bg-critical px-1 text-[8.5px] font-bold text-ink-inverse tabular-nums">
                                        2
                                    </span>
                                </button>
                                {notifOpen && (
                                    <NotificationsDropdown
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
                                    className="flex items-center gap-2 rounded-md border border-line bg-surface py-1 pr-2 pl-1 transition-colors duration-200 hover:border-line-strong hover:bg-surface-hover"
                                >
                                    <span className="flex h-6 w-6 items-center justify-center rounded-[7px] bg-ink text-[10px] font-bold tracking-tight text-ink-inverse">
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
                    <main className="flex-1 overflow-y-auto bg-canvas">
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
                                Cancel
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
        </TranslationProvider>
    );
}
