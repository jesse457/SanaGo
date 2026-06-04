import {
    Squares2X2Icon, MoonIcon, SunIcon,
    BuildingOfficeIcon, ChevronDownIcon,
    BeakerIcon, UserGroupIcon,
    ClipboardDocumentCheckIcon,
    BanknotesIcon, AdjustmentsHorizontalIcon,
    ShieldCheckIcon, Bars3Icon, XMarkIcon,
    Cog6ToothIcon, HeartIcon,
    ArrowRightStartOnRectangleIcon,
    BellIcon, UserCircleIcon,
    ArrowTrendingUpIcon,
    ChevronLeftIcon,
    ClockIcon,
    ExclamationCircleIcon,
    QuestionMarkCircleIcon,
} from '@heroicons/react/24/outline';
import { Link, usePage, router } from '@inertiajs/react';
import React, { useState, useEffect, useMemo, useCallback, memo, useRef } from 'react';
import { useTranslation, TranslationProvider } from '@/hooks/useTranslation';
import { LocaleSwitcher } from '@/Components/LocaleSwitcher';
import { FlashHandler } from '@/Components/FlashHandler';
import AnnouncementBanner from '@/Components/AnnouncementBanner';

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
        icon: <UserGroupIcon className="w-4 h-4" />,
        items: [
            { label: 'Patient Directory', labelKey: 'nav.patient_directory', href: '/patients', permission: 'view-patients' },
            { label: 'Admissions', labelKey: 'nav.admissions', href: '/admissions', permission: 'view-patients' },
            { label: 'Appointments', labelKey: 'nav.appointments', href: '/appointments', permission: 'view-appointments' },
        ]
    },
    {
        category: 'Clinical',
        categoryKey: 'nav.category.clinical',
        icon: <ClipboardDocumentCheckIcon className="w-4 h-4" />,
        items: [
            { label: 'Consultations', labelKey: 'nav.consultations', href: '/consultations/create', permission: 'view-consultations' },
            { label: 'Medical Records', labelKey: 'nav.medical_records', href: '/medical-records', permission: 'view-consultations' },
            { label: 'Patient Vitals', labelKey: 'nav.patient_vitals', href: '/vitals', permission: 'view-patients' },
        ]
    },
    {
        category: 'Surgery & Emergency',
        categoryKey: 'nav.category.surgery_emergency',
        icon: <HeartIcon className="w-4 h-4" />,
        items: [
            { label: 'Surgeries', labelKey: 'nav.surgeries', href: '/surgeries', permission: 'create-consultations' },
            { label: 'Emergency Cases', labelKey: 'nav.emergency_cases', href: '/emergencies', permission: 'create-admissions' },
        ]
    },
    {
        category: 'Lab & Diagnostics',
        categoryKey: 'nav.category.lab_diagnostics',
        icon: <BeakerIcon className="w-4 h-4" />,
        items: [
            { label: 'Test Catalog', labelKey: 'nav.test_catalog', href: '/lab/tests', permission: 'view-lab-tests' },
            { label: 'Lab Requests', labelKey: 'nav.lab_requests', href: '/lab/requests', permission: 'view-lab-tests' },
            { label: 'Lab Results', labelKey: 'nav.lab_results', href: '/lab/results', permission: 'view-lab-results' },
        ]
    },
    {
        category: 'Pharmacy',
        categoryKey: 'nav.category.pharmacy',
        icon: <AdjustmentsHorizontalIcon className="w-4 h-4" />,
        items: [
            { label: 'Drug Inventory', labelKey: 'nav.drug_inventory', href: '/pharmacy/drugs', permission: 'view-drugs' },
            { label: 'Dispensation', labelKey: 'nav.dispensation', href: '/pharmacy/dispensation', permission: 'dispense-drugs' },
        ]
    },
    {
        category: 'Infrastructure',
        categoryKey: 'nav.category.infrastructure',
        icon: <BuildingOfficeIcon className="w-4 h-4" />,
        items: [
            { label: 'Facility Map', labelKey: 'nav.facility_map', href: '/infrastructure', permission: 'manage-infrastructure' },
            { label: 'Beds', labelKey: 'nav.beds', href: '/beds', permission: 'manage-infrastructure' },
            { label: 'Wards', labelKey: 'nav.wards', href: '/wards', permission: 'manage-infrastructure' },
            { label: 'Bed Types', labelKey: 'nav.bed_types', href: '/bed-types', permission: 'manage-infrastructure' },
            { label: 'Supply Chain', labelKey: 'nav.supply_chain', href: '/supplies', permission: 'view-supplies' },
            { label: 'Departments', labelKey: 'nav.departments', href: '/departments', permission: 'manage-departments' },
        ]
    },
    {
        category: 'Finance',
        categoryKey: 'nav.category.finance',
        icon: <BanknotesIcon className="w-4 h-4" />,
        items: [
            { label: 'Invoices', labelKey: 'nav.invoices', href: '/finance/invoices', permission: 'view-finance' },
            { label: 'Revenue Analytics', labelKey: 'nav.revenue_analytics', href: '/finance/revenue', permission: 'view-finance' },
            { label: 'Expenditures', labelKey: 'nav.expenditures', href: '/finance/expenditures', permission: 'view-finance' },
        ]
    },
    {
        category: 'Administration',
        categoryKey: 'nav.category.administration',
        icon: <ShieldCheckIcon className="w-4 h-4" />,
        items: [
            { label: 'User Management', labelKey: 'nav.user_management', href: '/users', permission: 'manage-users' },
            { label: 'Roles & Access', labelKey: 'nav.roles_access', href: '/roles', permission: 'manage-roles' },
            { label: 'Shifts Roster', labelKey: 'nav.shifts_roster', href: '/shifts', permission: 'manage-users' },
            { label: 'Audit Logs', labelKey: 'nav.audit_logs', href: '/audit-logs', permission: 'view-audit-logs' },
        ]
    },
    {
        category: 'Support',
        categoryKey: 'nav.category.support',
        icon: <QuestionMarkCircleIcon className="w-4 h-4" />,
        items: [
            { label: 'All Feedback', labelKey: 'nav.all_feedback', href: '/feedback' },
        ]
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
    return typeof role === 'string' ? role : role?.name ?? '';
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
        .map(p => p.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);
}

