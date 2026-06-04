import { Head, Link } from '@inertiajs/react';
import { PencilSquareIcon, ArrowLeftIcon } from '@heroicons/react/24/outline';
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface Surgery {
    id: number;
    patient?: { id: number; first_name: string; last_name: string };
    surgeon?: { id: number; name: string };
    anesthetist?: { id: number; name: string } | null;
    bed?: { id: number; name: string; ward?: { name: string } } | null;
    surgery_name: string;
    description?: string;
    scheduled_at: string;
    completed_at?: string;
    estimated_duration_minutes?: number;
    urgency: string;
    status: string;
    surgery_fee: number;
    notes?: string;
    complications?: string;
}

interface Props { surgery: Surgery }

export default function SurgeryShow({ surgery }: Props) {
    const { t } = useTranslation();
    return (
        <AppLayout headerTitle={surgery.surgery_name} headerSubtitle={t('surgeries.show.subtitle')}>
            <Head title={t('surgeries.show.head_title', { name: surgery.surgery_name })} />
            <div className="max-w-3xl mx-auto space-y-6">
                <div className="flex items-center gap-4">
                    <Link href={route('surgeries.index')} className="flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600"><ArrowLeftIcon className="w-4 h-4" />{t('common.back')}</Link>
                    <Link href={route('surgeries.edit', surgery.id)} className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 ml-auto"><PencilSquareIcon className="w-4 h-4" />{t('common.edit')}</Link>
                </div>

                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('surgeries.show.patient')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{surgery.patient?.first_name} {surgery.patient?.last_name}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('surgeries.show.surgeon')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{surgery.surgeon?.name}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('surgeries.show.anesthetist')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{surgery.anesthetist?.name || t('common.na')}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('surgeries.show.bed_ward')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{surgery.bed?.name || t('common.na')} {surgery.bed?.ward?.name && `(${surgery.bed.ward.name})`}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('surgeries.show.scheduled')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{new Date(surgery.scheduled_at).toLocaleString()}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('surgeries.show.status_urgency')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100 capitalize">{surgery.status.replace('_', ' ')} — {surgery.urgency}</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('surgeries.show.fee')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{surgery.surgery_fee.toLocaleString()} FCFA</p>
                        </div>
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase">{t('surgeries.show.duration')}</p>
                            <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{surgery.estimated_duration_minutes ? t('surgeries.show.duration_minutes', { minutes: surgery.estimated_duration_minutes }) : t('common.na')}</p>
                        </div>
                    </div>
                    {surgery.notes && (
                        <div className="p-4 bg-gray-50 dark:bg-neutral-700/50 rounded-xl">
                            <p className="text-[10px] font-bold text-gray-400 uppercase mb-1">{t('surgeries.show.notes')}</p>
                            <p className="text-sm text-gray-700 dark:text-neutral-300">{surgery.notes}</p>
                        </div>
                    )}
                    {surgery.complications && (
                        <div className="p-4 bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-100 dark:border-red-800">
                            <p className="text-[10px] font-bold text-red-500 uppercase mb-1">{t('surgeries.show.complications')}</p>
                            <p className="text-sm text-red-700 dark:text-red-400">{surgery.complications}</p>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
