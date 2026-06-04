import {
    PlusIcon,
    HomeIcon,
    ChevronRightIcon,
    MagnifyingGlassIcon,
    EyeIcon,
    Squares2X2Icon,
    ListBulletIcon,
    InboxIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    HeartIcon
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
    }
}

const getTriageBadgeStyles = (level: string) => {
    const styles: Record<string, string> = {
        emergency: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-950',
        urgent: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-950',
        non_urgent: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-950',
    };
    return styles[level.toLowerCase()] || 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-950';
};

const getStatusBadgeStyles = (status: string) => {
    const styles: Record<string, string> = {
        active: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-950',
        stabilized: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-950',
        discharged: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-950',
        triage: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-950',
    };
    return styles[status.toLowerCase()] || styles.triage;
};

export default function EmergenciesIndex({ emergencies }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState('all');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('table');

    // Dynamically calculate clinical triage stats
    const stats = useMemo(() => {
        const total = emergencies.data.length;
        const active = emergencies.data.filter(e => e.status === 'active').length;
        const critical = emergencies.data.filter(e => e.triage_level.toLowerCase() === 'emergency').length;
        const stabilized = emergencies.data.filter(e => e.status === 'stabilized').length;
        return { total, active, critical, stabilized };
    }, [emergencies.data]);

    // Handle Search and Status filtering locally
    const filtered = useMemo(() => {
        let r = emergencies.data;
        if (filter !== 'all') r = r.filter(e => e.status === filter);
        if (search) {
            const q = search.toLowerCase();
            r = r.filter(e =>
                e.chief_complaint.toLowerCase().includes(q) ||
                e.patient?.first_name?.toLowerCase().includes(q) ||
                e.patient?.last_name?.toLowerCase().includes(q) ||
                e.attending_doctor?.name?.toLowerCase().includes(q)
            );
        }
        return r;
    }, [emergencies.data, filter, search]);

    return (
        <AppLayout headerTitle={t('emergencyCases')} headerSubtitle={t('urgentClinicalCareRegistry')}>
            <Head title={t('emergencies')} />
            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12">

                {/* 1. Breadcrumbs */}
                <nav className="flex text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-red-600 transition-colors cursor-pointer">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('home')}
                        </li>
                        <li><ChevronRightIcon className="w-4 h-4" /></li>
                        <li className="text-red-600 font-bold">{t('emergencies')}</li>
                    </ol>
                </nav>

                {/* 2. Top Metric Blocks */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label={t('totalRegistry')} value={stats.total} icon={<ClockIcon />} color="blue" />
                    <StatCard label={t('activeEmergencies')} value={stats.active} icon={<HeartIcon className="w-5 h-5 animate-pulse" />} color="rose" />
                    <StatCard label={t('criticalTriage')} value={stats.critical} icon={<ExclamationTriangleIcon />} color="amber" />
                    <StatCard label={t('stabilizedCases')} value={stats.stabilized} icon={<Squares2X2Icon className="w-5 h-5" />} color="emerald" />
                </div>

                {/* 3. Controls Layout */}
                <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    {/* Status Tabs */}
                    <div className="flex flex-wrap p-1 bg-gray-100 dark:bg-neutral-800 rounded-xl max-w-max">
                        {[{key: 'all', label: t('all')}, {key: 'triage', label: t('triage')}, {key: 'active', label: t('active')}, {key: 'stabilized', label: t('stabilized')}, {key: 'discharged', label: t('discharged')}].map(item => (
                            <button
                                key={item.key}
                                onClick={() => setFilter(item.key)}
                                className={`px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all ${
                                    filter === item.key
                                        ? 'bg-white dark:bg-neutral-700 text-red-600 shadow-sm'
                                        : 'text-gray-500 hover:text-gray-950 dark:hover:text-neutral-350'
                                }`}
                            >
                                {item.label}
                            </button>
                        ))}
                    </div>

                    {/* Search & Action Bar */}
                    <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <div className="relative flex-grow">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t('searchEmergencies')}
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                className="pl-9 pr-4 py-2 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none transition-all text-gray-900 dark:text-neutral-100"
                            />
                        </div>

                        <div className="flex items-center gap-2">
                            <Link
                                href={route('emergencies.create')}
                                className="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-red-700 transition-all whitespace-nowrap"
                            >
                                <PlusIcon className="w-4 h-4" /> {t('newEmergency')}
                            </Link>

                            <div className="flex bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg p-1">
                                <button
                                    onClick={() => setViewMode('grid')}
                                    className={`p-1.5 rounded-md transition-all ${viewMode === 'grid' ? 'bg-red-50 text-red-600 dark:bg-red-900/20' : 'text-gray-400 dark:text-neutral-500'}`}
                                >
                                    <Squares2X2Icon className="w-5 h-5" />
                                </button>
                                <button
                                    onClick={() => setViewMode('table')}
                                    className={`p-1.5 rounded-md transition-all ${viewMode === 'table' ? 'bg-red-50 text-red-600 dark:bg-red-900/20' : 'text-gray-400 dark:text-neutral-500'}`}
                                >
                                    <ListBulletIcon className="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* 4. Display Workspace */}
                <AnimatePresence mode="wait">
                    {filtered.length === 0 ? (
                        /* Empty State Container */
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
                            <h3 className="text-xl font-bold text-gray-900 dark:text-neutral-100">{t('noEmergencyCases')}</h3>
                            <p className="text-sm text-gray-500 dark:text-neutral-500 max-w-sm mt-2 mb-8">
                                {t('noEmergencyCasesDescription')}
                            </p>
                            <Link
                                href={route('emergencies.create')}
                                className="px-6 py-3 bg-red-600 text-white rounded-xl text-sm font-bold shadow-lg hover:bg-red-700 transition-all"
                            >
                                {t('registerEmergencyCase')}
                            </Link>
                        </motion.div>
                    ) : viewMode === 'grid' ? (
                        /* Grid View layout */
                        <motion.div
                            key="grid"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            {filtered.map(e => (
                                <div key={e.id} className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 hover:border-red-400 hover:shadow-md transition-all flex flex-col h-full relative group">
                                    <div className="flex justify-between items-start mb-4">
                                        <div className="h-10 w-10 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-650 dark:text-red-400 flex items-center justify-center font-black text-sm border border-red-100 dark:border-red-950">
                                            {e.patient?.first_name?.[0] || 'E'}{e.patient?.last_name?.[0] || 'C'}
                                        </div>
                                        <div className="flex flex-col gap-1.5 items-end">
                                            <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${getTriageBadgeStyles(e.triage_level)}`}>
                                                {e.triage_level}
                                            </span>
                                            <span className={`px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${getStatusBadgeStyles(e.status)}`}>
                                                {e.status}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="mb-4 flex-grow">
                                        <h3 className="text-lg font-bold text-gray-900 dark:text-neutral-100 truncate">
                                            {e.patient?.first_name} {e.patient?.last_name}
                                        </h3>
                                        <p className="text-xs text-gray-400 dark:text-neutral-500 mb-3 font-medium">
                                            {t('complaining')}: <span className="text-gray-650 dark:text-neutral-350">{e.chief_complaint}</span>
                                        </p>

                                        <div className="flex items-center gap-2 text-xs text-gray-600 dark:text-neutral-450 mt-1">
                                            <ClockIcon className="w-4 h-4 text-gray-400" />
                                            <span>{t('arrived')}: {new Date(e.arrived_at).toLocaleDateString()}</span>
                                        </div>
                                        <p className="text-xs text-gray-500 mt-2">{t('physician')}: <span className="font-semibold text-gray-700 dark:text-neutral-300">{e.attending_doctor?.name || t('unassigned')}</span></p>
                                    </div>

                                    <div className="pt-4 border-t border-gray-150 dark:border-neutral-700 flex items-center justify-between">
                                        <span className="text-xs text-gray-400 dark:text-neutral-500 font-bold uppercase tracking-wider">{t('fee')}: {e.emergency_fee?.toLocaleString() || 0} FCFA</span>
                                        <Link
                                            href={route('emergencies.show', e.id)}
                                            className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition-all"
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
                                        <th className="px-6 py-4">{t('patient')}</th>
                                        <th className="px-6 py-4">{t('complaint')}</th>
                                        <th className="px-6 py-4 text-center">{t('triage')}</th>
                                        <th className="px-6 py-4 text-center">{t('status')}</th>
                                        <th className="px-6 py-4">{t('arrived')}</th>
                                        <th className="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                                    {filtered.map(e => (
                                        <tr key={e.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700/30 transition-all">
                                            <td className="px-6 py-4">
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{e.patient?.first_name} {e.patient?.last_name}</p>
                                                <p className="text-xs text-gray-400 dark:text-neutral-500">{e.attending_doctor?.name || t('unassignedDoctor')}</p>
                                            </td>
                                            <td className="px-6 py-4 text-sm font-semibold text-gray-750 dark:text-neutral-300 max-w-xs truncate">
                                                {e.chief_complaint}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <span className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border ${getTriageBadgeStyles(e.triage_level)}`}>
                                                    {e.triage_level}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <span className={`inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border ${getStatusBadgeStyles(e.status)}`}>
                                                    {e.status}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-xs text-gray-500 dark:text-neutral-400 font-medium">
                                                {new Date(e.arrived_at).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <Link
                                                    href={route('emergencies.show', e.id)}
                                                    className="p-2 text-gray-400 hover:text-red-650 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg inline-block transition-all"
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

                {/* 5. Pagination */}
                {emergencies.links && emergencies.links.length > 0 && filtered.length > 0 && (
                    <div className="mt-6 flex items-center justify-between text-xs text-gray-500 dark:text-neutral-500">
                        <p>{t('showing')} <b>{emergencies.from || 0}</b> {t('to')} <b>{emergencies.to || 0}</b> {t('of')} <b>{emergencies.total || emergencies.data.length}</b> {t('emergencyEntries')}</p>
                        <div className="flex gap-1">
                            {emergencies.links.map((link, i) => (
                                <button
                                    key={i}
                                    disabled={!link.url}
                                    className={`px-3 py-1.5 border rounded-lg transition-all ${
                                        link.active
                                            ? 'bg-red-600 border-red-600 text-white'
                                            : 'bg-white text-gray-600 dark:bg-neutral-800 dark:text-neutral-400 border-gray-200 dark:border-neutral-700 hover:border-red-400'
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
