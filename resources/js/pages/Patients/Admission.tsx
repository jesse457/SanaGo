import {
    HomeIcon,
    ChevronRightIcon,
    UserIcon,
    CalendarIcon,
    PencilSquareIcon,
    BanknotesIcon,
    ArrowPathIcon,
    CheckIcon,
    MapPinIcon,
    InformationCircleIcon,
    ClockIcon,
    UserGroupIcon,
    ArrowLeftIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Bed {
    id: number;
    code: string;
    ward?: { name: string };
    bed_type?: { name: string };
}

interface Doctor {
    id: number;
    name: string;
}

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    age: number;
    gender: string;
}

interface Props {
    patient: Patient;
    availableBeds: Bed[];
    doctors: Doctor[];
}

export default function PatientAdmission({
    patient,
    availableBeds,
    doctors,
}: Props) {
    const { t } = useTranslation();
    const { data, setData, post, processing, errors } = useForm({
        patient_id: patient.id,
        bed_id: '',
        attending_doctor_id: '',
        admission_date: new Date().toISOString().split('T')[0],
        reason_for_admission: '',
        observation_fee: 5000, // Default deposit for Cameroon clinics
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('admissions.store'));
    };

    // Shared input styling to match VitalsForm
    const inputStyle = (error: string | undefined) => `
        w-full pl-12 pr-4 py-4 bg-white dark:bg-neutral-800 border-2 rounded-lg
        font-bold text-sm transition-all outline-none
        ${error ? 'border-red-500 focus:border-red-600' : 'border-gray-200 dark:border-neutral-700 focus:border-rose-600 focus:ring-0'}
    `;

    return (
        <AppLayout
            headerTitle={t('page.admission.title')}
            headerSubtitle={t('page.admission.subtitle', {
                firstName: patient.first_name,
                lastName: patient.last_name,
            })}
        >
            <Head title={t('page.admission.head_title')} />

            <div className="mx-auto max-w-6xl space-y-8 px-4 pb-20 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <nav className="flex text-[10px] font-black tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex cursor-pointer items-center transition-colors hover:text-rose-600">
                            <HomeIcon className="mr-1 h-3.5 w-3.5" />{' '}
                            {t('common.home')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="cursor-pointer transition-colors hover:text-rose-600">
                            {t('page.admission.breadcrumb_admissions')}
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="text-rose-600">
                            {t('page.admission.new_entry')}
                        </li>
                    </ol>
                </nav>

                <form onSubmit={handleSubmit} className="space-y-8">
                    <div className="overflow-hidden rounded-lg border-2 border-gray-100 bg-white shadow-md dark:border-neutral-700 dark:bg-neutral-800">
                        {/* Header: Patient Info Section */}
                        <div className="flex flex-col justify-between gap-4 border-b-2 border-gray-50 bg-gray-50/30 p-8 md:flex-row md:items-center dark:bg-neutral-700/30">
                            <div className="flex items-center gap-5">
                                <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-rose-600 text-white shadow-xl shadow-rose-100">
                                    <UserIcon className="h-7 w-7" />
                                </div>
                                <div>
                                    <h3 className="text-2xl font-black tracking-tight text-gray-900 dark:text-neutral-100">
                                        {patient.first_name} {patient.last_name}
                                    </h3>
                                    <p className="text-[10px] font-bold tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.admission.id')}:{' '}
                                        <span className="text-rose-500">
                                            {patient.patient_uid}
                                        </span>{' '}
                                        • {patient.age}{' '}
                                        {t('page.admission.yrs')} •{' '}
                                        {patient.gender}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3 rounded-xl border-2 border-gray-100 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-800">
                                <ClockIcon className="h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                <div className="flex flex-col">
                                    <label className="text-[9px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.admission.current_status')}
                                    </label>
                                    <span className="text-[10px] font-bold text-amber-600 uppercase">
                                        {t('page.admission.awaiting_bed')}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Form Body */}
                        <div className="space-y-10 p-10">
                            {/* Row 1: Bed & Doctor */}
                            <div className="grid grid-cols-1 gap-8 lg:grid-cols-2">
                                <div className="space-y-2">
                                    <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t(
                                            'page.admission.ward_bed_assignment',
                                        )}
                                    </label>
                                    <div className="relative">
                                        <MapPinIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                        <select
                                            value={data.bed_id}
                                            onChange={(e) =>
                                                setData(
                                                    'bed_id',
                                                    e.target.value,
                                                )
                                            }
                                            className={inputStyle(
                                                errors.bed_id,
                                            )}
                                        >
                                            <option value="">
                                                {t('page.admission.select_bed')}
                                            </option>
                                            {availableBeds.map((bed) => (
                                                <option
                                                    key={bed.id}
                                                    value={bed.id}
                                                >
                                                    {bed.ward?.name} - Bed{' '}
                                                    {bed.code}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    {errors.bed_id && (
                                        <p className="ml-1 text-[10px] font-bold text-red-500">
                                            {errors.bed_id}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t(
                                            'page.admission.attending_physician',
                                        )}
                                    </label>
                                    <div className="relative">
                                        <UserGroupIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                        <select
                                            value={data.attending_doctor_id}
                                            onChange={(e) =>
                                                setData(
                                                    'attending_doctor_id',
                                                    e.target.value,
                                                )
                                            }
                                            className={inputStyle(
                                                errors.attending_doctor_id,
                                            )}
                                        >
                                            <option value="">
                                                {t(
                                                    'page.admission.select_doctor',
                                                )}
                                            </option>
                                            {doctors.map((doc) => (
                                                <option
                                                    key={doc.id}
                                                    value={doc.id}
                                                >
                                                    Dr. {doc.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {/* Row 2: Date & Fees */}
                            <div className="grid grid-cols-1 gap-8 border-t-2 border-gray-50 pt-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.admission.admission_date')}
                                    </label>
                                    <div className="relative">
                                        <CalendarIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                        <input
                                            type="date"
                                            value={data.admission_date}
                                            onChange={(e) =>
                                                setData(
                                                    'admission_date',
                                                    e.target.value,
                                                )
                                            }
                                            className={inputStyle(
                                                errors.admission_date,
                                            )}
                                        />
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t(
                                            'page.admission.observation_deposit',
                                        )}
                                    </label>
                                    <div className="relative">
                                        <BanknotesIcon className="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                                        <input
                                            type="number"
                                            value={data.observation_fee}
                                            onChange={(e) =>
                                                setData(
                                                    'observation_fee',
                                                    e.target.value,
                                                )
                                            }
                                            className={inputStyle(
                                                errors.observation_fee,
                                            )}
                                            placeholder="5000"
                                        />
                                    </div>
                                </div>
                            </div>

                            {/* Row 3: Reason */}
                            <div className="space-y-2 border-t-2 border-gray-50 pt-4">
                                <label className="ml-1 text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.admission.reason_for_admission')}
                                </label>
                                <div className="relative">
                                    <PencilSquareIcon className="absolute top-4 left-4 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                    <textarea
                                        rows={3}
                                        value={data.reason_for_admission}
                                        onChange={(e) =>
                                            setData(
                                                'reason_for_admission',
                                                e.target.value,
                                            )
                                        }
                                        className="w-full resize-none rounded-lg border-2 border-gray-200 bg-white py-4 pr-4 pl-12 text-sm font-bold transition-all outline-none focus:border-rose-600 dark:border-neutral-700 dark:bg-neutral-800"
                                        placeholder={t(
                                            'page.admission.reason_placeholder',
                                        )}
                                    />
                                </div>
                                {errors.reason_for_admission && (
                                    <p className="ml-1 text-[10px] font-bold text-red-500">
                                        {errors.reason_for_admission}
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Action Bar */}
                    <div className="flex flex-col items-center justify-end gap-6 sm:flex-row">
                        <Link
                            href={route('patients.show', patient.id)}
                            className="flex w-full items-center justify-center gap-2 rounded-lg border-2 border-gray-200 bg-white px-10 py-5 font-black text-gray-500 transition-all hover:bg-gray-50 sm:w-auto dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-500 dark:hover:bg-neutral-700"
                        >
                            <ArrowLeftIcon className="h-5 w-5" />{' '}
                            {t('common.cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={processing}
                            className="flex w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-12 py-5 font-black text-white shadow-2xl transition-all hover:bg-black disabled:opacity-50 sm:w-auto dark:bg-neutral-800"
                        >
                            {processing ? (
                                <ArrowPathIcon className="h-6 w-6 animate-spin" />
                            ) : (
                                <CheckIcon className="h-6 w-6" />
                            )}
                            {t('page.admission.confirm_admission')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
