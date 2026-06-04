import {
    HomeIcon,
    ChevronRightIcon,
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
    XMarkIcon
} from '@heroicons/react/24/outline';
import { Head, Link, useForm } from '@inertiajs/react';
import React, { FormEvent, useMemo, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        patient_id: '' as string | number,
        doctor_id: '' as string | number,
        appointment_date: new Date().toISOString().split('T')[0],
        appointment_time: '', // Required for your Appointment Model
        reason_for_visit: '',
        consultation_fee: 2000,
    });

    const foundPatients = useMemo(() => {
        if (patientSearch.length < 2) return [];
        const term = patientSearch.toLowerCase();
        return patients.filter(p =>
            p.first_name.toLowerCase().includes(term) ||
            p.last_name.toLowerCase().includes(term) ||
            p.patient_uid.toLowerCase().includes(term)
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

    const inputStyle = (error: string | undefined) => `
        w-full pl-12 pr-4 py-4 bg-white dark:bg-neutral-800 border-2 rounded-lg
        font-bold text-sm transition-all outline-none
        ${error ? 'border-red-500 focus:border-red-600' : 'border-gray-200 dark:border-neutral-700 focus:border-rose-600 focus:ring-0'}
    `;

    return (
        <AppLayout
            headerTitle={t('queueManagement')}
            headerSubtitle={t('assigningPatientsToStreams')}
        >
            <Head title={t('bookAppointment')} />

            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 pb-20">

                {/* Breadcrumbs */}
                <nav className="flex text-[10px] font-black uppercase tracking-widest text-gray-400 dark:text-neutral-500">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-rose-600 cursor-pointer transition-colors">
                            <HomeIcon className="w-3.5 h-3.5 mr-1" /> {t('home')}
                        </li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="hover:text-rose-600 cursor-pointer transition-colors">{t('appointments')}</li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="text-rose-600">{t('newBooking')}</li>
                    </ol>
                </nav>

                <form onSubmit={handleSubmit} className="space-y-8">
                    <div className="bg-white dark:bg-neutral-800 rounded-lg border-2 border-gray-100 dark:border-neutral-700 shadow-md overflow-hidden">

                        {/* SECTION 1: Patient Selection */}
                        <div className="p-8 border-b-2 border-gray-50 bg-gray-50/30 dark:bg-neutral-700/30 dark:bg-neutral-700/30">
                            <div className="flex items-center gap-5">
                                <div className="h-14 w-14 rounded-xl bg-rose-600 flex items-center justify-center text-white shadow-xl shadow-rose-100 shrink-0">
                                    <UserIcon className="w-7 h-7" />
                                </div>
                                <div className="flex-1">
                                    <h3 className="text-2xl font-black text-gray-900 dark:text-neutral-100 tracking-tight">{t('patientAssignment')}</h3>
                                    <p className="text-[10px] font-black text-gray-400 dark:text-neutral-500 uppercase tracking-widest">{t('step01IdentifyPatient')}</p>
                                </div>
                            </div>

                            <div className="mt-8">
                                {!selectedPatient ? (
                                    <div className="relative">
                                        <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('searchDatabase')}</label>
                                        <div className="relative mt-2">
                                            <MagnifyingGlassIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                            <input
                                                type="text"
                                                className={inputStyle(errors.patient_id)}
                                                placeholder={t('enterNamePhoneOrPatientId')}
                                                value={patientSearch}
                                                onChange={(e) => setPatientSearch(e.target.value)}
                                            />
                                        </div>

                                        {foundPatients.length > 0 && (
                                            <div className="absolute z-20 mt-2 w-full bg-white dark:bg-neutral-800 border-2 border-gray-100 dark:border-neutral-700 rounded-xl shadow-2xl overflow-hidden">
                                                {foundPatients.map(p => (
                                                    <button
                                                        key={p.id}
                                                        type="button"
                                                        onClick={() => handleSelectPatient(p)}
                                                        className="w-full text-left px-6 py-4 hover:bg-rose-50 flex items-center justify-between border-b last:border-0 border-gray-50 group transition-colors"
                                                    >
                                                        <div>
                                                            <p className="font-black text-gray-900 dark:text-neutral-100 group-hover:text-rose-600">{p.first_name} {p.last_name}</p>
                                                            <p className="text-[10px] font-bold text-gray-400 dark:text-neutral-500 uppercase">{p.patient_uid}</p>
                                                        </div>
                                                        <CheckIcon className="w-5 h-5 text-rose-600 opacity-0 group-hover:opacity-100" />
                                                    </button>
                                                ))}
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <div className="flex items-center justify-between p-6 bg-rose-50 border-2 border-rose-100 rounded-xl">
                                        <div className="flex items-center gap-4">
                                            <div className="h-12 w-12 rounded-lg bg-white dark:bg-neutral-800 flex items-center justify-center text-rose-600 shadow-sm">
                                                <UserIcon className="w-6 h-6" />
                                            </div>
                                            <div>
                                                <p className="text-[9px] font-black uppercase text-rose-400">{t('targetPatient')}</p>
                                                <h4 className="text-lg font-black text-gray-900 dark:text-neutral-100">{selectedPatient.first_name} {selectedPatient.last_name}</h4>
                                                <p className="text-xs font-bold text-rose-600 tracking-widest">{selectedPatient.patient_uid}</p>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => {setSelectedPatient(null); setData('patient_id', '');}}
                                            className="p-2 hover:bg-rose-100 rounded-full text-rose-400 transition-colors"
                                        >
                                            <XMarkIcon className="w-6 h-6" />
                                        </button>
                                    </div>
                                )}
                                {errors.patient_id && <p className="text-[10px] font-bold text-red-500 mt-2 ml-1">{errors.patient_id}</p>}
                            </div>
                        </div>

                        {/* SECTION 2: Doctor Selection */}
                        <div className="p-10 space-y-6">
                            <div className="flex items-center gap-3">
                                <UserGroupIcon className="w-5 h-5 text-rose-500" />
                                <h4 className="text-[10px] font-black text-rose-500 uppercase tracking-widest">{t('step02AssignClinician')}</h4>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {doctors.map(doc => {
                                    const isSelected = data.doctor_id === doc.id;
                                    return (
                                        <button
                                            key={doc.id}
                                            type="button"
                                            onClick={() => setData('doctor_id', doc.id)}
                                            className={`
                                                relative p-6 rounded-xl border-2 text-left transition-all
                                                ${isSelected
                                                    ? 'border-gray-900 bg-gray-900 dark:bg-neutral-800 text-white shadow-xl translate-y-[-4px]'
                                                    : 'border-gray-100 dark:border-neutral-700 hover:border-rose-200 bg-white dark:bg-neutral-800 text-gray-900 dark:text-neutral-100'}
                                            `}
                                        >
                                            <div className="flex flex-col h-full justify-between">
                                                <div>
                                                    <p className={`text-[9px] font-black uppercase mb-1 ${isSelected ? 'text-rose-400' : 'text-gray-400 dark:text-neutral-500'}`}>
                                                        {doc.department?.name || t('generalPractitioner')}
                                                    </p>
                                                    <h5 className="font-black text-sm tracking-tight">Dr. {doc.name}</h5>
                                                </div>
                                                {isSelected && (
                                                    <div className="absolute top-4 right-4 h-5 w-5 bg-rose-500 rounded-full flex items-center justify-center">
                                                        <CheckIcon className="w-3 h-3 text-white stroke-[4px]" />
                                                    </div>
                                                )}
                                            </div>
                                        </button>
                                    );
                                })}
                            </div>
                            {errors.doctor_id && <p className="text-[10px] font-bold text-red-500 mt-1">{errors.doctor_id}</p>}
                        </div>

                        {/* SECTION 3: Billing & Logistics */}
                        <div className="p-10 bg-gray-50/50 dark:bg-neutral-700/50 dark:bg-neutral-700/50 border-t-2 border-gray-50 space-y-8">
                            <div className="flex items-center gap-3">
                                <BanknotesIcon className="w-5 h-5 text-rose-500" />
                                <h4 className="text-[10px] font-black text-rose-500 uppercase tracking-widest">{t('step03BillingLogistics')}</h4>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                                {/* Consultation Fee */}
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('feeFcfa')}</label>
                                    <div className="relative">
                                        <div className="absolute left-4 top-1/2 -translate-y-1/2 font-black text-gray-300 text-[10px] italic">{t('xaf')}</div>
                                        <input
                                            type="number"
                                            value={data.consultation_fee}
                                            onChange={(e) => setData('consultation_fee', parseInt(e.target.value))}
                                            className={inputStyle(errors.consultation_fee)}
                                            placeholder="2000"
                                        />
                                    </div>
                                </div>

                                {/* Appointment Date */}
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('date')}</label>
                                    <div className="relative">
                                        <CalendarDaysIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                        <input
                                            type="date"
                                            value={data.appointment_date}
                                            onChange={(e) => setData('appointment_date', e.target.value)}
                                            className={inputStyle(errors.appointment_date)}
                                        />
                                    </div>
                                </div>

                                {/* Appointment Time */}
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('time')}</label>
                                    <div className="relative">
                                        <ClockIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                        <input
                                            type="time"
                                            value={data.appointment_time}
                                            onChange={(e) => setData('appointment_time', e.target.value)}
                                            className={inputStyle(errors.appointment_time)}
                                        />
                                    </div>
                                </div>

                                {/* Reason for Visit */}
                                <div className="space-y-2">
                                    <label className="text-[10px] font-black uppercase text-gray-400 dark:text-neutral-500 ml-1">{t('reason')}</label>
                                    <div className="relative">
                                        <ChatBubbleBottomCenterTextIcon className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400 dark:text-neutral-500" />
                                        <input
                                            type="text"
                                            value={data.reason_for_visit}
                                            onChange={(e) => setData('reason_for_visit', e.target.value)}
                                            className={inputStyle(errors.reason_for_visit)}
                                            placeholder={t('checkup')}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Action Bar */}
                    <div className="flex flex-col sm:flex-row items-center justify-end gap-6">
                        <Link
                            href={route('appointments.index')}
                            className="w-full sm:w-auto px-10 py-5 bg-white dark:bg-neutral-800 border-2 border-gray-200 dark:border-neutral-700 rounded-lg font-black text-gray-500 dark:text-neutral-500 hover:bg-gray-50 dark:hover:bg-neutral-700 transition-all flex items-center justify-center gap-2"
                        >
                            <ArrowLeftIcon className="w-5 h-5" /> {t('cancel')}
                        </Link>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full sm:w-auto px-12 py-5 bg-gray-900 dark:bg-neutral-800 text-white rounded-lg font-black hover:bg-black transition-all shadow-2xl flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            {processing ? (
                                <ArrowPathIcon className="animate-spin h-6 w-6" />
                            ) : (
                                <ClipboardDocumentCheckIcon className="w-6 h-6" />
                            )}
                            {t('confirmAndQueuePatient')}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
