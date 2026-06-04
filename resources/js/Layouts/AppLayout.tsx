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
    ChevronLeftIcon,
    ClockIcon,
    ExclamationCircleIcon,
    QuestionMarkCircleIcon,
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
import { FlashHandler } from '@/Components/FlashHandler';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';

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
    roles?: { name: string }[];
    permissions?: string[];
    is_active: boolean;
    department?: { name: string } | null;
}

interface AppLayoutProps {
    headerTitle?: string;
    headerSubtitle?: string;
    headerAction?: React.ReactNode;
}

const NAV_CONFIG: NavGroup[] = [
    {
        category: 'Patient Care',
        categoryKey: 'nav.category.patient_care',
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
            {
                label: 'Appointments',
                labelKey: 'nav.appointments',
                href: '/appointments',
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
                label: 'Consultations',
                labelKey: 'nav.consultations',
                href: '/consultations/create',
                permission: 'view-consultations',
            },
            {
                label: 'Medical Records',
                labelKey: 'nav.medical_records',
                href: '/medical-records',
                permission: 'view-consultations',
            },
            {
                label: 'Patient Vitals',
                labelKey: 'nav.patient_vitals',
                href: '/vitals',
                permission: 'view-patients',
            },
        ],
    },
    {
        category: 'Surgery & Emergency',
        categoryKey: 'nav.category.surgery_emergency',
        icon: <HeartIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Surgeries',
                labelKey: 'nav.surgeries',
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
        ],
    },
    {
        category: 'Infrastructure',
        categoryKey: 'nav.category.infrastructure',
        icon: <BuildingOfficeIcon className="h-4 w-4" />,
        items: [
            {
                label: 'Facility Map',
                labelKey: 'nav.facility_map',
                href: '/infrastructure',
                permission: 'manage-infrastructure',
            },
            {
                label: 'Beds',
                labelKey: 'nav.beds',
                href: '/beds',
                permission: 'manage-infrastructure',
            },
            {
                label: 'Wards',
                labelKey: 'nav.wards',
                href: '/wards',
                permission: 'manage-infrastructure',
            },
            {
                label: 'Bed Types',
                labelKey: 'nav.bed_types',
                href: '/bed-types',
                permission: 'manage-infrastructure',
            },
            {
                label: 'Supply Chain',
                labelKey: 'nav.supply_chain',
                href: '/supplies',
                permission: 'view-supplies',
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
                label: 'Roles & Access',
                labelKey: 'nav.roles_access',
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
        ],
    },
    {
        category: 'Support',
        categoryKey: 'nav.category.support',
        icon: <QuestionMarkCircleIcon className="h-4 w-4" />,
        items: [
            {
                label: 'All Feedback',
                labelKey: 'nav.all_feedback',
                href: '/feedback',
            },
        ],
    },
];

const DASHBOARDS: Record<string, string> = {
    admin: '/dashboard/admin',
    doctor: '/dashboard/doctor',
    reception: '/dashboard/reception',
    lab_tech: '/dashboard/lab',
    pharma: '/dashboard/pharma',
};

function getRoleName(role: any): string {
    return typeof role === 'string' ? role : (role?.name ?? '');
}

function getUserDashboard(user: User | null): string {
    if (!user?.roles?.length) {
        return '/dashboard/admin';
    }

    const roleName = getRoleName(user.roles[0]);

    return DASHBOARDS[roleName] || '/dashboard/admin';
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
        admin: 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
        doctor: 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
        reception:
            'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
        lab_tech:
            'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
        pharma: 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 border-teal-100 dark:border-teal-800',
    };

    return (
        map[role] ||
        'bg-slate-50 dark:bg-neutral-700 text-slate-700 dark:text-neutral-300 border-slate-100 dark:border-neutral-600'
    );
}

