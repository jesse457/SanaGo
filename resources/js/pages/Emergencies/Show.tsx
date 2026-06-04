import {
    ArrowLeftIcon,
    PencilSquareIcon,
    UserIcon,
    MapPinIcon,
    DocumentTextIcon,
    ClipboardDocumentListIcon,
    CurrencyDollarIcon,
    ArrowPathIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import React from 'react';
import DetailItem from '@/Components/DetailItem';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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

interface Props {
    emergency: Emergency;
}

const getTriageColor = (
    level: string,
): 'red' | 'amber' | 'emerald' | 'blue' => {
    const colors = {
        emergency: 'red' as const,
        urgent: 'amber' as const,
        non_urgent: 'emerald' as const,
    };

    return colors[level.toLowerCase() as keyof typeof colors] || 'blue';
};

const getStatusColor = (
    status: string,
): 'red' | 'amber' | 'emerald' | 'blue' => {
    const colors = {
        active: 'red' as const,
        stabilized: 'amber' as const,
        discharged: 'emerald' as const,
        triage: 'blue' as const,
    };

    return colors[status.toLowerCase() as keyof typeof colors] || 'blue';
};

export default function EmergencyShow({ emergency }: Props) {
    const { t } = useTranslation();

    return (
        <AppLayout
            headerTitle={`${t('emergency')} #${emergency.id}`}
            headerSubtitle={t('caseDetails')}
        >
            <Head title={`${t('emergency')} #${emergency.id}`} />
            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('home'), href: route('dashboard') },
                        {
                            label: t('emergencies'),
                            href: route('emergencies.index'),
                        },
                        { label: `${t('emergency')} #${emergency.id}` },
                    ]}
                />

                <div className="flex items-center gap-4">
                    <Link
                        href={route('emergencies.index')}
                        className="flex items-center gap-2 text-sm text-gray-500 transition-colors hover:text-blue-600 dark:text-neutral-400 dark:hover:text-blue-400"
                    >
                        <ArrowLeftIcon className="h-4 w-4" />
                        {t('back')}
                    </Link>
                    <Link
                        href={route('emergencies.edit', emergency.id)}
                        className="ml-auto flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white transition-all hover:bg-blue-700"
                    >
                        <PencilSquareIcon className="h-4 w-4" />
                        {t('edit')}
                    </Link>
                </div>

                <PageSection>
                    <div className="space-y-6 p-6">
                        {/* Badges */}
                        <div className="flex items-center gap-3">
                            <StatusBadge
                                label={emergency.triage_level}
                                color={getTriageColor(emergency.triage_level)}
                            />
                            <StatusBadge
                                label={emergency.status}
                                color={getStatusColor(emergency.status)}
                            />
                        </div>

                        {/* Core Details */}
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <DetailItem
                                icon={UserIcon}
                                label={t('patient')}
                                value={`${emergency.patient?.first_name || ''} ${emergency.patient?.last_name || ''}`}
                            />
                            <DetailItem
                                icon={MapPinIcon}
                                label={t('arrivalMode')}
                                value={emergency.arrival_mode || t('na')}
                            />
                        </div>

                        <div className="grid grid-cols-1 gap-4">
                            <DetailItem
                                icon={DocumentTextIcon}
                                label={t('chiefComplaint')}
                                value={emergency.chief_complaint}
                            />
                        </div>

                        {emergency.primary_assessment && (
                            <div className="grid grid-cols-1 gap-4">
                                <DetailItem
                                    icon={ClipboardDocumentListIcon}
                                    label={t('assessment')}
                                    value={emergency.primary_assessment}
                                />
                            </div>
                        )}

                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <DetailItem
                                icon={UserIcon}
                                label={t('doctor')}
                                value={
                                    emergency.attending_doctor?.name ||
                                    t('unassigned')
                                }
                            />
                            <DetailItem
                                icon={UserIcon}
                                label={t('triageNurse')}
                                value={
                                    emergency.triage_nurse?.name ||
                                    t('unassigned')
                                }
                            />
                        </div>

                        {/* Treatment Notes - clinically significant, blue accent */}
                        {emergency.treatment_notes && (
                            <div className="rounded-xl border border-blue-100 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/10">
                                <div className="flex items-center gap-2">
                                    <DocumentTextIcon className="h-4 w-4 text-blue-500 dark:text-blue-400" />
                                    <span className="text-[10px] font-bold text-blue-500 uppercase dark:text-blue-400">
                                        {t('treatmentNotes')}
                                    </span>
                                </div>
                                <p className="mt-1 ml-6 text-sm font-bold text-blue-700 dark:text-blue-400">
                                    {emergency.treatment_notes}
                                </p>
                            </div>
                        )}

                        {/* Discharge Instructions - clinically significant, green accent */}
                        {emergency.discharge_instructions && (
                            <div className="rounded-xl border border-emerald-100 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/10">
                                <div className="flex items-center gap-2">
                                    <DocumentTextIcon className="h-4 w-4 text-emerald-500 dark:text-emerald-400" />
                                    <span className="text-[10px] font-bold text-emerald-500 uppercase dark:text-emerald-400">
                                        {t('dischargeInstructions')}
                                    </span>
                                </div>
                                <p className="mt-1 ml-6 text-sm font-bold text-emerald-700 dark:text-emerald-400">
                                    {emergency.discharge_instructions}
                                </p>
                            </div>
                        )}

                        {/* Financial & Disposition */}
                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <DetailItem
                                icon={CurrencyDollarIcon}
                                label={t('fee')}
                                value={`${emergency.emergency_fee.toLocaleString()} FCFA`}
                            />
                            <DetailItem
                                icon={ArrowPathIcon}
                                label={t('disposition')}
                                value={emergency.disposition || t('pending')}
                            />
                        </div>
                    </div>
                </PageSection>
            </div>
        </AppLayout>
    );
}
