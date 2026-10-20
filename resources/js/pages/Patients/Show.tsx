import {
    ArrowLeftIcon,
    BanknotesIcon,
    CalendarDaysIcon,
    CheckBadgeIcon,
    ExclamationTriangleIcon,
    MapPinIcon,
    PencilSquareIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import {
    Alert,
    Badge,
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    Dialog,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { AdmissionStatusBadge } from '@/pages/Patients/components/AdmissionStatusBadge';
import { PatientIdentity } from '@/pages/Patients/components/PatientIdentity';

interface Admission {
    id: number;
    status: 'Admitted' | 'Discharged' | 'Pending';
    admission_date: string;
    discharge_date: string | null;
    reason_for_admission: string;
    observation_fee: number;
    patient: {
        id: number;
        first_name: string;
        last_name: string;
        patient_uid: string;
        age: number;
        gender: string;
    };
    bed: { code: string; ward: any; bed_type: any };
    attending_doctor: { name: string };
}

export default function AdmissionShow({ admission }: { admission: Admission }) {
    const { t } = useTranslation();
    const [confirmDischarge, setConfirmDischarge] = useState(false);
    const { post, processing } = useForm({});

    const handleDischarge = () => {
        post(route('admissions.discharge', admission.id), {
            onSuccess: () => setConfirmDischarge(false),
        });
    };

    return (
        <AppLayout
            headerTitle={t('page.show.title')}
            headerSubtitle={t('page.show.subtitle', {
                firstName: admission.patient.first_name,
                lastName: admission.patient.last_name,
            })}
        >
            <Head
                title={t('page.show.head_title', {
                    firstName: admission.patient.first_name,
                })}
            />

            <div className="space-y-6 pb-12">
                <PageHeader
                    eyebrow={t('page.show.admissions_registry')}
                    title={t('page.show.title')}
                    description={t('page.show.subtitle', {
                        firstName: admission.patient.first_name,
                        lastName: admission.patient.last_name,
                    })}
                    breadcrumbs={[
                        {
                            label: t('page.show.admissions_registry'),
                            href: route('admissions.index'),
                        },
                        { label: t('page.show.dossier', { id: admission.id }) },
                    ]}
                    meta={<AdmissionStatusBadge status={admission.status} />}
                    actions={
                        <Button
                            href={route('admissions.index')}
                            variant="secondary"
                            icon={ArrowLeftIcon}
                        >
                            {t('page.show.return_to_registry')}
                        </Button>
                    }
                />

                <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    {/* LEFT COLUMN: patient & clinical info */}
                    <div className="space-y-6 lg:col-span-2">
                        <SectionCard
                            title={t('page.show.dossier', { id: admission.id })}
                            description={t('page.show.admission_date')}
                            contentClassName="space-y-6"
                        >
                            <PatientIdentity
                                firstName={admission.patient.first_name}
                                lastName={admission.patient.last_name}
                                patientUid={admission.patient.patient_uid}
                                demographics={`${admission.patient.age} Y · ${admission.patient.gender}`}
                                size="lg"
                            />

                            <div>
                                <p className="flex items-center gap-1.5 text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                    <PencilSquareIcon className="h-3.5 w-3.5" />
                                    {t('page.show.reason_for_admission')}
                                </p>
                                <blockquote className="mt-2 rounded-lg border border-line bg-surface-muted px-4 py-3 text-[13px] leading-relaxed text-ink-muted italic">
                                    &ldquo;{admission.reason_for_admission}
                                    &rdquo;
                                </blockquote>
                            </div>

                            <KeyValueGrid columns={2}>
                                <KeyValue
                                    label={t('page.show.admission_date')}
                                    value={admission.admission_date}
                                    icon={CalendarDaysIcon}
                                />
                                <KeyValue
                                    label={t('page.show.attending_physician')}
                                    value={`Dr. ${admission.attending_doctor.name}`}
                                    icon={UserGroupIcon}
                                />
                                {admission.discharge_date && (
                                    <KeyValue
                                        label={t('page.show.discharge_date')}
                                        value={admission.discharge_date}
                                        icon={CheckBadgeIcon}
                                    />
                                )}
                            </KeyValueGrid>
                        </SectionCard>
                    </div>

                    {/* RIGHT COLUMN: location, finance, actions */}
                    <div className="space-y-6">
                        <SectionCard
                            title={t('page.show.ward_location')}
                            icon={MapPinIcon}
                        >
                            <KeyValueGrid columns={1}>
                                <KeyValue
                                    label={t('page.show.ward')}
                                    value={admission?.bed?.ward?.name}
                                />
                                <KeyValue
                                    label={t('page.show.bed_code')}
                                    value={admission?.bed?.code}
                                    mono
                                />
                                <KeyValue
                                    label={t('page.show.bed_type')}
                                    value={admission?.bed?.bed_type?.name}
                                />
                            </KeyValueGrid>
                        </SectionCard>

                        <Card>
                            <CardHeader>
                                <CardTitle>
                                    {t('page.show.deposit_fee')}
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="flex items-baseline gap-1.5">
                                    <span className="text-[26px] leading-none font-semibold tracking-tight text-ink tabular-nums">
                                        {new Intl.NumberFormat('fr-FR').format(
                                            admission.observation_fee,
                                        )}
                                    </span>
                                    <span className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                        FCFA
                                    </span>
                                </div>
                                <p className="mt-2 flex items-center gap-1.5 text-[11px] font-medium text-ink-subtle">
                                    <BanknotesIcon className="h-3.5 w-3.5" />
                                    {t('page.show.recorded_at_intake')}
                                </p>
                            </CardContent>
                        </Card>

                        <div className="space-y-2">
                            {admission.status === 'Admitted' ? (
                                <Button
                                    variant="danger"
                                    icon={CheckBadgeIcon}
                                    fullWidth
                                    onClick={() => setConfirmDischarge(true)}
                                >
                                    {t('page.show.initiate_discharge')}
                                </Button>
                            ) : (
                                <Alert tone="info">
                                    {t('page.show.admission_closed')}
                                </Alert>
                            )}

                            <Button
                                href={route('admissions.index')}
                                variant="secondary"
                                icon={ArrowLeftIcon}
                                fullWidth
                            >
                                {t('page.show.return_to_registry')}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <Dialog
                open={confirmDischarge}
                onClose={() => setConfirmDischarge(false)}
                size="sm"
                title={t('page.show.confirm_discharge_title')}
                description={t('page.show.confirm_discharge_description')}
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setConfirmDischarge(false)}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button
                            variant="danger"
                            loading={processing}
                            onClick={handleDischarge}
                        >
                            {t('page.show.confirm_discharge_button')}
                        </Button>
                    </>
                }
            >
                <div className="flex items-start gap-3">
                    <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-critical/25 bg-critical-soft text-critical-ink">
                        <ExclamationTriangleIcon className="h-5 w-5" />
                    </span>
                    <div className="min-w-0">
                        <PatientIdentity
                            firstName={admission.patient.first_name}
                            lastName={admission.patient.last_name}
                            patientUid={admission.patient.patient_uid}
                            size="sm"
                        />
                        <p className="mt-2 text-[12.5px] leading-relaxed text-ink-muted">
                            {t('page.show.ward')}:{' '}
                            <Badge tone="neutral" variant="outline">
                                {admission?.bed?.code ?? '—'}
                            </Badge>
                        </p>
                    </div>
                </div>
            </Dialog>
        </AppLayout>
    );
}
