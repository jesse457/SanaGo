import {
    PlusIcon,
    HomeIcon,
    ChevronRightIcon,
    MagnifyingGlassIcon,
    EyeIcon,
    ClockIcon,
    ExclamationCircleIcon,
    Squares2X2Icon,
    ListBulletIcon,
    InboxIcon,
    CalendarDaysIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import React, { useState, useMemo } from 'react';
import StatCard from '@/Components/StatCard';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Surgery {
    id: number;
    patient?: { id: number; first_name: string; last_name: string };
    surgeon?: { id: number; name: string };
    surgery_name: string;
    scheduled_at: string;
    urgency: string;
    status: string;
    surgery_fee: number;
}

interface Props {
    surgeries: {
        data: Surgery[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        total?: number;
        from?: number;
        to?: number;
    };
}

const getUrgencyBadgeStyles = (urgency: string) => {
    const styles: Record<string, string> = {
        emergency:
            'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-950',
        urgent: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-950',
        routine:
            'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-950',
    };

    return styles[urgency.toLowerCase()] || styles.routine;
};

const getStatusBadgeStyles = (status: string) => {
    const styles: Record<string, string> = {
        completed:
            'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-950',
        in_progress:
            'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-950',
        cancelled:
            'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-950',
        scheduled:
            'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-950',
    };

    return styles[status.toLowerCase()] || styles.scheduled;
};

export default function SurgeriesIndex({ surgeries }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState('all');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('table');

    // Dynamically calculate dynamic schedule stats
    const stats = useMemo(() => {
        const total = surgeries.data.length;
        const emergencyCount = surgeries.data.filter(
            (s) => s.urgency.toLowerCase() === 'emergency',
        ).length;
        const activeOrScheduled = surgeries.data.filter(
            (s) => s.status === 'scheduled' || s.status === 'in_progress',
        ).length;
        const totalFees = surgeries.data.reduce(
            (sum, s) => sum + Number(s.surgery_fee || 0),
            0,
        );

        return { total, emergencyCount, activeOrScheduled, totalFees };
    }, [surgeries.data]);

    // Handle Search and Filter logic locally
    const filtered = useMemo(() => {
        let r = surgeries.data;

        if (filter !== 'all') {
            r = r.filter((s) => s.status === filter);
        }

        if (search) {
            const q = search.toLowerCase();
            r = r.filter(
                (s) =>
                    s.surgery_name.toLowerCase().includes(q) ||
                    s.patient?.first_name?.toLowerCase().includes(q) ||
                    s.patient?.last_name?.toLowerCase().includes(q) ||
                    s.surgeon?.name?.toLowerCase().includes(q),
            );
        }

        return r;
    }, [surgeries.data, filter, search]);

    return (
        <AppLayout
            headerTitle={t('surgeries.index.title')}
            headerSubtitle={t('surgeries.index.subtitle')}
        >
            <Head title={t('surgeries.index.head_title')} />
            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* 1. Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" />{' '}
                            {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-bold text-blue-600">
                            {t('surgeries.index.surgeries')}
                        </li>
                    </ol>
                </nav>

                {/* 2. Top Metric Blocks */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        label={t('surgeries.index.total_surgeries')}
                        value={stats.total}
                        icon={ClockIcon}
                        color="blue"
                    />
                    <StatCard
                        label={t('surgeries.index.scheduled_active')}
                        value={stats.activeOrScheduled}
                        icon={CalendarDaysIcon}
                        color="amber"
                    />
                    <StatCard
                        label={t('surgeries.index.emergencies')}
                        value={stats.emergencyCount}
                        icon={ExclamationCircleIcon}
                        color="red"
                    />
                    <StatCard
                        label={t('surgeries.index.total_fee_value')}
                        value={`${stats.totalFees.toLocaleString()} FCFA`}
                        icon={EyeIcon}
                        color="emerald"
                    />
                </div>

                {/* 3. Controls Layout */}
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                    {/* Status Tabs */}
                    <div className="flex max-w-max flex-wrap rounded-xl bg-gray-100 p-1 dark:bg-neutral-800">
                        {[
                            'all',
                            'scheduled',
                            'in_progress',
                            'completed',
                            'cancelled',
                        ].map((status) => (
                            <button
                                key={status}
                                onClick={() => setFilter(status)}
                                className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wider uppercase transition-all ${
                                    filter === status
                                        ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700'
                                        : 'text-gray-500 hover:text-gray-950 dark:hover:text-neutral-300'
                                }`}
                            >
                                {t(`surgeries.index.filter_${status}`)}
                            </button>
                        ))}
                    </div>

                    {/* Search & Actions Bar */}
                    <div className="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                        <div className="relative flex-grow">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t(
                                    'surgeries.index.search_placeholder',
                                )}
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm text-gray-900 transition-all outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>

                        <div className="flex items-center gap-2">
                            <Link
                                href={route('surgeries.create')}
                                className="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold whitespace-nowrap text-white shadow-sm transition-all hover:bg-blue-700"
                            >
                                <PlusIcon className="h-4 w-4" />{' '}
                                {t('surgeries.index.schedule_surgery')}
                            </Link>

                            <div className="flex rounded-lg border border-gray-200 bg-white p-1 dark:border-neutral-700 dark:bg-neutral-800">
                                <button
                                    onClick={() => setViewMode('grid')}
                                    className={`rounded-md p-1.5 transition-all ${viewMode === 'grid' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20' : 'text-gray-400 dark:text-neutral-500'}`}
                                >
                                    <Squares2X2Icon className="h-5 w-5" />
                                </button>
                                <button
                                    onClick={() => setViewMode('table')}
                                    className={`rounded-md p-1.5 transition-all ${viewMode === 'table' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20' : 'text-gray-400 dark:text-neutral-500'}`}
                                >
                                    <ListBulletIcon className="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* 4. Display Area (Dynamic Grid / Table transitions) */}
                <AnimatePresence mode="wait">
                    {filtered.length === 0 ? (
                        /* Empty State */
                        <motion.div
                            key="empty"
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -20 }}
                            className="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white py-24 text-center dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <div className="mb-4 flex h-20 w-20 items-center justify-center rounded-full border border-gray-100 bg-gray-50 dark:border-neutral-600 dark:bg-neutral-700">
                                <InboxIcon className="h-10 w-10 text-gray-300 dark:text-neutral-500" />
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 dark:text-neutral-100">
                                {t('surgeries.index.no_surgeries')}
                            </h3>
                            <p className="mt-2 mb-8 max-w-sm text-sm text-gray-500 dark:text-neutral-500">
                                {t('surgeries.index.no_surgeries_description')}
                            </p>
                            <Link
                                href={route('surgeries.create')}
                                className="rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg transition-all hover:bg-blue-700"
                            >
                                {t('surgeries.index.schedule_first_surgery')}
                            </Link>
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        /* Card Grid layout */
                        <motion.div
                            key="grid"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
                        >
                            {filtered.map((s) => (
                                <div
                                    key={s.id}
                                    className="group relative flex h-full flex-col rounded-xl border border-gray-200 bg-white p-6 transition-all hover:border-blue-400 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800"
                                >
                                    <div className="mb-4 flex items-start justify-between">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-sm font-bold text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                                            {s.patient?.first_name?.[0] || 'P'}
                                            {s.patient?.last_name?.[0] || 'S'}
                                        </div>
                                        <div className="flex flex-col items-end gap-1.5">
                                            <span
                                                className={`rounded border px-2 py-0.5 text-[10px] font-bold uppercase ${getUrgencyBadgeStyles(s.urgency)}`}
                                            >
                                                {s.urgency}
                                            </span>
                                            <span
                                                className={`rounded border px-2 py-0.5 text-[10px] font-bold uppercase ${getStatusBadgeStyles(s.status)}`}
                                            >
                                                {s.status.replace('_', ' ')}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="mb-4 flex-grow">
                                        <h3 className="truncate text-lg font-bold text-gray-900 dark:text-neutral-100">
                                            {s.surgery_name}
                                        </h3>
                                        <p className="mb-3 text-xs font-semibold text-gray-400 dark:text-neutral-500">
                                            {t('surgeries.index.patient_label')}{' '}
                                            {s.patient?.first_name}{' '}
                                            {s.patient?.last_name}
                                        </p>

                                        <div className="flex items-center gap-2 text-xs text-gray-600 dark:text-neutral-400">
                                            <ClockIcon className="h-4 w-4 text-gray-400" />
                                            <span>
                                                {new Date(
                                                    s.scheduled_at,
                                                ).toLocaleDateString()}
                                            </span>
                                        </div>
                                        <p className="mt-1 text-xs text-gray-500">
                                            {t('surgeries.index.surgeon_label')}{' '}
                                            <span className="font-semibold text-gray-700 dark:text-neutral-300">
                                                {s.surgeon?.name}
                                            </span>
                                        </p>
                                    </div>

                                    <div className="border-gray-150 flex items-center justify-between border-t pt-4 dark:border-neutral-700">
                                        <div className="text-base font-bold text-gray-900 dark:text-neutral-100">
                                            {s.surgery_fee.toLocaleString()}{' '}
                                            FCFA
                                        </div>
                                        <Link
                                            href={route('surgeries.show', s.id)}
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
                                            {t('surgeries.index.patient')}
                                        </th>
                                        <th className="px-6 py-4">
                                            {t('surgeries.index.procedure')}
                                        </th>
                                        <th className="px-6 py-4">
                                            {t('surgeries.index.surgeon')}
                                        </th>
                                        <th className="px-6 py-4">
                                            {t('surgeries.index.scheduled')}
                                        </th>
                                        <th className="px-6 py-4 text-center">
                                            {t('surgeries.index.status')}
                                        </th>
                                        <th className="px-6 py-4">
                                            {t('surgeries.index.fee')}
                                        </th>
                                        <th className="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                    {filtered.map((s) => (
                                        <tr
                                            key={s.id}
                                            className="transition-all hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                        >
                                            <td className="px-6 py-4">
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                    {s.patient?.first_name}{' '}
                                                    {s.patient?.last_name}
                                                </p>
                                            </td>
                                            <td className="px-6 py-4">
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                    {s.surgery_name}
                                                </p>
                                                <span
                                                    className={`mt-0.5 inline-block rounded border px-2 py-0.5 text-[10px] font-bold uppercase ${getUrgencyBadgeStyles(s.urgency)}`}
                                                >
                                                    {s.urgency}
                                                </span>
                                            </td>
                                            <td className="text-gray-750 dark:text-neutral-350 px-6 py-4 text-sm font-semibold">
                                                {s.surgeon?.name}
                                            </td>
                                            <td className="px-6 py-4 text-xs font-medium text-gray-500 dark:text-neutral-400">
                                                {new Date(
                                                    s.scheduled_at,
                                                ).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <span
                                                    className={`inline-block rounded-full border px-2.5 py-0.5 text-[10px] font-bold uppercase ${getStatusBadgeStyles(s.status)}`}
                                                >
                                                    {s.status.replace('_', ' ')}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm font-black text-gray-900 dark:text-neutral-100">
                                                {s.surgery_fee.toLocaleString()}{' '}
                                                FCFA
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link
                                                    href={route(
                                                        'surgeries.show',
                                                        s.id,
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

                {/* 5. Pagination Bar layout */}
                {surgeries.links &&
                    surgeries.links.length > 0 &&
                    filtered.length > 0 && (
                        <div className="mt-6 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-500">
                            <p>
                                {t('surgeries.index.showing_entries', {
                                    from: surgeries.from || 0,
                                    to: surgeries.to || 0,
                                    total:
                                        surgeries.total ||
                                        surgeries.data.length,
                                })}
                            </p>
                            <div className="flex gap-1">
                                {surgeries.links.map((link, i) => (
                                    <button
                                        key={i}
                                        disabled={!link.url}
                                        className={`rounded-lg border px-3 py-1.5 transition-all ${
                                            link.active
                                                ? 'border-blue-600 bg-blue-600 text-white'
                                                : 'border-gray-200 bg-white text-gray-600 hover:border-blue-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400'
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
