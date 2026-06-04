import {
    MagnifyingGlassIcon,
    BeakerIcon,
    CheckBadgeIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon,
    ClockIcon,
    ClipboardDocumentListIcon,
    ArrowRightIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import debounce from 'lodash/debounce';
import React, { useState, useEffect, useCallback } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Props {
    requests: {
        data: any[];
        links: any[];
        total: number;
        from: number;
        to: number;
    };
    filters: {
        search?: string;
        status?: string;
    };
    stats: {
        total: number;
        completed: number;
        inProgress: number;
        pending: number;
    };
}

const statusBadgeColor = (
    status: string,
): 'emerald' | 'blue' | 'amber' | 'red' => {
    switch (status) {
        case 'Completed':
            return 'emerald';
        case 'In_Progress':
            return 'blue';
        case 'Pending':
            return 'amber';
        default:
            return 'amber';
    }
};

export default function LabRequests({ requests, filters, stats }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');

    // --- Server-side Filtering Logic ---
    const handleFilter = useCallback(
        debounce((searchVal: string, statusVal: string) => {
            router.get(
                route('lab.test-requests'),
                { search: searchVal, status: statusVal },
                { preserveState: true, replace: true },
            );
        }, 300),
        [],
    );

    useEffect(() => {
        handleFilter(search, statusFilter);
    }, [search, statusFilter]);

    return (
        <AppLayout
            headerTitle={t('lab_tests.requests.title')}
            headerSubtitle={t('lab_tests.requests.subtitle')}
        >
            <Head title={t('lab_tests.requests.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* --- Breadcrumbs --- */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: route('dashboard') },
                        { label: t('lab_tests.requests.lab_requests') },
                    ]}
                />

                {/* --- Stats Overview --- */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label={t('lab_tests.requests.total_requests')}
                        value={stats.total}
                        icon={ClipboardDocumentListIcon}
                        color="blue"
                    />
                    <StatCard
                        label={t('lab_tests.requests.completed')}
                        value={stats.completed}
                        icon={CheckBadgeIcon}
                        color="emerald"
                    />
                    <StatCard
                        label={t('lab_tests.requests.in_progress')}
                        value={stats.inProgress}
                        icon={ClockIcon}
                        color="purple"
                    />
                    <StatCard
                        label={t('lab_tests.requests.pending')}
                        value={stats.pending}
                        icon={ExclamationTriangleIcon}
                        color="amber"
                    />
                </div>

                {/* --- Header Controls --- */}
                <div className="flex flex-col justify-between gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center dark:border-neutral-700 dark:bg-neutral-800">
                    <div className="flex flex-1 items-center gap-3">
                        <div className="relative w-full max-w-sm">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t(
                                    'lab_tests.requests.search_placeholder',
                                )}
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>
                        <select
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                            className="rounded-lg border border-gray-200 bg-white py-2 pr-10 pl-3 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        >
                            <option value="">
                                {t('lab_tests.requests.all_statuses')}
                            </option>
                            <option value="Pending">
                                {t('lab_tests.requests.pending')}
                            </option>
                            <option value="In_Progress">
                                {t('lab_tests.requests.in_progress')}
                            </option>
                            <option value="Completed">
                                {t('lab_tests.requests.completed')}
                            </option>
                        </select>
                    </div>

                    <div>
                        <ViewToggle view={viewMode} onChange={setViewMode} />
                    </div>
                </div>

                {/* --- Main Content --- */}
                <AnimatePresence mode="wait">
                    {requests.data.length === 0 ? (
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                        >
                            <EmptyState
                                icon={ClipboardDocumentListIcon}
                                title={t('lab_tests.requests.no_requests')}
                            />
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            {requests.data.map((req) => (
                                <div
                                    key={req.id}
                                    className="rounded-xl border border-b-4 border-gray-200 border-b-blue-500 bg-white p-6 transition-all hover:shadow-lg dark:border-neutral-700 dark:bg-neutral-800"
                                >
                                    <div className="mb-4 flex items-start justify-between">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 font-bold text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                                            {req.patient.first_name[0]}
                                            {req.patient.last_name[0]}
                                        </div>
                                        <StatusBadge
                                            label={req.status.replace('_', ' ')}
                                            color={statusBadgeColor(req.status)}
                                        />
                                    </div>
                                    <h3 className="text-base font-bold text-gray-900 dark:text-neutral-100">
                                        {req.patient.first_name}{' '}
                                        {req.patient.last_name}
                                    </h3>
                                    <p className="mb-4 font-mono text-[10px] text-gray-400 dark:text-neutral-500">
                                        {req.patient.patient_uid}
                                    </p>

                                    <div className="mb-4 rounded-lg border border-gray-100 bg-gray-50 p-3 dark:border-neutral-600 dark:bg-neutral-700/50">
                                        <div className="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-neutral-200">
                                            <BeakerIcon className="h-4 w-4 text-purple-500" />
                                            {req.test_definition.test_name}
                                        </div>
                                        <p className="mt-1 text-[10px] tracking-tighter text-gray-500 uppercase dark:text-neutral-400">
                                            {t('lab_tests.requests.code_label')}{' '}
                                            {req.test_definition.code}
                                        </p>
                                    </div>

                                    <div className="mt-auto flex items-center justify-between">
                                        <span className="text-[10px] font-bold text-gray-500 dark:text-neutral-400">
                                            {new Date(
                                                req.request_date,
                                            ).toLocaleDateString()}
                                        </span>
                                        <Link
                                            href={route('results.create', {
                                                request_id: req.id,
                                            })}
                                            className="rounded-lg bg-blue-600 px-4 py-1.5 text-[10px] font-black text-white uppercase transition-colors hover:bg-blue-700"
                                        >
                                            {t(
                                                'lab_tests.requests.process_result',
                                            )}
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <table className="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead className="bg-gray-50 dark:bg-neutral-700">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase dark:text-neutral-400">
                                            {t('lab_tests.requests.patient')}
                                        </th>
                                        <th className="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase dark:text-neutral-400">
                                            {t(
                                                'lab_tests.requests.test_definition',
                                            )}
                                        </th>
                                        <th className="px-6 py-3 text-center text-[10px] font-black text-gray-400 uppercase dark:text-neutral-400">
                                            {t('lab_tests.requests.status')}
                                        </th>
                                        <th className="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-neutral-700">
                                    {requests.data.map((req) => (
                                        <tr
                                            key={req.id}
                                            className="hover:bg-gray-50 dark:hover:bg-neutral-700"
                                        >
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                        {req.patient.first_name}{' '}
                                                        {req.patient.last_name}
                                                    </div>
                                                    <span className="font-mono text-[10px] text-gray-400 dark:text-neutral-500">
                                                        {
                                                            req.patient
                                                                .patient_uid
                                                        }
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-sm font-medium text-gray-700 dark:text-neutral-300">
                                                {req.test_definition.test_name}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge
                                                    label={req.status.replace(
                                                        '_',
                                                        ' ',
                                                    )}
                                                    color={statusBadgeColor(
                                                        req.status,
                                                    )}
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link
                                                    href={route(
                                                        'results.create',
                                                        { request_id: req.id },
                                                    )}
                                                    className="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                >
                                                    <ArrowRightIcon className="h-5 w-5" />
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </AnimatePresence>

                {/* --- Pagination --- */}
                <Pagination
                    links={requests.links}
                    meta={{
                        from: requests.from,
                        to: requests.to,
                        total: requests.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
