import { CheckIcon, CreditCardIcon } from '@heroicons/react/24/outline';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import {
    Button,
    Checkbox,
    Field,
    FieldSet,
    Input,
    PageHeader,
    Select,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { FormPageCard, FormStatusAlerts } from '@/pages/Radiology/components';

interface LocalPayer {
    id: number;
    name: string;
    type: string;
}

interface LocalPatientInsurance {
    id: number;
    patient_id: number;
    payer_id: number;
    policy_number: string;
    card_number: string;
    valid_from: string;
    valid_until: string;
    beneficiary_type: string;
    is_primary: boolean;
}

interface Props {
    insurance?: LocalPatientInsurance;
    patients: Array<{
        id: number;
        first_name: string;
        last_name: string;
        patient_uid: string;
    }>;
    payers: LocalPayer[];
}

const BENEFICIARY_OPTIONS = [
    { value: 'titulaire', label: 'Titulaire (Policy Holder)' },
    { value: 'conjoint', label: 'Conjoint (Spouse)' },
    { value: 'enfant', label: 'Enfant (Child)' },
];

export default function PatientInsurancesCreate({
    insurance,
    patients,
    payers,
}: Props) {
    const { t } = useTranslation();
    const isEdit = !!insurance;

    const { data, setData, post, put, processing, errors } = useForm({
        patient_id: insurance?.patient_id ? String(insurance.patient_id) : '',
        payer_id: insurance?.payer_id ? String(insurance.payer_id) : '',
        policy_number: insurance?.policy_number ?? '',
        card_number: insurance?.card_number ?? '',
        valid_from: insurance?.valid_from ?? '',
        valid_until: insurance?.valid_until ?? '',
        beneficiary_type: insurance?.beneficiary_type ?? 'titulaire',
        is_primary: insurance?.is_primary ?? true,
    });

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const patientOptions = patients.map((p) => ({
        value: String(p.id),
        label: `${p.last_name} ${p.first_name} — ${p.patient_uid}`,
    }));

    const payerOptions = payers.map((p) => ({
        value: String(p.id),
        label: `${p.name} (${p.type})`,
    }));

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);

        if (isEdit && insurance) {
            put(route('patient-insurances.update', insurance.id), {
                onSuccess: () => {
                    setSuccessMessage(t('insuranceUpdatedSuccessfully'));
                },
                onError: () => {
                    setErrorMessage(t('pleaseCheckForm'));
                },
            });
        } else {
            post(route('patient-insurances.store'), {
                onSuccess: () => {
                    setSuccessMessage(t('insuranceLinkedSuccessfully'));
                },
                onError: () => {
                    setErrorMessage(t('pleaseCheckForm'));
                },
            });
        }
    };

    const backRoute = isEdit
        ? route('patient-insurances.show', insurance!.id)
        : route('patient-insurances.index');

    const pageTitle = isEdit ? t('editInsurance') : t('linkPatientInsurance');

    return (
        <AppLayout
            headerTitle={pageTitle}
            headerSubtitle={t('connectPatientToInsurancePolicy')}
        >
            <Head title={isEdit ? t('editInsurance') : t('linkInsurance')} />

            <div className="mx-auto flex max-w-5xl flex-col gap-6">
                <PageHeader
                    title={pageTitle}
                    description={t('connectPatientToInsurancePolicy')}
                    breadcrumbs={[
                        { label: t('finance.breadcrumb', 'Finance') },
                        { label: t('patientInsurances') },
                        { label: isEdit ? t('edit') : t('new') },
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
                    title={t('policyDetails')}
                    headingIcon={CreditCardIcon}
                    onSubmit={handleSubmit}
                    footer={
                        <>
                            <Button variant="secondary" href={backRoute}>
                                {t('cancel')}
                            </Button>
                            <Button
                                type="submit"
                                loading={processing}
                                icon={CheckIcon}
                            >
                                {processing
                                    ? `${t('saving')}...`
                                    : isEdit
                                      ? t('update')
                                      : t('linkInsurance')}
                            </Button>
                        </>
                    }
                >
                    <FieldSet title={t('patientAndPayer')} columns={2}>
                        <Field
                            label={t('patient')}
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
                                placeholder={t('selectPatient')}
                                invalid={Boolean(errors.patient_id)}
                            >
                                {patientOptions.map((opt) => (
                                    <option key={opt.value} value={opt.value}>
                                        {opt.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <Field
                            label={t('payer')}
                            htmlFor="payer_id"
                            required
                            error={errors.payer_id}
                        >
                            <Select
                                id="payer_id"
                                value={data.payer_id}
                                onChange={(e) =>
                                    setData('payer_id', e.target.value)
                                }
                                placeholder={t('selectPayer')}
                                invalid={Boolean(errors.payer_id)}
                            >
                                {payerOptions.map((opt) => (
                                    <option key={opt.value} value={opt.value}>
                                        {opt.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>
                    </FieldSet>

                    <FieldSet title={t('policyDetails')} columns={2}>
                        <Field
                            label={t('policyNumber')}
                            htmlFor="policy_number"
                            required
                            error={errors.policy_number}
                        >
                            <Input
                                id="policy_number"
                                type="text"
                                value={data.policy_number}
                                onChange={(e) =>
                                    setData('policy_number', e.target.value)
                                }
                                invalid={Boolean(errors.policy_number)}
                            />
                        </Field>

                        <Field
                            label={t('cardNumber')}
                            htmlFor="card_number"
                            error={errors.card_number}
                        >
                            <Input
                                id="card_number"
                                type="text"
                                value={data.card_number}
                                onChange={(e) =>
                                    setData('card_number', e.target.value)
                                }
                                invalid={Boolean(errors.card_number)}
                            />
                        </Field>

                        <Field
                            label={t('validFrom')}
                            htmlFor="valid_from"
                            required
                            error={errors.valid_from}
                        >
                            <Input
                                id="valid_from"
                                type="date"
                                value={data.valid_from}
                                onChange={(e) =>
                                    setData('valid_from', e.target.value)
                                }
                                invalid={Boolean(errors.valid_from)}
                            />
                        </Field>

                        <Field
                            label={t('validUntil')}
                            htmlFor="valid_until"
                            required
                            error={errors.valid_until}
                        >
                            <Input
                                id="valid_until"
                                type="date"
                                value={data.valid_until}
                                onChange={(e) =>
                                    setData('valid_until', e.target.value)
                                }
                                invalid={Boolean(errors.valid_until)}
                            />
                        </Field>

                        <Field
                            label={t('beneficiaryType')}
                            htmlFor="beneficiary_type"
                            required
                            error={errors.beneficiary_type}
                        >
                            <Select
                                id="beneficiary_type"
                                value={data.beneficiary_type}
                                onChange={(e) =>
                                    setData('beneficiary_type', e.target.value)
                                }
                                invalid={Boolean(errors.beneficiary_type)}
                            >
                                {BENEFICIARY_OPTIONS.map((opt) => (
                                    <option key={opt.value} value={opt.value}>
                                        {opt.label}
                                    </option>
                                ))}
                            </Select>
                        </Field>

                        <div className="flex items-end pb-2.5">
                            <Checkbox
                                id="is_primary"
                                checked={data.is_primary}
                                onChange={(e) =>
                                    setData('is_primary', e.target.checked)
                                }
                                label={t('primaryInsurance')}
                            />
                        </div>
                    </FieldSet>
                </FormPageCard>
            </div>
        </AppLayout>
    );
}
