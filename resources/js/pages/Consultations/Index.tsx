import {
    ClockIcon,
    ClipboardDocumentListIcon,
    HeartIcon,
    PhoneIcon,
    UserIcon,
    CakeIcon,
    ArrowsRightLeftIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { format, isToday, isYesterday, parseISO, isValid } from 'date-fns';
import { useMemo, useState } from 'react';
import {
    Alert,
    Avatar,
    Badge,
    Button,
    Dialog,
    EmptyState,
    PageHeader,
    Tabs,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { MedicalRecordCard, VitalCard } from './components/PatientHistoryCards';
import type {
    MedicalRecordView,
    VitalView,
} from './components/PatientHistoryCards';

interface Patient {
    id: number;
    patient_uid: string;
    first_name: string;
    last_name: string;
    email: string | null;
    phone: string | null;
    age: number;
    photo_url: string | null;
    gender: 'male' | 'female' | 'other';
    address: string | null;
    last_visit_date: string | null;
    is_admitted_approve: boolean;
    medical_records?: MedicalRecord[];
    vitals?: Vital[];
}

interface MedicalRecord {
    id: number;
    created_at: string;
    diagnosis?: string;
    chief_complaint?: string;
    treatment_plan?: string;
    doctor?: { name?: string };
    attachments?: { id: number; name?: string; url?: string }[];
}

interface Vital {
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

interface Props {
    patient: Patient;
}

const groupDataByDate = <T extends Record<string, any>>(
    items: T[],
    dateField: keyof T,
) => {
    const groups: Record<string, T[]> = {
        today: [],
        yesterday: [],
        older: [],
    };

    items?.forEach((item) => {
        const dateValue = item[dateField];

        if (!dateValue) {
            return;
        }

        const date = parseISO(dateValue as string);

        if (!isValid(date)) {
            return;
        }

        if (isToday(date)) {
            groups.today.push(item);
        } else if (isYesterday(date)) {
            groups.yesterday.push(item);
        } else {
            groups.older.push(item);
        }
    });

    return groups;
};

/**
 * Consultations/Index — the patient clinical history: identity header, the
 * history/vitals tabs and the admission request flow.
 */
const PatientProfilePage = ({ patient }: Props) => {
    const { t } = useTranslation();
    const [activeTab, setActiveTab] = useState<'history' | 'vitals'>('history');
    const [showAdmissionDialog, setShowAdmissionDialog] = useState(false);
    const { post, processing } = useForm({});

    const historyBuckets = useMemo(
        () => groupDataByDate(patient.medical_records ?? [], 'created_at'),
        [patient.medical_records],
    );

    const vitalsBuckets = useMemo(
        () => groupDataByDate(patient.vitals ?? [], 'recorded_at'),
        [patient.vitals],
    );

    const hasHistory = Object.values(historyBuckets).some(
        (bucket) => bucket.length > 0,
    );
    const hasVitals = Object.values(vitalsBuckets).some(
        (bucket) => bucket.length > 0,
    );

    const requestAdmission = () => {
        post(route('request-admission', patient.id), {
            onSuccess: () => setShowAdmissionDialog(false),
        });
    };

    return (
        <div className="mx-auto max-w-7xl space-y-5">
            <Head
                title={`${patient.first_name} ${patient.last_name} – ${t('profile')}`}
            />

            <PageHeader
                title={`${patient.first_name} ${patient.last_name}`}
                description={patient.patient_uid}
                icon={UserIcon}
                breadcrumbs={[
                    { label: t('home'), href: '/' },
                    { label: t('patients'), href: route('patients.index') },
                    { label: `${patient.first_name} ${patient.last_name}` },
                ]}
                meta={
                    <>
                        <span className="flex items-center gap-1.5">
                            <CakeIcon className="h-3.5 w-3.5" />
                            {patient.age
                                ? `${patient.age} ${t('yrsOld')}`
                                : t('ageNa')}
                        </span>
                        <span className="flex items-center gap-1.5">
                            <UserIcon className="h-3.5 w-3.5" />
                            {patient.gender}
                        </span>
                        <span className="flex items-center gap-1.5">
                            <PhoneIcon className="h-3.5 w-3.5" />
                            {patient.phone || t('noPhone')}
                        </span>
                        {patient.last_visit_date && (
                            <span className="flex items-center gap-1.5">
                                <ClockIcon className="h-3.5 w-3.5" />
                                {t('lastVisit')}:{' '}
                                {format(
                                    parseISO(patient.last_visit_date),
                                    'MMM dd, yyyy',
                                )}
                            </span>
                        )}
                    </>
                }
                actions={
                    patient.is_admitted_approve ? (
                        <Badge tone="positive" dot>
                            {t('admission_pending')}
                        </Badge>
                    ) : (
                        <Button
                            icon={ArrowsRightLeftIcon}
                            loading={processing}
                            onClick={() => setShowAdmissionDialog(true)}
                        >
                            {t('requestAdmission')}
                        </Button>
                    )
                }
            />

            <div className="flex flex-wrap items-center gap-4">
                <Avatar
                    size="lg"
                    name={`${patient.first_name} ${patient.last_name}`}
                    src={patient.photo_url}
                />
                <Tabs
                    value={activeTab}
                    onChange={(value) =>
                        setActiveTab(value as 'history' | 'vitals')
                    }
                    items={[
                        {
                            value: 'history',
                            label: t('consultationHistory'),
                            icon: ClipboardDocumentListIcon,
                        },
                        {
                            value: 'vitals',
                            label: t('vitalSigns'),
                            icon: HeartIcon,
                        },
                    ]}
                />
            </div>

            {activeTab === 'history' &&
                (!hasHistory ? (
                    <EmptyState
                        icon={ClipboardDocumentListIcon}
                        title={t('page.patient_profile.noMedicalHistory')}
                        description={t(
                            'page.patient_profile.noMedicalHistoryDescription',
                        )}
                    />
                ) : (
                    <div className="space-y-8">
                        {Object.entries(historyBuckets).map(
                            ([bucket, records]) =>
                                records.length > 0 && (
                                    <section key={bucket}>
                                        <h2 className="mb-3 text-[11px] font-semibold tracking-[0.14em] text-ink-subtle uppercase">
                                            {t(bucket)}
                                        </h2>

                                        <div className="space-y-4">
                                            {records.map(
                                                (record: MedicalRecordView) => (
                                                    <MedicalRecordCard
                                                        key={record.id}
                                                        record={record}
                                                        t={t}
                                                    />
                                                ),
                                            )}
                                        </div>
                                    </section>
                                ),
                        )}
                    </div>
                ))}

            {activeTab === 'vitals' &&
                (!hasVitals ? (
                    <EmptyState
                        icon={HeartIcon}
                        title={t('page.patient_profile.noVitalSigns')}
                        description={t(
                            'page.patient_profile.noVitalSignsDescription',
                        )}
                    />
                ) : (
                    <div className="space-y-8">
                        {Object.entries(vitalsBuckets).map(
                            ([bucket, vitals]) =>
                                vitals.length > 0 && (
                                    <section key={bucket}>
                                        <h2 className="mb-3 text-[11px] font-semibold tracking-[0.14em] text-ink-subtle uppercase">
                                            {t(bucket)}
                                        </h2>

                                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                                            {vitals.map((vital: VitalView) => (
                                                <VitalCard
                                                    key={vital.id}
                                                    vital={vital}
                                                    t={t}
                                                />
                                            ))}
                                        </div>
                                    </section>
                                ),
                        )}
                    </div>
                ))}

            <Dialog
                open={showAdmissionDialog}
                onClose={() => setShowAdmissionDialog(false)}
                title={t('page.patient_profile.confirmAdmissionTitle')}
                description={t('page.patient_profile.confirmAdmissionMessage', {
                    name: `${patient.first_name} ${patient.last_name}`,
                })}
                size="sm"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setShowAdmissionDialog(false)}
                        >
                            {t('cancel')}
                        </Button>
                        <Button loading={processing} onClick={requestAdmission}>
                            {t('confirm')}
                        </Button>
                    </>
                }
            >
                <Alert tone="warning">
                    {t('page.patient_profile.confirmAdmissionMessage', {
                        name: `${patient.first_name} ${patient.last_name}`,
                    })}
                </Alert>
            </Dialog>
        </div>
    );
};

// Persistent layout.
PatientProfilePage.layout = (page: React.ReactNode) => (
    <AppLayout
        headerTitle="Patient Profile"
        headerSubtitle="Medical History & Vitals"
    >
        {page}
    </AppLayout>
);

export default PatientProfilePage;
