import React, { useState, useMemo, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    HomeIcon,
    ChevronRightIcon,
    MagnifyingGlassIcon,
    MapPinIcon,
    CalendarDaysIcon,
    ArrowRightIcon,
    PlusIcon,
    CheckBadgeIcon,
    ClockIcon,
    ArchiveBoxIcon,
    UsersIcon,
    InboxIcon,
    XMarkIcon,
    Squares2X2Icon,
    ListBulletIcon,
} from '@heroicons/react/24/outline';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Admission {
    id: number;
    patient: { first_name: string; last_name: string; patient_uid: string };
    bed: { code: string; ward: { name: string } };
    attending_doctor: { name: string };
    status: 'Pending' | 'Admitted' | 'Discharged';
    admission_date: string;
    reason_for_admission: string;
}

interface Props {
    admissions: {
        data: Admission[];
        links: { url: string | null; label: string; active: boolean }[];
        total?: number;
        from?: number;
        to?: number;
    };
    stats: {
        total: number;
        active: number;
        pending: number;
        discharged: number;
    };
    filters: { search?: string; status?: string };
}

export default function AdmissionsIndex({ admissions, stats, filters }: Props) {
    const { t } = useTranslation();
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('table');
    const [searchQuery, setSearchQuery] = useState(filters.search || '');

    // Server-side search logic with 300ms debounce
    useEffect(() => {
        const timeout = setTimeout(() => {
            router.get(
                route('admissions.index'),
                { search: searchQuery, status: filters.status || '' },
                { preserveState: true, replace: true },
            );
        }, 300);
        return () => clearTimeout(timeout);
    }, [searchQuery]);

    const handleFilter = (status: string | null) => {
        router.get(
            route('admissions.index'),
            { search: searchQuery, status: status || '' },
            { preserveState: true },
        );
    };

    const clearFilters = () => {
        setSearchQuery('');
        router.get(route('admissions.index'), {}, { preserveState: true });
    };

    return (
        <AppLayout
            headerTitle={t('page.checkin.title')}
            headerSubtitle={t('page.checkin.subtitle')}
        >
            <Head title={t('page.checkin.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* 1. Breadcrumbs */}
                <nav className="flex text-xs font-semibold text-gray-500 uppercase dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-rose-600">
                            <HomeIcon className="mr-1 h-4 w-4" />{' '}
                            {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-bold text-rose-600">
                            {t('page.checkin.breadcrumb_admissions')}
                        </li>
                    </ol>
                </nav>

                {/* 2. Stats Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label={t('page.checkin.total_registry')}
                        value={stats.total}
                        icon={<UsersIcon />}
                        color="gray"
                    />
                    <StatCard
                        label={t('page.checkin.currently_admitted')}
                        value={stats.active}
                        icon={<CheckBadgeIcon />}
                        color="rose"
                    />
                    <StatCard
                        label={t('page.checkin.pending_placement')}
                        value={stats.pending}
                        icon={<ClockIcon />}
                        color="amber"
                    />
                    <StatCard
                        label={t('page.checkin.total_discharged')}
                        value={stats.discharged}
                        icon={<ArchiveBoxIcon />}
                        color="blue"
                    />
                </div>

                {/* 3. Controls / Search & Filters Bar */}
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    <div className="flex flex-grow flex-col gap-3 sm:flex-row lg:max-w-2xl">
                        {/* Search Input */}
                        <div className="relative flex-grow">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t(
                                    'page.checkin.search_placeholder',
                                )}
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                                className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm outline-none focus:ring-2 focus:ring-rose-500 dark:border-neutral-700 dark:bg-neutral-800"
                            />
                        </div>

                        {/* Status Quick Filters */}
                        <div className="flex gap-2">
                            {['Admitted', 'Pending', 'Discharged'].map((s) => (
                                <button
                                    key={s}
                                    onClick={() =>
                                        handleFilter(
                                            filters.status === s ? '' : s,
                                        )
                                    }
                                    className={`rounded-lg border px-3 py-2 text-xs font-bold transition-all ${
                                        filters.status === s
                                            ? 'border-rose-600 bg-rose-600 text-white shadow-sm'
                                            : 'border-gray-200 bg-white text-gray-600 hover:border-rose-400 hover:text-rose-600 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400'
                                    }`}
                                >
                                    {s === 'Admitted'
                                        ? t('page.checkin.admitted')
                                        : s === 'Pending'
                                          ? t('page.checkin.pending')
                                          : t('page.checkin.discharged')}
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Action buttons and View Mode Toggle */}
                    <div className="flex items-center gap-3 self-end lg:self-auto">
                        <Link
                            href={route('appointments.create')}
                            className="flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-rose-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.checkin.new_admission')}
                        </Link>
                        <div className="flex rounded-lg border border-gray-200 bg-white p-1 dark:border-neutral-700 dark:bg-neutral-800">
                            <button
                                onClick={() => setViewMode('grid')}
                                className={`rounded-md p-1.5 ${viewMode === 'grid' ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20' : 'text-gray-400 dark:text-neutral-500'}`}
                            >
                                <Squares2X2Icon className="h-5 w-5" />
                            </button>
                            <button
                                onClick={() => setViewMode('table')}
                                className={`rounded-md p-1.5 ${viewMode === 'table' ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/20' : 'text-gray-400 dark:text-neutral-500'}`}
                            >
                                <ListBulletIcon className="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>

                {/* 4. Main Content Area */}
                <AnimatePresence mode="wait">
                    {admissions.data.length === 0 ? (
                        /* Empty State Container */
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -20 }}
                            className="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white py-24 text-center dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <div className="mb-4 flex h-20 w-20 items-center justify-center rounded-full border border-gray-100 bg-gray-50 dark:border-neutral-600 dark:bg-neutral-700">
                                <InboxIcon className="h-10 w-10 text-gray-300" />
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 dark:text-neutral-100">
                                {t('page.checkin.empty_title')}
                            </h3>
                            <p className="mt-2 mb-8 max-w-sm text-sm text-gray-500 dark:text-neutral-500">
                                {t('page.checkin.empty_description')}
                            </p>
                            <div className="flex gap-4">
                                <button
                                    onClick={clearFilters}
                                    className="dark:bg-neutral-850 flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-700 transition-all hover:bg-gray-50 dark:border-neutral-700 dark:text-neutral-300"
                                >
                                    <XMarkIcon className="h-4 w-4" />{' '}
                                    {t('page.checkin.clear_filters')}
                                </button>
                                <Link
                                    href={route('appointments.create')}
                                    className="flex items-center gap-2 rounded-xl bg-rose-600 px-6 py-3 text-sm font-bold text-white shadow-lg transition-all hover:bg-rose-700"
                                >
                                    <PlusIcon className="h-4 w-4" />{' '}
                                    {t('page.checkin.new_admission')}
                                </Link>
                            </div>
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        /* Grid View layout */
                        <motion.div
                            key="grid"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                        >
                            {admissions.data.map((admission) => (
                                <div
                                    key={admission.id}
                                    className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-rose-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                                >
                                    <div className="mb-4 flex items-start justify-between">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-900 text-sm font-black text-white dark:bg-neutral-700">
                                            {admission.patient.first_name[0]}
                                            {admission.patient.last_name[0]}
                                        </div>
                                        <StatusBadge
                                            status={admission.status}
                                        />
                                    </div>
                                    <div className="mb-4 flex-grow">
                                        <h3 className="truncate text-base font-bold text-gray-900 dark:text-neutral-100">
                                            {admission.patient.first_name}{' '}
                                            {admission.patient.last_name}
                                        </h3>
                                        <p className="mb-3 font-mono text-xs font-bold text-rose-500">
                                            {admission.patient.patient_uid}
                                        </p>

                                        <div className="mb-2 flex items-center gap-2">
                                            <CalendarDaysIcon className="h-3.5 w-3.5 text-gray-400 dark:text-neutral-500" />
                                            <span className="text-xs font-semibold text-gray-700 dark:text-neutral-300">
                                                {admission.admission_date}
                                            </span>
                                        </div>
                                        <p className="line-clamp-2 h-8 text-xs text-gray-500 dark:text-neutral-500">
                                            {admission.reason_for_admission ||
                                                t('page.checkin.no_reason')}
                                        </p>
                                    </div>
                                    <div className="flex items-center justify-between border-t border-gray-100 pt-4 dark:border-neutral-700">
                                        <div className="flex items-center gap-2">
                                            <MapPinIcon className="h-4 w-4 flex-shrink-0 text-rose-500" />
                                            <div>
                                                <div className="text-xs font-bold text-gray-950 uppercase dark:text-neutral-100">
                                                    {t('page.checkin.bed')}:{' '}
                                                    {admission.bed?.code}
                                                </div>
                                                <div className="text-[10px] font-semibold text-gray-400 uppercase dark:text-neutral-500">
                                                    {admission.bed?.ward?.name}
                                                </div>
                                            </div>
                                        </div>
                                        <Link
                                            href={route(
                                                'admissions.create',
                                                admission.id,
                                            )}
                                            className="rounded-lg bg-gray-50 p-2 text-gray-400 transition-all hover:bg-rose-50 hover:text-rose-600 dark:bg-neutral-700 dark:text-neutral-500 dark:hover:bg-rose-950/40"
                                        >
                                            <ArrowRightIcon className="h-4 w-4" />
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </motion.div>
                    ) : (
                        /* Table View layout */
                        <motion.div
                            key="table"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 text-xs font-bold text-gray-500 uppercase dark:bg-neutral-700 dark:text-neutral-500">
                                    <tr>
                                        <th className="px-6 py-4 text-left">
                                            {t('page.checkin.table_patient')}
                                        </th>
                                        <th className="px-6 py-4 text-left">
                                            {t(
                                                'page.checkin.table_date_reason',
                                            )}
                                        </th>
                                        <th className="px-6 py-4 text-left">
                                            {t('page.checkin.table_location')}
                                        </th>
                                        <th className="px-6 py-4 text-center">
                                            {t('page.checkin.table_status')}
                                        </th>
                                        <th className="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                    {admissions.data.map((admission) => (
                                        <tr
                                            key={admission.id}
                                            className="dark:hover:bg-neutral-750 transition-all hover:bg-gray-50"
                                        >
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-900 text-xs font-black text-white dark:bg-neutral-700">
                                                        {
                                                            admission.patient
                                                                .first_name[0]
                                                        }
                                                        {
                                                            admission.patient
                                                                .last_name[0]
                                                        }
                                                    </div>
                                                    <div>
                                                        <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                            {
                                                                admission
                                                                    .patient
                                                                    .first_name
                                                            }{' '}
                                                            {
                                                                admission
                                                                    .patient
                                                                    .last_name
                                                            }
                                                        </div>
                                                        <div className="font-mono text-[10px] font-bold tracking-wider text-rose-500 uppercase">
                                                            {
                                                                admission
                                                                    .patient
                                                                    .patient_uid
                                                            }
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="mb-1 flex items-center gap-2">
                                                    <CalendarDaysIcon className="h-3.5 w-3.5 text-gray-400 dark:text-neutral-500" />
                                                    <span className="text-xs font-bold text-gray-700 dark:text-neutral-300">
                                                        {
                                                            admission.admission_date
                                                        }
                                                    </span>
                                                </div>
                                                <div className="max-w-[180px] truncate text-[11px] text-gray-400 dark:text-neutral-500">
                                                    {
                                                        admission.reason_for_admission
                                                    }
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-2">
                                                    <MapPinIcon className="h-4 w-4 text-rose-500" />
                                                    <div>
                                                        <div className="text-xs font-bold text-gray-900 uppercase dark:text-neutral-100">
                                                            {t(
                                                                'page.checkin.bed',
                                                            )}
                                                            :{' '}
                                                            {
                                                                admission.bed
                                                                    ?.code
                                                            }
                                                        </div>
                                                        <div className="text-[9px] font-bold text-gray-400 uppercase dark:text-neutral-500">
                                                            {
                                                                admission.bed
                                                                    ?.ward?.name
                                                            }
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge
                                                    status={admission.status}
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link
                                                    href={route(
                                                        'admissions.create',
                                                        admission.id,
                                                    )}
                                                    className="inline-block rounded-lg border bg-gray-50 p-2 text-gray-400 transition-all hover:border-gray-200 hover:bg-white hover:text-rose-600 dark:bg-neutral-700 dark:text-neutral-500 dark:hover:border-neutral-600"
                                                >
                                                    <ArrowRightIcon className="h-4 w-4" />
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </motion.div>
                    )}
                </AnimatePresence>

                {/* 5. Pagination */}
                {admissions.data.length > 0 && (
                    <div className="mt-6 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-500">
                        <p>
                            {t('page.checkin.showing_entries', {
                                from: admissions.from || 0,
                                to: admissions.to || 0,
                                total:
                                    admissions.total || admissions.data.length,
                            })}
                        </p>
                        <div className="flex gap-1">
                            {admissions.links.map((link, i) => (
                                <button
                                    key={i}
                                    disabled={!link.url}
                                    onClick={() =>
                                        router.get(
                                            link.url!,
                                            {
                                                search: searchQuery,
                                                status: filters.status || '',
                                            },
                                            { preserveState: true },
                                        )
                                    }
                                    className={`rounded-lg border px-3 py-1.5 transition-all ${
                                        link.active
                                            ? 'border-rose-600 bg-rose-600 text-white'
                                            : 'border-gray-200 bg-white text-gray-600 hover:border-rose-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400'
                                    } ${!link.url ? 'cursor-not-allowed opacity-30' : ''}`}
                                    dangerouslySetInnerHTML={{
                                        __html: link.label,
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

function StatCard({ label, value, icon, color }: any) {
    const colorClasses: any = {
        rose: 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-900/10 dark:text-rose-400 dark:border-rose-950',
        blue: 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/10 dark:text-blue-400 dark:border-blue-950',
        amber: 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-900/10 dark:text-amber-400 dark:border-amber-950',
        gray: 'bg-gray-50 text-gray-600 border-gray-100 dark:bg-neutral-800 dark:text-neutral-400 dark:border-neutral-750',
    };
    return (
        <div className="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <div className={`rounded-lg border p-3 ${colorClasses[color]}`}>
                {React.cloneElement(icon as React.ReactElement, {
                    className: 'w-5 h-5',
                })}
            </div>
            <div>
                <p className="text-xl leading-none font-bold text-gray-900 dark:text-neutral-100">
                    {value}
                </p>
                <p className="mt-1 text-xs font-semibold text-gray-500 dark:text-neutral-500">
                    {label}
                </p>
            </div>
        </div>
    );
}

function StatusBadge({ status }: { status: string }) {
    const styles: any = {
        Admitted:
            'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-950',
        Pending:
            'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-950',
        Discharged:
            'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-950',
    };
    return (
        <span
            className={`rounded-full border px-2.5 py-0.5 text-[10px] font-bold ${styles[status]}`}
        >
            {status}
        </span>
    );
}
