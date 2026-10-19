import {
    ArrowRightIcon,
    ClockIcon,
    DocumentTextIcon,
    PaperClipIcon,
} from '@heroicons/react/24/outline';
import { format, parseISO } from 'date-fns';
import {
    Badge,
    Button,
    Card,
    KeyValue,
    KeyValueGrid,
    SectionCard,
} from '@/Components/ui';
import type { Translate } from './types';

export interface MedicalRecordView {
    id: number;
    created_at: string;
    diagnosis?: string;
    chief_complaint?: string;
    treatment_plan?: string;
    doctor?: { name?: string };
    attachments?: { id: number; name?: string; url?: string }[];
}

export interface VitalView {
    id: number;
    recorded_at: string;
    temperature?: number | string;
    systolic_bp?: number | string;
    diastolic_bp?: number | string;
    heart_rate?: number | string;
    oxygen_saturation?: number | string;
    is_abnormal?: boolean;
    recorded_by?: { name?: string };
}

interface MedicalRecordCardProps {
    record: MedicalRecordView;
    t: Translate;
}

/**
 * MedicalRecordCard — one entry of the patient's consultation history: the
 * clinical summary plus the link into the full record.
 */
export function MedicalRecordCard({ record, t }: MedicalRecordCardProps) {
    return (
        <SectionCard
            icon={DocumentTextIcon}
            title={t('clinicalRecord')}
            description={format(
                parseISO(record.created_at),
                'MMM dd, yyyy • hh:mm a',
            )}
            actions={
                <Badge tone="muted">
                    {t('dr')} {record.doctor?.name || t('unknown')}
                </Badge>
            }
            footer={
                <Button
                    href={route('consultations.show', record.id)}
                    variant="ghost"
                    size="sm"
                    trailingIcon={ArrowRightIcon}
                >
                    {t('viewDetails')}
                </Button>
            }
        >
            <KeyValueGrid columns={2}>
                <KeyValue
                    label={t('diagnosis')}
                    value={record.diagnosis || t('noDiagnosisRecorded')}
                />
                <KeyValue
                    label={t('chiefComplaint')}
                    value={record.chief_complaint || t('notProvided')}
                />
                <KeyValue
                    label={t('treatmentPlan')}
                    value={record.treatment_plan || t('na')}
                />
                <KeyValue
                    label={t('attachments')}
                    value={
                        record.attachments?.length ? (
                            <span className="flex flex-wrap gap-2">
                                {record.attachments.map((attachment, index) => (
                                    <a
                                        key={attachment.id ?? index}
                                        href={attachment.url || '#'}
                                        className="inline-flex items-center gap-1 text-accent-ink hover:underline"
                                    >
                                        <PaperClipIcon className="h-3 w-3" />
                                        {t('file')} {index + 1}
                                    </a>
                                ))}
                            </span>
                        ) : (
                            t('noFiles')
                        )
                    }
                />
            </KeyValueGrid>
        </SectionCard>
    );
}

interface VitalCardProps {
    vital: VitalView;
    t: Translate;
}

/**
 * VitalCard — one recorded set of observations, with the abnormal flag carried
 * on the reading that triggered it.
 */
export function VitalCard({ vital, t }: VitalCardProps) {
    return (
        <Card className="p-4">
            <div className="mb-3 flex items-center justify-between">
                <span className="flex items-center gap-1.5 text-[12px] font-semibold text-ink tabular-nums">
                    <ClockIcon className="h-3.5 w-3.5 text-ink-subtle" />
                    {format(parseISO(vital.recorded_at), 'hh:mm a')}
                </span>
                {vital.is_abnormal && (
                    <Badge tone="critical" dot>
                        {t('abnormal')}
                    </Badge>
                )}
            </div>

            <KeyValueGrid columns={2} tile={false}>
                <KeyValue
                    label={t('temperature')}
                    value={`${vital.temperature ?? '—'}°C`}
                    className={
                        vital.is_abnormal && Number(vital.temperature) > 38
                            ? 'text-critical-ink'
                            : undefined
                    }
                />
                <KeyValue
                    label={t('bloodPressure')}
                    value={`${vital.systolic_bp ?? '—'}/${vital.diastolic_bp ?? '—'}`}
                />
                <KeyValue
                    label={t('heartRate')}
                    value={`${vital.heart_rate ?? '—'} ${t('bpm')}`}
                />
                <KeyValue
                    label={t('oxygenSpo2')}
                    value={`${vital.oxygen_saturation ?? '—'}%`}
                />
            </KeyValueGrid>

            <div className="mt-3 flex items-center justify-between border-t border-line pt-3">
                <span className="text-[11px] text-ink-subtle">
                    {t('nurse')}: {vital.recorded_by?.name || t('staff')}
                </span>
                <Badge tone={vital.is_abnormal ? 'critical' : 'accent'} dot>
                    {t('vitalSigns')}
                </Badge>
            </div>
        </Card>
    );
}
