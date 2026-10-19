import {
    ClipboardDocumentCheckIcon,
    CheckIcon,
    ArrowPathIcon,
    ArrowLeftIcon,
    UserIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    BanknotesIcon,
    CalendarDaysIcon,
    ClockIcon,
    ChatBubbleBottomCenterTextIcon,
    XMarkIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import React, { useMemo, useState } from 'react';
import FormInput from '@/Components/FormInput';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Department {
    id: number;
    name: string;
}

interface Patient {
    id: number;
    first_name: string;
    last_name: string;
    patient_uid: string;
    phone?: string;
}

/**
 * Matches your User model:
 * doctors are Users with a department relationship
 */
interface DoctorUser {
    id: number;
    name: string;
    department_id: number;
    is_active: boolean;
    department?: Department;
}

interface Props {
    doctors: DoctorUser[];
    patients: Patient[];
}

export default function BookAppointment({ doctors, patients }: Props) {
    const { t } = useTranslation();
    const [patientSearch, setPatientSearch] = useState('');
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(
        null,
    );

    const { data, setData, post, processing, errors, reset } = useForm({
        patient_id: '' as string | number,
        doctor_id: '' as string | number,
        appointment_date: new Date().toISOString().split('T')[0],
        appointment_time: '', // Required for your Appointment Model
        reason_for_visit: '',
        consultation_fee: 2000,
    });

    const foundPatients = useMemo(() => {
        if (patientSearch.length < 2) {
            return [];
        }

        const term = patientSearch.toLowerCase();

        return patients.filter(
            (p) =>
                p.first_name.toLowerCase().includes(term) ||
                p.last_name.toLowerCase().includes(term) ||
                p.patient_uid.toLowerCase().includes(term),
        );
    }, [patientSearch, patients]);

    const handleSelectPatient = (patient: Patient) => {
        setSelectedPatient(patient);
        setData('patient_id', patient.id);
        setPatientSearch('');
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        // Updated route to match typical resource naming
        post(route('appointments.store'), {
            onSuccess: () => reset(),
        });
    };

    return (
        <AppLayout
            headerTitle={t('queueManagement')}
            headerSubtitle={t('assigningPatientsToStreams')}
        >
            <Head title={t('bookAppointment')} />

            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                {/* Breadcrumbs */}
                <PageBreadcrumb
                    items={[
                        { label: t('home') },
                        {
                            label: t('appointments'),
                            href: route('appointments.index'),
                        },
                        { label: t('newBooking') },
                    ]}
                />

                <form onSubmit={handleSubmit} className="space-y-8">
                    <PageSection>
                        {/* SECTION 1: Patient Selection */}
                        <div className="border-b border-line bg-surface-muted p-8">
                            <div className="flex items-center gap-5">
                                <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-accent text-ink-inverse shadow-float shadow-blue-100">
                                    <UserIcon className="h-7 w-7" />
                                </div>
                                <div className="flex-1">
                                    <h3 className="text-2xl font-black tracking-tight text-ink">
                                        {t('patientAssignment')}
                                    </h3>
                                    <p className="text-[10px] font-black tracking-widest text-ink-subtle uppercase">
                                        {t('step01IdentifyPatient')}
                                    </p>
                                </div>
                            </div>

                            <div className="mt-8">
                                {!selectedPatient ? (
                                    <div className="relative">
                                        <FormInput
                                            name="patient_search"
                                            label={t('searchDatabase')}
                                            icon={MagnifyingGlassIcon}
                                            placeholder={t(
                                                'enterNamePhoneOrPatientId',
                                            )}
                                            value={patientSearch}
                                            onChange={(value) =>
                                                setPatientSearch(value)
                                            }
                                        />

                                        {foundPatients.length > 0 && (
                                            <div className="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border-2 border-line bg-surface shadow-float">
                                                {foundPatients.map((p) => (
                                                    <button
                                                        key={p.id}
                                                        type="button"
                                                        onClick={() =>
                                                            handleSelectPatient(
                                                                p,
                                                            )
                                                        }
                                                        className="group flex w-full items-center justify-between border-b border-line px-6 py-4 text-left transition-colors last:border-0 hover:bg-accent-soft"
                                                    >
                                                        <div>
                                                            <p className="font-black text-ink group-hover:text-accent">
                                                                {p.first_name}{' '}
                                                                {p.last_name}
                                                            </p>
                                                            <p className="text-[10px] font-bold text-ink-subtle uppercase">
                                                                {p.patient_uid}
                                                            </p>
                                                        </div>
                                                        <CheckIcon className="h-5 w-5 text-accent-ink opacity-0 group-hover:opacity-100" />
                                                    </button>
                                                ))}
                                            </div>
                                        )}
                                        {errors.patient_id && (
                                            <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                                {errors.patient_id}
                                            </p>
                                        )}
                                    </div>
                                ) : (
                                    <div className="flex items-center justify-between rounded-xl border-2 border-accent/30 bg-accent-soft p-6">
                                        <div className="flex items-center gap-4">
                                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-surface text-accent-ink shadow-surface">
                                                <UserIcon className="h-6 w-6" />
                                            </div>
                                            <div>
                                                <p className="text-[9px] font-black text-accent uppercase">
                                                    {t('targetPatient')}
                                                </p>
                                                <h4 className="text-lg font-black text-ink">
                                                    {selectedPatient.first_name}{' '}
                                                    {selectedPatient.last_name}
                                                </h4>
                                                <p className="text-xs font-bold tracking-widest text-accent-ink">
                                                    {
                                                        selectedPatient.patient_uid
                                                    }
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setSelectedPatient(null);
                                                setData('patient_id', '');
                                            }}
                                            className="rounded-full p-2 text-accent transition-colors hover:bg-accent-soft"
                                        >
                                            <XMarkIcon className="h-6 w-6" />
                                        </button>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* SECTION 2: Doctor Selection */}
                        <div className="space-y-6 p-10">
                            <div className="flex items-center gap-3">
                                <UserGroupIcon className="h-5 w-5 text-accent" />
                                <h4 className="text-[10px] font-black tracking-widest text-accent uppercase">
                                    {t('step02AssignClinician')}
                                </h4>
                            </div>

                            <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                {doctors.map((doc) => {
                                    const isSelected =
                                        data.doctor_id === doc.id;

                                    return (
                                        <button
                                            key={doc.id}
                                            type="button"
                                            onClick={() =>
                                                setData('doctor_id', doc.id)
                                            }
                                            className={`relative rounded-xl border-2 p-6 text-left transition-all ${
                                                isSelected
                                                    ? 'translate-y-[-4px] border-ink bg-ink text-ink-inverse shadow-float'
                                                    : 'border-line bg-surface text-ink hover:border-accent/40'
                                            } `}
                                        >
                                            <div className="flex h-full flex-col justify-between">
                                                <div>
                                                    <p
                                                        className={`mb-1 text-[9px] font-black uppercase ${isSelected ? 'text-accent' : 'text-ink-subtle'}`}
                                                    >
                                                        {doc.department?.name ||
                                                            t(
                                                                'generalPractitioner',
                                                            )}
                                                    </p>
                                                    <h5 className="text-sm font-black tracking-tight">
                                                        Dr. {doc.name}
                                                    </h5>
                                                </div>
                                                {isSelected && (
                                                    <div className="bg-accent-soft0 absolute top-4 right-4 flex h-5 w-5 items-center justify-center rounded-full">
                                                        <CheckIcon className="h-3 w-3 stroke-[4px] text-ink-inverse" />
                                                    </div>
                                                )}
                                            </div>
                                        </button>
                                    );
                                })}
                            </div>
                            {errors.doctor_id && (
                                <p className="mt-1 ml-1 text-xs font-bold text-red-500">
                                    {errors.doctor_id}
                                </p>
                            )}
                        </div>

                        {/* SECTION 3: Billing & Logistics */}
                        <div className="space-y-8 border-t border-line bg-surface-muted p-10">
                            <div className="flex items-center gap-3">
                                <BanknotesIcon className="h-5 w-5 text-accent" />
                                <h4 className="text-[10px] font-black tracking-widest text-accent uppercase">
                                    {t('step03BillingLogistics')}
                                </h4>
                            </div>

                            <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                                {/* Consultation Fee */}
                                <FormInput
                                    name="consultation_fee"
                                    label={t('feeFcfa')}
                                    type="number"
                                    icon={BanknotesIcon}
                                    value={data.consultation_fee}
                                    onChange={(value) =>
                                        setData(
                                            'consultation_fee',
                                            value === '' ? 0 : parseInt(value),
                                        )
                                    }
                                    errors={errors}
                                    step="1"
                                    min="0"
                                />

                                {/* Appointment Date */}
                                <FormInput
                                    name="appointment_date"
                                    label={t('date')}
                                    type="date"
                                    icon={CalendarDaysIcon}
                                    value={data.appointment_date}
                                    onChange={(value) =>
                                        setData('appointment_date', value)
                                    }
                                    errors={errors}
                                />

                                {/* Appointment Time */}
                                <FormInput
                                    name="appointment_time"
                                    label={t('time')}
                                    type="time"
                                    icon={ClockIcon}
                                    value={data.appointment_time}
                                    onChange={(value) =>
                                        setData('appointment_time', value)
                                    }
                                    errors={errors}
                                />

                                {/* Reason for Visit */}
                                <FormInput
                                    name="reason_for_visit"
                                    label={t('reason')}
                                    icon={ChatBubbleBottomCenterTextIcon}
                                    placeholder={t('checkup')}
                                    value={data.reason_for_visit}
                                    onChange={(value) =>
                                        setData('reason_for_visit', value)
                                    }
                                    errors={errors}
                                />
                            </div>
                        </div>
                    </PageSection>

                    {/* Action Bar */}
                    <div className="flex flex-col items-center justify-end gap-6 sm:flex-row">
                        <Link
                            href={route('appointments.index')}
                            className="flex w-full items-center justify-center gap-2 rounded-xl border border-line bg-surface px-8 py-4 font-bold text-ink-subtle transition-all hover:bg-surface-hover sm:w-auto"
                        >
                            <ArrowLeftIcon className="h-5 w-5" /> {t('cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={processing}
                            className="flex w-full items-center justify-center gap-2 rounded-xl bg-accent px-10 py-4 font-bold text-ink-inverse shadow-raised transition-all hover:bg-accent-hover disabled:opacity-50 sm:w-auto"
                        >
                            {processing ? (
                                <ArrowPathIcon className="h-6 w-6 animate-spin" />
                            ) : (
                                <ClipboardDocumentCheckIcon className="h-6 w-6" />
                            )}
                            {t('confirmAndQueuePatient')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
