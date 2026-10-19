import {
    CalendarDaysIcon,
    ClipboardDocumentListIcon,
    BeakerIcon,
    DocumentTextIcon,
    PaperClipIcon,
    UserIcon,
    ArrowLeftIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import {
    Alert,
    Avatar,
    Badge,
    Button,
    Card,
    EmptyState,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
    Tabs,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface PrescriptionItem {
    id: number;
    medication: { name: string };
    dosage: string;
    frequency: string;
    duration: string;
    quantity_prescribed: number;
}

interface Prescription {
    id: number;
    prescription_date: string;
    items: PrescriptionItem[];
}

interface LabResult {
    id: number;
    result_date: string;
    status: string;
    results_text: string;
    attachments?: { id: number; file_name: string }[];
}

interface Consultation {
    id: number;
    created_at: string;
    patient: { id: number; first_name: string; last_name: string };
    doctor: { name: string };
    diagnosis: string | null;
    treatment_plan: string | null;
    chief_complaint: string | null;
    physical_exam: string | null;
    notes: string | null;
    prescriptions?: Prescription[];
    lab_results?: LabResult[];
}

interface Props {
    consultation: Consultation;
}

/**
 * Consultations/Detail — the read-only consultation record: who was seen, the
 * clinical narrative, then the prescription and laboratory tabs.
 */
export default function ConsultationDetail({ consultation }: Props) {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState<'prescriptions' | 'lab'>(
        'prescriptions',
    );
    const c = consultation;
    const patientName = c.patient
        ? `${c.patient.first_name} ${c.patient.last_name}`
        : t('patient');
    const labStatusTone = (status: string) =>
        status === 'Completed' ? 'positive' : 'warning';

    return (
        <AppLayout
            headerTitle={`${t('consultation')} #${c.id}`}
            headerSubtitle={patientName}
        >
            <Head title={`${t('consultation')} #${c.id}`} />

            <div className="mx-auto max-w-5xl space-y-5">
                <PageHeader
                    title={`${t('consultation')} #${c.id}`}
                    description={patientName}
                    breadcrumbs={[
                        { label: t('home'), href: '/' },
                        {
                            label: t('consultations'),
                            href: '/consultations/create',
                        },
                        { label: `#${c.id}` },
                    ]}
                    meta={
                        <>
                            <span className="flex items-center gap-1.5">
                                <CalendarDaysIcon className="h-3.5 w-3.5" />
                                {c.created_at}
                            </span>
                            <span className="flex items-center gap-1.5">
                                <UserIcon className="h-3.5 w-3.5" />
                                {c.doctor?.name ?? t('unknown')}
                            </span>
                        </>
                    }
                    actions={
                        <Button
                            href="/patients"
                            variant="secondary"
                            icon={ArrowLeftIcon}
                        >
                            {t('backToPatientList')}
                        </Button>
                    }
                />

                <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <SectionCard icon={UserIcon} title={t('patientInfo')}>
                        {c.patient ? (
                            <div className="flex items-center gap-3">
                                <Avatar size="md" name={patientName} />
                                <div className="min-w-0">
                                    <Button
                                        href={`/patients/${c.patient.id}`}
                                        variant="ghost"
                                        className="h-auto px-0 text-[14px] font-semibold text-ink hover:bg-transparent hover:text-accent-ink"
                                    >
                                        {patientName}
                                    </Button>
                                    <p className="text-[12px] text-ink-subtle">
                                        {t('id')}: #{c.patient.id}
                                    </p>
                                </div>
                            </div>
                        ) : (
                            <p className="text-[13px] text-ink-subtle">
                                {t('na')}
                            </p>
                        )}
                    </SectionCard>

                    <SectionCard
                        icon={CalendarDaysIcon}
                        title={t('visitDetails')}
                    >
                        <KeyValueGrid columns={2}>
                            <KeyValue label={t('date')} value={c.created_at} />
                            <KeyValue
                                label={t('doctor')}
                                value={c.doctor?.name ?? t('unknown')}
                            />
                        </KeyValueGrid>
                    </SectionCard>
                </div>

                <SectionCard icon={DocumentTextIcon} title={t('clinicalNotes')}>
                    <KeyValueGrid columns={2} tile>
                        <KeyValue
                            label={t('chiefComplaint')}
                            value={c.chief_complaint || t('na')}
                        />
                        <KeyValue
                            label={t('diagnosis')}
                            value={c.diagnosis || t('na')}
                        />
                        <KeyValue
                            label={t('physicalExam')}
                            value={c.physical_exam || t('na')}
                        />
                        <KeyValue
                            label={t('treatmentPlan')}
                            value={c.treatment_plan || t('na')}
                        />
                    </KeyValueGrid>

                    {c.notes && (
                        <Alert
                            tone="warning"
                            title={t('notes')}
                            className="mt-4"
                        >
                            {c.notes}
                        </Alert>
                    )}
                </SectionCard>

                <Card>
                    <div className="border-b border-line px-4 pt-3">
                        <Tabs
                            value={activeTab}
                            onChange={(value) =>
                                setActiveTab(value as 'prescriptions' | 'lab')
                            }
                            items={[
                                {
                                    value: 'prescriptions',
                                    label: t('prescriptions'),
                                    icon: ClipboardDocumentListIcon,
                                    count: c.prescriptions?.length,
                                },
                                {
                                    value: 'lab',
                                    label: t('labResults'),
                                    icon: BeakerIcon,
                                    count: c.lab_results?.length,
                                },
                            ]}
                        />
                    </div>

                    <div className="p-4 sm:p-5">
                        {activeTab === 'prescriptions' &&
                            (c.prescriptions?.length ? (
                                <div className="space-y-4">
                                    {c.prescriptions.map((rx) => (
                                        <div key={rx.id}>
                                            <p className="mb-2 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                                {t('prescribed')}:{' '}
                                                {rx.prescription_date}
                                            </p>
                                            <Table bare>
                                                <TableHeader>
                                                    <TableRow>
                                                        <TableHeaderCell>
                                                            {t('medication')}
                                                        </TableHeaderCell>
                                                        <TableHeaderCell>
                                                            {t('dosage')}
                                                        </TableHeaderCell>
                                                        <TableHeaderCell>
                                                            {t('frequency')}
                                                        </TableHeaderCell>
                                                        <TableHeaderCell>
                                                            {t('duration')}
                                                        </TableHeaderCell>
                                                        <TableHeaderCell
                                                            numeric
                                                        >
                                                            {t('qty')}
                                                        </TableHeaderCell>
                                                    </TableRow>
                                                </TableHeader>
                                                <TableBody>
                                                    {rx.items?.map((item) => (
                                                        <TableRow key={item.id}>
                                                            <TableCell className="font-medium">
                                                                {
                                                                    item
                                                                        .medication
                                                                        ?.name
                                                                }
                                                            </TableCell>
                                                            <TableCell muted>
                                                                {item.dosage}
                                                            </TableCell>
                                                            <TableCell muted>
                                                                {item.frequency}
                                                            </TableCell>
                                                            <TableCell muted>
                                                                {item.duration}
                                                            </TableCell>
                                                            <TableCell numeric>
                                                                {
                                                                    item.quantity_prescribed
                                                                }
                                                            </TableCell>
                                                        </TableRow>
                                                    ))}
                                                </TableBody>
                                            </Table>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <EmptyState
                                    panel={false}
                                    icon={ClipboardDocumentListIcon}
                                    title={t('noPrescriptionsRecorded')}
                                />
                            ))}

                        {activeTab === 'lab' &&
                            (c.lab_results?.length ? (
                                <div className="space-y-4">
                                    {c.lab_results.map((lab) => (
                                        <div
                                            key={lab.id}
                                            className="rounded-lg border border-line p-4"
                                        >
                                            <div className="mb-2 flex items-center justify-between">
                                                <p className="text-[13px] font-semibold text-ink">
                                                    {t('result')} #{lab.id}
                                                </p>
                                                <Badge
                                                    tone={labStatusTone(
                                                        lab.status,
                                                    )}
                                                    dot
                                                >
                                                    {lab.status}
                                                </Badge>
                                            </div>
                                            <p className="mb-1 text-[11px] text-ink-subtle">
                                                {lab.result_date}
                                            </p>
                                            <p className="text-[13px] leading-relaxed text-ink-muted">
                                                {lab.results_text}
                                            </p>
                                            {lab.attachments &&
                                                lab.attachments.length > 0 && (
                                                    <div className="mt-3 flex flex-wrap gap-2">
                                                        {lab.attachments.map(
                                                            (attachment) => (
                                                                <Badge
                                                                    key={
                                                                        attachment.id
                                                                    }
                                                                    tone="accent"
                                                                    variant="outline"
                                                                >
                                                                    <PaperClipIcon className="h-3 w-3" />
                                                                    {
                                                                        attachment.file_name
                                                                    }
                                                                </Badge>
                                                            ),
                                                        )}
                                                    </div>
                                                )}
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <EmptyState
                                    panel={false}
                                    icon={BeakerIcon}
                                    title={t('noLabResultsRecorded')}
                                />
                            ))}
                    </div>
                </Card>
            </div>
        </AppLayout>
    );
}
