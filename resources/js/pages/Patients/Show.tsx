import {
    HomeIcon,
    ChevronRightIcon,
    UserIcon,
    MapPinIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    BanknotesIcon,
    PencilSquareIcon,
    ArrowLeftIcon,
    CheckBadgeIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon,
    ClockIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Admission {
    id: number;
    status: 'Admitted' | 'Discharged' | 'Pending';
    admission_date: string;
    discharge_date: string | null;
    reason_for_admission: string;
    observation_fee: number;
    patient: {
        id: number;
        first_name: string;
        last_name: string;
        patient_uid: string;
        age: number;
        gender: string;
    };
    bed: { code: string; ward: any; bed_type: any };
    attending_doctor: { name: string };
}

export default function AdmissionShow({ admission }: { admission: Admission }) {
    const { t } = useTranslation();
    const [confirmDischarge, setConfirmDischarge] = useState(false);
    const { post, processing } = useForm({});

    const handleDischarge = () => {
        post(route('admissions.discharge', admission.id), {
            onSuccess: () => setConfirmDischarge(false),
        });
    };

    return (
        <AppLayout
            headerTitle={t('page.show.title')}
            headerSubtitle={t('page.show.subtitle', {
                firstName: admission.patient.first_name,
                lastName: admission.patient.last_name,
            })}
        >
            <Head
                title={t('page.show.head_title', {
                    firstName: admission.patient.first_name,
                })}
            />

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
                            <Link href={route('admissions.index')}>
                                {t('page.show.admissions_registry')}
                            </Link>
                        </li>
                        <li>
                            <ChevronRightIcon className="h-3 w-3" />
                        </li>
                        <li className="text-rose-600">
                            {t('page.show.dossier', { id: admission.id })}
                        </li>
                    </ol>
                </nav>

                {/* Main Content Grid */}
                <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    {/* LEFT COLUMN: Patient & Clinical Info */}
                    <div className="space-y-8 lg:col-span-2">
                        {/* Patient Identity Card */}
                        <div className="overflow-hidden rounded-xl border-2 border-gray-100 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <div className="flex items-center justify-between border-b-2 border-gray-50 bg-gray-50/30 p-8 dark:bg-neutral-700/30">
                                <div className="flex items-center gap-6">
                                    <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-900 text-2xl font-black text-white shadow-xl dark:bg-neutral-800">
                                        {admission.patient.first_name[0]}
                                        {admission.patient.last_name[0]}
                                    </div>
                                    <div>
                                        <h3 className="text-2xl font-black tracking-tight text-gray-900 uppercase dark:text-neutral-100">
                                            {admission.patient.first_name}{' '}
                                            {admission.patient.last_name}
                                        </h3>
                                        <p className="text-[10px] font-black tracking-[0.2em] text-rose-500 uppercase">
                                            {t('page.show.uid')}:{' '}
                                            {admission.patient.patient_uid} •{' '}
                                            {admission.patient.age}Y •{' '}
                                            {admission.patient.gender}
                                        </p>
                                    </div>
                                </div>
                                <StatusBadge status={admission.status} />
                            </div>

                            <div className="space-y-8 p-8">
                                <div className="space-y-2">
                                    <label className="flex items-center gap-2 text-[10px] font-black tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                                        <PencilSquareIcon className="h-4 w-4 text-rose-500" />
                                        {t('page.show.reason_for_admission')}
                                    </label>
                                    <div className="rounded-xl border-2 border-gray-100 bg-gray-50 p-6 dark:border-neutral-700 dark:bg-neutral-700">
                                        <p className="text-sm leading-relaxed font-bold text-gray-700 italic dark:text-neutral-300">
                                            "{admission.reason_for_admission}"
                                        </p>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 gap-8 pt-4 md:grid-cols-2">
                                    <DetailItem
                                        icon={<CalendarDaysIcon />}
                                        label={t('page.show.admission_date')}
                                        value={admission.admission_date}
                                    />
                                    <DetailItem
                                        icon={<UserGroupIcon />}
                                        label={t(
                                            'page.show.attending_physician',
                                        )}
                                        value={`Dr. ${admission.attending_doctor.name}`}
                                    />
                                    {admission.discharge_date && (
                                        <DetailItem
                                            icon={<CheckBadgeIcon />}
                                            label={t(
                                                'page.show.discharge_date',
                                            )}
                                            value={admission.discharge_date}
                                            color="text-rose-600"
                                        />
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* RIGHT COLUMN: Location & Actions */}
                    <div className="space-y-8">
                        {/* Bed Location Card */}
                        <div className="space-y-6 rounded-xl border-2 border-gray-100 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                            <div className="flex items-center gap-3">
                                <MapPinIcon className="h-5 w-5 text-rose-500" />
                                <h4 className="text-[10px] font-black tracking-widest text-rose-500 uppercase">
                                    {t('page.show.ward_location')}
                                </h4>
                            </div>

                            <div className="space-y-4">
                                <div className="flex items-end justify-between border-b-2 border-gray-50 pb-4">
                                    <span className="text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.show.ward')}
                                    </span>
                                    <span className="text-sm font-black text-gray-900 dark:text-neutral-100">
                                        {admission?.bed?.ward.name}
                                    </span>
                                </div>
                                <div className="flex items-end justify-between border-b-2 border-gray-50 pb-4">
                                    <span className="text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.show.bed_code')}
                                    </span>
                                    <span className="text-sm font-black text-gray-900 dark:text-neutral-100">
                                        {admission?.bed?.code}
                                    </span>
                                </div>
                                <div className="flex items-end justify-between">
                                    <span className="text-[10px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.show.bed_type')}
                                    </span>
                                    <span className="text-sm font-black text-gray-900 dark:text-neutral-100">
                                        {admission?.bed?.bed_type.name}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Financial Snapshot */}
                        <div className="space-y-4 rounded-xl bg-gray-900 p-8 text-white shadow-xl shadow-gray-200">
                            <div className="flex items-center gap-3">
                                <BanknotesIcon className="h-5 w-5 text-rose-400" />
                                <h4 className="text-[10px] font-black tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                                    {t('page.show.deposit_fee')}
                                </h4>
                            </div>
                            <p className="text-3xl font-black tracking-tighter">
                                {new Intl.NumberFormat('fr-FR').format(
                                    admission.observation_fee,
                                )}{' '}
                                <span className="ml-1 text-xs tracking-widest text-rose-400 uppercase">
                                    FCFA
                                </span>
                            </p>
                            <p className="text-[9px] font-bold tracking-widest text-gray-500 uppercase dark:text-neutral-500">
                                {t('page.show.recorded_at_intake')}
                            </p>
                        </div>

                        {/* ACTIONS */}
                        <div className="space-y-4 pt-4">
                            {admission.status === 'Admitted' ? (
                                <button
                                    onClick={() => setConfirmDischarge(true)}
                                    className="flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600 py-5 text-[10px] font-black tracking-[0.2em] text-white uppercase shadow-lg shadow-rose-100 transition-all hover:bg-rose-700"
                                >
                                    <CheckBadgeIcon className="h-5 w-5" />{' '}
                                    {t('page.show.initiate_discharge')}
                                </button>
                            ) : (
                                <div className="rounded-xl border-2 border-blue-100 bg-blue-50 p-6 text-center">
                                    <p className="text-[10px] font-black tracking-widest text-blue-600 uppercase">
                                        {t('page.show.admission_closed')}
                                    </p>
                                </div>
                            )}

                            <Link
                                href={route('admissions.index')}
                                className="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-gray-100 bg-white py-5 text-[10px] font-black tracking-[0.2em] text-gray-400 uppercase transition-all hover:bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-500 dark:hover:bg-neutral-700"
                            >
                                <ArrowLeftIcon className="h-4 w-4" />{' '}
                                {t('page.show.return_to_registry')}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            {/* --- DISCHARGE CONFIRMATION MODAL --- */}
            {confirmDischarge && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div
                        className="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"
                        onClick={() => setConfirmDischarge(false)}
                    />
                    <div className="relative w-full max-w-md overflow-hidden rounded-2xl border-2 border-gray-100 bg-white shadow-2xl dark:border-neutral-700 dark:bg-neutral-800">
                        <div className="space-y-6 p-8 text-center">
                            <div className="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-2 border-rose-100 bg-rose-50">
                                <ExclamationTriangleIcon className="h-10 w-10 text-rose-600" />
                            </div>
                            <div>
                                <h4 className="text-xl font-black tracking-tight text-gray-900 uppercase dark:text-neutral-100">
                                    {t('page.show.confirm_discharge_title')}
                                </h4>
                                <p className="mt-2 px-4 text-[10px] font-black tracking-[0.2em] text-gray-400 uppercase dark:text-neutral-500">
                                    {t(
                                        'page.show.confirm_discharge_description',
                                    )}
                                </p>
                            </div>
                            <div className="grid grid-cols-2 gap-4 pt-4">
                                <button
                                    onClick={() => setConfirmDischarge(false)}
                                    className="rounded-lg bg-gray-50 py-4 text-[10px] font-black tracking-widest text-gray-400 uppercase dark:bg-neutral-700 dark:text-neutral-500"
                                >
                                    {t('common.cancel')}
                                </button>
                                <button
                                    onClick={handleDischarge}
                                    disabled={processing}
                                    className="flex items-center justify-center gap-2 rounded-lg bg-rose-600 py-4 text-[10px] font-black tracking-widest text-white uppercase"
                                >
                                    {processing ? (
                                        <ArrowPathIcon className="h-4 w-4 animate-spin" />
                                    ) : (
                                        t('page.show.confirm_discharge_button')
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}

// --- Internal Helper Components ---

function DetailItem({
    icon,
    label,
    value,
    color = 'text-gray-900 dark:text-neutral-100',
}: any) {
    return (
        <div className="flex items-start gap-4">
            <div className="rounded-lg border-2 border-gray-100 bg-gray-50 p-2.5 text-gray-400 dark:border-neutral-700 dark:bg-neutral-700 dark:text-neutral-500">
                {React.cloneElement(icon, { className: 'w-5 h-5' })}
            </div>
            <div>
                <p className="mb-1 text-[9px] font-black tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                    {label}
                </p>
                <p
                    className={`text-sm font-black tracking-tight uppercase ${color}`}
                >
                    {value}
                </p>
            </div>
        </div>
    );
}

function StatusBadge({ status }: { status: string }) {
    const styles: any = {
        Admitted: 'bg-rose-50 text-rose-600 border-rose-100',
        Discharged:
            'bg-gray-50 text-gray-400 dark:text-neutral-500 border-gray-100 dark:border-neutral-700',
    };
    return (
        <span
            className={`rounded-full border-2 px-4 py-1.5 text-[10px] font-black tracking-widest uppercase ${styles[status]}`}
        >
            {status}
        </span>
    );
}
