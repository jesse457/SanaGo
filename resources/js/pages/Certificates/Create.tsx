import {
    CalendarDaysIcon,
    CheckIcon,
    DocumentTextIcon,
    HeartIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import {
    Button,
    Field,
    FieldSet,
    Input,
    PageHeader,
    SegmentedControl,
    Select,
    Textarea,
} from '@/Components/ui';
import type { IconType, SegmentedOption } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { FormPageCard } from '@/pages/Radiology/components';
import type { Patient } from '@/types';
import { CertificateType } from '@/types/enums';
import type { CertificateTypeEnum } from '@/types/enums';

interface Props {
    patients: Pick<
        Patient,
        'id' | 'first_name' | 'last_name' | 'patient_uid' | 'address'
    >[];
}

/**
 * Copy and icon per certificate type. The page renders all three certificates;
 * only the fields that the chosen type actually needs are shown.
 */
type TypeMeta = {
    label: string;
    segmentLabel: string;
    description: string;
    icon: IconType;
};

export default function CertificatesCreate({ patients }: Props) {
    const { t } = useTranslation();

    const { data, setData, post, processing, errors } = useForm({
        type: CertificateType.SICK_LEAVE as CertificateTypeEnum,
        patient_id: '',
        consultation_id: '' as string,
        from_date: '',
        to_date: '',
        diagnosis_summary: '',
        recommendations: '',
        employer_name: '',
        employer_address: '',
    });

    const typeMeta: Record<CertificateTypeEnum, TypeMeta> = {
        [CertificateType.SICK_LEAVE]: {
            label: t('sickLeaveCertificate'),
            segmentLabel: t('sickLeave'),
            description: t('sickLeaveCertificateDescription'),
            icon: CalendarDaysIcon,
        },
        [CertificateType.MEDICAL_FITNESS]: {
            label: t('medicalFitnessCertificate'),
            segmentLabel: t('medicalFitness'),
            description: t('medicalFitnessCertificateDescription'),
            icon: HeartIcon,
        },
        [CertificateType.DEATH]: {
            label: t('deathCertificate'),
            segmentLabel: t('death'),
            description: t('deathCertificateDescription'),
            icon: DocumentTextIcon,
        },
    };

    const typeOptions: SegmentedOption<CertificateTypeEnum>[] = [
        CertificateType.SICK_LEAVE,
        CertificateType.MEDICAL_FITNESS,
        CertificateType.DEATH,
    ].map((value) => {
        const meta = typeMeta[value];

        return {
            value,
            label: meta.segmentLabel,
            icon: meta.icon,
            ariaLabel: meta.label,
        };
    });

    const active = typeMeta[data.type];

    // Type-specific fields follow the chosen certificate: switching type drops
    // the values that no longer belong to it so nothing stale is submitted.
    const showDiagnosis = data.type !== CertificateType.DEATH;
    const showEmployer = data.type === CertificateType.SICK_LEAVE;

    const handleTypeChange = (nextType: CertificateTypeEnum) => {
        setData({
            ...data,
            type: nextType,
            diagnosis_summary:
                nextType === CertificateType.DEATH
                    ? ''
                    : data.diagnosis_summary,
            recommendations:
                nextType === CertificateType.SICK_LEAVE
                    ? data.recommendations
                    : '',
            employer_name:
                nextType === CertificateType.SICK_LEAVE
                    ? data.employer_name
                    : '',
            employer_address:
                nextType === CertificateType.SICK_LEAVE
                    ? data.employer_address
                    : '',
        });
    };

    const patientOptions = patients.map((p) => ({
        value: String(p.id),
        label: `${p.last_name} ${p.first_name} — ${p.patient_uid}`,
    }));

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post(route('certificates.store'), {});
    };

    return (
        <AppLayout
            headerTitle={t('certificates')}
            headerSubtitle={t('issueACertificate')}
        >
            <Head title={t('certificates')} />

            <div className="mx-auto flex max-w-5xl flex-col gap-6">
                <PageHeader
                    title={t('certificates')}
                    eyebrow={t('clinical')}
                    description={active.description}
                    icon={DocumentTextIcon}
                    actions={
                        <SegmentedControl
                            value={data.type}
                            onChange={handleTypeChange}
                            options={typeOptions}
                            size="sm"
                        />
                    }
                />

                <FormPageCard
                    title={active.label}
                    description={active.description}
                    headingIcon={active.icon}
                    onSubmit={handleSubmit}
                    footer={
                        <>
                            <Button
                                variant="secondary"
                                href={route('dashboard')}
                            >
                                {t('cancel')}
                            </Button>
                            <Button
                                type="submit"
                                loading={processing}
                                icon={CheckIcon}
                            >
                                {processing
                                    ? `${t('issuing')}...`
                                    : t('issueCertificate')}
                            </Button>
                        </>
                    }
                >
                    <FieldSet columns={1} title={t('patient')}>
                        <Field
                            label={t('selectPatient')}
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
                                placeholder={t('searchPatient')}
                                invalid={Boolean(errors.patient_id)}
                            >
                                {patientOptions.map((opt) => (
                                    <option key={opt.value} value={opt.value}>
                                        {opt.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>
                    </FieldSet>

                    <FieldSet title={t('certificateDetails')} columns={2}>
                        <Field
                            label={t('fromDate')}
                            htmlFor="from_date"
                            required
                            error={errors.from_date}
                        >
                            <Input
                                id="from_date"
                                type="date"
                                value={data.from_date}
                                onChange={(e) =>
                                    setData('from_date', e.target.value)
                                }
                                invalid={Boolean(errors.from_date)}
                            />
                        </Field>

                        <Field
                            label={t('toDate')}
                            htmlFor="to_date"
                            required
                            error={errors.to_date}
                        >
                            <Input
                                id="to_date"
                                type="date"
                                value={data.to_date}
                                onChange={(e) =>
                                    setData('to_date', e.target.value)
                                }
                                invalid={Boolean(errors.to_date)}
                            />
                        </Field>

                        {showDiagnosis && (
                            <Field
                                label={t('diagnosisSummary')}
                                htmlFor="diagnosis_summary"
                                error={errors.diagnosis_summary}
                                className="sm:col-span-2"
                            >
                                <Textarea
                                    id="diagnosis_summary"
                                    value={data.diagnosis_summary}
                                    onChange={(e) =>
                                        setData(
                                            'diagnosis_summary',
                                            e.target.value,
                                        )
                                    }
                                    placeholder={t('provideDiagnosisSummary')}
                                    rows={3}
                                    invalid={Boolean(errors.diagnosis_summary)}
                                />
                            </Field>
                        )}

                        {showEmployer && (
                            <>
                                <Field
                                    label={t('recommendations')}
                                    htmlFor="recommendations"
                                    error={errors.recommendations}
                                    className="sm:col-span-2"
                                >
                                    <Textarea
                                        id="recommendations"
                                        value={data.recommendations}
                                        onChange={(e) =>
                                            setData(
                                                'recommendations',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t(
                                            'provideRecommendations',
                                        )}
                                        rows={2}
                                        invalid={Boolean(
                                            errors.recommendations,
                                        )}
                                    />
                                </Field>

                                <Field
                                    label={t('employerName')}
                                    htmlFor="employer_name"
                                    error={errors.employer_name}
                                >
                                    <Input
                                        id="employer_name"
                                        type="text"
                                        value={data.employer_name}
                                        onChange={(e) =>
                                            setData(
                                                'employer_name',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('employerName')}
                                        invalid={Boolean(errors.employer_name)}
                                    />
                                </Field>

                                <Field
                                    label={t('employerAddress')}
                                    htmlFor="employer_address"
                                    error={errors.employer_address}
                                >
                                    <Input
                                        id="employer_address"
                                        type="text"
                                        value={data.employer_address}
                                        onChange={(e) =>
                                            setData(
                                                'employer_address',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={t('employerAddress')}
                                        invalid={Boolean(
                                            errors.employer_address,
                                        )}
                                    />
                                </Field>
                            </>
                        )}
                    </FieldSet>
                </FormPageCard>
            </div>
        </AppLayout>
    );
}
