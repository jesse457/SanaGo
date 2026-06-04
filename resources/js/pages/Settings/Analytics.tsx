import {
    BanknotesIcon,
    ArrowTrendingUpIcon,
    ArrowPathIcon,
    BeakerIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    BuildingOffice2Icon,
    FunnelIcon,
    MagnifyingGlassIcon,
    ArrowDownTrayIcon,
    EllipsisHorizontalIcon,
    DocumentMagnifyingGlassIcon,
    XMarkIcon,
    ClipboardDocumentIcon,
    ArrowUpRightIcon,
    ArrowDownRightIcon
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import React, { useState, useMemo, useCallback } from 'react';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { useTranslation } from '@/hooks/useTranslation';

// --- Types ---
interface PatientRevenue {
    id: number;
    patient_uid: string;
    first_name: string;
    last_name: string;
    medications: number;
    appointments: number;
    labs: number;
    admissions: number;
    total: number;
    last_visit?: string;
    insurance?: string;
}

type TimePeriod = 'today' | 'week' | 'month' | 'year';

interface RevenueBreakdown {
    medication: number;
    appointment: number;
    lab: number;
    admission: number;
}

export default function RevenueAnalytics() {
    const { t } = useTranslation();
    // --- State ---
    const [timePeriod, setTimePeriod] = useState<TimePeriod>('month');
    const [isLoading, setIsLoading] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [sortBy, setSortBy] = useState<'total' | 'name' | 'recent'>('total');
    const [sortOrder, setSortOrder] = useState<'asc' | 'desc'>('desc');
    const [selectedPatient, setSelectedPatient] = useState<PatientRevenue | null>(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [copiedField, setCopiedField] = useState<string | null>(null);

    // --- Mock Data ---
    const totalRevenue = 4850000;
    const revenueGrowth = 12.5;

    const breakdown: RevenueBreakdown = {
        medication: 1200000,
        appointment: 850000,
        lab: 1500000,
        admission: 1300000,
    };

    const patientRevenues: PatientRevenue[] = [
        {
            id: 1,
            patient_uid: 'PAT-001',
            first_name: 'Jean',
            last_name: 'Dupont',
            medications: 45000,
            appointments: 15000,
            labs: 85000,
            admissions: 0,
            total: 145000,
            last_visit: '2024-01-15',
            insurance: 'CNAM'
        },
        {
            id: 2,
            patient_uid: 'PAT-042',
            first_name: 'Marie',
            last_name: 'Ebola',
            medications: 12000,
            appointments: 15000,
            labs: 0,
            admissions: 250000,
            total: 277000,
            last_visit: '2024-01-18',
            insurance: 'Private'
        },
        {
            id: 3,
            patient_uid: 'PAT-089',
            first_name: 'Ahmadou',
            last_name: 'Bello',
            medications: 5000,
            appointments: 15000,
            labs: 30000,
            admissions: 0,
            total: 50000,
            last_visit: '2024-01-10',
            insurance: 'CNAM'
        },
    ];

    const periods: { key: TimePeriod; label: string }[] = [
        { key: 'today', label: t('page.analytics.today') },
        { key: 'week', label: t('page.analytics.week') },
        { key: 'month', label: t('page.analytics.month') },
        { key: 'year', label: t('page.analytics.year') },
    ];

    const metrics = [
        {
            title: t('page.analytics.medications'),
            amount: breakdown.medication,
            icon: BeakerIcon,
            color: 'text-emerald-600 dark:text-emerald-400',
            bg: 'bg-emerald-50 dark:bg-emerald-900/20',
            change: '+8.2%'
        },
        {
            title: t('page.analytics.consultations'),
            amount: breakdown.appointment,
            icon: CalendarDaysIcon,
            color: 'text-blue-600 dark:text-blue-400',
            bg: 'bg-blue-50 dark:bg-blue-900/20',
            change: '+15.1%'
        },
        {
            title: t('page.analytics.lab_tests'),
            amount: breakdown.lab,
            icon: ClipboardDocumentCheckIcon,
            color: 'text-amber-600 dark:text-amber-400',
            bg: 'bg-amber-50 dark:bg-amber-900/20',
            change: '+5.7%'
        },
        {
            title: t('page.analytics.admissions'),
            amount: breakdown.admission,
            icon: BuildingOffice2Icon,
            color: 'text-purple-600 dark:text-purple-400',
            bg: 'bg-purple-50 dark:bg-purple-900/20',
            change: '+22.3%'
        },
    ];

    // --- Helpers ---
    const formatFCFA = useCallback((num: number): string => {
        return new Intl.NumberFormat('fr-FR').format(num);
    }, []);

    const formatPercent = useCallback((num: number): string => {
        return `${num >= 0 ? '+' : ''}${num.toFixed(1)}%`;
    }, []);

    // --- Filtering & Sorting ---
    const filteredAndSortedPatients = useMemo(() => {
        let result = [...patientRevenues];

        if (searchQuery) {
            const query = searchQuery.toLowerCase();
            result = result.filter(p =>
                p.first_name.toLowerCase().includes(query) ||
                p.last_name.toLowerCase().includes(query) ||
                p.patient_uid.toLowerCase().includes(query)
            );
        }

        result.sort((a, b) => {
            let comparison = 0;

            if (sortBy === 'total') {
                comparison = a.total - b.total;
            } else if (sortBy === 'name') {
                comparison = `${a.last_name} ${a.first_name}`.localeCompare(`${b.last_name} ${b.first_name}`);
            } else if (sortBy === 'recent') {
                comparison = (a.last_visit || '').localeCompare(b.last_visit || '');
            }

            return sortOrder === 'asc' ? comparison : -comparison;
        });

        return result;
    }, [searchQuery, sortBy, sortOrder]);

    // --- Handlers ---
    const handleViewDetails = useCallback((patient: PatientRevenue) => {
        setSelectedPatient(patient);
        setIsModalOpen(true);
    }, []);

    const handleCloseModal = useCallback(() => {
        setIsModalOpen(false);
        setSelectedPatient(null);
        setCopiedField(null);
    }, []);

    const handleCopyToClipboard = useCallback(async (text: string, field: string) => {
        try {
            await navigator.clipboard.writeText(text);
            setCopiedField(field);
            setTimeout(() => setCopiedField(null), 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    }, []);

    const handleExportLedger = useCallback(() => {
        setIsLoading(true);
        setTimeout(() => {
            const dataStr = JSON.stringify(filteredAndSortedPatients, null, 2);
            const blob = new Blob([dataStr], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `revenue-ledger-${timePeriod}-${new Date().toISOString().split('T')[0]}.json`;
            a.click();
            URL.revokeObjectURL(url);
            setIsLoading(false);
        }, 800);
    }, [filteredAndSortedPatients, timePeriod]);

    const toggleSort = useCallback((field: 'total' | 'name' | 'recent') => {
        if (sortBy === field) {
            setSortOrder(prev => prev === 'asc' ? 'desc' : 'asc');
        } else {
            setSortBy(field);
            setSortOrder('desc');
        }
    }, [sortBy]);

    const getSortIcon = useCallback((field: 'total' | 'name' | 'recent') => {
        if (sortBy !== field) {
return null;
}

        return sortOrder === 'asc' ?
            <ArrowUpRightIcon className="w-3 h-3 inline ml-1" /> :
            <ArrowDownRightIcon className="w-3 h-3 inline ml-1" />;
    }, [sortBy, sortOrder]);

    return (
        <SuperAdminLayout
            headerTitle={t('page.analytics.title')}
            headerSubtitle={t('page.analytics.subtitle')}
            // headerIcon={<BanknotesIcon className="w-6 h-6" />}
        >
            <Head title={t('page.analytics.head_title')} />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                {/* Period Selector & Actions Bar */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex items-center gap-2">
                        {periods.map((p) => (
                            <button
                                key={p.key}
                                onClick={() => setTimePeriod(p.key)}
                                className={`px-3 py-1.5 text-xs font-medium rounded-md transition-all ${
                                    timePeriod === p.key
                                        ? 'bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 shadow-sm'
                                        : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-neutral-100 hover:bg-gray-100 dark:hover:bg-neutral-800'
                                }`}
                            >
                                {p.label}
                            </button>
                        ))}
                    </div>

                    <div className="flex items-center gap-2">
                        {/* Refresh Button */}
                        <button
                            onClick={() => {
                                setIsLoading(true);
                                setTimeout(() => setIsLoading(false), 1000);
                            }}
                            disabled={isLoading}
                            className="p-2 text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-200 hover:bg-gray-100 dark:hover:bg-neutral-800 rounded-lg transition-colors disabled:opacity-50"
                            title={t('page.analytics.refresh_data')}
                        >
                            <ArrowPathIcon className={`w-4 h-4 ${isLoading ? 'animate-spin' : ''}`} />
                        </button>
                    </div>
                </div>

                {/* Hero Summary Card */}
                <div className="relative overflow-hidden bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl p-6 shadow-sm">
                    <div className="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

                    <div className="relative z-10 flex flex-col lg:flex-row justify-between lg:items-end gap-6">
                        <div className="space-y-3">
                            <div className="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 rounded-md text-xs font-medium border border-emerald-200 dark:border-emerald-800">
                                <ArrowTrendingUpIcon className="w-3.5 h-3.5" />
                                {t('page.analytics.financial_performance')}
                            </div>

                            <div>
                                <p className="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">{t('page.analytics.gross_collection')} ({timePeriod})</p>
                                <div className="flex items-baseline gap-2">
                                    <span className="text-sm font-medium text-gray-400">FCFA</span>
                                    <h2 className="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-neutral-100 tracking-tight">
                                        {formatFCFA(totalRevenue)}
                                    </h2>
                                </div>
                            </div>

                            <p className="text-sm text-gray-600 dark:text-neutral-400">
                                {t('page.analytics.growth_vs_previous')}{' '}
                                <span className={`font-semibold ${revenueGrowth >= 0 ? 'text-emerald-600' : 'text-red-600'}`}>
                                    {formatPercent(revenueGrowth)}
                                </span>
                            </p>
                        </div>

                        {/* Mini Chart */}
                        <div className="flex gap-1.5 h-20 items-end">
                            {[35, 50, 45, 80, 60, 90, 75].map((height, i) => (
                                <div
                                    key={i}
                                    style={{ height: `${height}%` }}
                                    className={`w-2 sm:w-2.5 rounded-t transition-all ${
                                        i === 5
                                            ? 'bg-indigo-600'
                                            : 'bg-gray-200 dark:bg-neutral-700'
                                    }`}
                                />
                            ))}
                        </div>
                    </div>
                </div>

                {/* Metrics Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {metrics.map((m, i) => (
                        <div key={i} className="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 p-5 rounded-lg hover:shadow-sm transition-shadow">
                            <div className="flex items-start justify-between mb-4">
                                <div className={`p-2.5 rounded-lg ${m.bg} ${m.color}`}>
                                    <m.icon className="w-5 h-5" />
                                </div>
                                <span className={`text-xs font-medium ${m.change.startsWith('+') ? 'text-emerald-600' : 'text-red-600'}`}>
                                    {m.change}
                                </span>
                            </div>
                            <p className="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">{m.title}</p>
                            <div className="flex items-baseline gap-1">
                                <span className="text-xs text-gray-400">FCFA</span>
                                <span className="text-lg font-semibold text-gray-900 dark:text-neutral-100">{formatFCFA(m.amount)}</span>
                            </div>
                            <div className="w-full bg-gray-100 dark:bg-neutral-800 h-1.5 rounded-full mt-3 overflow-hidden">
                                <div
                                    style={{ width: `${(m.amount / totalRevenue) * 100}%` }}
                                    className="h-full bg-gray-900 dark:bg-neutral-100 rounded-full transition-all"
                                />
                            </div>
                        </div>
                    ))}
                </div>

                {/* Ledger Table */}
                <div className="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg overflow-hidden shadow-sm">
                    {/* Table Header */}
                    <div className="px-5 py-4 border-b border-gray-200 dark:border-neutral-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50/50 dark:bg-neutral-800/50">
                        <div className="flex items-center gap-2">
                            <FunnelIcon className="w-4 h-4 text-gray-500" />
                            <h3 className="text-sm font-semibold text-gray-900 dark:text-neutral-100">{t('page.analytics.transaction_ledger')}</h3>
                            <span className="px-2 py-0.5 bg-gray-200 dark:bg-neutral-700 rounded text-xs text-gray-600 dark:text-neutral-300">
                                {filteredAndSortedPatients.length}
                            </span>
                        </div>

                        <div className="flex items-center gap-2 w-full sm:w-auto">
                            {/* Search */}
                            <div className="relative flex-1 sm:flex-none">
                                <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    placeholder={t('page.analytics.search_patient')}
                                    className="w-full sm:w-48 pl-9 pr-3 py-2 border border-gray-300 dark:border-neutral-600 rounded-lg text-sm bg-white dark:bg-neutral-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none dark:text-neutral-100 placeholder-gray-400"
                                />
                            </div>

                            {/* Export */}
                            <button
                                onClick={handleExportLedger}
                                disabled={isLoading || filteredAndSortedPatients.length === 0}
                                className="p-2.5 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg text-gray-600 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors disabled:opacity-50"
                                title={t('page.analytics.export_json')}
                            >
                                <ArrowDownTrayIcon className="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {/* Table Content */}
                    <div className="overflow-x-auto">
                        {isLoading ? (
                            <div className="flex items-center justify-center py-12">
                                <div className="flex items-center gap-3 px-5 py-3 bg-gray-50 dark:bg-neutral-800 rounded-lg">
                                    <ArrowPathIcon className="animate-spin w-5 h-5 text-indigo-600" />
                                    <span className="text-sm font-medium text-gray-700 dark:text-neutral-300">{t('page.analytics.loading_data')}</span>
                                </div>
                            </div>
                        ) : filteredAndSortedPatients.length === 0 ? (
                            <div className="text-center py-12">
                                <DocumentMagnifyingGlassIcon className="w-12 h-12 text-gray-300 dark:text-neutral-600 mx-auto mb-3" />
                                <p className="text-sm font-medium text-gray-900 dark:text-neutral-100 mb-1">{t('page.analytics.no_patients')}</p>
                                <p className="text-xs text-gray-500 dark:text-neutral-400">
                                    {searchQuery ? t('page.analytics.try_adjusting_search') : t('page.analytics.no_revenue_records')}
                                </p>
                            </div>
                        ) : (
                            <table className="w-full">
                                <thead className="bg-gray-50 dark:bg-neutral-800/50">
                                    <tr>
                                        <th className="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">{t('page.analytics.col_patient')}</th>
                                        <th
                                            className="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide cursor-pointer hover:text-gray-700 dark:hover:text-neutral-300 hidden sm:table-cell"
                                            onClick={() => toggleSort('total')}
                                        >
                                            {t('page.analytics.col_medications')} {getSortIcon('total')}
                                        </th>
                                        <th className="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">{t('page.analytics.col_labs')}</th>
                                        <th
                                            className="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide cursor-pointer hover:text-gray-700 dark:hover:text-neutral-300"
                                            onClick={() => toggleSort('total')}
                                        >
                                            {t('page.analytics.col_total')} {getSortIcon('total')}
                                        </th>
                                        <th className="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">{t('page.analytics.col_actions')}</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-800">
                                    {filteredAndSortedPatients.map((row) => (
                                        <tr key={row.id} className="group hover:bg-gray-50 dark:hover:bg-neutral-800/40 transition-colors">
                                            <td className="px-5 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-lg bg-gray-100 dark:bg-neutral-800 flex items-center justify-center text-xs font-semibold text-gray-600 dark:text-neutral-300 border border-gray-200 dark:border-neutral-700">
                                                        {row.first_name[0]}{row.last_name[0]}
                                                    </div>
                                                    <div className="min-w-0">
                                                        <p className="text-sm font-medium text-gray-900 dark:text-neutral-100 truncate">
                                                            {row.first_name} {row.last_name}
                                                        </p>
                                                        <p className="text-xs text-gray-500 dark:text-neutral-400 font-mono">#{row.patient_uid}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-5 py-4 text-right text-sm text-gray-600 dark:text-neutral-300 hidden sm:table-cell">
                                                {formatFCFA(row.medications)}
                                            </td>
                                            <td className="px-5 py-4 text-right text-sm text-gray-600 dark:text-neutral-300 hidden md:table-cell">
                                                {formatFCFA(row.labs)}
                                            </td>
                                            <td className="px-5 py-4 text-right">
                                                <span className="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                                                    {formatFCFA(row.total)}
                                                </span>
                                            </td>
                                            <td className="px-5 py-4 text-center">
                                                <div className="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button
                                                        onClick={() => handleViewDetails(row)}
                                                        className="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
                                                        title={t('page.analytics.view_details')}
                                                    >
                                                        <DocumentMagnifyingGlassIcon className="w-4 h-4" />
                                                    </button>
                                                    <button
                                                        className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-neutral-800 rounded-lg transition-colors"
                                                        title={t('page.analytics.more_options')}
                                                    >
                                                        <EllipsisHorizontalIcon className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>
            </div>

            {/* Patient Details Modal */}
            {isModalOpen && selectedPatient && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" onClick={handleCloseModal}></div>
                    <div className="relative bg-white dark:bg-neutral-900 rounded-lg w-full max-w-lg shadow-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                        {/* Modal Header */}
                        <div className="px-5 py-4 border-b border-gray-200 dark:border-neutral-700 flex justify-between items-center bg-gray-50 dark:bg-neutral-800/50">
                            <div>
                                <h3 className="text-base font-semibold text-gray-900 dark:text-neutral-100">{t('page.analytics.patient_revenue_details')}</h3>
                                <p className="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">{selectedPatient.first_name} {selectedPatient.last_name}</p>
                            </div>
                            <button
                                onClick={handleCloseModal}
                                className="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800 rounded-lg transition-colors"
                            >
                                <XMarkIcon className="w-5 h-5" />
                            </button>
                        </div>

                        {/* Modal Body */}
                        <div className="p-5 space-y-5 max-h-[70vh] overflow-y-auto">
                            {/* Patient Header */}
                            <div className="flex items-center gap-4 p-4 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-lg border border-indigo-200 dark:border-indigo-800/30">
                                <div className="h-12 w-12 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-lg font-semibold">
                                    {selectedPatient.first_name[0]}{selectedPatient.last_name[0]}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                        {selectedPatient.first_name} {selectedPatient.last_name}
                                    </p>
                                    <p className="text-xs text-gray-500 dark:text-neutral-400 font-mono">
                                        {t('page.analytics.id')}: {selectedPatient.patient_uid}
                                    </p>
                                    {selectedPatient.insurance && (
                                        <p className="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                                            {t('page.analytics.insurance')}: {selectedPatient.insurance}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Revenue Breakdown */}
                            <div>
                                <label className="block text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase tracking-wide mb-3">{t('page.analytics.revenue_breakdown')}</label>
                                <div className="space-y-3">
                                    <RevenueRow
                                        label={t('page.analytics.medications')}
                                        amount={selectedPatient.medications}
                                        percentage={(selectedPatient.medications / selectedPatient.total) * 100}
                                        icon={BeakerIcon}
                                        color="emerald"
                                    />
                                    <RevenueRow
                                        label={t('page.analytics.consultations')}
                                        amount={selectedPatient.appointments}
                                        percentage={(selectedPatient.appointments / selectedPatient.total) * 100}
                                        icon={CalendarDaysIcon}
                                        color="blue"
                                    />
                                    <RevenueRow
                                        label={t('page.analytics.lab_tests')}
                                        amount={selectedPatient.labs}
                                        percentage={(selectedPatient.labs / selectedPatient.total) * 100}
                                        icon={ClipboardDocumentCheckIcon}
                                        color="amber"
                                    />
                                    <RevenueRow
                                        label={t('page.analytics.admissions')}
                                        amount={selectedPatient.admissions}
                                        percentage={(selectedPatient.admissions / selectedPatient.total) * 100}
                                        icon={BuildingOffice2Icon}
                                        color="purple"
                                    />
                                </div>
                            </div>

                            {/* Total */}
                            <div className="pt-4 border-t border-gray-200 dark:border-neutral-700">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm font-semibold text-gray-700 dark:text-neutral-300">{t('page.analytics.total_contribution')}</span>
                                    <span className="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                        FCFA {formatFCFA(selectedPatient.total)}
                                    </span>
                                </div>
                            </div>

                            {/* Copy Actions */}
                            <div className="flex gap-2 pt-2">
                                <button
                                    onClick={() => handleCopyToClipboard(selectedPatient.patient_uid, 'uid')}
                                    className={`flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors ${
                                        copiedField === 'uid'
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700'
                                    }`}
                                >
                                    {copiedField === 'uid' ? t('page.analytics.copied') : <><ClipboardDocumentIcon className="w-3.5 h-3.5" /> {t('page.analytics.copy_id')}</>}
                                </button>
                                <button
                                    onClick={() => handleCopyToClipboard(JSON.stringify(selectedPatient, null, 2), 'full')}
                                    className={`flex-1 flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition-colors ${
                                        copiedField === 'full'
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700'
                                    }`}
                                >
                                    {copiedField === 'full' ? t('page.analytics.copied') : <><ClipboardDocumentIcon className="w-3.5 h-3.5" /> {t('page.analytics.copy_json')}</>}
                                </button>
                            </div>
                        </div>

                        {/* Modal Footer */}
                        <div className="px-5 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700 flex justify-end">
                            <button
                                onClick={handleCloseModal}
                                className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-neutral-300 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition-colors"
                            >
                                {t('common.close')}
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Global Loading Overlay */}
            {isLoading && !isModalOpen && (
                <div className="fixed inset-0 z-40 flex items-center justify-center bg-white/30 dark:bg-neutral-900/30 backdrop-blur-sm pointer-events-none">
                    <div className="flex items-center gap-3 px-5 py-3 bg-white dark:bg-neutral-800 rounded-lg shadow-lg border border-gray-200 dark:border-neutral-700">
                        <ArrowPathIcon className="animate-spin w-5 h-5 text-indigo-600" />
                        <span className="text-sm font-medium text-gray-700 dark:text-neutral-300">{t('page.analytics.syncing_data')}</span>
                    </div>
                </div>
            )}
        </SuperAdminLayout>
    );
}

// --- Sub-Components ---

function RevenueRow({
    label,
    amount,
    percentage,
    icon: Icon,
    color
}: {
    label: string;
    amount: number;
    percentage: number;
    icon: React.ComponentType<{ className?: string }>;
    color: 'emerald' | 'blue' | 'amber' | 'purple';
}) {
    const formatFCFA = (num: number) => new Intl.NumberFormat('fr-FR').format(num);

    const colorClasses: Record<string, { bg: string; text: string; bar: string }> = {
        emerald: { bg: 'bg-emerald-50 dark:bg-emerald-900/20', text: 'text-emerald-600 dark:text-emerald-400', bar: 'bg-emerald-500' },
        blue: { bg: 'bg-blue-50 dark:bg-blue-900/20', text: 'text-blue-600 dark:text-blue-400', bar: 'bg-blue-500' },
        amber: { bg: 'bg-amber-50 dark:bg-amber-900/20', text: 'text-amber-600 dark:text-amber-400', bar: 'bg-amber-500' },
        purple: { bg: 'bg-purple-50 dark:bg-purple-900/20', text: 'text-purple-600 dark:text-purple-400', bar: 'bg-purple-500' },
    };

    const colors = colorClasses[color];

    return (
        <div className={`p-3 rounded-lg ${colors.bg} border border-gray-200 dark:border-neutral-700`}>
            <div className="flex items-center justify-between mb-2">
                <div className="flex items-center gap-2">
                    <Icon className={`w-4 h-4 ${colors.text}`} />
                    <span className="text-sm font-medium text-gray-700 dark:text-neutral-300">{label}</span>
                </div>
                <span className={`text-sm font-semibold ${colors.text}`}>
                    FCFA {formatFCFA(amount)}
                </span>
            </div>
            <div className="w-full h-1.5 bg-gray-200 dark:bg-neutral-700 rounded-full overflow-hidden">
                <div
                    className={`h-full ${colors.bar} rounded-full transition-all duration-300`}
                    style={{ width: `${Math.min(percentage, 100)}%` }}
                />
            </div>
            <p className="text-xs text-gray-500 dark:text-neutral-400 mt-1">
                {percentage.toFixed(1)}% {t('page.analytics.of_total')}
            </p>
        </div>
    );
}
