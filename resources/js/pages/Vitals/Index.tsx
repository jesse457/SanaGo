import {
    ArrowLeftIcon,
    ChartBarIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    HeartIcon,
    PlusCircleIcon,
    SparklesIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import {
    CategoryScale,
    Chart as ChartJS,
    Filler,
    Legend,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from 'chart.js';
import type { ChartOptions } from 'chart.js';
import { useMemo, useState } from 'react';
import { Line } from 'react-chartjs-2';
import { Pagination } from '@/Components/Pagination';
import {
    Badge,
    Button,
    EmptyState,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
    SegmentedControl,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { PatientIdentity } from '@/pages/Patients/components/PatientIdentity';
import type { Paginated, VitalRecord } from '@/types';

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Filler,
    Legend,
);

interface Props {
    patient: {
        id: number;
        patient_uid: string;
        first_name: string;
        last_name: string;
        date_of_birth?: string | null;
        gender?: string | null;
        age?: number | null;
        photo_url?: string | null;
    };
    /** `VitalController@index` paginates ten records per page. */
    vitals: Paginated<VitalRecord> | VitalRecord[];
}

/** Clinical reference ranges used for the out-of-range highlighting. */
const RANGE = {
    temperature: { warning: [36.1, 37.2], critical: [35, 38.5] },
    systolic: { warning: [90, 140], critical: [80, 180] },
    diastolic: { warning: [60, 90], critical: [50, 110] },
    heart_rate: { warning: [60, 100], critical: [40, 130] },
    respiratory_rate: { warning: [12, 20], critical: [8, 30] },
    oxygen_saturation: { warning: [95, 100], critical: [90, 100] },
} as const;

type Severity = 'normal' | 'warning' | 'critical';

function severityOf(
    value: number | null | undefined,
    range: {
        warning: readonly [number, number];
        critical: readonly [number, number];
    },
): Severity {
    if (value === null || value === undefined || Number.isNaN(value)) {
        return 'normal';
    }

    if (value < range.critical[0] || value > range.critical[1]) {
        return 'critical';
    }

    if (value < range.warning[0] || value > range.warning[1]) {
        return 'warning';
    }

    return 'normal';
}

const severityClass: Record<Severity, string> = {
    normal: 'text-ink',
    warning: 'text-warning-ink font-semibold',
    critical: 'text-critical-ink font-semibold',
};

function formatDateTime(value: string): string {
    const parsed = new Date(value);

    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return `${parsed.toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })} · ${parsed.toLocaleTimeString(undefined, {
        hour: '2-digit',
        minute: '2-digit',
    })}`;
}

function toNumber(value: string | number | null | undefined): number | null {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    const parsed = typeof value === 'string' ? Number(value) : value;

    return Number.isNaN(parsed) ? null : parsed;
}

/**
 * Vitals/Index — the patient's full vitals history: a trend chart for the
 * three headline series, a table with out-of-range values tinted by severity,
 * and the reference ranges so the highlighting is never a mystery.
 */
export default function VitalsIndex({ patient, vitals }: Props) {
    const { t } = useTranslation();
    const [metric, setMetric] = useState<
        'temperature' | 'heart_rate' | 'oxygen'
    >('temperature');

    const paginated = Array.isArray(vitals) ? null : vitals;
    const rows = useMemo(
        () => (Array.isArray(vitals) ? vitals : vitals.data),
        [vitals],
    );

    const fullName = `${patient.first_name} ${patient.last_name}`.trim();

    const stats = useMemo(() => {
        const abnormal = rows.filter((row) => row.is_abnormal).length;
        const latest = rows[0] ?? null;

        return {
            count: paginated?.total ?? rows.length,
            abnormal,
            latest,
            latestTemperature: latest ? toNumber(latest.temperature) : null,
        };
    }, [paginated?.total, rows]);

    /** Chart.js wants oldest → newest, the table wants newest → oldest. */
    const chronological = useMemo(() => [...rows].reverse(), [rows]);

    const chartData = useMemo(() => {
        const labels = chronological.map((row) =>
            new Date(row.recorded_at).toLocaleDateString(undefined, {
                day: '2-digit',
                month: 'short',
            }),
        );

        const series = {
            temperature: chronological.map((row) => toNumber(row.temperature)),
            heart_rate: chronological.map((row) => row.heart_rate),
            oxygen: chronological.map((row) => row.oxygen_saturation),
        };

        const meta = {
            temperature: {
                label: t('page.vitals_index.chart_temperature'),
                color: 'rgba(217, 119, 6, 1)',
                fill: 'rgba(217, 119, 6, 0.08)',
            },
            heart_rate: {
                label: t('page.vitals_index.chart_heart_rate'),
                color: 'rgba(225, 29, 72, 1)',
                fill: 'rgba(225, 29, 72, 0.08)',
            },
            oxygen: {
                label: t('page.vitals_index.chart_oxygen'),
                color: 'rgba(2, 132, 199, 1)',
                fill: 'rgba(2, 132, 199, 0.08)',
            },
        }[metric];

        return {
            labels,
            datasets: [
                {
                    label: meta.label,
                    data: series[metric],
                    borderColor: meta.color,
                    backgroundColor: meta.fill,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 2.5,
                    pointHoverRadius: 5,
                    spanGaps: true,
                },
            ],
        };
    }, [chronological, metric, t]);

    const chartOptions: ChartOptions<'line'> = useMemo(
        () => ({
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: (context) => ` ${context.formattedValue}`,
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: false,
                    border: { display: false },
                    grid: { color: 'rgba(120,120,120,0.12)' },
                    ticks: { font: { size: 11 } },
                },
                x: {
                    border: { display: false },
                    grid: { display: false },
                    ticks: { font: { size: 11 }, maxRotation: 0 },
                },
            },
        }),
        [],
    );

    const severityLabel: Record<Severity, string> = {
        normal: t('page.vitals_index.flag_normal'),
        warning: t('page.vitals_index.warning_hint'),
        critical: t('page.vitals_index.critical_hint'),
    };

    const cell = (
        value: number | null | undefined,
        range: {
            warning: readonly [number, number];
            critical: readonly [number, number];
        },
    ) => {
        const severity = severityOf(value, range);

        return (
            <span
                className={severityClass[severity]}
                title={
                    severity === 'normal' ? undefined : severityLabel[severity]
                }
            >
                {value ?? '—'}
            </span>
        );
    };

    return (
        <AppLayout
            headerTitle={t('page.vitals_index.title')}
            headerSubtitle={fullName}
        >
            <Head
                title={`${t('page.vitals_index.head_title')} · ${fullName}`}
            />

            <div className="flex flex-col gap-6 pb-12">
                <PageHeader
                    eyebrow={t('page.vitals_index.eyebrow')}
                    title={t('page.vitals_index.title')}
                    description={t('page.vitals_index.subtitle', {
                        name: fullName,
                    })}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/dashboard' },
                        {
                            label: t(
                                'page.patient_profile.breadcrumb_directory',
                            ),
                            href: route('patients.index'),
                        },
                        {
                            label: fullName,
                            href: route('patients.show', patient.id),
                        },
                        { label: t('page.vitals_index.title') },
                    ]}
                    meta={
                        <PatientIdentity
                            firstName={patient.first_name}
                            lastName={patient.last_name}
                            patientUid={patient.patient_uid}
                            photoUrl={patient.photo_url}
                            size="sm"
                        />
                    }
                    actions={
                        <>
                            <Button
                                variant="secondary"
                                icon={ArrowLeftIcon}
                                href={route('patients.show', patient.id)}
                            >
                                {t('page.vitals_index.back_to_patient')}
                            </Button>
                            <Button
                                icon={PlusCircleIcon}
                                href={route('vitals.create', patient.id)}
                            >
                                {t('page.vitals_index.record_cta')}
                            </Button>
                        </>
                    }
                />

                <StatGrid columns={3}>
                    <StatCard
                        label={t('page.vitals_index.stat_records')}
                        value={stats.count}
                        icon={HeartIcon}
                        tone="accent"
                    />
                    <StatCard
                        label={t('page.vitals_index.stat_latest')}
                        value={
                            stats.latestTemperature !== null
                                ? `${stats.latestTemperature} °C`
                                : '—'
                        }
                        icon={ClockIcon}
                        hint={
                            stats.latest
                                ? formatDateTime(stats.latest.recorded_at)
                                : undefined
                        }
                        tone="positive"
                    />
                    <StatCard
                        label={t('page.vitals_index.stat_abnormal')}
                        value={stats.abnormal}
                        icon={ExclamationTriangleIcon}
                        tone={stats.abnormal > 0 ? 'critical' : 'positive'}
                        hint={t('page.vitals_index.stat_abnormal_hint')}
                    />
                </StatGrid>

                <SectionCard
                    title={t('page.vitals_index.chart_title')}
                    description={t('page.vitals_index.chart_description')}
                    icon={ChartBarIcon}
                    actions={
                        <SegmentedControl
                            size="sm"
                            value={metric}
                            onChange={setMetric}
                            options={[
                                {
                                    value: 'temperature',
                                    label: t(
                                        'page.vitals_index.chart_temperature',
                                    ),
                                },
                                {
                                    value: 'heart_rate',
                                    label: t(
                                        'page.vitals_index.chart_heart_rate',
                                    ),
                                },
                                {
                                    value: 'oxygen',
                                    label: t('page.vitals_index.chart_oxygen'),
                                },
                            ]}
                        />
                    }
                >
                    {chronological.length >= 2 ? (
                        <div className="h-64 w-full">
                            <Line data={chartData} options={chartOptions} />
                        </div>
                    ) : (
                        <EmptyState
                            panel={false}
                            icon={SparklesIcon}
                            title={t('page.vitals_index.chart_empty')}
                            description={t(
                                'page.vitals_index.chart_empty_desc',
                            )}
                            className="py-10"
                        />
                    )}
                </SectionCard>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-4">
                    <div className="lg:col-span-3">
                        <SectionCard
                            title={t('page.vitals_index.table_title')}
                            description={t(
                                'page.vitals_index.table_description',
                            )}
                            contentClassName="px-0 py-0 sm:px-0"
                        >
                            {rows.length === 0 ? (
                                <div className="px-5 py-6 sm:px-6">
                                    <EmptyState
                                        panel={false}
                                        icon={HeartIcon}
                                        title={t('page.vitals_index.empty')}
                                        description={t(
                                            'page.vitals_index.empty_desc',
                                        )}
                                        action={
                                            <Button
                                                icon={PlusCircleIcon}
                                                href={route(
                                                    'vitals.create',
                                                    patient.id,
                                                )}
                                            >
                                                {t(
                                                    'page.vitals_index.record_cta',
                                                )}
                                            </Button>
                                        }
                                    />
                                </div>
                            ) : (
                                <>
                                    <Table bare scroll>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHeaderCell>
                                                    {t(
                                                        'page.vitals_index.col_recorded',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t(
                                                        'page.vitals_index.col_temperature',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t(
                                                        'page.vitals_index.col_bp',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t(
                                                        'page.vitals_index.col_pulse',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t(
                                                        'page.vitals_index.col_respiratory',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell numeric>
                                                    {t(
                                                        'page.vitals_index.col_oxygen',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell>
                                                    {t(
                                                        'page.vitals_index.col_recorded_by',
                                                    )}
                                                </TableHeaderCell>
                                                <TableHeaderCell>
                                                    {t(
                                                        'page.vitals_index.col_flag',
                                                    )}
                                                </TableHeaderCell>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {rows.map((row) => (
                                                <TableRow key={row.id}>
                                                    <TableCell>
                                                        {formatDateTime(
                                                            row.recorded_at,
                                                        )}
                                                    </TableCell>
                                                    <TableCell numeric>
                                                        {cell(
                                                            toNumber(
                                                                row.temperature,
                                                            ),
                                                            RANGE.temperature,
                                                        )}
                                                    </TableCell>
                                                    <TableCell numeric>
                                                        <span className="tabular-nums">
                                                            {cell(
                                                                row.systolic_bp,
                                                                RANGE.systolic,
                                                            )}
                                                            <span className="text-ink-subtle">
                                                                /
                                                            </span>
                                                            {cell(
                                                                row.diastolic_bp,
                                                                RANGE.diastolic,
                                                            )}
                                                        </span>
                                                    </TableCell>
                                                    <TableCell numeric>
                                                        {cell(
                                                            row.heart_rate,
                                                            RANGE.heart_rate,
                                                        )}
                                                    </TableCell>
                                                    <TableCell numeric>
                                                        {cell(
                                                            row.respiratory_rate,
                                                            RANGE.respiratory_rate,
                                                        )}
                                                    </TableCell>
                                                    <TableCell numeric>
                                                        {cell(
                                                            row.oxygen_saturation,
                                                            RANGE.oxygen_saturation,
                                                        )}
                                                    </TableCell>
                                                    <TableCell muted>
                                                        {row.recordedBy?.name ??
                                                            '—'}
                                                    </TableCell>
                                                    <TableCell>
                                                        {row.is_abnormal ? (
                                                            <Badge
                                                                tone="critical"
                                                                dot
                                                            >
                                                                {t(
                                                                    'page.vitals_index.flag_abnormal',
                                                                )}
                                                            </Badge>
                                                        ) : (
                                                            <Badge tone="positive">
                                                                {t(
                                                                    'page.vitals_index.flag_normal',
                                                                )}
                                                            </Badge>
                                                        )}
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>

                                    {paginated && (
                                        <div className="border-t border-line px-5 py-3 sm:px-6">
                                            <Pagination
                                                links={paginated.links}
                                                meta={{
                                                    from: paginated.from ?? 0,
                                                    to: paginated.to ?? 0,
                                                    total: paginated.total,
                                                }}
                                            />
                                        </div>
                                    )}
                                </>
                            )}
                        </SectionCard>
                    </div>

                    <SectionCard
                        title={t('page.vitals_index.legend_title')}
                        icon={ExclamationTriangleIcon}
                    >
                        <KeyValueGrid columns={1}>
                            <KeyValue
                                label={t('page.vitals_index.col_temperature')}
                                value={t(
                                    'page.vitals_index.legend_temperature',
                                )}
                            />
                            <KeyValue
                                label={t('page.vitals_index.col_bp')}
                                value={t('page.vitals_index.legend_bp')}
                            />
                            <KeyValue
                                label={t('page.vitals_index.col_pulse')}
                                value={t('page.vitals_index.legend_pulse')}
                            />
                            <KeyValue
                                label={t('page.vitals_index.col_respiratory')}
                                value={t(
                                    'page.vitals_index.legend_respiratory',
                                )}
                            />
                            <KeyValue
                                label={t('page.vitals_index.col_oxygen')}
                                value={t('page.vitals_index.legend_oxygen')}
                            />
                        </KeyValueGrid>

                        <div className="mt-4 flex flex-col gap-1.5 border-t border-line pt-4 text-[11.5px]">
                            <span className="flex items-center gap-2 text-ink-subtle">
                                <span className="h-2 w-2 rounded-full bg-warning" />
                                {t('page.vitals_index.warning_hint')}
                            </span>
                            <span className="flex items-center gap-2 text-ink-subtle">
                                <span className="h-2 w-2 rounded-full bg-critical" />
                                {t('page.vitals_index.critical_hint')}
                            </span>
                        </div>
                    </SectionCard>
                </div>
            </div>
        </AppLayout>
    );
}