// ─── Refined Modern Sidebar Navigation ───

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
            <div className="flex h-16 shrink-0 items-center border-b border-zinc-100 px-4 dark:border-zinc-800/80">
                <Link
                    href={dashboardUrl}
                    className="flex w-full items-center gap-3 overflow-hidden"
                >
                    <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-sm transition-transform duration-200 hover:scale-105">
                        <HeartIcon className="h-5 w-5" />
                    </div>
                    {isOpen && (
                        <div className="overflow-hidden">
                            <span className="text-base font-extrabold tracking-tight whitespace-nowrap text-zinc-900 dark:text-zinc-50">
                                Sana
                                <span className="text-blue-600 dark:text-blue-500">
                                    Go
                                </span>
                            </span>
                            <span className="-mt-0.5 block text-[9px] font-semibold tracking-wider text-zinc-400 uppercase">
                                {t('layout.hms_platform')}
                            </span>
                        </div>
                    )}
                </Link>
            </div>

            <nav className="scrollbar-thin scrollbar-thumb-zinc-200 dark:scrollbar-thumb-zinc-800 flex-1 space-y-1.5 overflow-y-auto overscroll-contain px-3 py-4">
                <Link
                    href={dashboardUrl}
                    className={`group relative flex items-center gap-3 rounded-lg px-3 py-2 transition-all duration-150 ${
                        isDashboardActive
                            ? 'bg-blue-50/80 font-medium text-blue-600 dark:bg-blue-950/30 dark:text-blue-400'
                            : 'text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800/40 dark:hover:text-zinc-100'
                    }`}
                >
                    <Squares2X2Icon
                        className={`h-4 w-4 shrink-0 transition-colors ${
                            isDashboardActive
                                ? 'text-blue-600 dark:text-blue-400'
                                : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300'
                        }`}
                    />
                    {isOpen && (
                        <span className="text-sm font-medium">
                            {t('nav.dashboard')}
                        </span>
                    )}
                </Link>

                {filteredNav.map((group) => {
                    const hasActiveChild = group.items.some((item) =>
                        isActive(item.href),
                    );
                    const isExpanded = openCategories[group.category];

                    return (
                        <div key={group.category} className="space-y-0.5">
                            <button
                                onClick={() => onToggleCategory(group.category)}
                                className={`group flex w-full items-center justify-between rounded-lg px-3 py-2 text-left transition-all duration-150 ${
                                    isExpanded
                                        ? 'font-medium text-zinc-900 dark:text-zinc-100'
                                        : 'text-zinc-500 dark:text-zinc-400'
                                } ${
                                    hasActiveChild && !isExpanded
                                        ? 'bg-zinc-50/60 text-zinc-900 dark:bg-zinc-800/10 dark:text-zinc-100'
                                        : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/40'
                                }`}
                            >
                                <div className="flex min-w-0 items-center gap-3">
                                    <div
                                        className={`transition-colors ${
                                            isExpanded || hasActiveChild
                                                ? 'text-blue-600 dark:text-blue-400'
                                                : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300'
                                        }`}
                                    >
                                        {group.icon}
                                    </div>
                                    {isOpen && (
                                        <span className="truncate text-xs font-semibold tracking-wide">
                                            {t(group.categoryKey)}
                                        </span>
                                    )}
                                </div>
                                {isOpen && (
                                    <div className="flex shrink-0 items-center gap-1.5">
                                        {group.badge && (
                                            <span className="rounded-full bg-blue-50 px-1.5 py-0.5 text-[9px] font-bold text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                                                {group.badge}
                                            </span>
                                        )}
                                        <ChevronDownIcon
                                            className={`h-3.5 w-3.5 text-zinc-400 transition-transform duration-200 dark:text-zinc-500 ${
                                                isExpanded ? 'rotate-180' : ''
                                            }`}
                                        />
                                    </div>
                                )}
                            </button>

                            {isOpen && isExpanded && (
                                <div className="animate-slideDown relative ml-4 space-y-0.5 border-l border-zinc-200/60 py-1 pl-5 dark:border-zinc-800/80">
                                    {group.items.map((item) => {
                                        const isItemActive = isActive(
                                            item.href,
                                        );

                                        return (
                                            <Link
                                                key={item.href}
                                                href={item.href}
                                                className={`group/item flex items-center justify-between rounded-md py-1.5 pr-2 pl-3 text-xs transition-all duration-150 ${
                                                    isItemActive
                                                        ? '-ml-[21.5px] border-l-2 border-blue-500 bg-blue-50/40 pl-[20px] font-semibold text-blue-600 dark:border-blue-400 dark:bg-blue-950/20 dark:text-blue-400'
                                                        : 'text-zinc-500 hover:bg-zinc-50/50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800/30 dark:hover:text-zinc-100'
                                                }`}
                                            >
                                                <span className="truncate">
                                                    {t(item.labelKey)}
                                                </span>
                                                {item.badge && (
                                                    <span
                                                        className={`rounded-full px-1.5 py-0.5 text-[9px] font-bold transition-colors ${
                                                            isItemActive
                                                                ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400'
                                                                : 'bg-zinc-100 text-zinc-500 group-hover/item:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400'
                                                        }`}
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

            <div className="space-y-1 border-t border-zinc-100 p-3 dark:border-zinc-800/80">
                <Link
                    href="/settings"
                    className="group flex items-center gap-3 rounded-lg px-3 py-2 text-zinc-500 transition-all duration-150 hover:bg-zinc-50 hover:text-zinc-900 dark:text-zinc-400 dark:hover:bg-zinc-800/40 dark:hover:text-zinc-100"
                >
                    <Cog6ToothIcon className="h-4 w-4 text-zinc-400 transition-colors group-hover:text-zinc-600 dark:group-hover:text-zinc-300" />
                    {isOpen && (
                        <span className="text-sm font-medium">
                            {t('nav.settings')}
                        </span>
                    )}
                </Link>
            </div>
        </>
    );
});

const NotificationsDropdown = memo(function NotificationsDropdown({
    onClose,
}: {
    onClose: () => void;
}) {
    const { t } = useTranslation();
    const notifications = [
        {
            id: 1,
            titleKey: 'notifications.new_patient_title',
            messageKey: 'notifications.new_patient_message',
            time: '2 min ago',
            read: false,
            type: 'info',
            icon: <UserGroupIcon className="h-5 w-5" />,
        },
        {
            id: 2,
            titleKey: 'notifications.lab_results_title',
            messageKey: 'notifications.lab_results_message',
            time: '15 min ago',
            read: false,
            type: 'success',
            icon: <BeakerIcon className="h-5 w-5" />,
        },
        {
            id: 3,
            titleKey: 'notifications.low_stock_title',
            messageKey: 'notifications.low_stock_message',
            time: '1 hour ago',
            read: true,
            type: 'warning',
            icon: <ExclamationCircleIcon className="h-5 w-5" />,
        },
        {
            id: 4,
            titleKey: 'notifications.appointment_title',
            messageKey: 'notifications.appointment_message',
            time: '2 hours ago',
            read: true,
            type: 'info',
            icon: <ClockIcon className="h-5 w-5" />,
        },
    ];

    const unreadCount = notifications.filter((n) => !n.read).length;
    const [filter, setFilter] = useState<'all' | 'unread'>('all');

    const filteredNotifications =
        filter === 'unread'
            ? notifications.filter((n) => !n.read)
            : notifications;

    const getTypeStyles = (type: string) => {
        const styles = {
            info: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
            success:
                'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
            warning:
                'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800',
        };

        return styles[type as keyof typeof styles] || styles.info;
    };

    return (
        <div className="animate-slideDown absolute top-full right-0 z-20 mt-2 w-96 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
            <div className="border-b border-gray-100 p-4 dark:border-neutral-700">
                <div className="mb-3 flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <h3 className="text-base font-bold text-gray-900 dark:text-neutral-100">
                            {t('common.notifications')}
                        </h3>
                        {unreadCount > 0 && (
                            <span className="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                {unreadCount} {t('common.new')}
                            </span>
                        )}
                    </div>
                    <div className="flex gap-1">
                        <button
                            onClick={() => setFilter('all')}
                            className={`rounded-md px-2 py-1 text-xs font-medium transition-colors ${
                                filter === 'all'
                                    ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-neutral-100'
                                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-neutral-300'
                            }`}
                        >
                            {t('common.all')}
                        </button>
                        <button
                            onClick={() => setFilter('unread')}
                            className={`rounded-md px-2 py-1 text-xs font-medium transition-colors ${
                                filter === 'unread'
                                    ? 'bg-gray-100 text-gray-900 dark:bg-neutral-700 dark:text-neutral-100'
                                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-neutral-300'
                            }`}
                        >
                            {t('common.unread')}
                        </button>
                    </div>
                </div>
                <button className="text-xs font-semibold text-blue-600 hover:underline">
                    {t('common.mark_all_read')}
                </button>
            </div>

            <div className="max-h-80 overflow-y-auto overscroll-contain">
                {filteredNotifications.map((n) => (
                    <div
                        key={n.id}
                        className={`group cursor-pointer border-b border-gray-50 px-4 py-3 transition-all hover:bg-gray-50 dark:border-neutral-700/50 dark:hover:bg-neutral-700/30 ${
                            !n.read
                                ? 'border-l-4 border-l-blue-600 bg-blue-50/30 dark:bg-blue-900/5'
                                : ''
                        }`}
                    >
                        <div className="flex gap-3">
                            <div
                                className={`rounded-lg p-2 ${getTypeStyles(n.type)} shrink-0`}
                            >
                                {n.icon}
                            </div>
                            <div className="min-w-0 flex-1">
                                <div className="flex items-start justify-between gap-2">
                                    <p className="truncate text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                        {t(n.titleKey)}
                                    </p>
                                    {!n.read && (
                                        <span className="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-blue-600" />
                                    )}
                                </div>
                                <p className="mt-1 line-clamp-2 text-xs text-gray-600 dark:text-neutral-400">
                                    {t(n.messageKey)}
                                </p>
                                <p className="mt-2 text-[10px] font-medium text-gray-400">
                                    {n.time}
                                </p>
                            </div>
                        </div>
                    </div>
                ))}

                {filteredNotifications.length === 0 && (
                    <div className="px-4 py-8 text-center">
                        <BellIcon className="mx-auto mb-3 h-12 w-12 text-gray-300 dark:text-neutral-600" />
                        <p className="text-sm font-medium text-gray-500 dark:text-neutral-400">
                            {t('common.no_notifications')}
                        </p>
                        <p className="mt-1 text-xs text-gray-400 dark:text-neutral-500">
                            {t('common.all_caught_up')}
                        </p>
                    </div>
                )}
            </div>

            <div className="border-t border-gray-100 bg-gray-50/50 p-3 dark:border-neutral-700 dark:bg-neutral-800/50">
                <Link
                    href="/notifications"
                    className="flex items-center justify-center gap-2 text-sm font-semibold text-blue-600 transition-colors hover:underline"
                    onClick={onClose}
                >
                    {t('common.view_all_notifications')}
                    <ArrowTrendingUpIcon className="h-4 w-4" />
                </Link>
            </div>
        </div>
    );
});

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
        <div className="animate-slideDown absolute top-full right-0 z-20 mt-2 w-72 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
            <div className="border-b border-gray-100 bg-gradient-to-br from-gray-50 to-white p-4 dark:border-neutral-700 dark:from-neutral-800 dark:to-neutral-800">
                <div className="flex items-center gap-3">
                    <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-lg font-bold text-white shadow-lg">
                        {user ? getInitials(user.name) : '??'}
                    </div>
                    <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-bold text-gray-900 dark:text-neutral-100">
                            {user?.name || t('common.staff_user')}
                        </p>
                        <p className="truncate text-xs text-gray-500 dark:text-neutral-400">
                            {user?.email}
                        </p>
                        <div className="mt-1.5 flex items-center gap-2">
                            <span
                                className={`rounded-md border px-2 py-0.5 text-[9px] font-bold uppercase ${getRoleBadge(userRole)}`}
                            >
                                {userRole.replace('_', ' ')}
                            </span>
                            {user?.department && (
                                <span className="rounded-md bg-gray-100 px-2 py-0.5 text-[9px] font-medium text-gray-600 dark:bg-neutral-700 dark:text-neutral-400">
                                    {user.department.name}
                                </span>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            <div className="py-2">
                <Link
                    href="/profile"
                    className="group flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition-colors duration-150 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    onClick={onClose}
                >
                    <div className="rounded-lg bg-gray-100 p-1.5 transition-colors group-hover:bg-blue-100 dark:bg-neutral-700 dark:group-hover:bg-blue-900/30">
                        <UserCircleIcon className="h-4 w-4 text-gray-500 transition-colors group-hover:text-blue-600" />
                    </div>
                    <div className="flex-1">
                        <p className="font-medium">{t('nav.my_profile')}</p>
                        <p className="text-[10px] text-gray-400">
                            {t('nav.profile_description')}
                        </p>
                    </div>
                </Link>

                <Link
                    href="/settings"
                    className="group flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition-colors duration-150 hover:bg-gray-50 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    onClick={onClose}
                >
                    <div className="rounded-lg bg-gray-100 p-1.5 transition-colors group-hover:bg-blue-100 dark:bg-neutral-700 dark:group-hover:bg-blue-900/30">
                        <Cog6ToothIcon className="h-4 w-4 text-gray-500 transition-colors group-hover:text-blue-600" />
                    </div>
                    <div className="flex-1">
                        <p className="font-medium">{t('nav.settings')}</p>
                        <p className="text-[10px] text-gray-400">
                            {t('nav.settings_description')}
                        </p>
                    </div>
                </Link>
            </div>

            <div className="border-t border-gray-100 pt-1 dark:border-neutral-700">
                <button
                    onClick={() => {
                        onClose();
                        onLogoutClick();
                    }}
                    className="group flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-red-600 transition-colors duration-150 hover:bg-red-50 dark:hover:bg-red-900/20"
                >
                    <div className="rounded-lg bg-red-50 p-1.5 transition-colors group-hover:bg-red-100 dark:bg-red-900/20 dark:group-hover:bg-red-900/30">
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

export default function AppLayout({
    children,
    headerTitle = 'Dashboard',
    headerSubtitle,
    headerAction,
}: React.PropsWithChildren<AppLayoutProps>) {
    const { url, props } = usePage();
    const { t } = useTranslation();
    const user = (props.auth as any)?.user as User | null;
    const tenant = (props as any)?.tenant as {
        id: string;
        name: string;
        slug: string;
    } | null;
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

        if (stored) {
            return stored === 'dark';
        }

        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    });

    const userMenuRef = useRef<HTMLDivElement>(null);
    const notifRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }, [isDark]);

    useEffect(() => {
        setMobileMenuOpen(false);
    }, [url]);

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

    useEffect(() => {
        const handleKeyPress = (e: KeyboardEvent) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                setSidebarOpen((prev) => !prev);
            }

            if (e.key === 'Escape') {
                setUserMenuOpen(false);
                setNotifOpen(false);
                setLogoutModalOpen(false);
            }
        };
        document.addEventListener('keydown', handleKeyPress);

        return () => document.removeEventListener('keydown', handleKeyPress);
    }, []);

    const userHasPermission = useCallback(
        (perm: string): boolean => {
            if (!user?.permissions) {
                return false;
            }

            return user.permissions.some(
                (p: any) => p === perm || p?.name === perm,
            );
        },
        [user?.permissions],
    );

    const filteredNav = useMemo(() => {
        if (isAdmin) {
            return NAV_CONFIG;
        }

        return NAV_CONFIG.map((group) => ({
            ...group,
            items: group.items.filter((item) => {
                if (!item.permission) {
                    return true;
                }

                return userHasPermission(item.permission);
            }),
        })).filter((group) => group.items.length > 0);
    }, [isAdmin, userHasPermission]);

    // Sanitizes query parameters and anchor tags before running active matching
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

    const openCategories = useMemo(() => {
        const result: Record<string, boolean> = {};
        filteredNav.forEach((group) => {
            const hasActiveChild = group.items.some((item) =>
                isActiveLink(item.href),
            );
            const manual = manuallyToggled[group.category];
            result[group.category] =
                manual !== undefined ? manual : hasActiveChild;
        });

        return result;
    }, [filteredNav, manuallyToggled, isActiveLink]);

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

    const dashboardUrl = useMemo(() => getUserDashboard(user), [user]);
    const userRole = useMemo(
        () => getRoleName(user?.roles?.[0]) || 'admin',
        [user],
    );
    const userInitials = useMemo(
        () => (user ? getInitials(user.name) : '??'),
        [user],
    );
    const unreadCount = 2; // TODO: wire to real data

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
            <div className="flex h-screen flex-col overflow-hidden bg-gray-50 font-sans dark:bg-neutral-950">
                <style>{`
                    @keyframes slideDown {
                        from { opacity: 0; transform: translateY(-4px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                    .animate-slideDown { animation: slideDown 0.15s ease-out; }
                    .line-clamp-2 {
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                    }
                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(4px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                    .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
                    .scrollbar-thin::-webkit-scrollbar { width: 4px; }
                    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
                    .scrollbar-thin::-webkit-scrollbar-thumb {
                        background-color: rgba(161, 161, 170, 0.3);
                        border-radius: 20px;
                    }
                    .dark .scrollbar-thin::-webkit-scrollbar-thumb {
                        background-color: rgba(63, 63, 70, 0.4);
                    }
                `}</style>

                {/* ─── Global Top Banner ─── */}
                <AnnouncementBanner />

                {/* ─── Split workspace layout ─── */}
                <div className="flex min-h-0 flex-1 overflow-hidden">
                    {/* ─── Mobile backdrop ─── */}
                    {mobileMenuOpen && (
                        <div
                            className="fixed inset-0 z-[60] bg-zinc-900/40 backdrop-blur-sm transition-opacity duration-300 lg:hidden"
                            onClick={() => setMobileMenuOpen(false)}
                        />
                    )}

                    {/* ─── Mobile sidebar ─── */}
                    <aside
                        className={`fixed inset-y-0 left-0 z-[70] flex w-72 transform flex-col border-r border-zinc-200 bg-white shadow-xl transition-transform duration-300 ease-out lg:hidden dark:border-zinc-800/80 dark:bg-zinc-900 ${
                            mobileMenuOpen
                                ? 'translate-x-0'
                                : '-translate-x-full'
                        }`}
                    >
                        <div className="absolute top-4 right-4 z-10">
                            <button
                                onClick={() => setMobileMenuOpen(false)}
                                className="dark:hover:bg-zinc-850 rounded-lg p-1.5 text-zinc-400 transition-colors hover:bg-zinc-100 hover:text-zinc-600 dark:hover:text-zinc-300"
                            >
                                <XMarkIcon className="h-5 w-5" />
                            </button>
                        </div>
                        <SidebarNav isOpen={true} {...sidebarProps} />
                    </aside>

                    {/* ─── Desktop sidebar ─── */}
                    <aside
                        className={`relative inset-y-0 left-0 z-50 hidden flex-col border-r border-zinc-200 bg-white transition-all duration-300 ease-out lg:flex dark:border-zinc-800/80 dark:bg-zinc-900 ${
                            sidebarOpen ? 'w-72' : 'w-20'
                        }`}
                    >
                        <SidebarNav isOpen={sidebarOpen} {...sidebarProps} />
                        <button
                            onClick={() => setSidebarOpen((prev) => !prev)}
                            className="absolute top-20 -right-3 z-50 rounded-full border border-zinc-200 bg-white p-1 text-zinc-400 shadow-sm transition-all duration-200 hover:scale-105 hover:text-blue-600 hover:shadow dark:border-zinc-800 dark:bg-zinc-900"
                            title={
                                sidebarOpen
                                    ? t('common.collapse_sidebar')
                                    : t('common.expand_sidebar')
                            }
                        >
                            <ChevronLeftIcon
                                className={`h-3.5 w-3.5 transition-transform duration-300 ${!sidebarOpen && 'rotate-180'}`}
                            />
                        </button>
                    </aside>

                    {/* ─── Main content area ─── */}
                    <div className="flex min-w-0 flex-1 flex-col">
                        {/* ─── Top header ─── */}
                        <header className="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-zinc-200 bg-white/95 px-4 backdrop-blur-xl lg:px-6 dark:border-zinc-800/80 dark:bg-zinc-900/95">
                            <div className="flex min-w-0 flex-1 items-center gap-4">
                                <button
                                    onClick={() => setMobileMenuOpen(true)}
                                    className="-ml-2 rounded-lg p-2 text-zinc-500 transition-colors duration-150 hover:bg-zinc-100 lg:hidden dark:hover:bg-zinc-800"
                                >
                                    <Bars3Icon className="h-5 w-5" />
                                </button>

                                <div className="min-w-0">
                                    <h1 className="truncate text-base font-bold tracking-tight text-zinc-900 lg:text-lg dark:text-zinc-50">
                                        {headerTitle}
                                    </h1>
                                </div>
                            </div>

                            <div className="ml-4 flex items-center gap-2 lg:gap-3">
                                {headerAction && (
                                    <div className="hidden md:block">
                                        {headerAction}
                                    </div>
                                )}

                                <LocaleSwitcher variant="full" />

                                <button
                                    onClick={toggleTheme}
                                    role="switch"
                                    aria-checked={isDark}
                                    aria-label={t('common.toggle_dark_mode')}
                                    className={`relative inline-flex h-8 w-14 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors duration-300 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:focus:ring-offset-zinc-900 ${
                                        isDark
                                            ? 'bg-blue-600'
                                            : 'bg-zinc-300 dark:bg-zinc-600'
                                    }`}
                                >
                                    <span className="sr-only">
                                        {t('common.toggle_theme')}
                                    </span>
                                    <span
                                        className={`flex h-6 w-6 items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300 ${
                                            isDark
                                                ? 'translate-x-6'
                                                : 'translate-x-0.5'
                                        }`}
                                    >
                                        {isDark ? (
                                            <MoonIcon className="h-3.5 w-3.5 text-blue-600" />
                                        ) : (
                                            <SunIcon className="h-3.5 w-3.5 text-amber-500" />
                                        )}
                                    </span>
                                </button>

                                <div className="relative" ref={notifRef}>
                                    <button
                                        onClick={() => {
                                            setNotifOpen((prev) => !prev);
                                            setUserMenuOpen(false);
                                        }}
                                        className="group relative rounded-lg p-2 text-zinc-400 transition-colors duration-150 hover:bg-zinc-100 hover:text-blue-600 dark:hover:bg-zinc-800"
                                        aria-label={t('common.notifications')}
                                    >
                                        <BellIcon className="h-8 w-7" />
                                        {unreadCount > 0 && (
                                            <span className="absolute top-1.5 right-1.5 flex h-[18px] min-w-[18px] items-center justify-center rounded-full border-2 border-white bg-red-500 px-1 text-[10px] font-bold text-white dark:border-zinc-900">
                                                {unreadCount}
                                            </span>
                                        )}
                                    </button>

                                    {notifOpen && (
                                        <NotificationsDropdown
                                            onClose={closeAllDropdowns}
                                        />
                                    )}
                                </div>

                                <div className="relative" ref={userMenuRef}>
                                    <button
                                        onClick={() => {
                                            setUserMenuOpen((prev) => !prev);
                                            setNotifOpen(false);
                                        }}
                                        className="group flex cursor-pointer items-center gap-3 rounded-xl p-1.5 transition-colors duration-150 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                                        aria-label={t('common.user_menu')}
                                    >
                                        <div className="hidden text-right md:block">
                                            <p className="text-xs leading-tight font-bold text-zinc-900 dark:text-zinc-50">
                                                {user?.name ||
                                                    t('common.staff_user')}
                                            </p>
                                            <p className="text-[10px] leading-tight font-medium text-zinc-400">
                                                {user?.department?.name ||
                                                    t('common.online')}
                                            </p>
                                        </div>
                                        <div className="relative">
                                            <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-sm font-bold text-white shadow-sm transition-all duration-200 group-hover:scale-102 group-hover:shadow">
                                                {userInitials}
                                            </div>
                                            {user?.is_active && (
                                                <span className="absolute -right-0.5 -bottom-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-green-500 dark:border-zinc-900" />
                                            )}
                                        </div>
                                        <ChevronDownIcon className="hidden h-3.5 w-3.5 text-zinc-400 transition-colors group-hover:text-zinc-600 md:block" />
                                    </button>

                                    {/* FIXED: Re-added conditional rendering wrapper for the user dropdown */}
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

                        {/* ─── Page content ─── */}
                        <main className="flex-1 overflow-y-auto bg-zinc-50/50 dark:bg-zinc-950">
                            <div className="mx-auto max-w-[1600px] p-4 lg:p-8">
                                <div className="animate-fadeIn">{children}</div>
                            </div>
                        </main>
                    </div>
                </div>
            </div>

            {/* ─── Confirm Logout Warning Modal ─── */}
            {logoutModalOpen && (
                <div
                    className="animate-fadeIn fixed inset-0 z-[100] flex items-center justify-center bg-zinc-950/40 p-4 backdrop-blur-sm dark:bg-zinc-950/60"
                    onClick={() => setLogoutModalOpen(false)}
                >
                    <div
                        className="animate-slideDown w-full max-w-sm scale-100 transform rounded-2xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-800 dark:bg-zinc-900"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="flex items-center gap-4">
                            <div className="shrink-0 rounded-xl bg-red-50 p-2.5 text-red-600 dark:bg-red-950/30 dark:text-red-400">
                                <ArrowRightStartOnRectangleIcon className="h-5 w-5" />
                            </div>
                            <div>
                                <h3 className="text-base font-bold text-zinc-900 dark:text-zinc-50">
                                    {t('nav.confirm_sign_out') ||
                                        'Sign Out of SanaGo?'}
                                </h3>
                                <p className="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                    {t('nav.sign_out_session') ||
                                        'You will be returned to the login screen.'}
                                </p>
                            </div>
                        </div>

                        <p className="mt-4 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">
                            Are you sure you want to end your active session?
                            Please make sure any working changes are safely
                            saved before proceeding.
                        </p>

                        <div className="mt-6 flex gap-3">
                            <button
                                onClick={() => setLogoutModalOpen(false)}
                                className="flex-1 rounded-lg border border-zinc-200 px-4 py-2 text-xs font-medium text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800"
                            >
                                Cancel
                            </button>
                            <button
                                onClick={() => {
                                    setLogoutModalOpen(false);
                                    router.post('/logout');
                                }}
                                className="flex-1 rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-all hover:bg-red-700 hover:shadow active:scale-98 dark:bg-red-700 dark:hover:bg-red-600"
                            >
                                Log Out
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </TranslationProvider>
    );
}
