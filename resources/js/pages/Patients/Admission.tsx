import {
    ArrowLeftIcon,
    CheckIcon,
    ClockIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React from 'react';
import {
    Alert,
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { AdmissionFormFields } from '@/pages/Patients/components/AdmissionFormFields';
import type {
    AdmissionBed,
    AdmissionDoctor,
} from '@/pages/Patients/components/AdmissionFormFields';
import { FormActionBar } from '@/pages/Patients/components/FormActionBar';
import { PatientIdentity } from '@/pages/Patients/components/PatientIdentity';

interface Bed {
    id: number;
    code: string;
    ward?: { name: string };
    bed_type?: { name: string };
}

interface Doctor {
    id: number;
    name: string;
}

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    age: number;
    gender: string;
}

interface Props {
    patient: Patient;
    availableBeds: Bed[];
    doctors: Doctor[];
    admission_id: number;
}

export default function PatientAdmission({
    patient,
    availableBeds,
    doctors,
    admission_id,
}: Props) {
    const { t } = useTranslation();
    const { data, setData, put, processing, errors } = useForm({
        patient_id: patient.id,
        bed_id: '',
        attending_doctor_id: '',
        admission_date: new Date().toISOString().split('T')[0],
        reason_for_admission: '',
        observation_fee: 5000, // Default deposit for Cameroon clinics
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('admissions.update', admission_id));
    };

    return (
        <AppLayout
            headerTitle={t('page.admission.title')}
            headerSubtitle={t('page.admission.subtitle', {
                firstName: patient.first_name,
                lastName: patient.last_name,
            })}
        >
            <Head title={t('page.admission.head_title')} />

            <div className="space-y-6 pb-12">
                <PageHeader
                    eyebrow={t('page.admission.new_entry')}
                    title={t('page.admission.title')}
                    description={t('page.admission.subtitle', {
                        firstName: patient.first_name,
                        lastName: patient.last_name,
                    })}
                    breadcrumbs={[
                        {
                            label: t('page.admission.breadcrumb_admissions'),
                            href: route('admissions.index'),
                        },
                        { label: t('page.admission.new_entry') },
                    ]}
                />

                <Card>
                    <CardHeader
                        actions={
                            <span className="flex items-center gap-2 rounded-md border border-warning/30 bg-warning-soft px-2.5 py-1 text-[11px] font-semibold tracking-[0.08em] text-warning-ink uppercase">
                                <ClockIcon className="h-3.5 w-3.5" />
                                {t('page.admission.awaiting_bed')}
                            </span>
                        }
                    >
                        <CardTitle className="text-[11px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                            {t('page.admission.current_status')}
                        </CardTitle>
                        <div className="mt-2.5">
                            <PatientIdentity
                                firstName={patient.first_name}
                                lastName={patient.last_name}
                                patientUid={patient.patient_uid}
                                demographics={`${patient.age} ${t('page.admission.yrs')} · ${patient.gender}`}
                                size="lg"
                            />
                        </div>
                    </CardHeader>
                    <CardContent className="py-3">
                        <p className="text-[12px] text-ink-subtle">
                            {t('page.admission.id')}: {patient.patient_uid}
                        </p>
                    </CardContent>
                </Card>

                {Object.keys(errors).length > 0 && (
                    <Alert tone="critical" title={t('error')}>
                        {t('someFieldsRequireAttention')}
                    </Alert>
                )}

                <form onSubmit={handleSubmit} className="space-y-6">
                    <SectionCard
                        title={t('page.admission.new_entry')}
                        description={t('page.admission.subtitle', {
                            firstName: patient.first_name,
                            lastName: patient.last_name,
                        })}
                        contentClassName="px-0 pb-0 sm:px-0"
                    >
                        <div className="px-5 sm:px-6">
                            <AdmissionFormFields
                                data={data}
                                errors={errors}
                                availableBeds={availableBeds as AdmissionBed[]}
                                doctors={doctors as AdmissionDoctor[]}
                                setData={setData as never}
                            />
                        </div>

                        <FormActionBar>
                            <Button
                                href={route('patients.show', patient.id)}
                                variant="secondary"
                                icon={ArrowLeftIcon}
                                className="w-full sm:w-auto"
                            >
                                {t('common.cancel')}
                            </Button>
                            <Button
                                type="submit"
                                variant="primary"
                                icon={CheckIcon}
                                loading={processing}
                                className="w-full sm:w-auto"
                            >
                                {t('page.admission.confirm_admission')}
                            </Button>
                        </FormActionBar>
                    </SectionCard>
                </form>
            </div>
        </AppLayout>
    );
}
