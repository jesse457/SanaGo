import { MagnifyingGlassIcon, UserCircleIcon } from '@heroicons/react/24/outline';
import { Head, useForm, Link } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

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
    const [selectedPatient, setSelectedPatient] = useState<Patient | null>(null);

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

    const filteredPatients = useMemo(() => {
        if (!patientQuery.trim()) {
return patients.slice(0, 10);
}

        const q = patientQuery.toLowerCase();

        return patients.filter(p =>
            `${p.first_name} ${p.last_name}`.toLowerCase().includes(q) ||
            p.patient_uid.toLowerCase().includes(q)
        ).slice(0, 10);
    }, [patients, patientQuery]);

    const selectPatient = (p: Patient) => {
        setSelectedPatient(p);
        setData('patient_id', String(p.id));
        setPatientQuery(`${p.first_name} ${p.last_name} (${p.patient_uid})`);
        setShowPatientDropdown(false);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('emergencies.store'));
    };

    return (
        <AppLayout headerTitle={t('newEmergency')} headerSubtitle={t('registerEmergencyCase')}>
            <Head title={t('newEmergency')} />
            <div className="max-w-3xl mx-auto">
                <form onSubmit={handleSubmit} className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-8 space-y-6">
                    <div className="grid grid-cols-2 gap-6">
                        {/* Patient Search (replaces raw ID input) */}
                        <div className="col-span-2">
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('patient')}</label>
                            <div className="relative mt-1.5">
                                <MagnifyingGlassIcon className="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" />
                                <input
                                    type="text"
                                    value={patientQuery}
                                    onChange={e => {
 setPatientQuery(e.target.value); setShowPatientDropdown(true); 
}}
                                    onFocus={() => setShowPatientDropdown(true)}
                                    onBlur={() => setTimeout(() => setShowPatientDropdown(false), 200)}
                                    placeholder={t('searchPatientNameOrUid')}
                                    className="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold"
                                />
                                {showPatientDropdown && filteredPatients.length > 0 && (
                                    <div className="absolute z-10 mt-1 w-full bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                        {filteredPatients.map(p => (
                                            <button
                                                key={p.id}
                                                type="button"
                                                onClick={() => selectPatient(p)}
                                                className="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors border-b border-gray-50 dark:border-neutral-700 last:border-0"
                                            >
                                                <UserCircleIcon className="w-5 h-5 text-gray-400" />
                                                <div>
                                                    <p className="text-sm font-semibold text-gray-900 dark:text-neutral-100">{p.first_name} {p.last_name}</p>
                                                    <p className="text-xs text-gray-500">{p.patient_uid} &middot; {p.gender} &middot; {p.date_of_birth}</p>
                                                </div>
                                            </button>
                                        ))}
                                    </div>
                                )}
                            </div>
                            {selectedPatient && (
                                <p className="mt-2 text-xs text-blue-600">
                                    <Link href={`/patients/${selectedPatient.id}`} className="hover:underline">{t('viewProfile')} {selectedPatient.first_name}{t('sProfile')}</Link>
                                </p>
                            )}
                            <input type="hidden" value={data.patient_id} />
                            {errors.patient_id && <p className="mt-1 text-xs text-red-500">{errors.patient_id}</p>}
                        </div>

                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('triageLevel')}</label>
                            <select value={data.triage_level} onChange={e => setData('triage_level', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold">
                                <option value="normal">{t('normal')}</option>
                                <option value="urgent">{t('urgent')}</option>
                                <option value="emergency">{t('emergency')}</option>
                            </select>
                        </div>
                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('doctorId')}</label>
                            <input type="number" value={data.attending_doctor_id} onChange={e => setData('attending_doctor_id', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" />
                        </div>
                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('triageNurseId')}</label>
                            <input type="number" value={data.triage_nurse_id} onChange={e => setData('triage_nurse_id', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" />
                        </div>
                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('arrivalMode')}</label>
                            <input type="text" value={data.arrival_mode} onChange={e => setData('arrival_mode', e.target.value)} placeholder={t('ambulanceWalkIn')} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" />
                        </div>
                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('arrivalDateTime')}</label>
                            <input type="datetime-local" value={data.arrived_at} onChange={e => setData('arrived_at', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" required />
                        </div>
                        <div className="col-span-2">
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('chiefComplaint')}</label>
                            <input type="text" value={data.chief_complaint} onChange={e => setData('chief_complaint', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" required />
                        </div>
                        <div className="col-span-2">
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('primaryAssessment')}</label>
                            <textarea value={data.primary_assessment} onChange={e => setData('primary_assessment', e.target.value)} rows={3} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" />
                        </div>
                        <div>
                            <label className="text-xs font-semibold text-gray-500 uppercase ml-1">{t('feeFcfa')}</label>
                            <input type="number" step="0.01" value={data.emergency_fee} onChange={e => setData('emergency_fee', e.target.value)} className="w-full mt-1.5 px-4 py-3 bg-gray-50 dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-xl text-sm font-bold" />
                        </div>
                    </div>
                    <div className="flex gap-4 pt-4">
                        <button type="button" onClick={() => window.history.back()} className="flex-1 py-3 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-xl font-bold text-gray-500">{t('cancel')}</button>
                        <button type="submit" disabled={processing} className="flex-[2] py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700">{processing ? t('saving') : t('registerEmergency')}</button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
