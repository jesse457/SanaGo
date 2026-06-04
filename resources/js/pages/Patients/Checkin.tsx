import {
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
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import React, { useState, useEffect } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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

const statusColors: Record<string, 'rose' | 'amber' | 'blue'> = {
    Admitted: 'rose',
    Pending: 'amber',
    Discharged: 'blue',
};

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
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.checkin.breadcrumb_admissions') },
                    ]}
                />

                {/* 2. Stats Cards */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label={t('page.checkin.total_registry')}
                        value={stats.total}
                        icon={UsersIcon}
                        color="blue"
                    />
                    <StatCard
                        label={t('page.checkin.currently_admitted')}
                        value={stats.active}
                        icon={CheckBadgeIcon}
                        color="purple"
                    />
                    <StatCard
                        label={t('page.checkin.pending_placement')}
                        value={stats.pending}
                        icon={ClockIcon}
                        color="amber"
                    />
                    <StatCard
                        label={t('page.checkin.total_discharged')}
                        value={stats.discharged}
                        icon={ArchiveBoxIcon}
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
                                className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800"
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
                                            ? 'border-blue-600 bg-blue-600 text-white shadow-sm'
                                            : 'border-gray-200 bg-white text-gray-600 hover:border-blue-400 hover:text-blue-600 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400'
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
                            className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('page.checkin.new_admission')}
                        </Link>
                        <ViewToggle view={viewMode} onChange={setViewMode} />
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
                            className="space-y-6"
                        >
                            <EmptyState
                                icon={InboxIcon}
                                title={t('page.checkin.empty_title')}
                                description={t(
                                    'page.checkin.empty_description',
                                )}
                            />
                            <div className="flex justify-center gap-4">
                                <button
                                    onClick={clearFilters}
                                    className="flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-700 transition-all hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
                                >
                                    <XMarkIcon className="h-4 w-4" />{' '}
                                    {t('page.checkin.clear_filters')}
                                </button>
                                <Link
                                    href={route('appointments.create')}
                                    className="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg transition-all hover:bg-blue-700"
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
                                    className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                                >
                                    <div className="mb-4 flex items-start justify-between">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-900 text-sm font-black text-white dark:bg-neutral-700">
                                            {admission.patient.first_name[0]}
                                            {admission.patient.last_name[0]}
                                        </div>
                                        <StatusBadge
                                            label={admission.status}
                                            color={
                                                statusColors[
                                                    admission.status
                                                ] || 'gray'
                                            }
                                        />
                                    </div>
                                    <div className="mb-4 flex-grow">
                                        <h3 className="truncate text-base font-bold text-gray-900 dark:text-neutral-100">
                                            {admission.patient.first_name}{' '}
                                            {admission.patient.last_name}
                                        </h3>
                                        <p className="mb-3 font-mono text-xs font-bold text-blue-500">
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
                                            <MapPinIcon className="h-4 w-4 flex-shrink-0 text-blue-500" />
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
                                            className="rounded-lg bg-gray-50 p-2 text-gray-400 transition-all hover:bg-blue-50 hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-500 dark:hover:bg-blue-950/40"
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
                                                        <div className="font-mono text-[10px] font-bold tracking-wider text-blue-500 uppercase">
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
                                                    <MapPinIcon className="h-4 w-4 text-blue-500" />
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
                                                    label={admission.status}
                                                    color={
                                                        statusColors[
                                                            admission.status
                                                        ] || 'gray'
                                                    }
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link
                                                    href={route(
                                                        'admissions.create',
                                                        admission.id,
                                                    )}
                                                    className="inline-block rounded-lg border bg-gray-50 p-2 text-gray-400 transition-all hover:border-gray-200 hover:bg-white hover:text-blue-600 dark:bg-neutral-700 dark:text-neutral-500 dark:hover:border-neutral-600"
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
                    <Pagination
                        links={admissions.links}
                        meta={{
                            from: admissions.from || 0,
                            to: admissions.to || 0,
                            total: admissions.total || admissions.data.length,
                        }}
                    />
                )}
            </div>
        </AppLayout>
    );
}
