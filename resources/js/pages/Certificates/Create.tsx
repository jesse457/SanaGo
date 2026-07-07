import {
    HomeIcon,
    ChevronRightIcon,
    UserIcon,
    CalendarDaysIcon,
    DocumentTextIcon,
    BriefcaseIcon,
    MapPinIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { Patient } from '@/types';

interface Props {
    type: string;
    patients: Pick<Patient, 'id' | 'first_name' | 'last_name' | 'patient_uid' | 'address'>[];
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

            <div className="mx-auto max-w-8xl space-y-6 px-4 sm:px-6 lg:px-8">
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
                            {t('clinical')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {typeLabel}
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

                {/* Main Form Card */}
                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <form onSubmit={handleSubmit} className="space-y-8 p-6">
                        {/* Section 1: Patient */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <UserIcon className="h-5 w-5" />
                                {t('patient')}
                            </h3>

                            <div className="space-y-1">
                                <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                    {t('selectPatient')}{' '}
                                    <span className="text-red-500">*</span>
                                </label>
                                <div className="relative">
                                    <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <UserIcon className="h-5 w-5 text-gray-400" />
                                    </div>
                                    <select
                                        value={data.patient_id}
                                        onChange={(e) =>
                                            setData('patient_id', e.target.value)
                                        }
                                        className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.patient_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                    >
                                        <option value="">
                                            {t('searchPatient')}
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
                                </div>
                                {errors.patient_id && (
                                    <p className="mt-1 text-xs text-red-500">
                                        {errors.patient_id}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Section 2: Certificate Details */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <DocumentTextIcon className="h-5 w-5" />
                                {t('certificateDetails')}
                            </h3>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('fromDate')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CalendarDaysIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="date"
                                            value={data.from_date}
                                            onChange={(e) =>
                                                setData(
                                                    'from_date',
                                                    e.target.value,
                                                )
                                            }
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.from_date ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.from_date && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.from_date}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('toDate')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CalendarDaysIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="date"
                                            value={data.to_date}
                                            onChange={(e) =>
                                                setData(
                                                    'to_date',
                                                    e.target.value,
                                                )
                                            }
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.to_date ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.to_date && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.to_date}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {type !== 'death' && (
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('diagnosisSummary')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <DocumentTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <textarea
                                            value={data.diagnosis_summary}
                                            onChange={(e) =>
                                                setData(
                                                    'diagnosis_summary',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'provideDiagnosisSummary',
                                            )}
                                            rows={3}
                                            className={`w-full resize-none rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.diagnosis_summary ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.diagnosis_summary && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.diagnosis_summary}
                                        </p>
                                    )}
                                </div>
                            )}

                            {type === 'sick_leave' && (
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('recommendations')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <DocumentTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <textarea
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
                                            className={`w-full resize-none rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.recommendations ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.recommendations && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.recommendations}
                                        </p>
                                    )}
                                </div>
                            )}

                            {type === 'sick_leave' && (
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-1">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                            {t('employerName')}
                                        </label>
                                        <div className="relative">
                                            <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <BriefcaseIcon className="h-5 w-5 text-gray-400" />
                                            </div>
                                            <input
                                                type="text"
                                                value={data.employer_name}
                                                onChange={(e) =>
                                                    setData(
                                                        'employer_name',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder={t('employerName')}
                                                className="w-full rounded-lg border border-gray-300 py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                            {t('employerAddress')}
                                        </label>
                                        <div className="relative">
                                            <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <MapPinIcon className="h-5 w-5 text-gray-400" />
                                            </div>
                                            <input
                                                type="text"
                                                value={data.employer_address}
                                                onChange={(e) =>
                                                    setData(
                                                        'employer_address',
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder={t(
                                                    'employerAddress',
                                                )}
                                                className="w-full rounded-lg border border-gray-300 py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                            />
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href={route('dashboard')}
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
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                        {t('issuing')}...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />
                                        {t('issueCertificate')}
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
