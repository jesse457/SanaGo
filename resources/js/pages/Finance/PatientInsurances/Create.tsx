import {
    HomeIcon,
    ChevronRightIcon,
    UserIcon,
    CheckBadgeIcon,
    ArrowPathIcon,
    CheckIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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

    return (
        <AppLayout
            headerTitle={
                isEdit ? t('editInsurance') : t('linkPatientInsurance')
            }
            headerSubtitle={t('connectPatientToInsurancePolicy')}
        >
            <Head
                title={isEdit ? t('editInsurance') : t('linkInsurance')}
            />

            <div className="max-w-8xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" /> {t('home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('finance')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('patientInsurances')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {isEdit ? t('edit') : t('new')}
                        </li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-start justify-between rounded-lg border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                            <div>
                                <h3 className="text-sm font-bold text-emerald-800 dark:text-emerald-300">
                                    {t('success')}
                                </h3>
                                <div className="mt-1 text-sm text-emerald-700 dark:text-emerald-400">
                                    {successMessage}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setSuccessMessage(null)}
                            className="rounded-lg p-1.5 text-emerald-500 transition hover:text-emerald-700 dark:hover:text-emerald-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                {(errorMessage || Object.keys(errors).length > 0) && (
                    <div className="flex items-start justify-between rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                        <div className="flex items-center gap-3">
                            <ExclamationTriangleIcon className="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" />
                            <div>
                                <h3 className="text-sm font-bold text-red-800 dark:text-red-300">
                                    {t('error')}
                                </h3>
                                <div className="mt-1 text-sm text-red-700 dark:text-red-400">
                                    {errorMessage ||
                                        t('someFieldsRequireAttention')}
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => setErrorMessage(null)}
                            className="rounded-lg p-1.5 text-red-500 transition hover:text-red-700 dark:hover:text-red-300"
                        >
                            <XMarkIcon className="h-5 w-5" />
                        </button>
                    </div>
                )}

                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <form onSubmit={handleSubmit} className="space-y-8 p-6">
                        {/* Section: Patient & Payer */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <UserIcon className="h-5 w-5" />
                                {t('patientAndPayer')}
                            </h3>
                            <div className="grid grid-cols-1 gap-4">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('patient')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={data.patient_id}
                                        onChange={(e) =>
                                            setData(
                                                'patient_id',
                                                e.target.value,
                                            )
                                        }
                                        className={`w-full rounded-lg border py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.patient_id ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    >
                                        <option value="">
                                            {t('selectPatient')}
                                        </option>
                                        {patientOptions.map((opt) => (
                                            <option
                                                key={opt.value}
                                                value={opt.value}
                                            >
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.patient_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.patient_id}
                                        </p>
                                    )}
                                </div>
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('payer')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={data.payer_id}
                                        onChange={(e) =>
                                            setData('payer_id', e.target.value)
                                        }
                                        className={`w-full rounded-lg border py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.payer_id ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    >
                                        <option value="">
                                            {t('selectPayer')}
                                        </option>
                                        {payerOptions.map((opt) => (
                                            <option
                                                key={opt.value}
                                                value={opt.value}
                                            >
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.payer_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.payer_id}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Section: Policy Details */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <CheckBadgeIcon className="h-5 w-5" />
                                {t('policyDetails')}
                            </h3>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('policyNumber')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        value={data.policy_number}
                                        onChange={(e) =>
                                            setData(
                                                'policy_number',
                                                e.target.value,
                                            )
                                        }
                                        className={`w-full rounded-lg border py-2 pr-3 pl-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.policy_number ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    />
                                    {errors.policy_number && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.policy_number}
                                        </p>
                                    )}
                                </div>
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('cardNumber')}
                                    </label>
                                    <input
                                        type="text"
                                        value={data.card_number}
                                        onChange={(e) =>
                                            setData(
                                                'card_number',
                                                e.target.value,
                                            )
                                        }
                                        className={`w-full rounded-lg border py-2 pr-3 pl-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.card_number ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    />
                                    {errors.card_number && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.card_number}
                                        </p>
                                    )}
                                </div>
                            </div>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('validFrom')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        value={data.valid_from}
                                        onChange={(e) =>
                                            setData(
                                                'valid_from',
                                                e.target.value,
                                            )
                                        }
                                        className={`w-full rounded-lg border py-2 pr-3 pl-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.valid_from ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    />
                                    {errors.valid_from && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.valid_from}
                                        </p>
                                    )}
                                </div>
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('validUntil')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="date"
                                        value={data.valid_until}
                                        onChange={(e) =>
                                            setData(
                                                'valid_until',
                                                e.target.value,
                                            )
                                        }
                                        className={`w-full rounded-lg border py-2 pr-3 pl-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.valid_until ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    />
                                    {errors.valid_until && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.valid_until}
                                        </p>
                                    )}
                                </div>
                            </div>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('beneficiaryType')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={data.beneficiary_type}
                                        onChange={(e) =>
                                            setData(
                                                'beneficiary_type',
                                                e.target.value,
                                            )
                                        }
                                        className={`w-full rounded-lg border py-2 px-3 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.beneficiary_type ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    >
                                        {BENEFICIARY_OPTIONS.map((opt) => (
                                            <option
                                                key={opt.value}
                                                value={opt.value}
                                            >
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.beneficiary_type && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.beneficiary_type}
                                        </p>
                                    )}
                                </div>
                                <div className="flex items-center gap-2 pt-6">
                                    <input
                                        type="checkbox"
                                        id="is_primary"
                                        checked={data.is_primary}
                                        onChange={(e) =>
                                            setData(
                                                'is_primary',
                                                e.target.checked,
                                            )
                                        }
                                        className="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-neutral-600 dark:bg-neutral-800"
                                    />
                                    <label
                                        htmlFor="is_primary"
                                        className="text-sm font-medium text-gray-700 dark:text-neutral-300"
                                    >
                                        {t('primaryInsurance')}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href={backRoute}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('cancel')}
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />{' '}
                                        {t('saving')}...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />{' '}
                                        {isEdit
                                            ? t('update')
                                            : t('linkInsurance')}
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AppLayout>
    );
}
