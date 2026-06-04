import {
    PlusIcon,
    MagnifyingGlassIcon,
    EyeIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    HeartIcon,
    InboxIcon,
    Squares2X2Icon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import React, { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import FormInput from '@/Components/FormInput';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import ViewToggle from '@/Components/ViewToggle';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Emergency {
    id: number;
    patient?: { id: number; first_name: string; last_name: string };
    attending_doctor?: { id: number; name: string } | null;
    triage_nurse?: { id: number; name: string } | null;
    chief_complaint: string;
    triage_level: string;
    status: string;
    arrived_at: string;
    emergency_fee: number;
}

interface Props {
    emergencies: {
        data: Emergency[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        total?: number;
        from?: number;
        to?: number;
    };
}

const getTriageColor = (
    level: string,
): 'red' | 'amber' | 'emerald' | 'blue' => {
    const colors = {
        emergency: 'red' as const,
        urgent: 'amber' as const,
        non_urgent: 'emerald' as const,
    };

    return colors[level.toLowerCase() as keyof typeof colors] || 'blue';
};

const getStatusColor = (
    status: string,
): 'red' | 'amber' | 'emerald' | 'blue' => {
    const colors = {
        active: 'red' as const,
        stabilized: 'amber' as const,
        discharged: 'emerald' as const,
        triage: 'blue' as const,
    };

    return colors[status.toLowerCase() as keyof typeof colors] || 'blue';
};

export default function EmergenciesIndex({ emergencies }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState('all');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('table');

    // Dynamically calculate clinical triage stats
    const stats = useMemo(() => {
        const total = emergencies.data.length;
        const active = emergencies.data.filter(
            (e) => e.status === 'active',
        ).length;
        const critical = emergencies.data.filter(
            (e) => e.triage_level.toLowerCase() === 'emergency',
        ).length;
        const stabilized = emergencies.data.filter(
            (e) => e.status === 'stabilized',
        ).length;

        return { total, active, critical, stabilized };
    }, [emergencies.data]);

    // Handle Search and Status filtering locally
    const filtered = useMemo(() => {
        let r = emergencies.data;

        if (filter !== 'all') {
            r = r.filter((e) => e.status === filter);
        }

        if (search) {
            const q = search.toLowerCase();
            r = r.filter(
                (e) =>
                    e.chief_complaint.toLowerCase().includes(q) ||
                    e.patient?.first_name?.toLowerCase().includes(q) ||
                    e.patient?.last_name?.toLowerCase().includes(q) ||
                    e.attending_doctor?.name?.toLowerCase().includes(q),
            );
        }

        return r;
    }, [emergencies.data, filter, search]);

    return (
        <AppLayout
            headerTitle={t('emergencyCases')}
            headerSubtitle={t('urgentClinicalCareRegistry')}
        >
            <Head title={t('emergencies')} />
            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('home'), href: route('dashboard') },
                        { label: t('emergencies') },
                    ]}
                />

                {/* Top Metric Blocks */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        icon={ClockIcon}
                        label={t('totalRegistry')}
                        value={stats.total}
                        color="blue"
                    />
                    <StatCard
                        icon={HeartIcon}
                        label={t('activeEmergencies')}
                        value={stats.active}
                        color="red"
                    />
                    <StatCard
                        icon={ExclamationTriangleIcon}
                        label={t('criticalTriage')}
                        value={stats.critical}
                        color="amber"
                    />
                    <StatCard
                        icon={Squares2X2Icon}
                        label={t('stabilizedCases')}
                        value={stats.stabilized}
                        color="emerald"
                    />
                </div>

                {/* Controls Layout */}
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    {/* Status Tabs */}
                    <div className="flex max-w-max flex-wrap rounded-xl bg-gray-100 p-1 dark:bg-neutral-800">
                        {[
                            { key: 'all', label: t('all') },
                            { key: 'triage', label: t('triage') },
                            { key: 'active', label: t('active') },
                            { key: 'stabilized', label: t('stabilized') },
                            { key: 'discharged', label: t('discharged') },
                        ].map((item) => (
                            <button
                                key={item.key}
                                onClick={() => setFilter(item.key)}
                                className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wider uppercase transition-all ${
                                    filter === item.key
                                        ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700'
                                        : 'dark:hover:text-neutral-350 text-gray-500 hover:text-gray-950'
                                }`}
                            >
                                {item.label}
                            </button>
                        ))}
                    </div>

                    {/* Search & Action Bar */}
                    <div className="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                        <FormInput
                            name="search"
                            icon={MagnifyingGlassIcon}
                            placeholder={t('searchEmergencies')}
                            value={search}
                            onChange={setSearch}
                            className="flex-grow sm:min-w-[240px]"
                        />

                        <div className="flex items-center gap-2">
                            <Link
                                href={route('emergencies.create')}
                                className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold whitespace-nowrap text-white shadow-sm transition-all hover:bg-blue-700"
                            >
                                <PlusIcon className="h-4 w-4" />{' '}
                                {t('newEmergency')}
                            </Link>

                            <ViewToggle
                                view={viewMode}
                                onChange={setViewMode}
                            />
                        </div>
                    </div>
                </div>

                {/* Display Workspace */}
                <AnimatePresence mode="wait">
                    {filtered.length === 0 ? (
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -20 }}
                        >
                            <EmptyState
                                icon={InboxIcon}
                                title={t('noEmergencyCases')}
                                description={t('noEmergencyCasesDescription')}
                                action={{
                                    label: t('registerEmergencyCase'),
                                    href: route('emergencies.create'),
                                }}
                            />
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
                            {filtered.map((e) => (
                                <div
                                    key={e.id}
                                    className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                                >
                                    <div className="mb-4 flex items-start justify-between">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-lg border border-red-100 bg-red-50 text-sm font-black text-red-600 dark:border-red-950 dark:bg-red-900/20 dark:text-red-400">
                                            {e.patient?.first_name?.[0] || 'E'}
                                            {e.patient?.last_name?.[0] || 'C'}
                                        </div>
                                        <div className="flex flex-col items-end gap-1.5">
                                            <StatusBadge
                                                label={e.triage_level}
                                                color={getTriageColor(
                                                    e.triage_level,
                                                )}
                                            />
                                            <StatusBadge
                                                label={e.status}
                                                color={getStatusColor(e.status)}
                                            />
                                        </div>
                                    </div>

                                    <div className="mb-4 flex-grow">
                                        <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                            {e.patient?.first_name}{' '}
                                            {e.patient?.last_name}
                                        </h3>
                                        <p className="mb-3 text-xs font-medium text-gray-400 dark:text-neutral-500">
                                            {t('complaining')}:{' '}
                                            <span className="dark:text-neutral-350 text-gray-600">
                                                {e.chief_complaint}
                                            </span>
                                        </p>

                                        <div className="dark:text-neutral-450 mt-1 flex items-center gap-2 text-xs text-gray-600">
                                            <ClockIcon className="h-4 w-4 text-gray-400" />
                                            <span>
                                                {t('arrived')}:{' '}
                                                {new Date(
                                                    e.arrived_at,
                                                ).toLocaleDateString()}
                                            </span>
                                        </div>
                                        <p className="mt-2 text-xs text-gray-500">
                                            {t('physician')}:{' '}
                                            <span className="font-semibold text-gray-700 dark:text-neutral-300">
                                                {e.attending_doctor?.name ||
                                                    t('unassigned')}
                                            </span>
                                        </p>
                                    </div>

                                    <div className="border-gray-150 flex items-center justify-between border-t pt-4 dark:border-neutral-700">
                                        <span className="text-xs font-bold tracking-wider text-gray-400 uppercase dark:text-neutral-500">
                                            {t('fee')}:{' '}
                                            {e.emergency_fee?.toLocaleString() ||
                                                0}{' '}
                                            FCFA
                                        </span>
                                        <Link
                                            href={route(
                                                'emergencies.show',
                                                e.id,
                                            )}
                                            className="rounded-lg p-2 text-gray-400 transition-all hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-950/40"
                                        >
                                            <EyeIcon className="h-4 w-4" />
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </motion.div>
                    ) : (
                        /* Table layout */
                        <motion.div
                            key="table"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <table className="w-full text-left">
                                <thead className="bg-gray-50 text-xs font-bold tracking-wider text-gray-500 uppercase dark:bg-neutral-700 dark:text-neutral-500">
                                    <tr>
                                        <th className="px-6 py-4">
                                            {t('patient')}
                                        </th>
                                        <th className="px-6 py-4">
                                            {t('complaint')}
                                        </th>
                                        <th className="px-6 py-4 text-center">
                                            {t('triage')}
                                        </th>
                                        <th className="px-6 py-4 text-center">
                                            {t('status')}
                                        </th>
                                        <th className="px-6 py-4">
                                            {t('arrived')}
                                        </th>
                                        <th className="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                    {filtered.map((e) => (
                                        <tr
                                            key={e.id}
                                            className="transition-all hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                        >
                                            <td className="px-6 py-4">
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                    {e.patient?.first_name}{' '}
                                                    {e.patient?.last_name}
                                                </p>
                                                <p className="text-xs text-gray-400 dark:text-neutral-500">
                                                    {e.attending_doctor?.name ||
                                                        t('unassignedDoctor')}
                                                </p>
                                            </td>
                                            <td className="max-w-xs truncate px-6 py-4 text-sm font-semibold text-gray-700 dark:text-neutral-300">
                                                {e.chief_complaint}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge
                                                    label={e.triage_level}
                                                    color={getTriageColor(
                                                        e.triage_level,
                                                    )}
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge
                                                    label={e.status}
                                                    color={getStatusColor(
                                                        e.status,
                                                    )}
                                                />
                                            </td>
                                            <td className="px-6 py-4 text-xs font-medium text-gray-500 dark:text-neutral-400">
                                                {new Date(
                                                    e.arrived_at,
                                                ).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link
                                                    href={route(
                                                        'emergencies.show',
                                                        e.id,
                                                    )}
                                                    className="inline-block rounded-lg p-2 text-gray-400 transition-all hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20"
                                                >
                                                    <EyeIcon className="h-4 w-4" />
                                                </Link>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </motion.div>
                    )}
                </AnimatePresence>

                {/* Pagination */}
                {emergencies.links &&
                    emergencies.links.length > 0 &&
                    filtered.length > 0 && (
                        <Pagination
                            links={emergencies.links}
                            meta={{
                                from: emergencies.from || 0,
                                to: emergencies.to || 0,
                                total:
                                    emergencies.total ||
                                    emergencies.data.length,
                            }}
                        />
                    )}
            </div>
        </AppLayout>
    );
}
