import { Head, Link } from '@inertiajs/react';
import { ArrowLeftIcon, PencilSquareIcon } from '@heroicons/react/24/outline';
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Emergency {
    id: number;
    patient?: { id: number; first_name: string; last_name: string };
    attending_doctor?: { id: number; name: string } | null;
    triage_nurse?: { id: number; name: string } | null;
    admission?: { id: number; reason_for_admission: string } | null;
    arrival_mode?: string;
    chief_complaint: string;
    primary_assessment?: string;
    triage_level: string;
    status: string;
    disposition?: string;
    arrived_at: string;
    triaged_at?: string;
    stabilized_at?: string;
    discharged_at?: string;
    emergency_fee: number;
    treatment_notes?: string;
    discharge_instructions?: string;
}

interface Props { emergency: Emergency }

export default function EmergencyShow({ emergency }: Props) {
    const { t } = useTranslation();
    return (
        <AppLayout headerTitle={`${t('emergency')} #${emergency.id}`} headerSubtitle={t('caseDetails')}>
            <Head title={`${t('emergency')} #${emergency.id}`} />
            <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex items-center gap-4">
                    <Link href={route('emergencies.index')} className="flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600"><ArrowLeftIcon className="w-4 h-4" />{t('back')}</Link>
                    <Link href={route('emergencies.edit', emergency.id)} className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 ml-auto"><PencilSquareIcon className="w-4 h-4" />{t('edit')}</Link>
                </div>

                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 space-y-4">
                    <div className="flex items-center gap-3">
                        <span className={`px-3 py-1 rounded-lg text-xs font-bold uppercase border ${
                            emergency.triage_level === 'emergency' ? 'bg-red-50 dark:bg-red-900/20 text-red-700 border-red-200' :
                            emergency.triage_level === 'urgent' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 border-amber-200' :
                            'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 border-emerald-200'
                        }`}>{emergency.triage_level}</span>
                        <span className={`px-3 py-1 rounded-lg text-xs font-bold uppercase border bg-blue-50 dark:bg-blue-900/20 text-blue-700 border-blue-200`}>{emergency.status}</span>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('patient')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{emergency.patient?.first_name} {emergency.patient?.last_name}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('arrivalMode')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{emergency.arrival_mode || t('na')}</p>
                        </div>
                    </div>

                    <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                        <p className="text-[10px] font-bold text-gray-400 uppercase">{t('chiefComplaint')}</p>
                        <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{emergency.chief_complaint}</p>
                    </div>

                    {emergency.primary_assessment && (
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('assessment')}</p>
                            <p className="text-sm text-gray-700 dark:text-neutral-300">{emergency.primary_assessment}</p>
                        </div>
                    )}

                    <div className="grid grid-cols-2 gap-4">
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('doctor')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{emergency.attending_doctor?.name || t('unassigned')}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('triageNurse')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{emergency.triage_nurse?.name || t('unassigned')}</p>
                        </div>
                    </div>

                    {emergency.treatment_notes && (
                        <div className="p-4 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-800">
                            <p className="text-[10px] font-bold text-blue-500 uppercase">{t('treatmentNotes')}</p>
                            <p className="text-sm text-blue-700 dark:text-blue-400">{emergency.treatment_notes}</p>
                        </div>
                    )}

                    {emergency.discharge_instructions && (
                        <div className="p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-800">
                            <p className="text-[10px] font-bold text-emerald-500 uppercase">{t('dischargeInstructions')}</p>
                            <p className="text-sm text-emerald-700 dark:text-emerald-400">{emergency.discharge_instructions}</p>
                        </div>
                    )}

                    <div className="grid grid-cols-2 gap-4">
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('fee')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{emergency.emergency_fee.toLocaleString()} FCFA</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('disposition')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{emergency.disposition || t('pending')}</p>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
