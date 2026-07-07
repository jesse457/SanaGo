import {
    HomeIcon,
    ChevronRightIcon,
    HeartIcon,
    UserIcon,
    ExclamationTriangleIcon,
    MapPinIcon,
    CalendarDaysIcon,
    DocumentTextIcon,
    ClipboardDocumentListIcon,
    CurrencyDollarIcon,
    MagnifyingGlassIcon,
    UserCircleIcon,
    CheckCircleIcon,
    XMarkIcon,
    ArrowPathIcon,
    CheckIcon,
} from '@heroicons/react/24/outline';
import { Head, useForm, Link } from '@inertiajs/react';
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

export default function EmergencyCreate({ patients }: Props) {
    const { t } = useTranslation();
    const [patientQuery, setPatientQuery] = useState('');
    const [showPatientDropdown, setShowPatientDropdown] = useState(false);
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(
        null,
    );

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

    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

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
                        .includes(q) ||
                    p.patient_uid.toLowerCase().includes(q),
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
        post(route('emergencies.store'), {
            onSuccess: () => {
                setSuccessMessage(t('emergencyRegisteredSuccessfully'));
            },
            onError: () => {
                setErrorMessage(t('pleaseCheckForm'));
            },
        });
    };

    return (
        <AppLayout
            headerTitle={t('newEmergency')}
            headerSubtitle={t('registerEmergencyCase')}
        >
            <Head title={t('newEmergency')} />
            <div className="mx-auto max-w-8xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
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
                            {t('emergencies')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-4 w-4" />
                        </li>
                        <li className="font-medium text-blue-600">
                            {t('newEmergency')}
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
                        {/* Section: Emergency Case */}
                        <div className="space-y-4">
                            <h3 className="flex items-center gap-2 border-b border-gray-100 pb-2 text-sm font-bold tracking-wide text-blue-600 uppercase dark:border-neutral-700">
                                <HeartIcon className="h-5 w-5" />
                                {t('registerEmergencyCase')}
                            </h3>

                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                {/* Patient Search */}
                                <div className="md:col-span-2 space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('patient')}{' '}
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
                                                'searchPatientNameOrUid',
                                            )}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.patient_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                        {showPatientDropdown &&
                                            filteredPatients.length > 0 && (
                                                <div className="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800">
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
                                                                className="flex w-full items-center gap-3 border-b border-gray-50 px-4 py-3 text-left transition-colors last:border-0 hover:bg-blue-50 dark:border-neutral-700 dark:hover:bg-blue-900/20"
                                                            >
                                                                <UserCircleIcon className="h-5 w-5 shrink-0 text-gray-400 dark:text-neutral-500" />
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
                                        <p className="mt-2 text-xs text-blue-600">
                                            <Link
                                                href={`/patients/${selectedPatient.id}`}
                                                className="hover:underline"
                                            >
                                                {t('viewProfile')}{' '}
                                                {selectedPatient.first_name}
                                                {t('sProfile')}
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

                                {/* Triage Level */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('triageLevel')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <ExclamationTriangleIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <select
                                            value={data.triage_level}
                                            onChange={(e) =>
                                                setData(
                                                    'triage_level',
                                                    e.target.value,
                                                )
                                            }
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.triage_level ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        >
                                            <option value="normal">
                                                {t('normal')}
                                            </option>
                                            <option value="urgent">
                                                {t('urgent')}
                                            </option>
                                            <option value="emergency">
                                                {t('emergency')}
                                            </option>
                                        </select>
                                    </div>
                                    {errors.triage_level && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.triage_level}
                                        </p>
                                    )}
                                </div>

                                {/* Doctor ID */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('doctorId')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <UserIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.attending_doctor_id}
                                            onChange={(e) =>
                                                setData(
                                                    'attending_doctor_id',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t('doctorId')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.attending_doctor_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.attending_doctor_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {
                                                errors.attending_doctor_id
                                            }
                                        </p>
                                    )}
                                </div>

                                {/* Triage Nurse ID */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('triageNurseId')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <UserIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            value={data.triage_nurse_id}
                                            onChange={(e) =>
                                                setData(
                                                    'triage_nurse_id',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t('triageNurseId')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.triage_nurse_id ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.triage_nurse_id && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.triage_nurse_id}
                                        </p>
                                    )}
                                </div>

                                {/* Arrival Mode */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('arrivalMode')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <MapPinIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={data.arrival_mode}
                                            onChange={(e) =>
                                                setData(
                                                    'arrival_mode',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t('ambulanceWalkIn')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.arrival_mode ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.arrival_mode && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.arrival_mode}
                                        </p>
                                    )}
                                </div>

                                {/* Arrival Date/Time */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('arrivalDateTime')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CalendarDaysIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="datetime-local"
                                            value={data.arrived_at}
                                            onChange={(e) =>
                                                setData(
                                                    'arrived_at',
                                                    e.target.value,
                                                )
                                            }
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.arrived_at ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.arrived_at && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.arrived_at}
                                        </p>
                                    )}
                                </div>

                                {/* Chief Complaint */}
                                <div className="md:col-span-2 space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('chiefComplaint')}{' '}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <DocumentTextIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="text"
                                            value={data.chief_complaint}
                                            onChange={(e) =>
                                                setData(
                                                    'chief_complaint',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t('chiefComplaint')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.chief_complaint ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.chief_complaint && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.chief_complaint}
                                        </p>
                                    )}
                                </div>

                                {/* Primary Assessment */}
                                <div className="md:col-span-2 space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('primaryAssessment')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute top-3 left-0 flex items-start pl-3">
                                            <ClipboardDocumentListIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <textarea
                                            value={data.primary_assessment}
                                            onChange={(e) =>
                                                setData(
                                                    'primary_assessment',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t(
                                                'primaryAssessment',
                                            )}
                                            rows={3}
                                            className={`w-full resize-none rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.primary_assessment ? 'border-red-300 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.primary_assessment && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.primary_assessment}
                                        </p>
                                    )}
                                </div>

                                {/* Emergency Fee */}
                                <div className="space-y-1">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        {t('feeFcfa')}
                                    </label>
                                    <div className="relative">
                                        <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <CurrencyDollarIcon className="h-5 w-5 text-gray-400" />
                                        </div>
                                        <input
                                            type="number"
                                            step="0.01"
                                            value={data.emergency_fee}
                                            onChange={(e) =>
                                                setData(
                                                    'emergency_fee',
                                                    e.target.value,
                                                )
                                            }
                                            placeholder={t('feeFcfa')}
                                            className={`w-full rounded-lg border py-2 pr-3 pl-10 text-sm transition-all focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-700 dark:text-neutral-100 ${errors.emergency_fee ? 'border-red-300 ring-1 ring-red-100 dark:border-red-500' : 'border-gray-300 dark:border-neutral-600'}`}
                                        />
                                    </div>
                                    {errors.emergency_fee && (
                                        <p className="mt-1 text-xs text-red-500">
                                            {errors.emergency_fee}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Form Actions */}
                        <div className="flex items-center justify-end gap-3 border-t border-gray-100 pt-4 dark:border-neutral-700">
                            <Link
                                href={route('emergencies.index')}
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
                                        {t('registering')}...
                                    </span>
                                ) : (
                                    <span className="flex items-center gap-2">
                                        <CheckIcon className="h-4 w-4" />
                                        {t('registerEmergency')}
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
