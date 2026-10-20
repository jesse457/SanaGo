import {
    BanknotesIcon,
    BeakerIcon,
    CalendarDaysIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    DocumentTextIcon,
    EnvelopeIcon,
    ExclamationTriangleIcon,
    HeartIcon,
    IdentificationIcon,
    MapPinIcon,
    PencilSquareIcon,
    PhoneIcon,
    PlusCircleIcon,
    UserGroupIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import type { ReactNode } from 'react';
import {
    Badge,
    Button,
    EmptyState,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Tabs,
} from '@/Components/ui';
import type { BadgeTone, TabItem } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { AdmissionStatusBadge } from '@/pages/Patients/components/AdmissionStatusBadge';
import { PatientIdentity } from '@/pages/Patients/components/PatientIdentity';
import type { AdmissionRecord, VitalRecord } from '@/types';

/**
 * The dossier consumes the props `PatientController@show` sends:
 *
 *   patient, stats, admissions, vitals, medicalRecords, appointments,
 *   invoices, prescriptions
 *
 * Every collection is optional so the screen still renders if the controller
 * stops eager-loading a relation.
 */

interface ProfilePatient {
    id: number;
    patient_uid: string;
    first_name: string;
    last_name: string;
    email?: string | null;
    phone?: string | null;
    date_of_birth?: string | null;
    gender?: string | null;
    address?: string | null;
    allergies?: string | null;
    blood_type?: string | null;
    last_visit_date?: string | null;
    age?: number | null;
    photo_url?: string | null;
    created_at?: string | null;
    latest_admission?: AdmissionRecord | null;
}

interface ConsultationRecord {
    id: number;
    record_date: string;
    doctor_id: number;
    chief_complaint?: string | null;
    diagnosis?: string | null;
    treatment_plan?: string | null;
    doctor?: { id: number; name: string } | null;
}

interface InvoiceRecord {
    id: number;
    invoice_number: string;
    invoice_date: string;
    due_date?: string | null;
    subtotal?: string | number;
    tax_amount?: string | number;
    total_amount: string | number;
    status: string;
    paid_at?: string | null;
}

interface AppointmentRecord {
    id: number;
    appointment_date: string;
    appointment_time?: string | null;
    status: string;
    consultation_fee?: string | number | null;
    doctor?: { id: number; name: string } | null;
}

interface PrescriptionRecord {
    id: number;
    prescription_date: string;
    status: string;
    total_amount?: string | number | null;
    doctor?: { id: number; name: string } | null;
}

interface ProfileStats {
    admissions?: number;
    vitals?: number;
    consultations?: number;
    outstanding?: number;
}

interface Props {
    patient: ProfilePatient;
    stats?: ProfileStats;
    admissions?: AdmissionRecord[];
    vitals?: VitalRecord[];
    medicalRecords?: ConsultationRecord[];
    appointments?: AppointmentRecord[];
    invoices?: InvoiceRecord[];
    prescriptions?: PrescriptionRecord[];
}

const INVOICE_TONES: Record<string, BadgeTone> = {
    draft: 'muted',
    pending: 'warning',
    paid: 'positive',
    overdue: 'critical',
    cancelled: 'critical',
};

const APPOINTMENT_TONES: Record<string, BadgeTone> = {
    scheduled: 'accent',
    waiting: 'warning',
    in_consultation: 'accent',
    pending: 'warning',
    completed: 'positive',
    cancelled: 'critical',
};

const PRESCRIPTION_TONES: Record<string, BadgeTone> = {
    new: 'accent',
    partial: 'warning',
    completed: 'positive',
    cancelled: 'critical',
};

function formatDate(value?: string | null): string {
    if (!value) {
        return '—';
    }

    const parsed = new Date(value);

    if (Number.isNaN(parsed.getTime())) {
        return value;
    }

    return parsed.toLocaleDateString(undefined, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function formatDateTime(value?: string | null): string {
    if (!value) {
        return '—';
    }

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

function formatMoney(value?: string | number | null): string {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    const amount = typeof value === 'string' ? Number(value) : value;

    if (Number.isNaN(amount)) {
        return String(value);
    }

    return new Intl.NumberFormat(undefined, {
        maximumFractionDigits: 0,
    }).format(amount);
}

function humanise(value?: string | null): string {
    if (!value) {
        return '—';
    }

    return value
        .replace(/[_-]+/g, ' ')
        .replace(/\b\w/g, (character) => character.toUpperCase());
}

function SectionEmpty({
    icon,
    title,
    description,
    action,
}: {
    icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
    title: string;
    description?: string;
    action?: ReactNode;
}) {
    return (
        <EmptyState
            panel={false}
            icon={icon}
            title={title}
            description={description}
            action={action}
            className="py-10"
        />
    );
}

/**
 * Patients/Profile — the full patient dossier: identity, live admission,
 * vitals, admissions, consultations, prescriptions, appointments and billing.
 */
export default function PatientProfile({
    patient,
    stats,
    admissions = [],
    vitals = [],
    medicalRecords = [],
    appointments = [],
    invoices = [],
    prescriptions = [],
}: Props) {
    const { t } = useTranslation();
    const [tab, setTab] = useState('vitals');

    const fullName = `${patient.first_name} ${patient.last_name}`.trim();
    const currentAdmission =
        patient.latest_admission ??
        admissions.find((admission) => admission.status === 'admitted') ??
        null;

    const outstanding = useMemo(() => {
        if (typeof stats?.outstanding === 'number') {
            return stats.outstanding;
        }

        return invoices
            .filter(
                (invoice) =>
                    invoice.status !== 'paid' && invoice.status !== 'cancelled',
            )
            .reduce(
                (total, invoice) => total + Number(invoice.total_amount ?? 0),
                0,
            );
    }, [invoices, stats?.outstanding]);

    const demographics = [
        patient.age
            ? `${patient.age} ${t('page.patient_profile.years')}`
            : null,
        patient.gender ? humanise(patient.gender) : null,
    ]
        .filter(Boolean)
        .join(' · ');

    const tabs: TabItem[] = [
        {
            value: 'vitals',
            label: t('page.patient_profile.tab_vitals'),
            icon: HeartIcon,
            count: stats?.vitals ?? vitals.length,
        },
        {
            value: 'admissions',
            label: t('page.patient_profile.tab_admissions'),
            icon: ClipboardDocumentListIcon,
            count: stats?.admissions ?? admissions.length,
        },
        {
            value: 'consultations',
            label: t('page.patient_profile.tab_consultations'),
            icon: DocumentTextIcon,
            count: stats?.consultations ?? medicalRecords.length,
        },
        {
            value: 'prescriptions',
            label: t('page.patient_profile.tab_prescriptions'),
            icon: ClipboardDocumentListIcon,
            count: prescriptions.length,
        },
        {
            value: 'appointments',
            label: t('page.patient_profile.tab_appointments'),
            icon: CalendarDaysIcon,
            count: appointments.length,
        },
        {
            value: 'invoices',
            label: t('page.patient_profile.tab_invoices'),
            icon: BanknotesIcon,
            count: invoices.length,
        },
    ];

    return (
        <AppLayout
            headerTitle={t('page.patient_profile.title')}
            headerSubtitle={fullName}
        >
            <Head
                title={`${fullName} · ${t('page.patient_profile.head_title')}`}
            />

            <div className="flex flex-col gap-6 pb-12">
                <PageHeader
                    eyebrow={t('page.patient_profile.eyebrow')}
                    title={fullName}
                    description={t('page.patient_profile.subtitle', {
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
                        { label: fullName },
                    ]}
                    meta={
                        <>
                            <span className="flex items-center gap-1.5 font-mono tabular-nums">
                                <IdentificationIcon className="h-3.5 w-3.5" />
                                {patient.patient_uid}
                            </span>
                            {demographics && (
                                <span className="flex items-center gap-1.5">
                                    <UserIcon className="h-3.5 w-3.5" />
                                    {demographics}
                                </span>
                            )}
                            {patient.blood_type && (
                                <span className="flex items-center gap-1.5">
                                    <BeakerIcon className="h-3.5 w-3.5" />
                                    {t('page.patient_profile.blood_type')}:{' '}
                                    {patient.blood_type}
                                </span>
                            )}
                            <span className="flex items-center gap-1.5">
                                <PhoneIcon className="h-3.5 w-3.5" />
                                {patient.phone ?? t('common.na')}
                            </span>
                            {patient.last_visit_date && (
                                <span className="flex items-center gap-1.5">
                                    <ClockIcon className="h-3.5 w-3.5" />
                                    {t('page.patient_profile.last_visit')}:{' '}
                                    {formatDate(patient.last_visit_date)}
                                </span>
                            )}
                        </>
                    }
                    actions={
                        <>
                            <Button
                                variant="secondary"
                                icon={PencilSquareIcon}
                                href={route('patients.index')}
                                preserveScroll
                            >
                                {t('page.patient_profile.edit_patient')}
                            </Button>
                            <Button
                                variant="secondary"
                                icon={CalendarDaysIcon}
                                href={route('admissions.create', patient.id)}
                            >
                                {t('page.patient_profile.new_admission')}
                            </Button>
                            <Button
                                variant="secondary"
                                icon={DocumentTextIcon}
                                href={`/consultations/create?patient_id=${patient.id}`}
                            >
                                {t('page.patient_profile.new_consultation')}
                            </Button>
                            <Button
                                icon={HeartIcon}
                                href={route('vitals.create', patient.id)}
                            >
                                {t('page.patient_profile.new_vitals')}
                            </Button>
                        </>
                    }
                />

                <StatGrid columns={4}>
                    <StatCard
                        label={t('page.patient_profile.stat_admissions')}
                        value={stats?.admissions ?? admissions.length}
                        icon={ClipboardDocumentListIcon}
                        tone="accent"
                    />
                    <StatCard
                        label={t('page.patient_profile.stat_vitals')}
                        value={stats?.vitals ?? vitals.length}
                        icon={HeartIcon}
                        tone="positive"
                    />
                    <StatCard
                        label={t('page.patient_profile.stat_consultations')}
                        value={stats?.consultations ?? medicalRecords.length}
                        icon={DocumentTextIcon}
                        tone="neutral"
                    />
                    <StatCard
                        label={t('page.patient_profile.stat_outstanding')}
                        value={`${formatMoney(outstanding)} FCFA`}
                        icon={BanknotesIcon}
                        tone={outstanding > 0 ? 'warning' : 'positive'}
                    />
                </StatGrid>

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div className="flex flex-col gap-6 lg:col-span-2">
                        <SectionCard
                            title={t('page.patient_profile.identity')}
                            icon={IdentificationIcon}
                            actions={
                                <PatientIdentity
                                    firstName={patient.first_name}
                                    lastName={patient.last_name}
                                    patientUid={patient.patient_uid}
                                    photoUrl={patient.photo_url}
                                    size="sm"
                                />
                            }
                        >
                            <KeyValueGrid columns={2}>
                                <KeyValue
                                    label={t('page.patient_profile.full_name')}
                                    value={fullName}
                                    icon={UserIcon}
                                />
                                <KeyValue
                                    label={t(
                                        'page.patient_profile.patient_uid',
                                    )}
                                    value={patient.patient_uid}
                                    icon={IdentificationIcon}
                                    mono
                                />
                                <KeyValue
                                    label={t('page.patient_profile.age')}
                                    value={
                                        patient.age
                                            ? `${patient.age} ${t('page.patient_profile.years')}`
                                            : null
                                    }
                                />
                                <KeyValue
                                    label={t('page.patient_profile.gender')}
                                    value={humanise(patient.gender)}
                                />
                                <KeyValue
                                    label={t('page.patient_profile.blood_type')}
                                    value={patient.blood_type}
                                />
                                <KeyValue
                                    label={t(
                                        'page.patient_profile.date_of_birth',
                                    )}
                                    value={formatDate(patient.date_of_birth)}
                                    icon={CalendarDaysIcon}
                                />
                                <KeyValue
                                    label={t(
                                        'page.patient_profile.registered_on',
                                    )}
                                    value={formatDate(patient.created_at)}
                                />
                                <KeyValue
                                    label={t('page.patient_profile.last_visit')}
                                    value={formatDate(patient.last_visit_date)}
                                    icon={ClockIcon}
                                />
                            </KeyValueGrid>

                            <div className="mt-5 border-t border-line pt-5">
                                <p className="mb-3 text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                    {t('page.patient_profile.contact')}
                                </p>
                                <KeyValueGrid columns={2}>
                                    <KeyValue
                                        label={t('page.patient_profile.phone')}
                                        value={patient.phone}
                                        icon={PhoneIcon}
                                    />
                                    <KeyValue
                                        label={t('page.patient_profile.email')}
                                        value={patient.email}
                                        icon={EnvelopeIcon}
                                    />
                                    <KeyValue
                                        label={t(
                                            'page.patient_profile.address',
                                        )}
                                        value={patient.address}
                                        icon={MapPinIcon}
                                        className="sm:col-span-2"
                                    />
                                </KeyValueGrid>
                            </div>

                            <div className="mt-5 border-t border-line pt-5">
                                <p className="mb-2 flex items-center gap-1.5 text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                    <ExclamationTriangleIcon className="h-3.5 w-3.5" />
                                    {t('page.patient_profile.allergies')}
                                </p>
                                {patient.allergies ? (
                                    <p className="rounded-lg border border-warning/30 bg-warning-soft px-3.5 py-2.5 text-[13px] leading-relaxed font-medium text-warning-ink">
                                        {patient.allergies}
                                    </p>
                                ) : (
                                    <p className="text-[13px] text-ink-subtle">
                                        {t('page.patient_profile.no_allergies')}
                                    </p>
                                )}
                            </div>
                        </SectionCard>

                        <SectionCard
                            title={t('page.patient_profile.title')}
                            description={t('page.patient_profile.subtitle', {
                                name: fullName,
                            })}
                            contentClassName="px-0 py-0 sm:px-0"
                        >
                            <div className="overflow-x-auto px-5 pt-1 sm:px-6">
                                <Tabs
                                    items={tabs}
                                    value={tab}
                                    onChange={setTab}
                                />
                            </div>

                            <div className="px-5 py-4 sm:px-6">
                                {tab === 'vitals' && (
                                    <VitalsSection
                                        vitals={vitals}
                                        patientId={patient.id}
                                    />
                                )}
                                {tab === 'admissions' && (
                                    <AdmissionsSection
                                        admissions={admissions}
                                    />
                                )}
                                {tab === 'consultations' && (
                                    <ConsultationsSection
                                        records={medicalRecords}
                                    />
                                )}
                                {tab === 'prescriptions' && (
                                    <PrescriptionsSection
                                        records={prescriptions}
                                    />
                                )}
                                {tab === 'appointments' && (
                                    <AppointmentsSection
                                        records={appointments}
                                    />
                                )}
                                {tab === 'invoices' && (
                                    <InvoicesSection invoices={invoices} />
                                )}
                            </div>
                        </SectionCard>
                    </div>

                    <div className="flex flex-col gap-6">
                        <SectionCard
                            title={t('page.patient_profile.current_admission')}
                            icon={UserGroupIcon}
                            actions={
                                currentAdmission ? (
                                    <AdmissionStatusBadge
                                        status={currentAdmission.status}
                                        label={humanise(
                                            currentAdmission.status,
                                        )}
                                    />
                                ) : undefined
                            }
                        >
                            {currentAdmission ? (
                                <KeyValueGrid columns={1}>
                                    <KeyValue
                                        label={t(
                                            'page.patient_profile.admission_date',
                                        )}
                                        value={formatDate(
                                            currentAdmission.admission_date,
                                        )}
                                        icon={CalendarDaysIcon}
                                    />
                                    <KeyValue
                                        label={t(
                                            'page.patient_profile.ward_bed',
                                        )}
                                        value={
                                            currentAdmission.bed
                                                ? [
                                                      currentAdmission.bed.ward
                                                          ?.name,
                                                      currentAdmission.bed.code,
                                                  ]
                                                      .filter(Boolean)
                                                      .join(' · ')
                                                : null
                                        }
                                        icon={MapPinIcon}
                                    />
                                    <KeyValue
                                        label={t(
                                            'page.patient_profile.attending_doctor',
                                        )}
                                        value={
                                            currentAdmission.attending_doctor
                                                ?.name
                                        }
                                        icon={UserIcon}
                                    />
                                    <KeyValue
                                        label={t('page.patient_profile.reason')}
                                        value={
                                            currentAdmission.reason_for_admission
                                        }
                                        icon={PencilSquareIcon}
                                    />
                                    {currentAdmission.discharge_date && (
                                        <KeyValue
                                            label={t(
                                                'page.patient_profile.discharge_date',
                                            )}
                                            value={formatDate(
                                                currentAdmission.discharge_date,
                                            )}
                                        />
                                    )}
                                </KeyValueGrid>
                            ) : (
                                <EmptyState
                                    panel={false}
                                    icon={ClipboardDocumentListIcon}
                                    title={t(
                                        'page.patient_profile.not_currently_admitted',
                                    )}
                                    description={t(
                                        'page.patient_profile.not_currently_admitted_desc',
                                    )}
                                    className="py-6"
                                />
                            )}
                        </SectionCard>

                        <SectionCard
                            title={t('page.patient_profile.vitals_title')}
                            description={t(
                                'page.patient_profile.vitals_description',
                            )}
                            icon={HeartIcon}
                            actions={
                                <Button
                                    size="sm"
                                    variant="ghost"
                                    href={route('vitals.index', patient.id)}
                                >
                                    {t('page.patient_profile.view_vitals')}
                                </Button>
                            }
                        >
                            {vitals.length > 0 ? (
                                <KeyValueGrid columns={2}>
                                    <KeyValue
                                        label={t(
                                            'page.vitals_index.col_temperature',
                                        )}
                                        value={
                                            vitals[0].temperature
                                                ? `${vitals[0].temperature} °C`
                                                : null
                                        }
                                    />
                                    <KeyValue
                                        label={t('page.vitals_index.col_bp')}
                                        value={
                                            vitals[0].systolic_bp ||
                                            vitals[0].diastolic_bp
                                                ? `${vitals[0].systolic_bp ?? '—'}/${vitals[0].diastolic_bp ?? '—'}`
                                                : null
                                        }
                                    />
                                    <KeyValue
                                        label={t('page.vitals_index.col_pulse')}
                                        value={vitals[0].heart_rate}
                                    />
                                    <KeyValue
                                        label={t(
                                            'page.vitals_index.col_oxygen',
                                        )}
                                        value={
                                            vitals[0].oxygen_saturation
                                                ? `${vitals[0].oxygen_saturation} %`
                                                : null
                                        }
                                    />
                                    <KeyValue
                                        label={t(
                                            'page.patient_profile.recorded_at',
                                        )}
                                        value={formatDateTime(
                                            vitals[0].recorded_at,
                                        )}
                                        className="sm:col-span-2"
                                    />
                                </KeyValueGrid>
                            ) : (
                                <SectionEmpty
                                    icon={HeartIcon}
                                    title={t('page.patient_profile.no_vitals')}
                                    description={t(
                                        'page.patient_profile.no_vitals_desc',
                                    )}
                                    action={
                                        <Button
                                            size="sm"
                                            icon={PlusCircleIcon}
                                            href={route(
                                                'vitals.create',
                                                patient.id,
                                            )}
                                        >
                                            {t(
                                                'page.patient_profile.new_vitals',
                                            )}
                                        </Button>
                                    }
                                />
                            )}
                        </SectionCard>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

function VitalsSection({
    vitals,
    patientId,
}: {
    vitals: VitalRecord[];
    patientId: number;
}) {
    const { t } = useTranslation();

    if (vitals.length === 0) {
        return (
            <SectionEmpty
                icon={HeartIcon}
                title={t('page.patient_profile.no_vitals')}
                description={t('page.patient_profile.no_vitals_desc')}
                action={
                    <Button
                        size="sm"
                        icon={PlusCircleIcon}
                        href={route('vitals.create', patientId)}
                    >
                        {t('page.patient_profile.new_vitals')}
                    </Button>
                }
            />
        );
    }

    return (
        <Table bare scroll>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>
                        {t('page.patient_profile.recorded_at')}
                    </TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('page.vitals_index.col_temperature')}
                    </TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('page.vitals_index.col_bp')}
                    </TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('page.vitals_index.col_pulse')}
                    </TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('page.vitals_index.col_oxygen')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.patient_profile.recorded_by')}
                    </TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {vitals.map((vital) => (
                    <TableRow key={vital.id}>
                        <TableCell>
                            {formatDateTime(vital.recorded_at)}
                            {vital.is_abnormal && (
                                <Badge tone="critical" className="ml-2">
                                    {t('page.vitals_index.flag_abnormal')}
                                </Badge>
                            )}
                        </TableCell>
                        <TableCell numeric>
                            {vital.temperature ?? '—'}
                        </TableCell>
                        <TableCell
                            numeric
                        >{`${vital.systolic_bp ?? '—'}/${vital.diastolic_bp ?? '—'}`}</TableCell>
                        <TableCell numeric>{vital.heart_rate ?? '—'}</TableCell>
                        <TableCell numeric>
                            {vital.oxygen_saturation ?? '—'}
                        </TableCell>
                        <TableCell muted>
                            {vital.recordedBy?.name ?? '—'}
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

function AdmissionsSection({ admissions }: { admissions: AdmissionRecord[] }) {
    const { t } = useTranslation();

    if (admissions.length === 0) {
        return (
            <SectionEmpty
                icon={ClipboardDocumentListIcon}
                title={t('page.patient_profile.no_admissions')}
                description={t('page.patient_profile.no_admissions_desc')}
            />
        );
    }

    return (
        <ol className="relative flex flex-col gap-4 border-l border-line pl-5">
            {admissions.map((admission) => (
                <li key={admission.id} className="relative">
                    <span className="absolute top-2 -left-[26px] h-2.5 w-2.5 rounded-full border-2 border-surface bg-accent" />
                    <div className="flex flex-wrap items-start justify-between gap-2">
                        <div className="min-w-0">
                            <p className="text-[13px] font-semibold tracking-tight text-ink">
                                {formatDate(admission.admission_date)}
                                {admission.discharge_date &&
                                    ` → ${formatDate(admission.discharge_date)}`}
                            </p>
                            <p className="mt-0.5 text-[12px] text-ink-subtle">
                                {admission.bed
                                    ? [
                                          admission.bed.ward?.name,
                                          admission.bed.code,
                                      ]
                                          .filter(Boolean)
                                          .join(' · ')
                                    : t('common.not_assigned')}
                                {admission.attending_doctor?.name &&
                                    ` · ${admission.attending_doctor.name}`}
                            </p>
                            {admission.reason_for_admission && (
                                <p className="mt-1.5 text-[13px] leading-relaxed text-ink-muted">
                                    {admission.reason_for_admission}
                                </p>
                            )}
                        </div>
                        <AdmissionStatusBadge
                            status={admission.status}
                            label={humanise(admission.status)}
                        />
                    </div>
                </li>
            ))}
        </ol>
    );
}

function ConsultationsSection({ records }: { records: ConsultationRecord[] }) {
    const { t } = useTranslation();

    if (records.length === 0) {
        return (
            <SectionEmpty
                icon={DocumentTextIcon}
                title={t('page.patient_profile.no_consultations')}
                description={t('page.patient_profile.no_consultations_desc')}
            />
        );
    }

    return (
        <ul className="flex flex-col divide-y divide-line">
            {records.map((record) => (
                <li key={record.id} className="py-3 first:pt-0 last:pb-0">
                    <div className="flex flex-wrap items-center justify-between gap-2">
                        <p className="text-[13px] font-semibold text-ink">
                            {formatDate(record.record_date)}
                        </p>
                        <span className="text-[11.5px] text-ink-subtle">
                            {record.doctor?.name ?? '—'}
                        </span>
                    </div>
                    {record.chief_complaint && (
                        <p className="mt-1 text-[12.5px] leading-relaxed text-ink-muted">
                            <span className="font-medium text-ink-subtle">
                                {t('page.patient_profile.chief_complaint')}:
                            </span>{' '}
                            {record.chief_complaint}
                        </p>
                    )}
                    {record.diagnosis && (
                        <p className="mt-1 text-[12.5px] leading-relaxed text-ink-muted">
                            <span className="font-medium text-ink-subtle">
                                {t('page.patient_profile.diagnosis')}:
                            </span>{' '}
                            {record.diagnosis}
                        </p>
                    )}
                </li>
            ))}
        </ul>
    );
}

function PrescriptionsSection({ records }: { records: PrescriptionRecord[] }) {
    const { t } = useTranslation();

    if (records.length === 0) {
        return (
            <SectionEmpty
                icon={ClipboardDocumentListIcon}
                title={t('page.patient_profile.no_prescriptions')}
                description={t('page.patient_profile.no_prescriptions_desc')}
            />
        );
    }

    return (
        <Table bare scroll>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>
                        {t('page.patient_profile.issued')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.patient_profile.doctor')}
                    </TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('page.patient_profile.total')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.patient_profile.status')}
                    </TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {records.map((record) => (
                    <TableRow key={record.id}>
                        <TableCell>
                            {formatDate(record.prescription_date)}
                        </TableCell>
                        <TableCell muted>
                            {record.doctor?.name ?? '—'}
                        </TableCell>
                        <TableCell numeric>
                            {formatMoney(record.total_amount)}
                        </TableCell>
                        <TableCell>
                            <Badge
                                tone={
                                    PRESCRIPTION_TONES[record.status] ??
                                    'neutral'
                                }
                                dot
                            >
                                {humanise(record.status)}
                            </Badge>
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

function AppointmentsSection({ records }: { records: AppointmentRecord[] }) {
    const { t } = useTranslation();

    if (records.length === 0) {
        return (
            <SectionEmpty
                icon={CalendarDaysIcon}
                title={t('page.patient_profile.no_appointments')}
                description={t('page.patient_profile.no_appointments_desc')}
            />
        );
    }

    return (
        <Table bare scroll>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>
                        {t('page.patient_profile.date')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.patient_profile.doctor')}
                    </TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('page.patient_profile.amount')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.patient_profile.status')}
                    </TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {records.map((record) => (
                    <TableRow key={record.id}>
                        <TableCell>
                            {formatDate(record.appointment_date)}
                            {record.appointment_time && (
                                <span className="ml-1.5 text-ink-subtle tabular-nums">
                                    {record.appointment_time}
                                </span>
                            )}
                        </TableCell>
                        <TableCell muted>
                            {record.doctor?.name ?? '—'}
                        </TableCell>
                        <TableCell numeric>
                            {formatMoney(record.consultation_fee)}
                        </TableCell>
                        <TableCell>
                            <Badge
                                tone={
                                    APPOINTMENT_TONES[record.status] ??
                                    'neutral'
                                }
                                dot
                            >
                                {humanise(record.status)}
                            </Badge>
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}

function InvoicesSection({ invoices }: { invoices: InvoiceRecord[] }) {
    const { t } = useTranslation();

    if (invoices.length === 0) {
        return (
            <SectionEmpty
                icon={BanknotesIcon}
                title={t('page.patient_profile.no_invoices')}
                description={t('page.patient_profile.no_invoices_desc')}
            />
        );
    }

    return (
        <Table bare scroll>
            <TableHeader>
                <TableRow>
                    <TableHeaderCell>
                        {t('page.patient_profile.invoice_number')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.patient_profile.issued')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.patient_profile.due')}
                    </TableHeaderCell>
                    <TableHeaderCell numeric>
                        {t('page.patient_profile.total')}
                    </TableHeaderCell>
                    <TableHeaderCell>
                        {t('page.patient_profile.status')}
                    </TableHeaderCell>
                </TableRow>
            </TableHeader>
            <TableBody>
                {invoices.map((invoice) => (
                    <TableRow key={invoice.id}>
                        <TableCell className="font-mono text-[12.5px]">
                            {invoice.invoice_number}
                        </TableCell>
                        <TableCell muted>
                            {formatDate(invoice.invoice_date)}
                        </TableCell>
                        <TableCell muted>
                            {formatDate(invoice.due_date)}
                        </TableCell>
                        <TableCell numeric>
                            {formatMoney(invoice.total_amount)}
                        </TableCell>
                        <TableCell>
                            <Badge
                                tone={
                                    INVOICE_TONES[invoice.status] ?? 'neutral'
                                }
                                dot
                            >
                                {humanise(invoice.status)}
                            </Badge>
                        </TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}
