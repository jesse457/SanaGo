import {
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
    ClipboardDocumentIcon,
    ArrowUpRightIcon,
    ArrowDownRightIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import React, { useState, useMemo, useCallback } from 'react';
import EmptyState from '@/Components/EmptyState';
import Modal from '@/Components/Modal';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

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
    const [selectedPatient, setSelectedPatient] =
        useState<PatientRevenue | null>(null);
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
            insurance: 'CNAM',
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
            insurance: 'Private',
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
            insurance: 'CNAM',
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
            change: '+8.2%',
        },
        {
            title: t('page.analytics.consultations'),
            amount: breakdown.appointment,
            icon: CalendarDaysIcon,
            color: 'text-blue-600 dark:text-blue-400',
            bg: 'bg-blue-50 dark:bg-blue-900/20',
            change: '+15.1%',
        },
        {
            title: t('page.analytics.lab_tests'),
            amount: breakdown.lab,
            icon: ClipboardDocumentCheckIcon,
            color: 'text-amber-600 dark:text-amber-400',
            bg: 'bg-amber-50 dark:bg-amber-900/20',
            change: '+5.7%',
        },
        {
            title: t('page.analytics.admissions'),
            amount: breakdown.admission,
            icon: BuildingOffice2Icon,
            color: 'text-purple-600 dark:text-purple-400',
            bg: 'bg-purple-50 dark:bg-purple-900/20',
            change: '+22.3%',
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
            result = result.filter(
                (p) =>
                    p.first_name.toLowerCase().includes(query) ||
                    p.last_name.toLowerCase().includes(query) ||
                    p.patient_uid.toLowerCase().includes(query),
            );
        }

        result.sort((a, b) => {
            let comparison = 0;

            if (sortBy === 'total') {
                comparison = a.total - b.total;
            } else if (sortBy === 'name') {
                comparison = `${a.last_name} ${a.first_name}`.localeCompare(
                    `${b.last_name} ${b.first_name}`,
                );
            } else if (sortBy === 'recent') {
                comparison = (a.last_visit || '').localeCompare(
                    b.last_visit || '',
                );
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

    const handleCopyToClipboard = useCallback(
        async (text: string, field: string) => {
            try {
                await navigator.clipboard.writeText(text);
                setCopiedField(field);
                setTimeout(() => setCopiedField(null), 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
            }
        },
        [],
    );

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

    const toggleSort = useCallback(
        (field: 'total' | 'name' | 'recent') => {
            if (sortBy === field) {
                setSortOrder((prev) => (prev === 'asc' ? 'desc' : 'asc'));
            } else {
                setSortBy(field);
                setSortOrder('desc');
            }
        },
        [sortBy],
    );

    const getSortIcon = useCallback(
        (field: 'total' | 'name' | 'recent') => {
            if (sortBy !== field) {
                return null;
            }

            return sortOrder === 'asc' ? (
                <ArrowUpRightIcon className="ml-1 inline h-3 w-3" />
            ) : (
                <ArrowDownRightIcon className="ml-1 inline h-3 w-3" />
            );
        },
        [sortBy, sortOrder],
    );

    return (
        <SuperAdminLayout
            headerTitle={t('page.analytics.title')}
            headerSubtitle={t('page.analytics.subtitle')}
        >
            <Head title={t('page.analytics.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div className="mb-1">
                    <PageBreadcrumb
                        items={[
                            { label: t('common.home'), href: '/' },
                            { label: t('page.analytics.head_title') },
                        ]}
                    />
                </div>

                {/* Period Selector & Actions Bar */}
                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex items-center gap-2">
                        {periods.map((p) => (
                            <button
                                key={p.key}
                                onClick={() => setTimePeriod(p.key)}
                                className={`rounded-md px-3 py-1.5 text-xs font-medium transition-all ${
                                    timePeriod === p.key
                                        ? 'bg-neutral-900 text-white shadow-sm dark:bg-white dark:text-neutral-900'
                                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100'
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
                            className="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-200"
                            title={t('page.analytics.refresh_data')}
                        >
                            <ArrowPathIcon
                                className={`h-4 w-4 ${isLoading ? 'animate-spin' : ''}`}
                            />
                        </button>
                    </div>
                </div>

                {/* Hero Summary Card */}
                <div className="relative overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div className="pointer-events-none absolute top-0 right-0 h-64 w-64 translate-x-1/2 -translate-y-1/2 rounded-full bg-blue-500/5 blur-3xl"></div>

                    <div className="relative z-10 flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
                        <div className="space-y-3">
                            <div className="inline-flex items-center gap-1.5 rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">
                                <ArrowTrendingUpIcon className="h-3.5 w-3.5" />
                                {t('page.analytics.financial_performance')}
                            </div>

                            <div>
                                <p className="mb-1 text-xs font-medium text-gray-500 dark:text-neutral-400">
                                    {t('page.analytics.gross_collection')} (
                                    {timePeriod})
                                </p>
                                <div className="flex items-baseline gap-2">
                                    <span className="text-sm font-medium text-gray-400">
                                        FCFA
                                    </span>
                                    <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-neutral-100">
                                        {formatFCFA(totalRevenue)}
                                    </h2>
                                </div>
                            </div>

                            <p className="text-sm text-gray-600 dark:text-neutral-400">
                                {t('page.analytics.growth_vs_previous')}{' '}
                                <span
                                    className={`font-semibold ${revenueGrowth >= 0 ? 'text-emerald-600' : 'text-red-600'}`}
                                >
                                    {formatPercent(revenueGrowth)}
                                </span>
                            </p>
                        </div>

                        {/* Mini Chart */}
                        <div className="flex h-20 items-end gap-1.5">
                            {[35, 50, 45, 80, 60, 90, 75].map((height, i) => (
                                <div
                                    key={i}
                                    style={{ height: `${height}%` }}
                                    className={`w-2 rounded-t transition-all sm:w-2.5 ${
                                        i === 5
                                            ? 'bg-blue-600'
                                            : 'bg-gray-200 dark:bg-neutral-700'
                                    }`}
                                />
                            ))}
                        </div>
                    </div>
                </div>

                {/* Metrics Grid */}
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {metrics.map((m, i) => (
                        <div
                            key={i}
                            className="rounded-lg border border-gray-200 bg-white p-5 transition-shadow hover:shadow-sm dark:border-neutral-700 dark:bg-neutral-900"
                        >
                            <div className="mb-4 flex items-start justify-between">
                                <div
                                    className={`rounded-lg p-2.5 ${m.bg} ${m.color}`}
                                >
                                    <m.icon className="h-5 w-5" />
                                </div>
                                <span
                                    className={`text-xs font-medium ${m.change.startsWith('+') ? 'text-emerald-600' : 'text-red-600'}`}
                                >
                                    {m.change}
                                </span>
                            </div>
                            <p className="mb-1 text-xs font-medium text-gray-500 dark:text-neutral-400">
                                {m.title}
                            </p>
                            <div className="flex items-baseline gap-1">
                                <span className="text-xs text-gray-400">
                                    FCFA
                                </span>
                                <span className="text-lg font-semibold text-gray-900 dark:text-neutral-100">
                                    {formatFCFA(m.amount)}
                                </span>
                            </div>
                            <div className="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-neutral-800">
                                <div
                                    style={{
                                        width: `${(m.amount / totalRevenue) * 100}%`,
                                    }}
                                    className="h-full rounded-full bg-gray-900 transition-all dark:bg-neutral-100"
                                />
                            </div>
                        </div>
                    ))}
                </div>

                {/* Ledger Table */}
                <div className="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    {/* Table Header */}
                    <div className="flex flex-col items-start justify-between gap-4 border-b border-gray-200 bg-gray-50/50 px-5 py-4 sm:flex-row sm:items-center dark:border-neutral-700 dark:bg-neutral-800/50">
                        <div className="flex items-center gap-2">
                            <FunnelIcon className="h-4 w-4 text-gray-500" />
                            <h3 className="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                {t('page.analytics.transaction_ledger')}
                            </h3>
                            <span className="rounded bg-gray-200 px-2 py-0.5 text-xs text-gray-600 dark:bg-neutral-700 dark:text-neutral-300">
                                {filteredAndSortedPatients.length}
                            </span>
                        </div>

                        <div className="flex w-full items-center gap-2 sm:w-auto">
                            {/* Search */}
                            <div className="relative flex-1 sm:flex-none">
                                <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <input
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) =>
                                        setSearchQuery(e.target.value)
                                    }
                                    placeholder={t(
                                        'page.analytics.search_patient',
                                    )}
                                    className="w-full rounded-lg border border-gray-300 bg-white py-2 pr-3 pl-9 text-sm placeholder-gray-400 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 sm:w-48 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-100"
                                />
                            </div>

                            {/* Export */}
                            <button
                                onClick={handleExportLedger}
                                disabled={
                                    isLoading ||
                                    filteredAndSortedPatients.length === 0
                                }
                                className="rounded-lg border border-gray-300 bg-white p-2.5 text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                title={t('page.analytics.export_json')}
                            >
                                <ArrowDownTrayIcon className="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    {/* Table Content */}
                    <div className="overflow-x-auto">
                        {isLoading ? (
                            <div className="flex items-center justify-center py-12">
                                <div className="flex items-center gap-3 rounded-lg bg-gray-50 px-5 py-3 dark:bg-neutral-800">
                                    <ArrowPathIcon className="h-5 w-5 animate-spin text-blue-600" />
                                    <span className="text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('page.analytics.loading_data')}
                                    </span>
                                </div>
                            </div>
                        ) : filteredAndSortedPatients.length === 0 ? (
                            <div className="py-8">
                                <EmptyState
                                    icon={DocumentMagnifyingGlassIcon}
                                    title={t('page.analytics.no_patients')}
                                    description={
                                        searchQuery
                                            ? t(
                                                  'page.analytics.try_adjusting_search',
                                              )
                                            : t(
                                                  'page.analytics.no_revenue_records',
                                              )
                                    }
                                />
                            </div>
                        ) : (
                            <table className="w-full">
                                <thead className="bg-gray-50 dark:bg-neutral-800/50">
                                    <tr>
                                        <th className="px-5 py-3 text-left text-xs font-semibold tracking-wide text-gray-500 uppercase">
                                            {t('page.analytics.col_patient')}
                                        </th>
                                        <th
                                            className="hidden cursor-pointer px-5 py-3 text-right text-xs font-semibold tracking-wide text-gray-500 uppercase hover:text-gray-700 sm:table-cell dark:hover:text-neutral-300"
                                            onClick={() => toggleSort('total')}
                                        >
                                            {t(
                                                'page.analytics.col_medications',
                                            )}{' '}
                                            {getSortIcon('total')}
                                        </th>
                                        <th className="hidden px-5 py-3 text-right text-xs font-semibold tracking-wide text-gray-500 uppercase md:table-cell">
                                            {t('page.analytics.col_labs')}
                                        </th>
                                        <th
                                            className="cursor-pointer px-5 py-3 text-right text-xs font-semibold tracking-wide text-gray-500 uppercase hover:text-gray-700 dark:hover:text-neutral-300"
                                            onClick={() => toggleSort('total')}
                                        >
                                            {t('page.analytics.col_total')}{' '}
                                            {getSortIcon('total')}
                                        </th>
                                        <th className="px-5 py-3 text-center text-xs font-semibold tracking-wide text-gray-500 uppercase">
                                            {t('page.analytics.col_actions')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 dark:divide-neutral-800">
                                    {filteredAndSortedPatients.map((row) => (
                                        <tr
                                            key={row.id}
                                            className="group transition-colors hover:bg-gray-50 dark:hover:bg-neutral-800/40"
                                        >
                                            <td className="px-5 py-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-xs font-semibold text-gray-600 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                                                        {row.first_name[0]}
                                                        {row.last_name[0]}
                                                    </div>
                                                    <div className="min-w-0">
                                                        <p className="truncate text-sm font-medium text-gray-900 dark:text-neutral-100">
                                                            {row.first_name}{' '}
                                                            {row.last_name}
                                                        </p>
                                                        <p className="font-mono text-xs text-gray-500 dark:text-neutral-400">
                                                            #{row.patient_uid}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="hidden px-5 py-4 text-right text-sm text-gray-600 sm:table-cell dark:text-neutral-300">
                                                {formatFCFA(row.medications)}
                                            </td>
                                            <td className="hidden px-5 py-4 text-right text-sm text-gray-600 md:table-cell dark:text-neutral-300">
                                                {formatFCFA(row.labs)}
                                            </td>
                                            <td className="px-5 py-4 text-right">
                                                <span className="inline-flex items-center rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-sm font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400">
                                                    {formatFCFA(row.total)}
                                                </span>
                                            </td>
                                            <td className="px-5 py-4 text-center">
                                                <div className="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                                    <button
                                                        onClick={() =>
                                                            handleViewDetails(
                                                                row,
                                                            )
                                                        }
                                                        className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20"
                                                        title={t(
                                                            'page.analytics.view_details',
                                                        )}
                                                    >
                                                        <DocumentMagnifyingGlassIcon className="h-4 w-4" />
                                                    </button>
                                                    <button
                                                        className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-neutral-800"
                                                        title={t(
                                                            'page.analytics.more_options',
                                                        )}
                                                    >
                                                        <EllipsisHorizontalIcon className="h-4 w-4" />
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
            <Modal
                isOpen={isModalOpen}
                onClose={handleCloseModal}
                title={t('page.analytics.patient_revenue_details')}
                maxWidth="max-w-lg"
            >
                <div className="max-h-[70vh] space-y-5 overflow-y-auto">
                    {/* Patient Header */}
                    <div className="flex items-center gap-4 rounded-lg border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-800/30 dark:bg-blue-900/10">
                        <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-lg font-semibold text-white">
                            {selectedPatient?.first_name[0]}
                            {selectedPatient?.last_name[0]}
                        </div>
                        <div className="min-w-0 flex-1">
                            <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                {selectedPatient?.first_name}{' '}
                                {selectedPatient?.last_name}
                            </p>
                            <p className="font-mono text-xs text-gray-500 dark:text-neutral-400">
                                {t('page.analytics.id')}:{' '}
                                {selectedPatient?.patient_uid}
                            </p>
                            {selectedPatient?.insurance && (
                                <p className="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                    {t('page.analytics.insurance')}:{' '}
                                    {selectedPatient.insurance}
                                </p>
                            )}
                        </div>
                    </div>

                    {selectedPatient && (
                        <>
                            {/* Revenue Breakdown */}
                            <div>
                                <label className="mb-3 block text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                                    {t('page.analytics.revenue_breakdown')}
                                </label>
                                <div className="space-y-3">
                                    <RevenueRow
                                        label={t('page.analytics.medications')}
                                        amount={selectedPatient.medications}
                                        percentage={
                                            (selectedPatient.medications /
                                                selectedPatient.total) *
                                            100
                                        }
                                        icon={BeakerIcon}
                                        color="emerald"
                                    />
                                    <RevenueRow
                                        label={t(
                                            'page.analytics.consultations',
                                        )}
                                        amount={selectedPatient.appointments}
                                        percentage={
                                            (selectedPatient.appointments /
                                                selectedPatient.total) *
                                            100
                                        }
                                        icon={CalendarDaysIcon}
                                        color="blue"
                                    />
                                    <RevenueRow
                                        label={t('page.analytics.lab_tests')}
                                        amount={selectedPatient.labs}
                                        percentage={
                                            (selectedPatient.labs /
                                                selectedPatient.total) *
                                            100
                                        }
                                        icon={ClipboardDocumentCheckIcon}
                                        color="amber"
                                    />
                                    <RevenueRow
                                        label={t('page.analytics.admissions')}
                                        amount={selectedPatient.admissions}
                                        percentage={
                                            (selectedPatient.admissions /
                                                selectedPatient.total) *
                                            100
                                        }
                                        icon={BuildingOffice2Icon}
                                        color="purple"
                                    />
                                </div>
                            </div>

                            {/* Total */}
                            <div className="border-t border-gray-200 pt-4 dark:border-neutral-700">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm font-semibold text-gray-700 dark:text-neutral-300">
                                        {t('page.analytics.total_contribution')}
                                    </span>
                                    <span className="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                        FCFA {formatFCFA(selectedPatient.total)}
                                    </span>
                                </div>
                            </div>

                            {/* Copy Actions */}
                            <div className="flex gap-2 pt-2">
                                <button
                                    onClick={() =>
                                        handleCopyToClipboard(
                                            selectedPatient.patient_uid,
                                            'uid',
                                        )
                                    }
                                    className={`flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors ${
                                        copiedField === 'uid'
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700'
                                    }`}
                                >
                                    {copiedField === 'uid' ? (
                                        t('page.analytics.copied')
                                    ) : (
                                        <>
                                            <ClipboardDocumentIcon className="h-3.5 w-3.5" />{' '}
                                            {t('page.analytics.copy_id')}
                                        </>
                                    )}
                                </button>
                                <button
                                    onClick={() =>
                                        handleCopyToClipboard(
                                            JSON.stringify(
                                                selectedPatient,
                                                null,
                                                2,
                                            ),
                                            'full',
                                        )
                                    }
                                    className={`flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-medium transition-colors ${
                                        copiedField === 'full'
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700'
                                    }`}
                                >
                                    {copiedField === 'full' ? (
                                        t('page.analytics.copied')
                                    ) : (
                                        <>
                                            <ClipboardDocumentIcon className="h-3.5 w-3.5" />{' '}
                                            {t('page.analytics.copy_json')}
                                        </>
                                    )}
                                </button>
                            </div>
                        </>
                    )}

                    {/* Footer */}
                    <div className="flex justify-end border-t border-gray-100 pt-4 dark:border-neutral-700">
                        <button
                            onClick={handleCloseModal}
                            className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                        >
                            {t('common.close')}
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Global Loading Overlay */}
            {isLoading && !isModalOpen && (
                <div className="pointer-events-none fixed inset-0 z-40 flex items-center justify-center bg-white/30 backdrop-blur-sm dark:bg-neutral-900/30">
                    <div className="flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-5 py-3 shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                        <ArrowPathIcon className="h-5 w-5 animate-spin text-blue-600" />
                        <span className="text-sm font-medium text-gray-700 dark:text-neutral-300">
                            {t('page.analytics.syncing_data')}
                        </span>
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
    color,
}: {
    label: string;
    amount: number;
    percentage: number;
    icon: React.ComponentType<{ className?: string }>;
    color: 'emerald' | 'blue' | 'amber' | 'purple';
}) {
    const { t } = useTranslation();
    const formatFCFA = (num: number) =>
        new Intl.NumberFormat('fr-FR').format(num);

    const colorClasses: Record<
        string,
        { bg: string; text: string; bar: string }
    > = {
        emerald: {
            bg: 'bg-emerald-50 dark:bg-emerald-900/20',
            text: 'text-emerald-600 dark:text-emerald-400',
            bar: 'bg-emerald-500',
        },
        blue: {
            bg: 'bg-blue-50 dark:bg-blue-900/20',
            text: 'text-blue-600 dark:text-blue-400',
            bar: 'bg-blue-500',
        },
        amber: {
            bg: 'bg-amber-50 dark:bg-amber-900/20',
            text: 'text-amber-600 dark:text-amber-400',
            bar: 'bg-amber-500',
        },
        purple: {
            bg: 'bg-purple-50 dark:bg-purple-900/20',
            text: 'text-purple-600 dark:text-purple-400',
            bar: 'bg-purple-500',
        },
    };

    const colors = colorClasses[color];

    return (
        <div
            className={`rounded-lg p-3 ${colors.bg} border border-gray-200 dark:border-neutral-700`}
        >
            <div className="mb-2 flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <Icon className={`h-4 w-4 ${colors.text}`} />
                    <span className="text-sm font-medium text-gray-700 dark:text-neutral-300">
                        {label}
                    </span>
                </div>
                <span className={`text-sm font-semibold ${colors.text}`}>
                    FCFA {formatFCFA(amount)}
                </span>
            </div>
            <div className="h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-neutral-700">
                <div
                    className={`h-full ${colors.bar} rounded-full transition-all duration-300`}
                    style={{ width: `${Math.min(percentage, 100)}%` }}
                />
            </div>
            <p className="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                {percentage.toFixed(1)}% {t('page.analytics.of_total')}
            </p>
        </div>
    );
}
