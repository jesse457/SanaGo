import { CameraIcon, CheckIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    Field,
    FieldSet,
    PageHeader,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { FormPageCard } from '@/pages/Radiology/components';
import type { Patient } from '@/types';

interface Props {
    patients: Pick<
        Patient,
        'id' | 'first_name' | 'last_name' | 'patient_uid'
    >[];
}

const IMAGING_TYPES = [
    { value: 'x_ray', label: 'X-Ray' },
    { value: 'ultrasound', label: 'Ultrasound' },
    { value: 'ct_scan', label: 'CT Scan' },
    { value: 'mri', label: 'MRI' },
    { value: 'mammography', label: 'Mammography' },
];

const BODY_PARTS = [
    { value: 'chest', label: 'Chest' },
    { value: 'abdomen', label: 'Abdomen' },
    { value: 'pelvis', label: 'Pelvis' },
    { value: 'head', label: 'Head' },
    { value: 'spine', label: 'Spine' },
    { value: 'extremity_upper', label: 'Upper Extremity' },
    { value: 'extremity_lower', label: 'Lower Extremity' },
];

export default function RadiologyRequestsCreate({ patients }: Props) {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors, reset } = useForm({
        patient_id: '',
        imaging_type: '',
        body_part: '',
        clinical_notes: '',
    });

    const patientOptions = patients.map((p) => ({
        value: String(p.id),
        label: `${p.last_name} ${p.first_name} — ${p.patient_uid}`,
    }));

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(route('radiology.requests.store'), {
            onSuccess: () => {
                reset();
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('radiology.newRequest')}
            headerSubtitle={t('radiology.requestImaging')}
        >
            <Head title={t('radiology.newRequest')} />

            <div className="mx-auto flex max-w-5xl flex-col gap-6">
                <PageHeader
                    eyebrow={t('radiology.requests.imaging_requests')}
                    title={t('radiology.newRequest')}
                    description={t('radiology.requestImaging')}
                    breadcrumbs={[
                        {
                            label: t('radiology.requests.imaging_requests'),
                            href: route('radiology.requests.index'),
                        },
                        { label: t('radiology.newRequest') },
                    ]}
                />

                <FormPageCard
                    title={t('radiology.patient')}
                    headingIcon={CameraIcon}
                    onSubmit={handleSubmit}
                    footer={
                        <>
                            <Button
                                variant="secondary"
                                href={route('radiology.requests.index')}
                            >
                                {t('common.cancel')}
                            </Button>
                            <Button
                                type="submit"
                                loading={processing}
                                icon={CheckIcon}
                            >
                                {processing
                                    ? `${t('radiology.submitting')}...`
                                    : t('radiology.submitRequest')}
                            </Button>
                        </>
                    }
                >
                    <FieldSet columns={1}>
                        <Field
                            label={t('radiology.selectPatient')}
                            htmlFor="patient_id"
                            required
                            error={errors.patient_id}
                        >
                            <Select
                                id="patient_id"
                                value={data.patient_id}
                                onChange={(e) =>
                                    setData('patient_id', e.target.value)
                                }
                                placeholder={t(
                                    'radiology.selectPatientPlaceholder',
                                )}
                                invalid={Boolean(errors.patient_id)}
                            >
                                {patientOptions.map((p) => (
                                    <option key={p.value} value={p.value}>
                                        {p.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>
                    </FieldSet>

                    <FieldSet title={t('radiology.imagingDetails')} columns={2}>
                        <Field
                            label={t('radiology.imagingType')}
                            htmlFor="imaging_type"
                            required
                            error={errors.imaging_type}
                        >
                            <Select
                                id="imaging_type"
                                value={data.imaging_type}
                                onChange={(e) =>
                                    setData('imaging_type', e.target.value)
                                }
                                placeholder={t('radiology.selectImagingType')}
                                invalid={Boolean(errors.imaging_type)}
                            >
                                {IMAGING_TYPES.map((it) => (
                                    <option key={it.value} value={it.value}>
                                        {it.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field
                            label={t('radiology.bodyPart')}
                            htmlFor="body_part"
                            required
                            error={errors.body_part}
                        >
                            <Select
                                id="body_part"
                                value={data.body_part}
                                onChange={(e) =>
                                    setData('body_part', e.target.value)
                                }
                                placeholder={t('radiology.selectBodyPart')}
                                invalid={Boolean(errors.body_part)}
                            >
                                {BODY_PARTS.map((bp) => (
                                    <option key={bp.value} value={bp.value}>
                                        {bp.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field
                            label={t('radiology.clinicalNotes')}
                            htmlFor="clinical_notes"
                            labelAction={
                                <span className="text-[10px] font-medium tracking-normal text-ink-subtle normal-case">
                                    {t('common.optional')}
                                </span>
                            }
                            error={errors.clinical_notes}
                            className="sm:col-span-2"
                        >
                            <Textarea
                                id="clinical_notes"
                                value={data.clinical_notes}
                                onChange={(e) =>
                                    setData('clinical_notes', e.target.value)
                                }
                                rows={3}
                                placeholder={t(
                                    'radiology.clinicalNotesPlaceholder',
                                )}
                                invalid={Boolean(errors.clinical_notes)}
                            />
                        </Field>
                    </FieldSet>
                </FormPageCard>
            </div>
        </AppLayout>
    );
}
