import { CheckIcon, DocumentTextIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import {
    Button,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
    Textarea,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { FormPageCard, FormStatusAlerts } from '@/pages/Radiology/components';
import type { Patient } from '@/types';

interface Props {
    type: string;
    patients: Pick<
        Patient,
        'id' | 'first_name' | 'last_name' | 'patient_uid' | 'address'
    >[];
}

export default function CertificatesCreate({ type, patients }: Props) {
    const { t } = useTranslation();
    const [patientSearch] = useState('');

    const typeLabel =
        type === 'sick_leave'
            ? t('Sick Leave Certificate')
            : type === 'medical_fitness'
              ? t('Medical Fitness Certificate')
              : t('Death Certificate');

    const { data, setData, post, processing, errors } = useForm({
        patient_id: '',
        consultation_id: '' as string,
        from_date: '',
        to_date: '',
        diagnosis_summary: '',
        recommendations: '',
        employer_name: '',
        employer_address: '',
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const patientOptions = patients
        .filter(
            (p) =>
                p.first_name
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()) ||
                p.last_name
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()) ||
                p.patient_uid
                    .toLowerCase()
                    .includes(patientSearch.toLowerCase()),
        )
        .map((p) => ({
            value: String(p.id),
            label: `${p.last_name} ${p.first_name} — ${p.patient_uid}`,
        }));

    const routeName =
        type === 'sick_leave'
            ? 'certificates.sick-leave.store'
            : type === 'medical_fitness'
              ? 'certificates.medical.store'
              : 'certificates.death.store';

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route(routeName), {
            onSuccess: () => {
                setSuccessMessage(t('certificateIssuedSuccessfully'));
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <AppLayout
            headerTitle={typeLabel}
            headerSubtitle={t('Issue a medical certificate')}
        >
            <Head title={typeLabel} />

            <div className="mx-auto flex max-w-5xl flex-col gap-6">
                <PageHeader
                    title={typeLabel}
                    description={t('Issue a medical certificate')}
                    breadcrumbs={[
                        { label: t('clinical') },
                        { label: typeLabel },
                    ]}
                />

                <FormStatusAlerts
                    successMessage={successMessage}
                    onDismissSuccess={() => setSuccessMessage(null)}
                    errorMessage={errorMessage}
                    hasFieldErrors={Object.keys(errors).length > 0}
                    successTitle={t('success')}
                    errorTitle={t('error')}
                    errorFallback={t('someFieldsRequireAttention')}
                />

                <FormPageCard
                    title={typeLabel}
                    headingIcon={DocumentTextIcon}
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

                        {type !== 'death' && (
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

                        {type === 'sick_leave' && (
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
                                    placeholder={t('provideRecommendations')}
                                    rows={2}
                                    invalid={Boolean(errors.recommendations)}
                                />
                            </Field>
                        )}

                        {type === 'sick_leave' && (
                            <>
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
