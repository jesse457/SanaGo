import { Head, Link } from '@inertiajs/react';
import { ArrowLeftIcon, DocumentTextIcon, BuildingOffice2Icon, CheckCircleIcon, XCircleIcon, ClockIcon } from '@heroicons/react/24/outline';
import DetailItem from '@/Components/DetailItem';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import PageSection from '@/Components/PageSection';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import type { Claim } from '@/types';

interface Props {
    claim: Claim;
}

export default function ClaimsShow({ claim }: Props) {
    const { t } = useTranslation();

    const statusColor = (status: string) => {
        switch (status) {
            case 'draft':
                return 'gray';
            case 'submitted':
                return 'blue';
            case 'approved':
                return 'emerald';
            case 'rejected':
                return 'red';
            case 'paid':
                return 'purple';
            default:
                return 'gray';
        }
    };

    return (
        <AppLayout headerTitle={t('Claim Detail')} headerSubtitle={`${t('Claim for invoice')} ${claim.invoice?.invoice_number ?? '—'}`}>
            <Head title={t('Claim Detail')} />
            <PageBreadcrumb items={[{ label: t('Finance') }, { label: t('Claims'), href: route('finance.claims.index') }, { label: `#${claim.id}` }]} />

            <div className="max-w-4xl mx-auto space-y-6">
                <div className="flex items-center justify-between">
                    <Link href={route('finance.claims.index')} className="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-neutral-400 hover:text-blue-600 dark:hover:text-blue-400">
                        <ArrowLeftIcon className="h-4 w-4" />
                        {t('Back to Claims')}
                    </Link>
                    <StatusBadge label={t(claim.status)} color={statusColor(claim.status)} />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <PageSection title={t('Claim Information')} icon={<DocumentTextIcon className="h-4 w-4" />}>
                        <div className="space-y-3">
                            <DetailItem label={t('Claim Amount')} value={`${Number(claim.claim_amount).toLocaleString()} XAF`} />
                            <DetailItem label={t('Status')} value={t(claim.status)} />
                            <DetailItem label={t('Submitted At')} value={claim.submitted_at ? new Date(claim.submitted_at).toLocaleDateString() : t('Not yet submitted')} />
                            <DetailItem label={t('Paid At')} value={claim.paid_at ? new Date(claim.paid_at).toLocaleDateString() : '—'} />
                            <DetailItem label={t('Remittance Ref')} value={claim.remittance_reference || '—'} />
                            {claim.rejection_reason && <DetailItem label={t('Rejection Reason')} value={claim.rejection_reason} />}
                        </div>
                    </PageSection>

                    <PageSection title={t('Invoice Details')} icon={<DocumentTextIcon className="h-4 w-4" />}>
                        <div className="space-y-3">
                            <DetailItem label={t('Invoice #')} value={claim.invoice?.invoice_number ?? '—'} />
                            <DetailItem label={t('Total Amount')} value={claim.invoice ? `${Number(claim.invoice.total_amount).toLocaleString()} XAF` : '—'} />
                            <DetailItem label={t('Patient')} value={claim.invoice?.patient ? `${claim.invoice.patient.first_name} ${claim.invoice.patient.last_name}` : '—'} />
                        </div>
                    </PageSection>

                    <PageSection title={t('Payer Information')} icon={<BuildingOffice2Icon className="h-4 w-4" />}>
                        <div className="space-y-3">
                            <DetailItem label={t('Payer')} value={claim.payer?.name ?? '—'} />
                            <DetailItem label={t('Type')} value={claim.payer?.type ?? '—'} />
                            <DetailItem label={t('Coverage')} value={claim.payer ? `${claim.payer.coverage_percentage}%` : '—'} />
                        </div>
                    </PageSection>

                    {claim.patient_insurance && (
                        <PageSection title={t('Insurance Policy')} icon={<ShieldCheckIcon className="h-4 w-4" />}>
                            <div className="space-y-3">
                                <DetailItem label={t('Policy #')} value={claim.patient_insurance.policy_number} />
                                <DetailItem label={t('Card #')} value={claim.patient_insurance.card_number || '—'} />
                            </div>
                        </PageSection>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}

function ShieldCheckIcon(props: any) {
    return (
        <svg {...props} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
        </svg>
    );
}
