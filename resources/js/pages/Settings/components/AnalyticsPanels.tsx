import {
    ArrowDownRightIcon,
    ArrowUpRightIcon,
    BeakerIcon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    ClipboardDocumentIcon,
} from '@heroicons/react/24/outline';
import {
    Avatar,
    Badge,
    Button,
    Dialog,
    KeyValue,
    KeyValueGrid,
    ProgressBar,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import { RevenueRow } from '@/pages/Settings/components/RevenueRow';

export interface PatientRevenue {
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

export interface RevenueBreakdown {
    medication: number;
    appointment: number;
    lab: number;
    admission: number;
}

export type AnalyticsMetricTone = 'positive' | 'accent' | 'warning' | 'muted';

export interface AnalyticsMetric {
    key: string;
    title: string;
    amount: number;
    icon: React.ComponentType<{ className?: string }>;
    tone: AnalyticsMetricTone;
    change: string;
}

export function formatFCFA(num: number): string {
    return new Intl.NumberFormat('fr-FR').format(num);
}

export function formatPercent(num: number): string {
    return `${num >= 0 ? '+' : ''}${num.toFixed(1)}%`;
}

/** SortIndicator — the arrow shown on a sortable ledger column. */
export function SortIndicator({
    direction,
}: {
    direction: 'asc' | 'desc' | null;
}) {
    if (!direction) {
        return null;
    }

    return direction === 'asc' ? (
        <ArrowUpRightIcon className="ml-1 inline h-3 w-3" />
    ) : (
        <ArrowDownRightIcon className="ml-1 inline h-3 w-3" />
    );
}

/**
 * PatientRevenueDialog — the "patient revenue details" modal: identity panel,
 * revenue breakdown with progress rows, total and copy actions.
 */
export function PatientRevenueDialog({
    open,
    patient,
    copiedField,
    onCopy,
    onClose,
}: {
    open: boolean;
    patient: PatientRevenue | null;
    copiedField: string | null;
    onCopy: (text: string, field: string) => void;
    onClose: () => void;
}) {
    const { t } = useTranslation();

    return (
        <Dialog
            open={open}
            onClose={onClose}
            size="lg"
            title={t('page.analytics.patient_revenue_details')}
            footer={
                <Button variant="secondary" onClick={onClose}>
                    {t('common.close')}
                </Button>
            }
        >
            {patient && (
                <div className="space-y-5">
                    <div className="flex items-center gap-3 rounded-lg border border-line bg-surface-muted px-4 py-3.5">
                        <Avatar
                            name={`${patient.first_name} ${patient.last_name}`}
                            size="lg"
                        />
                        <div className="min-w-0 flex-1">
                            <p className="truncate text-[13.5px] font-semibold text-ink">
                                {patient.first_name} {patient.last_name}
                            </p>
                            <p className="font-mono text-[12px] text-ink-subtle">
                                {t('page.analytics.id')}: {patient.patient_uid}
                            </p>
                            {patient.insurance && (
                                <p className="mt-1">
                                    <Badge tone="neutral" variant="outline">
                                        {patient.insurance}
                                    </Badge>
                                </p>
                            )}
                        </div>
                    </div>

                    <div>
                        <p className="mb-2.5 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            {t('page.analytics.revenue_breakdown')}
                        </p>
                        <div className="space-y-2.5">
                            <RevenueRow
                                label={t('page.analytics.medications')}
                                amount={patient.medications}
                                percentage={
                                    (patient.medications / patient.total) * 100
                                }
                                icon={BeakerIcon}
                                tone="positive"
                            />
                            <RevenueRow
                                label={t('page.analytics.consultations')}
                                amount={patient.appointments}
                                percentage={
                                    (patient.appointments / patient.total) * 100
                                }
                                icon={CalendarDaysIcon}
                                tone="accent"
                            />
                            <RevenueRow
                                label={t('page.analytics.lab_tests')}
                                amount={patient.labs}
                                percentage={
                                    (patient.labs / patient.total) * 100
                                }
                                icon={ClipboardDocumentCheckIcon}
                                tone="warning"
                            />
                            <RevenueRow
                                label={t('page.analytics.admissions')}
                                amount={patient.admissions}
                                percentage={
                                    (patient.admissions / patient.total) * 100
                                }
                                icon={BuildingOffice2Icon}
                                tone="muted"
                            />
                        </div>
                    </div>

                    <KeyValueGrid columns={1}>
                        <KeyValue
                            label={t('page.analytics.total_contribution')}
                            value={
                                <span className="text-[15px] font-semibold text-positive-ink tabular-nums">
                                    FCFA {formatFCFA(patient.total)}
                                </span>
                            }
                        />
                    </KeyValueGrid>

                    <div className="flex gap-2">
                        <CopyButton
                            active={copiedField === 'uid'}
                            label={t('page.analytics.copy_id')}
                            onCopy={() => onCopy(patient.patient_uid, 'uid')}
                        />
                        <CopyButton
                            active={copiedField === 'full'}
                            label={t('page.analytics.copy_json')}
                            onCopy={() =>
                                onCopy(JSON.stringify(patient, null, 2), 'full')
                            }
                        />
                    </div>
                </div>
            )}
        </Dialog>
    );
}

function CopyButton({
    active,
    label,
    onCopy,
}: {
    active: boolean;
    label: string;
    onCopy: () => void;
}) {
    const { t } = useTranslation();

    return (
        <Button
            variant={active ? 'secondary' : 'outline'}
            size="sm"
            fullWidth
            icon={ClipboardDocumentIcon}
            onClick={onCopy}
        >
            {active ? t('page.analytics.copied') : label}
        </Button>
    );
}

/**
 * MetricProgress — the thin share bar under each KPI card.
 */
export function MetricProgress({ value }: { value: number }) {
    return <ProgressBar value={value} size="sm" />;
}
