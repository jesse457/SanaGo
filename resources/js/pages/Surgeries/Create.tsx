import { Head, useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { Alert, PageHeader } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { ClinicalTeamSection } from './components/ClinicalTeamSection';
import { PatientLookup } from './components/PatientLookup';
import { ProcedureSection } from './components/ProcedureSection';
import {
    NotesSection,
    SchedulingSection,
    StatusSection,
} from './components/SchedulingSections';
import { SurgeryFormActions } from './components/SurgeryFormActions';
import type { Bed, Doctor, Patient, Surgery } from './components/types';

interface Props {
    surgery?: Surgery;
    patients: Patient[];
    doctors: Doctor[];
    beds: Bed[];
}

/**
 * Surgeries/Create — the schedule-a-surgery workspace. The page owns the form
 * state and the composition; every clinical block is a component under
 * `components/`.
 */
export default function SurgeryForm({
    surgery,
    patients,
    doctors,
    beds = [],
}: Props) {
    const { t } = useTranslation();
    const isEdit = !!surgery;

    // Patient, surgeon, anesthetist and bed lookups each keep their own query.
    const [patientQuery, setPatientQuery] = useState('');
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(
        surgery?.patient ?? null,
    );
    const [surgeonQuery, setSurgeonQuery] = useState('');
    const [isExternalSurgeon, setIsExternalSurgeon] = useState(false);
    const [anesthetistQuery, setAnesthetistQuery] = useState('');
    const [isExternalAnesthetist, setIsExternalAnesthetist] = useState(false);
    const [bedQuery, setBedQuery] = useState('');

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        patient_id: surgery?.patient_id?.toString() || '',
        surgeon_id: surgery?.surgeon_id?.toString() || '',
        surgeon_name: surgery?.surgeon_name || '',
        anesthetist_id: surgery?.anesthetist_id?.toString() || '',
        anesthetist_name: surgery?.anesthetist_name || '',
        bed_id: surgery?.bed_id?.toString() || '',
        surgery_name: surgery?.surgery_name || '',
        description: surgery?.description || '',
        scheduled_at: surgery?.scheduled_at
            ? surgery.scheduled_at.slice(0, 16)
            : new Date().toISOString().slice(0, 16),
        completed_at: surgery?.completed_at || '',
        estimated_duration_minutes:
            surgery?.estimated_duration_minutes?.toString() || '',
        urgency: surgery?.urgency || 'normal',
        status: surgery?.status || 'scheduled',
        surgery_fee: surgery?.surgery_fee?.toString() || '',
        notes: surgery?.notes || '',
        complications: surgery?.complications || '',
    });

    // Hydrate the clinician lookups from an existing surgery record. These run
    // once per mount against the server payload (edit mode), so mirroring it
    // into local query state is an external-event response, not derived state.
    /* eslint-disable react-hooks/set-state-in-effect */
    useEffect(() => {
        if (surgery) {
            if (surgery.surgeon_name && !surgery.surgeon_id) {
                setIsExternalSurgeon(true);
                setSurgeonQuery(surgery.surgeon_name);
            } else if (surgery.surgeon) {
                setSurgeonQuery(surgery.surgeon.name);
            }

            if (surgery.anesthetist_name && !surgery.anesthetist_id) {
                setIsExternalAnesthetist(true);
                setAnesthetistQuery(surgery.anesthetist_name);
            } else if (surgery.anesthetist) {
                setAnesthetistQuery(surgery.anesthetist.name);
            }
        }
    }, [surgery]);

    // Hydrate the patient label and the recovery-bed label for edit mode.
    useEffect(() => {
        if (surgery?.patient) {
            setPatientQuery(
                `${surgery.patient.first_name} ${surgery.patient.last_name} (${surgery.patient.patient_uid})`,
            );
        }
    }, [surgery]);

    useEffect(() => {
        if (surgery?.bed_id && beds.length > 0) {
            const currentBed = beds.find((bed) => bed.id === surgery.bed_id);

            if (currentBed) {
                setBedQuery(
                    `${currentBed.code} - ${currentBed.ward_name} (${currentBed.type_name})`,
                );
            } else {
                setBedQuery(`Bed #${surgery.bed_id}`);
            }
        }
    }, [surgery, beds]);

    const selectPatient = (patient: Patient) => {
        setSelectedPatient(patient);
        setData('patient_id', String(patient.id));
        setPatientQuery(
            `${patient.first_name} ${patient.last_name} (${patient.patient_uid})`,
        );
    };

    const selectSurgeon = (doctor: Doctor) => {
        setData('surgeon_id', String(doctor.id));
        setData('surgeon_name', '');
        setIsExternalSurgeon(false);
        setSurgeonQuery(doctor.name);
    };

    const toggleExternalSurgeon = () => {
        const next = !isExternalSurgeon;
        setIsExternalSurgeon(next);

        if (next) {
            setData('surgeon_id', '');
            setSurgeonQuery('');
        } else {
            setData('surgeon_name', '');
        }
    };

    const selectAnesthetist = (doctor: Doctor) => {
        setData('anesthetist_id', String(doctor.id));
        setData('anesthetist_name', '');
        setIsExternalAnesthetist(false);
        setAnesthetistQuery(doctor.name);
    };

    const toggleExternalAnesthetist = () => {
        const next = !isExternalAnesthetist;
        setIsExternalAnesthetist(next);

        if (next) {
            setData('anesthetist_id', '');
            setAnesthetistQuery('');
        } else {
            setData('anesthetist_name', '');
        }
    };

    const selectBed = (bed: Bed) => {
        setData('bed_id', String(bed.id));
        setBedQuery(`${bed.code} - ${bed.ward_name} (${bed.type_name})`);
    };

    const handleSubmit = (event: React.FormEvent) => {
        event.preventDefault();
        setErrorMessage(null);

        if (isEdit && surgery) {
            put(route('surgeries.update', surgery.id), {
                onSuccess: () => {
                    setSuccessMessage(t('surgeries.create.success_message'));
                },
                onError: () => {
                    setErrorMessage(t('surgeries.create.error_message'));
                },
            });

            return;
        }

        post(route('surgeries.store'), {
            onSuccess: () => {
                setSuccessMessage(t('surgeries.create.success_message'));
                reset();
                setSelectedPatient(null);
                setPatientQuery('');
                setSurgeonQuery('');
                setAnesthetistQuery('');
                setBedQuery('');
                setIsExternalSurgeon(false);
                setIsExternalAnesthetist(false);
            },
            onError: () => {
                setErrorMessage(t('surgeries.create.error_message'));
            },
        });
    };

    const statusOptions = [
        { value: 'scheduled', label: t('surgeries.create.status_scheduled') },
        {
            value: 'in_progress',
            label: t('surgeries.create.status_in_progress'),
        },
        { value: 'completed', label: t('surgeries.create.status_completed') },
        { value: 'cancelled', label: t('surgeries.create.status_cancelled') },
    ];

    return (
        <AppLayout
            headerTitle={
                isEdit
                    ? t('surgeries.create.edit_title')
                    : t('surgeries.create.title')
            }
            headerSubtitle={
                isEdit
                    ? `${t('surgeries.create.edit_subtitle')}: ${surgery?.surgery_name}`
                    : t('surgeries.create.subtitle')
            }
        >
            <Head
                title={
                    isEdit
                        ? t('surgeries.create.edit_head_title')
                        : t('surgeries.create.head_title')
                }
            />

            <div className="mx-auto max-w-5xl space-y-5">
                <PageHeader
                    title={
                        isEdit
                            ? t('surgeries.create.edit_title')
                            : t('surgeries.create.title')
                    }
                    description={
                        isEdit
                            ? `${t('surgeries.create.edit_subtitle')}: ${surgery?.surgery_name}`
                            : t('surgeries.create.subtitle')
                    }
                    breadcrumbs={[
                        { label: t('common.home'), href: route('home') },
                        {
                            label: t('surgeries.create.surgeries'),
                            href: route('surgeries.index'),
                        },
                        {
                            label: isEdit
                                ? t('surgeries.create.edit_surgery')
                                : t('surgeries.create.schedule_surgery'),
                        },
                    ]}
                />

                {successMessage && (
                    <Alert
                        tone="positive"
                        title={t('surgeries.create.success')}
                    >
                        {successMessage}
                    </Alert>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <Alert tone="critical" title={t('surgeries.create.error')}>
                        {errorMessage || t('surgeries.create.error_attention')}
                    </Alert>
                )}

                <form onSubmit={handleSubmit} className="space-y-4">
                    <ProcedureSection
                        surgeryName={data.surgery_name}
                        description={data.description}
                        errors={errors}
                        setData={setData}
                        t={t}
                    />

                    <ClinicalTeamSection
                        doctors={doctors}
                        beds={beds}
                        isExternalSurgeon={isExternalSurgeon}
                        isExternalAnesthetist={isExternalAnesthetist}
                        surgeonQuery={surgeonQuery}
                        anesthetistQuery={anesthetistQuery}
                        surgeonName={data.surgeon_name}
                        anesthetistName={data.anesthetist_name}
                        bedQuery={bedQuery}
                        surgeonId={data.surgeon_id}
                        anesthetistId={data.anesthetist_id}
                        errors={errors}
                        patientField={
                            <PatientLookup
                                patients={patients}
                                query={patientQuery}
                                selectedPatient={selectedPatient}
                                onQueryChange={setPatientQuery}
                                onSelect={selectPatient}
                                error={errors.patient_id}
                                t={t}
                            />
                        }
                        onSurgeonQueryChange={setSurgeonQuery}
                        onSurgeonSelect={selectSurgeon}
                        onToggleExternalSurgeon={toggleExternalSurgeon}
                        onAnesthetistQueryChange={setAnesthetistQuery}
                        onAnesthetistSelect={selectAnesthetist}
                        onToggleExternalAnesthetist={toggleExternalAnesthetist}
                        onBedQueryChange={setBedQuery}
                        onBedSelect={selectBed}
                        setData={setData}
                        t={t}
                    />

                    <SchedulingSection
                        scheduledAt={data.scheduled_at}
                        estimatedDurationMinutes={
                            data.estimated_duration_minutes
                        }
                        urgency={data.urgency}
                        surgeryFee={data.surgery_fee}
                        errors={errors}
                        setData={setData}
                        t={t}
                    />

                    <StatusSection
                        status={data.status}
                        completedAt={data.completed_at}
                        scheduledAt={data.scheduled_at}
                        complications={data.complications}
                        statusOptions={statusOptions}
                        errors={errors}
                        setData={setData}
                        t={t}
                    />

                    <NotesSection
                        notes={data.notes}
                        error={errors.notes}
                        setData={setData}
                        t={t}
                    />

                    <SurgeryFormActions
                        isEdit={isEdit}
                        processing={processing}
                        t={t}
                    />
                </form>
            </div>
        </AppLayout>
    );
}
