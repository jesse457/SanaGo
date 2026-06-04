import React, { useState, useEffect, useCallback } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    ChevronRightIcon,
    MagnifyingGlassIcon,
    HomeIcon,
    BeakerIcon,
    Squares2X2Icon,
    ListBulletIcon,
    CheckBadgeIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon,
    ClockIcon,
    ClipboardDocumentListIcon,
    ArrowRightIcon
} from '@heroicons/react/24/outline';
import debounce from 'lodash/debounce';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
                { preserveState: true, replace: true }
            );
        }, 300),
        []
    );

    useEffect(() => {
        handleFilter(search, statusFilter);
    }, [search, statusFilter]);

    return (
        <AppLayout headerTitle={t('lab_tests.requests.title')} headerSubtitle={t('lab_tests.requests.subtitle')}>
            <Head title={t('lab_tests.requests.head_title')} />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8 pb-12">
                {/* --- Breadcrumbs --- */}
                <nav className="flex text-[10px] font-black uppercase tracking-widest text-gray-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="hover:text-blue-600 cursor-pointer transition-colors flex items-center">
                            <Link href="/dashboard"><HomeIcon className="w-3 h-3 mr-1" /> {t('common.home')}</Link>
                        </li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="text-blue-600">{t('lab_tests.requests.lab_requests')}</li>
                    </ol>
                </nav>

                {/* --- Stats Overview --- */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard label={t('lab_tests.requests.total_requests')} value={stats.total} icon={<ClipboardDocumentListIcon />} color="blue" />
                    <StatCard label={t('lab_tests.requests.completed')} value={stats.completed} icon={<CheckBadgeIcon />} color="emerald" />
                    <StatCard label={t('lab_tests.requests.in_progress')} value={stats.inProgress} icon={<ClockIcon />} color="purple" />
                    <StatCard label={t('lab_tests.requests.pending')} value={stats.pending} icon={<ExclamationTriangleIcon />} color="amber" />
                </div>

                {/* --- Header Controls --- */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <div className="flex items-center gap-3 flex-1">
                        <div className="relative w-full max-w-sm">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t('lab_tests.requests.search_placeholder')}
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pl-9 pr-4 py-2 w-full border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                            />
                        </div>
                        <select
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                            className="pl-3 pr-10 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                        >
                            <option value="">{t('lab_tests.requests.all_statuses')}</option>
                            <option value="Pending">{t('lab_tests.requests.pending')}</option>
                            <option value="In_Progress">{t('lab_tests.requests.in_progress')}</option>
                            <option value="Completed">{t('lab_tests.requests.completed')}</option>
                        </select>
                    </div>

                    <div className="flex bg-gray-100 p-1 rounded-lg">
                        <button onClick={() => setViewMode('grid')} className={`p-1.5 rounded-md ${viewMode === 'grid' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500'}`}><Squares2X2Icon className="w-5 h-5" /></button>
                        <button onClick={() => setViewMode('table')} className={`p-1.5 rounded-md ${viewMode === 'table' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500'}`}><ListBulletIcon className="w-5 h-5" /></button>
                    </div>
                </div>

                {/* --- Main Content --- */}
                <AnimatePresence mode="wait">
                    {requests.data.length === 0 ? (
                        <div className="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300">
                            <ArrowPathIcon className="w-12 h-12 text-gray-200 mx-auto mb-4" />
                            <h3 className="text-sm font-bold text-gray-900">{t('lab_tests.requests.no_requests')}</h3>
                        </div>
                    ) : viewMode === 'grid' ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {requests.data.map((req) => (
                                <div key={req.id} className="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all border-b-4 border-b-blue-500">
                                    <div className="flex justify-between items-start mb-4">
                                        <div className="h-10 w-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                            {req.patient.first_name[0]}{req.patient.last_name[0]}
                                        </div>
                                        <StatusBadge status={req.status} />
                                    </div>
                                    <h3 className="text-base font-bold text-gray-900">{req.patient.first_name} {req.patient.last_name}</h3>
                                    <p className="text-[10px] font-mono text-gray-400 mb-4">{req.patient.patient_uid}</p>

                                    <div className="bg-gray-50 p-3 rounded-lg border border-gray-100 mb-4">
                                        <div className="flex items-center gap-2 text-sm font-bold text-gray-700">
                                            <BeakerIcon className="w-4 h-4 text-purple-500" />
                                            {req.test_definition.test_name}
                                        </div>
                                        <p className="text-[10px] text-gray-500 mt-1 uppercase tracking-tighter">{t('lab_tests.requests.code_label')} {req.test_definition.code}</p>
                                    </div>

                                    <div className="flex items-center justify-between mt-auto">
                                        <span className="text-[10px] text-gray-500 font-bold">{new Date(req.request_date).toLocaleDateString()}</span>
                                        <Link
                                            href={route('results.create', { request_id: req.id })}
                                            className="px-4 py-1.5 bg-blue-600 text-white text-[10px] font-black uppercase rounded-lg hover:bg-blue-700 transition-colors"
                                        >
                                            {t('lab_tests.requests.process_result')}
                                        </Link>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-[10px] font-black uppercase text-gray-400">{t('lab_tests.requests.patient')}</th>
                                        <th className="px-6 py-3 text-left text-[10px] font-black uppercase text-gray-400">{t('lab_tests.requests.test_definition')}</th>
                                        <th className="px-6 py-3 text-center text-[10px] font-black uppercase text-gray-400">{t('lab_tests.requests.status')}</th>
                                        <th className="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200">
                                    {requests.data.map((req) => (
                                        <tr key={req.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700">
                                            <td className="px-6 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="font-bold text-sm text-gray-900">{req.patient.first_name} {req.patient.last_name}</div>
                                                    <span className="text-[10px] font-mono text-gray-400">{req.patient.patient_uid}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-sm font-medium text-gray-700">{req.test_definition.test_name}</td>
                                            <td className="px-6 py-4 text-center"><StatusBadge status={req.status} /></td>
                                            <td className="px-6 py-4 text-right">
                                                <Link href={route('results.create', { request_id: req.id })} className="text-blue-600 hover:text-blue-800">
                                                    <ArrowRightIcon className="w-5 h-5" />
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
                <div className="flex items-center justify-between mt-6">
                    <p className="text-xs text-gray-500">{t('lab_tests.requests.showing_orders', { from: requests.from, to: requests.to, total: requests.total })}</p>
                    <div className="flex gap-2">
                        {requests.links.map((link, i) => (
                            <Link
                                key={i}
                                href={link.url || ''}
                                className={`px-3 py-1 rounded-lg text-xs font-bold border transition-all ${link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 dark:hover:bg-neutral-700'} ${!link.url && 'opacity-50 pointer-events-none'}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

// ... StatCard and StatusBadge sub-components remain similar but use database status strings ...
function StatCard({ label, value, icon, color }: any) {
    const colors: any = {
        blue: 'bg-blue-50 text-blue-600',
        emerald: 'bg-emerald-50 text-emerald-600',
        purple: 'bg-purple-50 text-purple-600',
        amber: 'bg-amber-50 text-amber-600'
    };
    return (
        <div className="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div className={`p-2.5 rounded-lg ${colors[color]}`}>{React.cloneElement(icon, { className: "w-5 h-5" })}</div>
            <div>
                <p className="text-lg font-black text-gray-900 leading-none">{value}</p>
                <p className="text-[10px] font-bold text-gray-400 uppercase mt-1">{label}</p>
            </div>
        </div>
    );
}

function StatusBadge({ status }: { status: string }) {
    const styles: any = {
        Completed: 'bg-emerald-50 text-emerald-700 border-emerald-100',
        In_Progress: 'bg-blue-50 text-blue-700 border-blue-100',
        Pending: 'bg-amber-50 text-amber-700 border-amber-100'
    };
    return (
        <span className={`px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border ${styles[status] || styles.Pending}`}>
            {status.replace('_', ' ')}
        </span>
    );
}
