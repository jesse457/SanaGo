import {
    ArrowPathIcon,
    ClipboardDocumentListIcon,
    CurrencyDollarIcon,
    DocumentTextIcon,
    HeartIcon,
    MapPinIcon,
    PencilSquareIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import {
    Alert,
    Button,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import {
    CaseStatusBadge,
    TriageBadge,
} from '@/pages/Emergencies/components/TriageBadges';

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

export default function EmergencyShow({ emergency }: Props) {
    const { t } = useTranslation();

    const patientName = `${emergency.patient?.first_name || ''} ${
        emergency.patient?.last_name || ''
    }`.trim();

    const title = `${t('emergency')} #${emergency.id}`;

    return (
        <AppLayout headerTitle={title} headerSubtitle={t('caseDetails')}>
            <Head title={title} />
            <div className="mx-auto max-w-6xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={HeartIcon}
                    eyebrow={t('emergencies')}
                    title={title}
                    description={t('caseDetails')}
                    breadcrumbs={[
                        { label: t('common.home'), href: route('dashboard') },
                        {
                            label: t('emergencies'),
                            href: route('emergencies.index'),
                        },
                        { label: title },
                    ]}
                    meta={
                        <>
                            <TriageBadge
                                level={emergency.triage_level}
                                label={t(emergency.triage_level)}
                            />
                            <CaseStatusBadge
                                status={emergency.status}
                                label={t(emergency.status)}
                            />
                            {patientName && (
                                <span className="font-medium text-ink-muted">
                                    {patientName}
                                </span>
                            )}
                        </>
                    }
                    actions={
                        <>
                            <Button
                                variant="secondary"
                                icon={ArrowPathIcon}
                                href={route('emergencies.index')}
                            >
                                {t('back')}
                            </Button>
                            <Button
                                icon={PencilSquareIcon}
                                href={route('emergencies.edit', emergency.id)}
                            >
                                {t('edit')}
                            </Button>
                        </>
                    }
                />

                <SectionCard
                    icon={ClipboardDocumentListIcon}
                    title={t('caseDetails')}
                >
                    <KeyValueGrid columns={2}>
                        <KeyValue
                            icon={UserIcon}
                            label={t('patient')}
                            value={patientName}
                        />
                        <KeyValue
                            icon={MapPinIcon}
                            label={t('arrivalMode')}
                            value={emergency.arrival_mode || t('na')}
                        />
                        <KeyValue
                            icon={UserIcon}
                            label={t('doctor')}
                            value={
                                emergency.attending_doctor?.name ||
                                t('unassigned')
                            }
                        />
                        <KeyValue
                            icon={UserIcon}
                            label={t('triageNurse')}
                            value={
                                emergency.triage_nurse?.name || t('unassigned')
                            }
                        />
                    </KeyValueGrid>

                    <div className="mt-4 space-y-3">
                        <KeyValue
                            icon={DocumentTextIcon}
                            label={t('chiefComplaint')}
                            value={emergency.chief_complaint}
                        />
                        {emergency.primary_assessment && (
                            <KeyValue
                                icon={ClipboardDocumentListIcon}
                                label={t('assessment')}
                                value={emergency.primary_assessment}
                            />
                        )}
                    </div>

                    <div className="mt-4">
                        <KeyValueGrid columns={2}>
                            <KeyValue
                                icon={CurrencyDollarIcon}
                                label={t('fee')}
                                value={`${emergency.emergency_fee.toLocaleString()} FCFA`}
                            />
                            <KeyValue
                                icon={ArrowPathIcon}
                                label={t('disposition')}
                                value={emergency.disposition || t('pending')}
                            />
                        </KeyValueGrid>
                    </div>
                </SectionCard>

                {emergency.treatment_notes && (
                    <Alert tone="info" title={t('treatmentNotes')}>
                        {emergency.treatment_notes}
                    </Alert>
                )}

                {emergency.discharge_instructions && (
                    <Alert tone="positive" title={t('dischargeInstructions')}>
                        {emergency.discharge_instructions}
                    </Alert>
                )}
            </div>
        </AppLayout>
    );
}
