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
    CalendarDaysIcon
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
    }
}

const getUrgencyBadgeStyles = (urgency: string) => {
    const styles: Record<string, string> = {
        emergency: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-950',
        urgent: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-950',
        routine: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-950',
    };
    return styles[urgency.toLowerCase()] || styles.routine;
};

const getStatusBadgeStyles = (status: string) => {
    const styles: Record<string, string> = {
        completed: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-950',
        in_progress: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-950',
        cancelled: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-950',
        scheduled: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-950',
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
        const emergencyCount = surgeries.data.filter(s => s.urgency.toLowerCase() === 'emergency').length;
        const activeOrScheduled = surgeries.data.filter(s => s.status === 'scheduled' || s.status === 'in_progress').length;
        const totalFees = surgeries.data.reduce((sum, s) => sum + Number(s.surgery_fee || 0), 0);
        return { total, emergencyCount, activeOrScheduled, totalFees };
    }, [surgeries.data]);

    // Handle Search and Filter logic locally
    const filtered = useMemo(() => {
        let r = surgeries.data;
        if (filter !== 'all') r = r.filter(s => s.status === filter);
        if (search) {
            const q = search.toLowerCase();
            r = r.filter(s =>
                s.surgery_name.toLowerCase().includes(q) ||
                s.patient?.first_name?.toLowerCase().includes(q) ||
                s.patient?.last_name?.toLowerCase().includes(q) ||
                s.surgeon?.name?.toLowerCase().includes(q)
            );
        }
        return r;
    }, [surgeries.data, filter, search]);

    return (
        <AppLayout headerTitle={t('surgeries.index.title')} headerSubtitle={t('surgeries.index.subtitle')}>
            <Head title={t('surgeries.index.head_title')} />
            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12">

                {/* 1. Breadcrumbs */}
                <nav className="flex text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 transition-colors cursor-pointer">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}
                        </li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-blue-600 font-bold">{t('surgeries.index.surgeries')}</li>
                    </ol>
                </nav>

                {/* 2. Top Metric Blocks */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label={t('surgeries.index.total_surgeries')} value={stats.total} icon={<ClockIcon />} color="blue" />
                    <StatCard label={t('surgeries.index.scheduled_active')} value={stats.activeOrScheduled} icon={<CalendarDaysIcon className="w-5 h-5" />} color="amber" />
                    <StatCard label={t('surgeries.index.emergencies')} value={stats.emergencyCount} icon={<ExclamationCircleIcon />} color="rose" />
                    <StatCard label={t('surgeries.index.total_fee_value')} value={`${stats.totalFees.toLocaleString()} FCFA`} icon={<EyeIcon />} color="emerald" />
                </div>

                {/* 3. Controls Layout */}
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    {/* Status Tabs */}
                    <div className="flex flex-wrap p-1 bg-gray-100 dark:bg-neutral-800 rounded-xl max-w-max">
                        {['all', 'scheduled', 'in_progress', 'completed', 'cancelled'].map(status => (
                            <button
                                key={status}
                                onClick={() => setFilter(status)}
                                className={`px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all ${
                                    filter === status
                                        ? 'bg-white dark:bg-neutral-700 text-blue-600 shadow-sm'
                                        : 'text-gray-500 hover:text-gray-950 dark:hover:text-neutral-300'
                                }`}
                            >
                                {t(`surgeries.index.filter_${status}`)}
                            </button>
                        ))}
                    </div>

                    {/* Search & Actions Bar */}
                    <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div className="relative flex-grow">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t('surgeries.index.search_placeholder')}
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                className="pl-9 pr-4 py-2 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all text-gray-900 dark:text-neutral-100"
                            />
                        </div>

                        <div className="flex items-center gap-2">
                            <Link
                                href={route('surgeries.create')}
                                className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-blue-700 transition-all whitespace-nowrap"
                            >
                                <PlusIcon className="w-4 h-4" /> {t('surgeries.index.schedule_surgery')}
                            </Link>

                            <div className="flex bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-1">
                                <button
                                    onClick={() => setViewMode('grid')}
                                    className={`p-1.5 rounded-md transition-all ${viewMode === 'grid' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20' : 'text-gray-400 dark:text-neutral-500'}`}
                                >
                                    <Squares2X2Icon className="w-5 h-5" />
                                </button>
                                <button
                                    onClick={() => setViewMode('table')}
                                    className={`p-1.5 rounded-md transition-all ${viewMode === 'table' ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20' : 'text-gray-400 dark:text-neutral-500'}`}
                                >
                                    <ListBulletIcon className="w-5 h-5" />
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
                            className="flex flex-col items-center justify-center py-24 bg-white dark:bg-neutral-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-neutral-700 text-center"
                        >
                            <div className="h-20 w-20 bg-gray-50 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4 border border-gray-100 dark:border-neutral-600">
                                <InboxIcon className="w-10 h-10 text-gray-300 dark:text-neutral-500" />
                            </div>
                            <h3 className="text-xl font-bold text-gray-900 dark:text-neutral-100">{t('surgeries.index.no_surgeries')}</h3>
                            <p className="text-sm text-gray-500 dark:text-neutral-500 max-w-sm mt-2 mb-8">
                                {t('surgeries.index.no_surgeries_description')}
                            </p>
                            <Link
                                href={route('surgeries.create')}
                                className="px-6 py-3 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-blue-700 transition-all"
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
                            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            {filtered.map(s => (
                                <div key={s.id} className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 hover:border-blue-400 hover:shadow-md transition-all flex flex-col h-full relative group">
                                    <div className="flex justify-between items-start mb-4">
                                        <div className="h-10 w-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm">
                                            {s.patient?.first_name?.[0] || 'P'}{s.patient?.last_name?.[0] || 'S'}
                                        </div>
                                        <div className="flex flex-col gap-1.5 items-end">
                                            <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${getUrgencyBadgeStyles(s.urgency)}`}>
                                                {s.urgency}
                                            </span>
                                            <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${getStatusBadgeStyles(s.status)}`}>
                                                {s.status.replace('_', ' ')}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="mb-4 flex-grow">
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 truncate">
                                            {s.surgery_name}
                                        </h3>
                                        <p className="text-xs font-semibold text-gray-400 dark:text-neutral-500 mb-3">
                                            {t('surgeries.index.patient_label')} {s.patient?.first_name} {s.patient?.last_name}
                                        </p>

                                        <div className="flex items-center gap-2 text-xs text-gray-600 dark:text-neutral-400">
                                            <ClockIcon className="w-4 h-4 text-gray-400" />
                                            <span>{new Date(s.scheduled_at).toLocaleDateString()}</span>
                                        </div>
                                        <p className="text-xs text-gray-500 mt-1">{t('surgeries.index.surgeon_label')} <span className="font-semibold text-gray-700 dark:text-neutral-300">{s.surgeon?.name}</span></p>
                                    </div>

                                    <div className="pt-4 border-t border-gray-150 dark:border-neutral-700 flex items-center justify-between">
                                        <div className="text-base font-bold text-gray-900 dark:text-neutral-100">
                                            {s.surgery_fee.toLocaleString()} FCFA
                                        </div>
                                        <Link
                                            href={route('surgeries.show', s.id)}
                                            className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 rounded-lg transition-all"
                                        >
                                            <EyeIcon className="w-4 h-4" />
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
                            className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden"
                        >
                            <table className="w-full text-left">
                                <thead className="bg-gray-50 dark:bg-neutral-700 text-xs font-bold text-gray-500 dark:text-neutral-500 uppercase tracking-wider">
                                    <tr>
                                        <th className="px-6 py-4">{t('surgeries.index.patient')}</th>
                                        <th className="px-6 py-4">{t('surgeries.index.procedure')}</th>
                                        <th className="px-6 py-4">{t('surgeries.index.surgeon')}</th>
                                        <th className="px-6 py-4">{t('surgeries.index.scheduled')}</th>
                                        <th className="px-6 py-4 text-center">{t('surgeries.index.status')}</th>
                                        <th className="px-6 py-4">{t('surgeries.index.fee')}</th>
                                        <th className="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                    {filtered.map(s => (
                                        <tr key={s.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700/30 transition-all">
                                            <td className="px-6 py-4">
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                    {s.patient?.first_name} {s.patient?.last_name}
                                                </p>
                                            </td>
                                            <td className="px-6 py-4">
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{s.surgery_name}</p>
                                                <span className={`inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase border mt-0.5 ${getUrgencyBadgeStyles(s.urgency)}`}>
                                                    {s.urgency}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm font-semibold text-gray-750 dark:text-neutral-350">
                                                {s.surgeon?.name}
                                            </td>
                                            <td className="px-6 py-4 text-xs text-gray-500 dark:text-neutral-400 font-medium">
                                                {new Date(s.scheduled_at).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <span className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border ${getStatusBadgeStyles(s.status)}`}>
                                                    {s.status.replace('_', ' ')}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm font-black text-gray-900 dark:text-neutral-100">
                                                {s.surgery_fee.toLocaleString()} FCFA
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link
                                                    href={route('surgeries.show', s.id)}
                                                    className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg inline-block transition-all"
                                                >
                                                    <EyeIcon className="w-4 h-4" />
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
                {surgeries.links && surgeries.links.length > 0 && filtered.length > 0 && (
                    <div className="mt-6 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-500">
                        <p>{t('surgeries.index.showing_entries', { from: surgeries.from || 0, to: surgeries.to || 0, total: surgeries.total || surgeries.data.length })}</p>
                        <div className="flex gap-1">
                            {surgeries.links.map((link, i) => (
                                <button
                                    key={i}
                                    disabled={!link.url}
                                    className={`px-3 py-1.5 border rounded-lg transition-all ${
                                        link.active
                                            ? 'bg-blue-600 border-blue-600 text-white'
                                            : 'bg-white text-gray-600 dark:bg-neutral-800 dark:text-neutral-400 border-gray-200 dark:border-neutral-700 hover:border-blue-400'
                                    } ${!link.url ? 'opacity-30 cursor-not-allowed' : ''}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
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
        blue: 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/10 dark:text-blue-400 dark:border-blue-950',
        emerald: 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/10 dark:text-emerald-400 dark:border-emerald-950',
        amber: 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-900/10 dark:text-amber-400 dark:border-amber-950',
        rose: 'bg-red-50 text-red-650 border-red-100 dark:bg-red-900/10 dark:text-red-400 dark:border-red-950'
    };
    return (
        <div className="bg-white dark:bg-neutral-800 p-5 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm flex items-center gap-4">
            <div className={`p-3 rounded-lg border ${colorClasses[color]}`}>
                {React.cloneElement(icon as React.ReactElement, { className: "w-5 h-5" })}
            </div>
            <div>
                <p className="text-lg font-bold text-gray-950 dark:text-neutral-55 leading-none">{value}</p>
                <p className="text-xs font-semibold text-gray-550 dark:text-neutral-500 mt-1.5">{label}</p>
            </div>
        </div>
    );
}
