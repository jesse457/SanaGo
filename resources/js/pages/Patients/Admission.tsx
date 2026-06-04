import {
    UserIcon,
    CalendarIcon,
    PencilSquareIcon,
    BanknotesIcon,
    ArrowPathIcon,
    CheckIcon,
    MapPinIcon,
    ClockIcon,
    UserGroupIcon,
    ArrowLeftIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React from 'react';
import FormInput from '@/Components/FormInput';
import FormSelect from '@/Components/FormSelect';
import FormTextarea from '@/Components/FormTextarea';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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

    return (
        <AppLayout
            headerTitle={t('page.admission.title')}
            headerSubtitle={t('page.admission.subtitle', {
                firstName: patient.first_name,
                lastName: patient.last_name,
            })}
        >
            <Head title={t('page.admission.head_title')} />

            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('common.home') },
                        { label: t('page.admission.breadcrumb_admissions') },
                        { label: t('page.admission.new_entry') },
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-6">
                    <PageSection>
                        {/* Header: Patient Info Section */}
                        <div className="flex flex-col justify-between gap-4 border-b-2 border-gray-50 bg-gray-50/30 p-8 md:flex-row md:items-center dark:bg-neutral-700/30">
                            <div className="flex items-center gap-5">
                                <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-xl shadow-blue-100">
                                    <UserIcon className="h-7 w-7" />
                                </div>
                                <div>
                                    <h3 className="text-2xl font-black tracking-tight text-gray-900 dark:text-neutral-100">
                                        {patient.first_name} {patient.last_name}
                                    </h3>
                                    <p className="text-[10px] font-bold tracking-widest text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.admission.id')}:{' '}
                                        <span className="text-blue-500">
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
                                    <span className="text-[9px] font-black text-gray-400 uppercase dark:text-neutral-500">
                                        {t('page.admission.current_status')}
                                    </span>
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
                                <FormSelect
                                    name="bed_id"
                                    label={t(
                                        'page.admission.ward_bed_assignment',
                                    )}
                                    icon={MapPinIcon}
                                    value={data.bed_id}
                                    onChange={(v) => setData('bed_id', v)}
                                    errors={errors}
                                    placeholder={t('page.admission.select_bed')}
                                    options={availableBeds.map((bed) => ({
                                        value: bed.id,
                                        label: `${bed.ward?.name} - Bed ${bed.code}`,
                                    }))}
                                />

                                <FormSelect
                                    name="attending_doctor_id"
                                    label={t(
                                        'page.admission.attending_physician',
                                    )}
                                    icon={UserGroupIcon}
                                    value={data.attending_doctor_id}
                                    onChange={(v) =>
                                        setData('attending_doctor_id', v)
                                    }
                                    errors={errors}
                                    placeholder={t(
                                        'page.admission.select_doctor',
                                    )}
                                    options={doctors.map((doc) => ({
                                        value: doc.id,
                                        label: `Dr. ${doc.name}`,
                                    }))}
                                />
                            </div>

                            {/* Row 2: Date & Fees */}
                            <div className="grid grid-cols-1 gap-8 border-t-2 border-gray-50 pt-4 md:grid-cols-2">
                                <FormInput
                                    name="admission_date"
                                    label={t('page.admission.admission_date')}
                                    icon={CalendarIcon}
                                    type="date"
                                    value={data.admission_date}
                                    onChange={(v) =>
                                        setData('admission_date', v)
                                    }
                                    errors={errors}
                                />

                                <FormInput
                                    name="observation_fee"
                                    label={t(
                                        'page.admission.observation_deposit',
                                    )}
                                    icon={BanknotesIcon}
                                    type="number"
                                    value={data.observation_fee}
                                    onChange={(v) =>
                                        setData(
                                            'observation_fee',
                                            parseInt(v) || 0,
                                        )
                                    }
                                    errors={errors}
                                    placeholder="5000"
                                />
                            </div>

                            {/* Row 3: Reason */}
                            <FormTextarea
                                name="reason_for_admission"
                                label={t('page.admission.reason_for_admission')}
                                icon={PencilSquareIcon}
                                value={data.reason_for_admission}
                                onChange={(v) =>
                                    setData('reason_for_admission', v)
                                }
                                placeholder={t(
                                    'page.admission.reason_placeholder',
                                )}
                                errors={errors}
                                rows={3}
                                className="border-t-2 border-gray-50 pt-4"
                            />
                        </div>
                    </PageSection>

                    {/* Action Bar */}
                    <div className="flex flex-col items-center justify-end gap-6 sm:flex-row">
                        <Link
                            href={route('patients.show', patient.id)}
                            className="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-gray-200 bg-white px-10 py-5 font-black text-gray-500 transition-all hover:bg-gray-50 sm:w-auto dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-500 dark:hover:bg-neutral-700"
                        >
                            <ArrowLeftIcon className="h-5 w-5" />{' '}
                            {t('common.cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={processing}
                            className="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-12 py-5 font-black text-white shadow-2xl transition-all hover:bg-blue-700 disabled:opacity-50 sm:w-auto"
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
