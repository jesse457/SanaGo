import {
    ArrowDownTrayIcon,
    ArrowPathIcon,
    BeakerIcon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    DocumentMagnifyingGlassIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useCallback, useMemo, useState } from 'react';
import {
    Avatar,
    Badge,
    Button,
    Card,
    CardHeader,
    CardTitle,
    EmptyState,
    PageHeader,
    ProgressBar,
    SegmentedControl,
    SkeletonRows,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    ToolbarSearch,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import {
    formatFCFA,
    formatPercent,
    PatientRevenueDialog,
    SortIndicator,
} from '@/pages/Settings/components/AnalyticsPanels';
import type {
    AnalyticsMetric,
    PatientRevenue,
    RevenueBreakdown,
} from '@/pages/Settings/components/AnalyticsPanels';

type TimePeriod = 'today' | 'week' | 'month' | 'year';
type SortField = 'total' | 'name' | 'recent';

/** Stand-in series for the hero sparkline until the backend supplies one. */
const HERO_SERIES = [35, 50, 45, 80, 60, 90, 75];
const HERO_PEAK_INDEX = 5;

export default function RevenueAnalytics() {
    const { t } = useTranslation();

    const [timePeriod, setTimePeriod] = useState<TimePeriod>('month');
    const [isLoading, setIsLoading] = useState(false);
    const [searchQuery, setSearchQuery] = useState('');
    const [sortBy, setSortBy] = useState<SortField>('total');
    const [sortOrder, setSortOrder] = useState<'asc' | 'desc'>('desc');
    const [selectedPatient, setSelectedPatient] =
        useState<PatientRevenue | null>(null);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [copiedField, setCopiedField] = useState<string | null>(null);

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

    const periods: { value: TimePeriod; label: string }[] = [
        { value: 'today', label: t('page.analytics.today') },
        { value: 'week', label: t('page.analytics.week') },
        { value: 'month', label: t('page.analytics.month') },
        { value: 'year', label: t('page.analytics.year') },
    ];

    const metrics: AnalyticsMetric[] = [
        {
            key: 'medications',
            title: t('page.analytics.medications'),
            amount: breakdown.medication,
            icon: BeakerIcon,
            tone: 'positive',
            change: '+8.2%',
        },
        {
            key: 'consultations',
            title: t('page.analytics.consultations'),
            amount: breakdown.appointment,
            icon: CalendarDaysIcon,
            tone: 'accent',
            change: '+15.1%',
        },
        {
            key: 'lab_tests',
            title: t('page.analytics.lab_tests'),
            amount: breakdown.lab,
            icon: ClipboardDocumentCheckIcon,
            tone: 'warning',
            change: '+5.7%',
        },
        {
            key: 'admissions',
            title: t('page.analytics.admissions'),
            amount: breakdown.admission,
            icon: BuildingOffice2Icon,
            tone: 'muted',
            change: '+22.3%',
        },
    ];

    const filteredAndSortedPatients = useMemo(() => {
        let result = [...patientRevenues];

        if (searchQuery) {
            const query = searchQuery.toLowerCase();
            result = result.filter(
                (patient) =>
                    patient.first_name.toLowerCase().includes(query) ||
                    patient.last_name.toLowerCase().includes(query) ||
                    patient.patient_uid.toLowerCase().includes(query),
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
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [searchQuery, sortBy, sortOrder]);

    const toggleSort = useCallback(
        (field: SortField) => {
            if (sortBy === field) {
                setSortOrder((previous) =>
                    previous === 'asc' ? 'desc' : 'asc',
                );
            } else {
                setSortBy(field);
                setSortOrder('desc');
            }
        },
        [sortBy],
    );

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
            } catch {
                setCopiedField(null);
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
            const anchor = document.createElement('a');
            anchor.href = url;
            anchor.download = `revenue-ledger-${timePeriod}-${new Date().toISOString().split('T')[0]}.json`;
            anchor.click();
            URL.revokeObjectURL(url);
            setIsLoading(false);
        }, 800);
    }, [filteredAndSortedPatients, timePeriod]);

    const handleRefresh = useCallback(() => {
        setIsLoading(true);
        setTimeout(() => setIsLoading(false), 1000);
    }, []);

    const sortDirection = (field: SortField) =>
        sortBy === field ? sortOrder : null;

    return (
        <SuperAdminLayout
            headerTitle={t('page.analytics.title')}
            headerSubtitle={t('page.analytics.subtitle')}
        >
            <Head title={t('page.analytics.head_title')} />

            <div className="mx-auto max-w-7xl space-y-5 pb-12">
                <PageHeader
                    eyebrow={t('nav.category.insights')}
                    title={t('page.analytics.title')}
                    description={t('page.analytics.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/' },
                        { label: t('page.analytics.head_title') },
                    ]}
                    actions={
                        <Button
                            variant="secondary"
                            size="sm"
                            icon={ArrowPathIcon}
                            loading={isLoading}
                            onClick={handleRefresh}
                        >
                            {t('page.analytics.refresh_data')}
                        </Button>
                    }
                    meta={
                        <SegmentedControl
                            options={periods}
                            value={timePeriod}
                            onChange={setTimePeriod}
                            size="sm"
                        />
                    }
                />

                <Card>
                    <div className="flex flex-col justify-between gap-6 p-5 sm:p-6 lg:flex-row lg:items-end">
                        <div className="space-y-3">
                            <Badge tone="positive" dot>
                                {t('page.analytics.financial_performance')}
                            </Badge>
                            <div>
                                <p className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                    {t('page.analytics.gross_collection')} (
                                    {timePeriod})
                                </p>
                                <p className="mt-1 flex items-baseline gap-2">
                                    <span className="text-[12px] font-medium text-ink-subtle">
                                        FCFA
                                    </span>
                                    <span className="text-[30px] leading-none font-semibold tracking-[-0.02em] text-ink tabular-nums">
                                        {formatFCFA(totalRevenue)}
                                    </span>
                                </p>
                            </div>
                            <p className="text-[13px] text-ink-muted">
                                {t('page.analytics.growth_vs_previous')}{' '}
                                <span
                                    className={
                                        revenueGrowth >= 0
                                            ? 'font-semibold text-positive-ink tabular-nums'
                                            : 'font-semibold text-critical-ink tabular-nums'
                                    }
                                >
                                    {formatPercent(revenueGrowth)}
                                </span>
                            </p>
                        </div>

                        <div
                            aria-hidden="true"
                            className="flex h-16 items-end gap-1.5"
                        >
                            {HERO_SERIES.map((height, index) => (
                                <div
                                    key={index}
                                    style={{ height: `${height}%` }}
                                    className={
                                        index === HERO_PEAK_INDEX
                                            ? 'w-2 origin-bottom scale-y-100 rounded-t-sm bg-accent transition-transform duration-300 ease-premium'
                                            : 'w-2 rounded-t-sm bg-surface-inset'
                                    }
                                />
                            ))}
                        </div>
                    </div>
                </Card>

                <StatGrid>
                    {metrics.map((metric) => (
                        <StatCard
                            key={metric.key}
                            label={metric.title}
                            value={formatFCFA(metric.amount)}
                            icon={metric.icon}
                            tone={
                                metric.tone === 'muted'
                                    ? 'neutral'
                                    : metric.tone
                            }
                            trend={{
                                value: metric.change,
                                direction: 'up',
                            }}
                            footer={
                                <div className="space-y-1.5">
                                    <ProgressBar
                                        value={
                                            (metric.amount / totalRevenue) * 100
                                        }
                                        size="sm"
                                    />
                                    <span className="block text-[11px] text-ink-subtle tabular-nums">
                                        FCFA
                                    </span>
                                </div>
                            }
                        />
                    ))}
                </StatGrid>

                <Card>
                    <CardHeader
                        actions={
                            <div className="flex items-center gap-2">
                                <ToolbarSearch
                                    value={searchQuery}
                                    onChange={setSearchQuery}
                                    placeholder={t(
                                        'page.analytics.search_patient',
                                    )}
                                    className="w-full sm:w-44"
                                />
                                <Button
                                    variant="secondary"
                                    size="icon"
                                    icon={ArrowDownTrayIcon}
                                    aria-label={t('page.analytics.export_json')}
                                    title={t('page.analytics.export_json')}
                                    disabled={
                                        isLoading ||
                                        filteredAndSortedPatients.length === 0
                                    }
                                    onClick={handleExportLedger}
                                />
                            </div>
                        }
                    >
                        <div className="flex items-center gap-2">
                            <CardTitle>
                                {t('page.analytics.transaction_ledger')}
                            </CardTitle>
                            <Badge tone="muted">
                                {filteredAndSortedPatients.length}
                            </Badge>
                        </div>
                    </CardHeader>

                    {isLoading ? (
                        <div className="p-4 sm:p-5">
                            <SkeletonRows rows={4} columns={4} />
                        </div>
                    ) : filteredAndSortedPatients.length === 0 ? (
                        <div className="p-4 sm:p-5">
                            <EmptyState
                                icon={DocumentMagnifyingGlassIcon}
                                title={t('page.analytics.no_patients')}
                                description={
                                    searchQuery
                                        ? t(
                                              'page.analytics.try_adjusting_search',
                                          )
                                        : t('page.analytics.no_revenue_records')
                                }
                                panel={false}
                            />
                        </div>
                    ) : (
                        <Table bare>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>
                                        {t('page.analytics.col_patient')}
                                    </TableHeaderCell>
                                    <TableHeaderCell
                                        numeric
                                        className="hidden cursor-pointer sm:table-cell"
                                        onClick={() => toggleSort('total')}
                                    >
                                        {t('page.analytics.col_medications')}
                                        <SortIndicator
                                            direction={sortDirection('total')}
                                        />
                                    </TableHeaderCell>
                                    <TableHeaderCell
                                        numeric
                                        className="hidden md:table-cell"
                                    >
                                        {t('page.analytics.col_labs')}
                                    </TableHeaderCell>
                                    <TableHeaderCell
                                        numeric
                                        className="cursor-pointer"
                                        onClick={() => toggleSort('total')}
                                    >
                                        {t('page.analytics.col_total')}
                                        <SortIndicator
                                            direction={sortDirection('total')}
                                        />
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t('page.analytics.col_actions')}
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {filteredAndSortedPatients.map((row) => (
                                    <TableRow key={row.id} className="group">
                                        <TableCell>
                                            <div className="flex items-center gap-3">
                                                <Avatar
                                                    name={`${row.first_name} ${row.last_name}`}
                                                    size="sm"
                                                />
                                                <div className="min-w-0">
                                                    <p className="truncate text-[13px] font-medium text-ink">
                                                        {row.first_name}{' '}
                                                        {row.last_name}
                                                    </p>
                                                    <p className="font-mono text-[11.5px] text-ink-subtle">
                                                        #{row.patient_uid}
                                                    </p>
                                                </div>
                                            </div>
                                        </TableCell>
                                        <TableCell
                                            numeric
                                            muted
                                            className="hidden sm:table-cell"
                                        >
                                            {formatFCFA(row.medications)}
                                        </TableCell>
                                        <TableCell
                                            numeric
                                            muted
                                            className="hidden md:table-cell"
                                        >
                                            {formatFCFA(row.labs)}
                                        </TableCell>
                                        <TableCell numeric>
                                            <Badge tone="positive">
                                                {formatFCFA(row.total)}
                                            </Badge>
                                        </TableCell>
                                        <TableCell numeric>
                                            <div className="flex items-center justify-end gap-1 opacity-0 transition-opacity duration-200 ease-premium group-hover:opacity-100 focus-within:opacity-100">
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    icon={
                                                        DocumentMagnifyingGlassIcon
                                                    }
                                                    aria-label={t(
                                                        'page.analytics.view_details',
                                                    )}
                                                    title={t(
                                                        'page.analytics.view_details',
                                                    )}
                                                    onClick={() =>
                                                        handleViewDetails(row)
                                                    }
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </Card>
            </div>

            <PatientRevenueDialog
                open={isModalOpen}
                patient={selectedPatient}
                copiedField={copiedField}
                onCopy={handleCopyToClipboard}
                onClose={handleCloseModal}
            />

            {isLoading && !isModalOpen && (
                <div className="pointer-events-none fixed inset-0 z-40 flex items-center justify-center bg-canvas/50 backdrop-blur-[2px]">
                    <div className="flex items-center gap-3 rounded-lg border border-line bg-surface px-4 py-3 shadow-float">
                        <ArrowPathIcon className="h-4 w-4 animate-spin text-accent" />
                        <span className="text-[13px] font-medium text-ink">
                            {t('page.analytics.syncing_data')}
                        </span>
                    </div>
                </div>
            )}
        </SuperAdminLayout>
    );
}
