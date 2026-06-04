import {
    HomeIcon,
    ChevronRightIcon,
    CubeIcon,
    DocumentTextIcon,
    AdjustmentsHorizontalIcon,
    ArchiveBoxArrowDownIcon,
    RectangleStackIcon,
    TagIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XMarkIcon,
    ArrowPathIcon,
    CheckIcon,
    MagnifyingGlassIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    date_of_birth: string | null;
    gender: string | null;
}

interface Props {
    patients: Patient[];
}

export default function SurgeryCreate({ patients }: Props) {
    const { t } = useTranslation();
    const [patientQuery, setPatientQuery] = useState('');
    const [showPatientDropdown, setShowPatientDropdown] = useState(false);
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(
        null,
    );

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        patient_id: '',
        surgeon_id: '',
        anesthetist_id: '',
        bed_id: '',
        surgery_name: '',
        description: '',
        scheduled_at: '',
        estimated_duration_minutes: '',
        urgency: 'normal',
        surgery_fee: '',
        notes: '',
    });

    const filteredPatients = useMemo(() => {
        if (!patientQuery.trim()) {
            return patients.slice(0, 10);
        }

        const q = patientQuery.toLowerCase();

        return patients
            .filter(
                (p) =>
                    `${p.first_name} ${p.last_name}`
                        .toLowerCase()
                        .includes(q) || p.patient_uid.toLowerCase().includes(q),
            )
            .slice(0, 10);
    }, [patients, patientQuery]);

    const selectPatient = (p: Patient) => {
        setSelectedPatient(p);
        setData('patient_id', String(p.id));
        setPatientQuery(`${p.first_name} ${p.last_name} (${p.patient_uid})`);
        setShowPatientDropdown(false);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setErrorMessage(null);
        post(route('surgeries.store'), {
            onSuccess: () => {
                setSuccessMessage(t('surgeries.create.success_message'));
                reset();
                setSelectedPatient(null);
                setPatientQuery('');
            },
            onError: () => {
                setErrorMessage(t('surgeries.create.error_message'));
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('surgeries.create.title')}
            headerSubtitle={t('surgeries.create.subtitle')}
        >
            <Head title={t('surgeries.create.head_title')} />

            <div className="max-w-8xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-xs font-semibold tracking-wide text-gray-500 uppercase dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-blue-600">
                            <HomeIcon className="mr-1 h-4 w-4" />{' '}
                            {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-blue-600">
                            {t('surgeries.create.surgeries')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('surgeries.create.schedule_surgery')}
                        </li>
                    </ol>
                </nav>

                {/* Alerts */}
                {successMessage && (
                    <div className="flex items-start justify-between rounded-lg border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <div className="flex items-center gap-3">
                            <CheckCircleIcon className="h-5 w-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" />
                            <div>
                                <h3 className="text-sm font-bold text-emerald-800 dark:text-emerald-300">
                                    {t('surgeries.create.success')}
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
                            <ExclamationTriangleIcon className="h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400" />
                            <div>
                                <h3 className="text-sm font-bold text-red-800 dark:text-red-300">
                                    {t('surgeries.create.error')}
                                </h3>
                                <div className="mt-1 text-sm text-red-700 dark:text-red-400">
                                    {errorMessage ||
                                        t('surgeries.create.error_attention')}
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
                        {/* Section 1: Procedure Details */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <CubeIcon className="h-5 w-5" />
                                {t('surgeries.create.section_procedure')}
                            </h3>

                            <div className="grid grid-cols-2 gap-4">
                                {/* Procedure Name */}
                                <div className="col-span-2 space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('surgeries.create.procedure_name')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <DocumentTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={data.surgery_name}
                                            onChange={(e) =>
                                                setData(
                                                    'surgery_name',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'surgeries.create.procedure_name_placeholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.surgery_name ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                            required
                                        />
                                    </div>
                                    {errors.surgery_name && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.surgery_name}
                                        </p>
                                    )}
                                </div>

                                {/* Patient Lookup */}
                                <div className="col-span-2 space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('surgeries.create.patient_name')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <MagnifyingGlassIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={patientQuery}
                                            onChange={(e) => {
                                                setPatientQuery(e.target.value);
                                                setShowPatientDropdown(true);
                                            }}
                                            onFocus={() =>
                                                setShowPatientDropdown(true)
                                            }
                                            onBlur={() =>
                                                setTimeout(
                                                    () =>
                                                        setShowPatientDropdown(
                                                            false,
                                                        ),
                                                    200,
                                                )
                                            }
                                            placeholder={t(
                                                'surgeries.create.patient_search_placeholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.patient_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                        {showPatientDropdown &&
                                            filteredPatients.length > 0 && (
                                                <div className="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
                                                    {filteredPatients.map(
                                                        (p) => (
                                                            <button
                                                                key={p.id}
                                                                type="button"
                                                                onClick={() =>
                                                                    selectPatient(
                                                                        p,
                                                                    )
                                                                }
                                                                className="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-3 text-left transition-colors last:border-0 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-blue-900/20"
                                                            >
                                                                <UserCircleIcon className="h-5 w-5 shrink-0 text-gray-400" />
                                                                <div>
                                                                    <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">
                                                                        {
                                                                            p.first_name
                                                                        }{' '}
                                                                        {
                                                                            p.last_name
                                                                        }
                                                                    </p>
                                                                    <p className="text-xs text-gray-500 dark:text-neutral-400">
                                                                        {
                                                                            p.patient_uid
                                                                        }{' '}
                                                                        &middot;{' '}
                                                                        {
                                                                            p.gender
                                                                        }{' '}
                                                                        &middot;{' '}
                                                                        {
                                                                            p.date_of_birth
                                                                        }
                                                                    </p>
                                                                </div>
                                                            </button>
                                                        ),
                                                    )}
                                                </div>
                                            )}
                                    </div>
                                    {selectedPatient && (
                                        <p className="mt-1.5 text-xs font-medium text-blue-600 dark:text-blue-400">
                                            <Link
                                                href={`/patients/${selectedPatient.id}`}
                                                className="hover:underline"
                                            >
                                                {t(
                                                    'surgeries.create.view_profile',
                                                    {
                                                        name: selectedPatient.first_name,
                                                    },
                                                )}
                                            </Link>
                                        </p>
                                    )}
                                    <input
                                        type="hidden"
                                        value={data.patient_id}
                                    />
                                    {errors.patient_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.patient_id}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Section 2: Clinical Team & Logistics */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <AdjustmentsHorizontalIcon className="h-5 w-5" />
                                {t('surgeries.create.section_clinical_team')}
                            </h3>

                            <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                                {/* Surgeon ID */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('surgeries.create.surgeon_id')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <AdjustmentsHorizontalIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.surgeon_id}
                                            onChange={(e) =>
                                                setData(
                                                    'surgeon_id',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'surgeries.create.surgeon_id_placeholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.surgeon_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                            required
                                        />
                                    </div>
                                    {errors.surgeon_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.surgeon_id}
                                        </p>
                                    )}
                                </div>

                                {/* Anesthetist ID */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('surgeries.create.anesthetist_id')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <AdjustmentsHorizontalIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.anesthetist_id}
                                            onChange={(e) =>
                                                setData(
                                                    'anesthetist_id',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'surgeries.create.anesthetist_id_placeholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.anesthetist_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.anesthetist_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.anesthetist_id}
                                        </p>
                                    )}
                                </div>

                                {/* Bed ID */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('surgeries.create.recovery_bed_id')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <RectangleStackIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.bed_id}
                                            onChange={(e) =>
                                                setData(
                                                    'bed_id',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'surgeries.create.recovery_bed_id_placeholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.bed_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.bed_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.bed_id}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Section 3: Scheduling & Cost */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <ArchiveBoxArrowDownIcon className="h-5 w-5" />
                                {t('surgeries.create.section_scheduling')}
                            </h3>

                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                {/* Scheduled At */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t(
                                            'surgeries.create.scheduled_date_time',
                                        )}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <DocumentTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="datetime-local"
                                            value={data.scheduled_at}
                                            onChange={(e) =>
                                                setData(
                                                    'scheduled_at',
                                                    e.target.value,
                                                )
                                            }
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.scheduled_at ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                            required
                                        />
                                    </div>
                                    {errors.scheduled_at && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.scheduled_at}
                                        </p>
                                    )}
                                </div>

                                {/* Estimated Duration */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('surgeries.create.est_duration')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <DocumentTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={
                                                data.estimated_duration_minutes
                                            }
                                            onChange={(e) =>
                                                setData(
                                                    'estimated_duration_minutes',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'surgeries.create.duration_placeholder',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.estimated_duration_minutes ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.estimated_duration_minutes && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.estimated_duration_minutes}
                                        </p>
                                    )}
                                </div>

                                {/* Urgency Dropdown */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('surgeries.create.urgency_level')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <TagIcon className="pointer-events-none h-5 w-5 text-gray-400" />
                                        </div>
                                        <select
                                            value={data.urgency}
                                            onChange={(e) =>
                                                setData(
                                                    'urgency',
                                                    e.target.value,
                                                )
                                            }
                                            className="w-full rounded-lg border border-gray-300 py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-700 dark:text-neutral-100"
                                        >
                                            <option value="normal">
                                                {t('surgeries.create.normal')}
                                            </option>
                                            <option value="urgent">
                                                {t('surgeries.create.urgent')}
                                            </option>
                                            <option value="emergency">
                                                {t(
                                                    'surgeries.create.emergency',
                                                )}
                                            </option>
                                        </select>
                                    </div>
                                    {errors.urgency && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.urgency}
                                        </p>
                                    )}
                                </div>

                                {/* Surgery Fee */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('surgeries.create.surgery_fee')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <span className="absolute top-1/2 left-3 -translate-y-1/2 text-xs font-semibold text-gray-400">
                                            FCFA
                                        </span>
                                        <input
                                            type="number"
                                            step="0.01"
                                            value={data.surgery_fee}
                                            onChange={(e) =>
                                                setData(
                                                    'surgery_fee',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="0.00"
                                            className={`w-full rounded-lg border py-2 pr-3 pl-14 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.surgery_fee ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                            required
                                        />
                                    </div>
                                    {errors.surgery_fee && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.surgery_fee}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Section 4: Notes */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <TagIcon className="h-5 w-5" />
                                {t('surgeries.create.additional_notes')}{' '}
                                <span className="ml-1 text-xs font-normal text-gray-400">
                                    ({t('surgeries.create.optional')})
                                </span>
                            </h3>

                            <div className="space-y-1">
                                <textarea
                                    value={data.notes}
                                    onChange={(e) =>
                                        setData('notes', e.target.value)
                                    }
                                    placeholder={t(
                                        'surgeries.create.notes_placeholder',
                                    )}
                                    rows={3}
                                    className={`w-full resize-none rounded-lg border px-3 py-2 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.notes ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                />
                                {errors.notes && (
                                    <p className="mt-1 text-xs text-red-500">
                                        {errors.notes}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <button
                                type="button"
                                onClick={() => window.history.back()}
                                className="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            >
                                {t('common.cancel')}
                            </button>

                            <button
                                type="submit"
                                disabled={processing}
                                className="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                        {t('surgeries.create.scheduling')}
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />
                                        {t(
                                            'surgeries.create.schedule_surgery_button',
                                        )}
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