function getRoleBadge(role: string): string {
    const map: Record<string, string> = {
        admin: 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
        doctor: 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
        reception: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
        lab_tech: 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
        pharma: 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 border-teal-100 dark:border-teal-800',
    };

    return map[role] || 'bg-slate-50 dark:bg-neutral-700 text-slate-700 dark:text-neutral-300 border-slate-100 dark:border-neutral-600';
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
            <div className="h-16 flex items-center px-4 border-b border-zinc-100 dark:border-zinc-800/80 shrink-0">
                <Link href={dashboardUrl} className="flex items-center gap-3 overflow-hidden w-full">
                    <div className="h-9 w-9 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-sm shrink-0 transition-transform hover:scale-105 duration-200">
                        <HeartIcon className="w-5 h-5" />
                    </div>
                    {isOpen && (
                        <div className="overflow-hidden">
                            <span className="font-extrabold text-base text-zinc-900 dark:text-zinc-50 tracking-tight whitespace-nowrap">
                                Sana<span className="text-blue-600 dark:text-blue-500">Go</span>
                            </span>
                            <span className="block text-[9px] text-zinc-400 font-semibold uppercase tracking-wider -mt-0.5">
                                {t('layout.hms_platform')}
                            </span>
                        </div>
                    )}
                </Link>
            </div>

            <nav className="flex-1 overflow-y-auto py-4 px-3 space-y-1.5 overscroll-contain scrollbar-thin scrollbar-thumb-zinc-200 dark:scrollbar-thumb-zinc-800">
                <Link
                    href={dashboardUrl}
                    className={`flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-150 group relative ${
                        isDashboardActive
                            ? 'bg-blue-50/80 text-blue-600 dark:bg-blue-950/30 dark:text-blue-400 font-medium'
                            : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-50 dark:hover:bg-zinc-800/40'
                    }`}
                >
                    <Squares2X2Icon className={`w-4 h-4 shrink-0 transition-colors ${
                        isDashboardActive ? 'text-blue-600 dark:text-blue-400' : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300'
                    }`} />
                    {isOpen && <span className="text-sm font-medium">{t('nav.dashboard')}</span>}
                </Link>

                {filteredNav.map((group) => {
                    const hasActiveChild = group.items.some(item => isActive(item.href));
                    const isExpanded = openCategories[group.category];

                    return (
                        <div key={group.category} className="space-y-0.5">
                            <button
                                onClick={() => onToggleCategory(group.category)}
                                className={`w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-150 group text-left ${
                                    isExpanded
                                        ? 'text-zinc-900 dark:text-zinc-100 font-medium'
                                        : 'text-zinc-500 dark:text-zinc-400'
                                } ${
                                    hasActiveChild && !isExpanded
                                        ? 'bg-zinc-50/60 dark:bg-zinc-800/10 text-zinc-900 dark:text-zinc-100'
                                        : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/40'
                                }`}
                            >
                                <div className="flex items-center gap-3 min-w-0">
                                    <div className={`transition-colors ${
                                        isExpanded || hasActiveChild
                                            ? 'text-blue-600 dark:text-blue-400'
                                            : 'text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300'
                                    }`}>
                                        {group.icon}
                                    </div>
                                    {isOpen && (
                                        <span className="text-xs font-semibold tracking-wide truncate">
                                            {t(group.categoryKey)}
                                        </span>
                                    )}
                                </div>
                                {isOpen && (
                                    <div className="flex items-center gap-1.5 shrink-0">
                                        {group.badge && (
                                            <span className="px-1.5 py-0.5 text-[9px] font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-full">
                                                {group.badge}
                                            </span>
                                        )}
                                        <ChevronDownIcon
                                            className={`w-3.5 h-3.5 text-zinc-400 dark:text-zinc-500 transition-transform duration-200 ${
                                                isExpanded ? 'rotate-180' : ''
                                            }`}
                                        />
                                    </div>
                                )}
                            </button>

                            {isOpen && isExpanded && (
                                <div className="relative pl-5 ml-4 border-l border-zinc-200/60 dark:border-zinc-800/80 space-y-0.5 py-1 animate-slideDown">
                                    {group.items.map((item) => {
                                        const isItemActive = isActive(item.href);

                                        return (
                                            <Link
                                                key={item.href}
                                                href={item.href}
                                                className={`group/item flex items-center justify-between py-1.5 pl-3 pr-2 text-xs rounded-md transition-all duration-150 ${
                                                    isItemActive
                                                        ? 'text-blue-600 dark:text-blue-400 font-semibold bg-blue-50/40 dark:bg-blue-950/20 border-l-2 border-blue-500 dark:border-blue-400 -ml-[21.5px] pl-[20px]'
                                                        : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30'
                                                }`}
                                            >
                                                <span className="truncate">{t(item.labelKey)}</span>
                                                {item.badge && (
                                                    <span className={`px-1.5 py-0.5 text-[9px] font-bold rounded-full transition-colors ${
                                                        isItemActive
                                                            ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400'
                                                            : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 group-hover/item:bg-zinc-200'
                                                    }`}>
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

            <div className="p-3 border-t border-zinc-100 dark:border-zinc-800/80 space-y-1">
                <Link
                    href="/settings"
                    className="flex items-center gap-3 px-3 py-2 text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition-all duration-150 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/40 group"
                >
                    <Cog6ToothIcon className="w-4 h-4 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors" />
                    {isOpen && <span className="text-sm font-medium">{t('nav.settings')}</span>}
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
            icon: <UserGroupIcon className="w-5 h-5" />
        },
        {
            id: 2,
            titleKey: 'notifications.lab_results_title',
            messageKey: 'notifications.lab_results_message',
            time: '15 min ago',
            read: false,
            type: 'success',
            icon: <BeakerIcon className="w-5 h-5" />
        },
        {
            id: 3,
            titleKey: 'notifications.low_stock_title',
            messageKey: 'notifications.low_stock_message',
            time: '1 hour ago',
            read: true,
            type: 'warning',
            icon: <ExclamationCircleIcon className="w-5 h-5" />
        },
        {
            id: 4,
            titleKey: 'notifications.appointment_title',
            messageKey: 'notifications.appointment_message',
            time: '2 hours ago',
            read: true,
            type: 'info',
            icon: <ClockIcon className="w-5 h-5" />
        },
    ];

    const unreadCount = notifications.filter(n => !n.read).length;
    const [filter, setFilter] = useState<'all' | 'unread'>('all');

    const filteredNotifications = filter === 'unread'
        ? notifications.filter(n => !n.read)
        : notifications;

    const getTypeStyles = (type: string) => {
        const styles = {
            info: 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
            success: 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800',
            warning: 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800',
        };

        return styles[type as keyof typeof styles] || styles.info;
    };

    return (
        <div className="absolute right-0 top-full mt-2 w-96 bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 shadow-2xl z-20 overflow-hidden animate-slideDown">
            <div className="p-4 border-b border-gray-100 dark:border-neutral-700">
                <div className="flex items-center justify-between mb-3">
                    <div className="flex items-center gap-2">
                        <h3 className="text-base font-bold text-gray-900 dark:text-neutral-100">{t('common.notifications')}</h3>
                        {unreadCount > 0 && (
                            <span className="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] font-bold">
                                {unreadCount} {t('common.new')}
                            </span>
                        )}
                    </div>
                    <div className="flex gap-1">
                        <button
                            onClick={() => setFilter('all')}
                            className={`px-2 py-1 text-xs font-medium rounded-md transition-colors ${
                                filter === 'all'
                                    ? 'bg-gray-100 dark:bg-neutral-700 text-gray-900 dark:text-neutral-100'
                                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-neutral-300'
                            }`}
                        >
                            {t('common.all')}
                        </button>
                        <button
                            onClick={() => setFilter('unread')}
                            className={`px-2 py-1 text-xs font-medium rounded-md transition-colors ${
                                filter === 'unread'
                                    ? 'bg-gray-100 dark:bg-neutral-700 text-gray-900 dark:text-neutral-100'
                                    : 'text-gray-500 hover:text-gray-700 dark:hover:text-neutral-300'
                            }`}
                        >
                            {t('common.unread')}
                        </button>
                    </div>
                </div>
                <button className="text-xs text-blue-600 font-semibold hover:underline">
                    {t('common.mark_all_read')}
                </button>
            </div>

            <div className="max-h-80 overflow-y-auto overscroll-contain">
                {filteredNotifications.map(n => (
                    <div
                        key={n.id}
                        className={`px-4 py-3 border-b border-gray-50 dark:border-neutral-700/50 hover:bg-gray-50 dark:hover:bg-neutral-700/30 transition-all cursor-pointer group ${
                            !n.read ? 'border-l-4 border-l-blue-600 bg-blue-50/30 dark:bg-blue-900/5' : ''
                        }`}
                    >
                        <div className="flex gap-3">
                            <div className={`p-2 rounded-lg ${getTypeStyles(n.type)} shrink-0`}>
                                {n.icon}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex items-start justify-between gap-2">
                                    <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate">
                                        {t(n.titleKey)}
                                    </p>
                                    {!n.read && (
                                        <span className="w-2 h-2 mt-1.5 bg-blue-600 rounded-full shrink-0" />
                                    )}
                                </div>
                                <p className="text-xs text-gray-600 dark:text-neutral-400 mt-1 line-clamp-2">
                                    {t(n.messageKey)}
                                </p>
                                <p className="text-[10px] text-gray-400 mt-2 font-medium">
                                    {n.time}
                                </p>
                            </div>
                        </div>
                    </div>
                ))}

                {filteredNotifications.length === 0 && (
                    <div className="px-4 py-8 text-center">
                        <BellIcon className="w-12 h-12 text-gray-300 dark:text-neutral-600 mx-auto mb-3" />
                        <p className="text-sm text-gray-500 dark:text-neutral-400 font-medium">
                            {t('common.no_notifications')}
                        </p>
                        <p className="text-xs text-gray-400 dark:text-neutral-500 mt-1">
                            {t('common.all_caught_up')}
                        </p>
                    </div>
                )}
            </div>

            <div className="p-3 border-t border-gray-100 dark:border-neutral-700 bg-gray-50/50 dark:bg-neutral-800/50">
                <Link
                    href="/notifications"
                    className="flex items-center justify-center gap-2 text-sm text-blue-600 font-semibold hover:underline transition-colors"
                    onClick={onClose}
                >
                    {t('common.view_all_notifications')}
                    <ArrowTrendingUpIcon className="w-4 h-4" />
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
        <div className="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-neutral-800 rounded-2xl border border-gray-200 dark:border-neutral-700 shadow-2xl z-20 overflow-hidden animate-slideDown">
            <div className="p-4 bg-gradient-to-br from-gray-50 to-white dark:from-neutral-800 dark:to-neutral-800 border-b border-gray-100 dark:border-neutral-700">
                <div className="flex items-center gap-3">
                    <div className="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg shrink-0">
                        {user ? getInitials(user.name) : '??'}
                    </div>
                    <div className="flex-1 min-w-0">
                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100 truncate">
                            {user?.name || t('common.staff_user')}
                        </p>
                        <p className="text-xs text-gray-500 dark:text-neutral-400 truncate">
                            {user?.email}
                        </p>
                        <div className="flex items-center gap-2 mt-1.5">
                            <span className={`px-2 py-0.5 rounded-md text-[9px] font-bold uppercase border ${getRoleBadge(userRole)}`}>
                                {userRole.replace('_', ' ')}
                            </span>
                            {user?.department && (
                                <span className="px-2 py-0.5 rounded-md text-[9px] font-medium bg-gray-100 dark:bg-neutral-700 text-gray-600 dark:text-neutral-400">
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
                    className="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors duration-150 group"
                    onClick={onClose}
                >
                    <div className="p-1.5 rounded-lg bg-gray-100 dark:bg-neutral-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                        <UserCircleIcon className="w-4 h-4 text-gray-500 group-hover:text-blue-600 transition-colors" />
                    </div>
                    <div className="flex-1">
                        <p className="font-medium">{t('nav.my_profile')}</p>
                        <p className="text-[10px] text-gray-400">{t('nav.profile_description')}</p>
                    </div>
                </Link>

                <Link
                    href="/settings"
                    className="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors duration-150 group"
                    onClick={onClose}
                >
                    <div className="p-1.5 rounded-lg bg-gray-100 dark:bg-neutral-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 transition-colors">
                        <Cog6ToothIcon className="w-4 h-4 text-gray-500 group-hover:text-blue-600 transition-colors" />
                    </div>
                    <div className="flex-1">
                        <p className="font-medium">{t('nav.settings')}</p>
                        <p className="text-[10px] text-gray-400">{t('nav.settings_description')}</p>
                    </div>
                </Link>
            </div>

            <div className="border-t border-gray-100 dark:border-neutral-700 pt-1">
                <button
                    onClick={() => {
                        onClose();
                        onLogoutClick();
                    }}
                    className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150 group text-left"
                >
                    <div className="p-1.5 rounded-lg bg-red-50 dark:bg-red-900/20 group-hover:bg-red-100 dark:group-hover:bg-red-900/30 transition-colors">
                        <ArrowRightStartOnRectangleIcon className="w-4 h-4" />
                    </div>
                    <div className="flex-1">
                        <p className="font-medium">{t('nav.sign_out')}</p>
                        <p className="text-[10px] text-red-400">{t('nav.sign_out_description')}</p>
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
    const tenant = (props as any)?.tenant as { id: string; name: string; slug: string } | null;
    const isAdmin = user?.roles?.some((r: any) => r === 'admin' || r?.name === 'admin') ?? false;

    const [sidebarOpen, setSidebarOpen] = useState(true);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [manuallyToggled, setManuallyToggled] = useState<Record<string, boolean>>({});
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

    useEffect(() => {
        const handleKeyPress = (e: KeyboardEvent) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                setSidebarOpen(prev => !prev);
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

    const userHasPermission = useCallback((perm: string): boolean => {
        if (!user?.permissions) {
            return false;
        }
        return user.permissions.some((p: any) => p === perm || p?.name === perm);
    }, [user?.permissions]);

    const filteredNav = useMemo(() => {
        if (isAdmin) {
            return NAV_CONFIG;
        }
        return NAV_CONFIG
            .map(group => ({
                ...group,
                items: group.items.filter(item => {
                    if (!item.permission) {
                        return true;
                    }
                    return userHasPermission(item.permission);
                })
            }))
            .filter(group => group.items.length > 0);
    }, [isAdmin, userHasPermission]);

    // Sanitizes query parameters and anchor tags before running active matching
    const isActiveLink = useCallback((href: string) => {
        const currentPath = url.split('?')[0].split('#')[0].replace(/\/$/, '');
        const targetPath = href.split('?')[0].split('#')[0].replace(/\/$/, '');

        return currentPath === targetPath || currentPath.startsWith(targetPath + '/');
    }, [url]);

    const openCategories = useMemo(() => {
        const result: Record<string, boolean> = {};
        filteredNav.forEach(group => {
            const hasActiveChild = group.items.some(item => isActiveLink(item.href));
            const manual = manuallyToggled[group.category];
            result[group.category] = manual !== undefined ? manual : hasActiveChild;
        });
        return result;
    }, [filteredNav, manuallyToggled, isActiveLink]);

    const toggleCategory = useCallback((category: string) => {
        setManuallyToggled(prev => ({ ...prev, [category]: !openCategories[category] }));
    }, [openCategories]);

    const toggleTheme = useCallback(() => {
        setIsDark(prev => !prev);
    }, []);

    const closeAllDropdowns = useCallback(() => {
        setUserMenuOpen(false);
        setNotifOpen(false);
    }, []);

    const dashboardUrl = useMemo(() => getUserDashboard(user), [user]);
    const userRole = useMemo(() => getRoleName(user?.roles?.[0]) || 'admin', [user]);
    const userInitials = useMemo(() => user ? getInitials(user.name) : '??', [user]);
    const unreadCount = 2; // TODO: wire to real data

    const sidebarProps = useMemo(() => ({
        filteredNav,
        openCategories,
        onToggleCategory: toggleCategory,
        dashboardUrl,
        isActive: isActiveLink,
    }), [filteredNav, openCategories, toggleCategory, dashboardUrl, isActiveLink]);

    return (
        <TranslationProvider>
            <FlashHandler />
            <div className="flex flex-col h-screen bg-gray-50 dark:bg-neutral-950 font-sans overflow-hidden">
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
                <div className="flex flex-1 min-h-0 overflow-hidden">
                    {/* ─── Mobile backdrop ─── */}
                    {mobileMenuOpen && (
                        <div
                            className="fixed inset-0 z-[60] bg-zinc-900/40 backdrop-blur-sm lg:hidden transition-opacity duration-300"
                            onClick={() => setMobileMenuOpen(false)}
                        />
                    )}

                    {/* ─── Mobile sidebar ─── */}
                    <aside
                        className={`fixed inset-y-0 left-0 z-[70] w-72 bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800/80 flex flex-col transform transition-transform duration-300 ease-out lg:hidden shadow-xl ${
                            mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
                        }`}
                    >
                        <div className="absolute right-4 top-4 z-10">
                            <button
                                onClick={() => setMobileMenuOpen(false)}
                                className="p-1.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-850 rounded-lg transition-colors"
                            >
                                <XMarkIcon className="w-5 h-5" />
                            </button>
                        </div>
                        <SidebarNav isOpen={true} {...sidebarProps} />
                    </aside>

                    {/* ─── Desktop sidebar ─── */}
                    <aside
                        className={`hidden lg:flex relative inset-y-0 left-0 z-50 bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800/80 flex-col transition-all duration-300 ease-out ${
                            sidebarOpen ? 'w-72' : 'w-20'
                        }`}
                    >
                        <SidebarNav isOpen={sidebarOpen} {...sidebarProps} />
                        <button
                            onClick={() => setSidebarOpen(prev => !prev)}
                            className="absolute -right-3 top-20 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-full p-1 text-zinc-400 hover:text-blue-600 shadow-sm hover:shadow z-50 transition-all duration-200 hover:scale-105"
                            title={sidebarOpen ? t('common.collapse_sidebar') : t('common.expand_sidebar')}
                        >
                            <ChevronLeftIcon className={`w-3.5 h-3.5 transition-transform duration-300 ${!sidebarOpen && 'rotate-180'}`} />
                        </button>
                    </aside>

                    {/* ─── Main content area ─── */}
                    <div className="flex-1 flex flex-col min-w-0">
                        {/* ─── Top header ─── */}
                        <header className="h-16 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-xl border-b border-zinc-200 dark:border-zinc-800/80 px-4 lg:px-6 flex items-center justify-between sticky top-0 z-40">
                            <div className="flex items-center gap-4 flex-1 min-w-0">
                                <button
                                    onClick={() => setMobileMenuOpen(true)}
                                    className="p-2 -ml-2 text-zinc-500 lg:hidden hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors duration-150"
                                >
                                    <Bars3Icon className="w-5 h-5" />
                                </button>

                                <div className="min-w-0">
                                    <h1 className="text-base lg:text-lg font-bold text-zinc-900 dark:text-zinc-50 tracking-tight truncate">
                                        {headerTitle}
                                    </h1>
                                </div>
                            </div>

                            <div className="flex items-center gap-2 lg:gap-3 ml-4">
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
                                    className={`relative inline-flex h-8 w-14 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 ${
                                        isDark ? 'bg-blue-600' : 'bg-zinc-300 dark:bg-zinc-600'
                                    }`}
                                >
                                    <span className="sr-only">{t('common.toggle_theme')}</span>
                                    <span
                                        className={`flex h-6 w-6 items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300 ${
                                            isDark ? 'translate-x-6' : 'translate-x-0.5'
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
                                            setNotifOpen(prev => !prev);
                                            setUserMenuOpen(false);
                                        }}
                                        className="relative p-2 text-zinc-400 hover:text-blue-600 transition-colors duration-150 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 group"
                                        aria-label={t('common.notifications')}
                                    >
                                        <BellIcon className="w-7 h-8" />
                                        {unreadCount > 0 && (
                                            <span className="absolute top-1.5 right-1.5 flex items-center justify-center min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full px-1 border-2 border-white dark:border-zinc-900">
                                                {unreadCount}
                                            </span>
                                        )}
                                    </button>

                                    {notifOpen && <NotificationsDropdown onClose={closeAllDropdowns} />}
                                </div>

                                <div className="relative" ref={userMenuRef}>
                                    <button
                                        onClick={() => {
                                            setUserMenuOpen(prev => !prev);
                                            setNotifOpen(false);
                                        }}
                                        className="flex items-center gap-3 p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-colors duration-150 cursor-pointer group"
                                        aria-label={t('common.user_menu')}
                                    >
                                        <div className="hidden md:block text-right">
                                            <p className="text-xs font-bold text-zinc-900 dark:text-zinc-50 leading-tight">
                                                {user?.name || t('common.staff_user')}
                                            </p>
                                            <p className="text-[10px] text-zinc-400 font-medium leading-tight">
                                                {user?.department?.name || t('common.online')}
                                            </p>
                                        </div>
                                        <div className="relative">
                                            <div className="h-9 w-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm group-hover:shadow transition-all duration-200 group-hover:scale-102">
                                                {userInitials}
                                            </div>
                                            {user?.is_active && (
                                                <span className="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-zinc-900 rounded-full" />
                                            )}
                                        </div>
                                        <ChevronDownIcon className="hidden md:block w-3.5 h-3.5 text-zinc-400 group-hover:text-zinc-600 transition-colors" />
                                    </button>

                                    {/* FIXED: Re-added conditional rendering wrapper for the user dropdown */}
                                    {userMenuOpen && (
                                        <UserMenuDropdown
                                            user={user}
                                            userRole={userRole}
                                            onClose={closeAllDropdowns}
                                            onLogoutClick={() => setLogoutModalOpen(true)}
                                        />
                                    )}
                                </div>
                            </div>
                        </header>

                        {/* ─── Page content ─── */}
                        <main className="flex-1 overflow-y-auto bg-zinc-50/50 dark:bg-zinc-950">
                            <div className="p-4 lg:p-8 max-w-[1600px] mx-auto">
                                <div className="animate-fadeIn">
                                    {children}
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            </div>

            {/* ─── Confirm Logout Warning Modal ─── */}
            {logoutModalOpen && (
                <div
                    className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/40 dark:bg-zinc-950/60 backdrop-blur-sm animate-fadeIn"
                    onClick={() => setLogoutModalOpen(false)}
                >
                    <div
                        className="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-sm w-full p-6 shadow-xl transform scale-100 animate-slideDown"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="flex items-center gap-4">
                            <div className="p-2.5 bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 rounded-xl shrink-0">
                                <ArrowRightStartOnRectangleIcon className="w-5 h-5" />
                            </div>
                            <div>
                                <h3 className="text-base font-bold text-zinc-900 dark:text-zinc-50">
                                    {t('nav.confirm_sign_out') || 'Sign Out of SanaGo?'}
                                </h3>
                                <p className="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {t('nav.sign_out_session') || 'You will be returned to the login screen.'}
                                </p>
                            </div>
                        </div>

                        <p className="mt-4 text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            Are you sure you want to end your active session? Please make sure any working changes are safely saved before proceeding.
                        </p>

                        <div className="flex gap-3 mt-6">
                            <button
                                onClick={() => setLogoutModalOpen(false)}
                                className="flex-1 py-2 px-4 rounded-lg border border-zinc-200 dark:border-zinc-800 text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                onClick={() => {
                                    setLogoutModalOpen(false);
                                    router.post('/logout');
                                }}
                                className="flex-1 py-2 px-4 rounded-lg bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white text-xs font-semibold shadow-sm hover:shadow active:scale-98 transition-all"
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
