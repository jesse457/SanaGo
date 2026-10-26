import {
    CalendarDaysIcon,
    CheckIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
    HeartIcon,
    MapPinIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import {
    Button,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SectionCard,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import {
    emergencyPatientName,
    PatientSearchField,
    TriageLevelField,
} from '@/pages/Emergencies/components/PatientSearchField';
import type { EmergencyPatient } from '@/pages/Emergencies/components/PatientSearchField';
import { TriageBadge } from '@/pages/Emergencies/components/TriageBadges';

interface Props {
    patients: EmergencyPatient[];
}

export default function EmergencyCreate({ patients }: Props) {
    const { t } = useTranslation();
    const [patientQuery, setPatientQuery] = useState('');
    const [showPatientDropdown, setShowPatientDropdown] = useState(false);
    const [selectedPatient, setSelectedPatient] =
        useState<EmergencyPatient | null>(null);

    const { data, setData, post, processing, errors } = useForm({
        patient_id: '',
        attending_doctor_id: '',
        triage_nurse_id: '',
        arrival_mode: '',
        chief_complaint: '',
        primary_assessment: '',
        triage_level: 'normal',
        arrived_at: new Date().toISOString().slice(0, 16),
        emergency_fee: '',
        treatment_notes: '',
    });

    const selectPatient = (patient: EmergencyPatient) => {
        setSelectedPatient(patient);
        setData('patient_id', String(patient.id));
        setPatientQuery(
            `${emergencyPatientName(patient)} (${patient.patient_uid})`,
        );
        setShowPatientDropdown(false);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('emergencies.store'), {});
    };

    return (
        <AppLayout
            headerTitle={t('newEmergency')}
            headerSubtitle={t('registerEmergencyCase')}
        >
            <Head title={t('newEmergency')} />
            <div className="max-w-8xl mx-auto space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={HeartIcon}
                    eyebrow={t('emergencies')}
                    title={t('newEmergency')}
                    description={t('registerEmergencyCase')}
                    breadcrumbs={[
                        {
                            label: t('emergencies'),
                            href: route('emergencies.index'),
                        },
                        { label: t('newEmergency') },
                    ]}
                    meta={
                        selectedPatient ? (
                            <>
                                <span className="font-medium text-ink-muted">
                                    {emergencyPatientName(selectedPatient)}
                                </span>
                                <span
                                    aria-hidden="true"
                                    className="text-line-strong"
                                >
                                    /
                                </span>
                                <span className="font-mono tabular-nums">
                                    {selectedPatient.patient_uid}
                                </span>
                                <TriageBadge
                                    level={data.triage_level}
                                    label={t(data.triage_level)}
                                />
                            </>
                        ) : (
                            <TriageBadge
                                level={data.triage_level}
                                label={t(data.triage_level)}
                            />
                        )
                    }
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <SectionCard
                        icon={HeartIcon}
                        title={t('registerEmergencyCase')}
                    >
                        <div className="space-y-6">
                            <FieldSet columns={2}>
                                <PatientSearchField
                                    patients={patients}
                                    query={patientQuery}
                                    onQueryChange={setPatientQuery}
                                    open={showPatientDropdown}
                                    onOpenChange={setShowPatientDropdown}
                                    selected={selectedPatient}
                                    onSelect={selectPatient}
                                    patientId={data.patient_id}
                                    label={t('patient')}
                                    placeholder={t('searchPatientNameOrUid')}
                                    viewProfileLabel={t('viewProfile')}
                                    error={errors.patient_id}
                                />

                                <TriageLevelField
                                    label={t('triageLevel')}
                                    value={data.triage_level}
                                    onChange={(value) =>
                                        setData('triage_level', value)
                                    }
                                    error={errors.triage_level}
                                    options={[
                                        {
                                            value: 'normal',
                                            label: t('normal'),
                                        },
                                        {
                                            value: 'urgent',
                                            label: t('urgent'),
                                        },
                                        {
                                            value: 'emergency',
                                            label: t('emergency'),
                                        },
                                    ]}
                                />

                                <Field
                                    label={t('arrivalMode')}
                                    htmlFor="arrival_mode"
                                    error={errors.arrival_mode}
                                >
                                    <Input
                                        id="arrival_mode"
                                        name="arrival_mode"
                                        icon={MapPinIcon}
                                        value={data.arrival_mode}
                                        onChange={(e) =>
                                            setData(
                                                'arrival_mode',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('ambulanceWalkIn')}
                                        invalid={Boolean(errors.arrival_mode)}
                                    />
                                </Field>

                                <Field
                                    label={t('arrivalDateTime')}
                                    htmlFor="arrived_at"
                                    error={errors.arrived_at}
                                >
                                    <Input
                                        id="arrived_at"
                                        name="arrived_at"
                                        type="datetime-local"
                                        icon={CalendarDaysIcon}
                                        value={data.arrived_at}
                                        onChange={(e) =>
                                            setData(
                                                'arrived_at',
                                                e.target.value,
                                            )
                                        }
                                        invalid={Boolean(errors.arrived_at)}
                                    />
                                </Field>

                                <Field
                                    label={t('doctorId')}
                                    htmlFor="attending_doctor_id"
                                    optional
                                    error={errors.attending_doctor_id}
                                >
                                    <Input
                                        id="attending_doctor_id"
                                        name="attending_doctor_id"
                                        type="number"
                                        inputMode="numeric"
                                        icon={UserIcon}
                                        value={data.attending_doctor_id}
                                        onChange={(e) =>
                                            setData(
                                                'attending_doctor_id',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('doctorId')}
                                        invalid={Boolean(
                                            errors.attending_doctor_id,
                                        )}
                                    />
                                </Field>

                                <Field
                                    label={t('triageNurseId')}
                                    htmlFor="triage_nurse_id"
                                    optional
                                    error={errors.triage_nurse_id}
                                >
                                    <Input
                                        id="triage_nurse_id"
                                        name="triage_nurse_id"
                                        type="number"
                                        inputMode="numeric"
                                        icon={UserIcon}
                                        value={data.triage_nurse_id}
                                        onChange={(e) =>
                                            setData(
                                                'triage_nurse_id',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('triageNurseId')}
                                        invalid={Boolean(
                                            errors.triage_nurse_id,
                                        )}
                                    />
                                </Field>

                                <Field
                                    label={`${t('feeFcfa')} (FCFA)`}
                                    htmlFor="emergency_fee"
                                    optional
                                    error={errors.emergency_fee}
                                >
                                    <Input
                                        id="emergency_fee"
                                        name="emergency_fee"
                                        type="number"
                                        inputMode="decimal"
                                        step="0.01"
                                        min="0"
                                        icon={CurrencyDollarIcon}
                                        value={data.emergency_fee}
                                        onChange={(e) =>
                                            setData(
                                                'emergency_fee',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('feeFcfa')}
                                        className="tabular-nums"
                                        invalid={Boolean(errors.emergency_fee)}
                                    />
                                </Field>
                            </FieldSet>

                            <FieldSet columns={1}>
                                <Field
                                    label={t('chiefComplaint')}
                                    htmlFor="chief_complaint"
                                    required
                                    error={errors.chief_complaint}
                                >
                                    <Input
                                        id="chief_complaint"
                                        name="chief_complaint"
                                        icon={DocumentTextIcon}
                                        value={data.chief_complaint}
                                        onChange={(e) =>
                                            setData(
                                                'chief_complaint',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('chiefComplaint')}
                                        invalid={Boolean(
                                            errors.chief_complaint,
                                        )}
                                    />
                                </Field>

                                <Field
                                    label={t('primaryAssessment')}
                                    htmlFor="primary_assessment"
                                    optional
                                    error={errors.primary_assessment}
                                >
                                    <Textarea
                                        id="primary_assessment"
                                        name="primary_assessment"
                                        rows={3}
                                        value={data.primary_assessment}
                                        onChange={(e) =>
                                            setData(
                                                'primary_assessment',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('primaryAssessment')}
                                        invalid={Boolean(
                                            errors.primary_assessment,
                                        )}
                                    />
                                </Field>

                                <Field
                                    label={t('treatmentNotes')}
                                    htmlFor="treatment_notes"
                                    optional
                                    error={errors.treatment_notes}
                                >
                                    <Textarea
                                        id="treatment_notes"
                                        name="treatment_notes"
                                        rows={2}
                                        value={data.treatment_notes}
                                        onChange={(e) =>
                                            setData(
                                                'treatment_notes',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('treatmentNotes')}
                                        invalid={Boolean(
                                            errors.treatment_notes,
                                        )}
                                    />
                                </Field>
                            </FieldSet>
                        </div>
                    </SectionCard>

                    <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <Button
                            variant="secondary"
                            href={route('emergencies.index')}
                        >
                            {t('cancel')}
                        </Button>
                        <Button
                            type="submit"
                            icon={CheckIcon}
                            loading={processing}
                        >
                            {processing
                                ? t('registering')
                                : t('registerEmergency')}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
