import {
    CheckIcon,
    ClipboardDocumentCheckIcon,
    HeartIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import {
    Alert,
    Button,
    Checkbox,
    Field,
    FieldSet,
    Input,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
}

export default function VitalsForm({ patient }: { patient: Patient }) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors, reset } = useForm({
        patient_id: patient.id,
        recorded_at: new Date().toISOString().slice(0, 16),
        temperature: '',
        systolic_bp: '',
        diastolic_bp: '',
        heart_rate: '',
        oxygen_saturation: '',
        respiratory_rate: '',
        is_abnormal: false,
        notes: '',
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route('vitals.store'), {
            onSuccess: () => {
                setSuccessMessage(t('page.vitals_create.recordedSuccessfully'));
                reset();
            },
            onError: () => {
                setErrorMessage(t('page.vitals_create.pleaseCheckForm'));
            },
        });
    };

    const hasErrors = Boolean(errorMessage) || Object.keys(errors).length > 0;

    return (
        <AppLayout
            headerTitle={t('page.vitals_create.title')}
            headerSubtitle={t('page.vitals_create.subtitle', {
                firstName: patient.first_name,
                lastName: patient.last_name,
            })}
        >
            <Head title={t('page.vitals_create.head_title')} />

            <div className="max-w-8xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                <PageHeader
                    icon={HeartIcon}
                    eyebrow={t('page.vitals_create.patient_records')}
                    title={t('page.vitals_create.vitals_entry')}
                    description={t('page.vitals_create.subtitle', {
                        firstName: patient.first_name,
                        lastName: patient.last_name,
                    })}
                    breadcrumbs={[
                        {
                            label: `${patient.first_name} ${patient.last_name}`,
                            href: route('patients.show', patient.id),
                        },
                        { label: t('page.vitals_create.vitals_entry') },
                    ]}
                    meta={
                        <>
                            <span className="font-mono tabular-nums">
                                {patient.patient_uid}
                            </span>
                            {data.is_abnormal && (
                                <span className="font-semibold text-critical-ink">
                                    {t('page.vitals_create.mark_abnormal')}
                                </span>
                            )}
                        </>
                    }
                />

                {successMessage && (
                    <Alert tone="positive" title={t('common.success')}>
                        {successMessage}
                    </Alert>
                )}

                {hasErrors && (
                    <Alert tone="critical" title={t('common.error')}>
                        {errorMessage || t('common.someFieldsRequireAttention')}
                    </Alert>
                )}

                <form onSubmit={handleSubmit} className="space-y-6">
                    <SectionCard
                        icon={UserCircleIcon}
                        title={t('page.vitals_create.recording_timestamp')}
                    >
                        <div className="space-y-4">
                            <KeyValueGrid columns={2}>
                                <KeyValue
                                    label={t('patient')}
                                    value={`${patient.first_name} ${patient.last_name}`}
                                />
                                <KeyValue
                                    label={t('page.vitals_create.patient_id')}
                                    value={patient.patient_uid}
                                    mono
                                />
                            </KeyValueGrid>

                            <FieldSet columns={1}>
                                <Field
                                    label={t(
                                        'page.vitals_create.recording_timestamp',
                                    )}
                                    htmlFor="recorded_at"
                                    required
                                    error={errors.recorded_at}
                                >
                                    <Input
                                        id="recorded_at"
                                        name="recorded_at"
                                        type="datetime-local"
                                        value={data.recorded_at}
                                        onChange={(e) =>
                                            setData(
                                                'recorded_at',
                                                e.target.value,
                                            )
                                        }
                                        invalid={Boolean(errors.recorded_at)}
                                    />
                                </Field>
                            </FieldSet>
                        </div>
                    </SectionCard>

                    <SectionCard
                        icon={HeartIcon}
                        title={t('page.vitals_create.vitals_biometrics')}
                    >
                        <div className="space-y-6">
                            <FieldSet columns={3}>
                                <Field
                                    label={`${t('page.vitals_create.temperature')} (°C)`}
                                    htmlFor="temperature"
                                    required
                                    error={errors.temperature}
                                    hint="36.1 – 37.2 °C"
                                >
                                    <Input
                                        id="temperature"
                                        name="temperature"
                                        type="number"
                                        inputMode="decimal"
                                        step="0.1"
                                        value={data.temperature}
                                        onChange={(e) =>
                                            setData(
                                                'temperature',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            'page.vitals_create.temp_placeholder',
                                        )}
                                        className="tabular-nums"
                                        invalid={Boolean(errors.temperature)}
                                    />
                                </Field>

                                <Field
                                    label={`${t('page.vitals_create.respiratory_rate')} (/min)`}
                                    htmlFor="respiratory_rate"
                                    required
                                    error={errors.respiratory_rate}
                                    hint="12 – 20 /min"
                                >
                                    <Input
                                        id="respiratory_rate"
                                        name="respiratory_rate"
                                        type="number"
                                        inputMode="numeric"
                                        value={data.respiratory_rate}
                                        onChange={(e) =>
                                            setData(
                                                'respiratory_rate',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            'page.vitals_create.resp_placeholder',
                                        )}
                                        className="tabular-nums"
                                        invalid={Boolean(
                                            errors.respiratory_rate,
                                        )}
                                    />
                                </Field>

                                <Field
                                    label={`${t('page.vitals_create.heart_rate')} (bpm)`}
                                    htmlFor="heart_rate"
                                    error={errors.heart_rate}
                                    hint="60 – 100 bpm"
                                >
                                    <Input
                                        id="heart_rate"
                                        name="heart_rate"
                                        type="number"
                                        inputMode="numeric"
                                        icon={HeartIcon}
                                        value={data.heart_rate}
                                        onChange={(e) =>
                                            setData(
                                                'heart_rate',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            'page.vitals_create.hr_placeholder',
                                        )}
                                        className="tabular-nums"
                                        invalid={Boolean(errors.heart_rate)}
                                    />
                                </Field>
                            </FieldSet>

                            <FieldSet
                                columns={3}
                                title={t('page.vitals_create.bp_oxygenation')}
                            >
                                <Field
                                    label={`${t('page.vitals_create.systolic_bp')} (mmHg)`}
                                    htmlFor="systolic_bp"
                                    error={errors.systolic_bp}
                                    hint="90 – 120 mmHg"
                                >
                                    <Input
                                        id="systolic_bp"
                                        name="systolic_bp"
                                        type="number"
                                        inputMode="numeric"
                                        value={data.systolic_bp}
                                        onChange={(e) =>
                                            setData(
                                                'systolic_bp',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            'page.vitals_create.sys_placeholder',
                                        )}
                                        className="tabular-nums"
                                        invalid={Boolean(errors.systolic_bp)}
                                    />
                                </Field>

                                <Field
                                    label={`${t('page.vitals_create.diastolic_bp')} (mmHg)`}
                                    htmlFor="diastolic_bp"
                                    error={errors.diastolic_bp}
                                    hint="60 – 80 mmHg"
                                >
                                    <Input
                                        id="diastolic_bp"
                                        name="diastolic_bp"
                                        type="number"
                                        inputMode="numeric"
                                        value={data.diastolic_bp}
                                        onChange={(e) =>
                                            setData(
                                                'diastolic_bp',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            'page.vitals_create.dia_placeholder',
                                        )}
                                        className="tabular-nums"
                                        invalid={Boolean(errors.diastolic_bp)}
                                    />
                                </Field>

                                <Field
                                    label={`${t('page.vitals_create.oxygen_saturation')} (%)`}
                                    htmlFor="oxygen_saturation"
                                    error={errors.oxygen_saturation}
                                    hint="95 – 100 %"
                                >
                                    <Input
                                        id="oxygen_saturation"
                                        name="oxygen_saturation"
                                        type="number"
                                        inputMode="numeric"
                                        min="0"
                                        max="100"
                                        value={data.oxygen_saturation}
                                        onChange={(e) =>
                                            setData(
                                                'oxygen_saturation',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            'page.vitals_create.oxy_placeholder',
                                        )}
                                        className="tabular-nums"
                                        invalid={Boolean(
                                            errors.oxygen_saturation,
                                        )}
                                    />
                                </Field>
                            </FieldSet>
                        </div>
                    </SectionCard>

                    <SectionCard
                        icon={ClipboardDocumentCheckIcon}
                        title={t('page.vitals_create.clinical_status')}
                    >
                        <FieldSet columns={1}>
                            <Checkbox
                                id="is_abnormal"
                                name="is_abnormal"
                                checked={data.is_abnormal}
                                onChange={(e) =>
                                    setData('is_abnormal', e.target.checked)
                                }
                                label={t('page.vitals_create.mark_abnormal')}
                                description={t(
                                    'page.vitals_create.clinical_status',
                                )}
                            />

                            <Field
                                label={t(
                                    'page.vitals_create.observation_notes',
                                )}
                                htmlFor="notes"
                                optional
                                error={errors.notes}
                            >
                                <Textarea
                                    id="notes"
                                    name="notes"
                                    rows={3}
                                    value={data.notes}
                                    onChange={(e) =>
                                        setData('notes', e.target.value)
                                    }
                                    placeholder={t(
                                        'page.vitals_create.notes_placeholder',
                                    )}
                                    invalid={Boolean(errors.notes)}
                                />
                            </Field>
                        </FieldSet>
                    </SectionCard>

                    <div className="flex flex-wrap items-center justify-end gap-2 border-t border-line pt-4">
                        <Button
                            variant="secondary"
                            href={route('patients.show', patient.id)}
                        >
                            {t('common.cancel')}
                        </Button>
                        <Button
                            type="submit"
                            icon={CheckIcon}
                            loading={processing}
                        >
                            {processing
                                ? t('page.vitals_create.saving')
                                : t('page.vitals_create.commit_record')}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
