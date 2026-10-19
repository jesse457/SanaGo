import {
    ArrowLeftIcon,
    DocumentTextIcon,
    BuildingOffice2Icon,
    ShieldCheckIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import {
    Button,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SectionCard,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { ClaimStatusBadge } from '@/pages/Finance/components';
import type { Claim } from '@/types';

interface Props {
    claim: Claim;
}

export default function ClaimsShow({ claim }: Props) {
    const { t } = useTranslation();

    return (
        <AppLayout
            headerTitle={t('Claim Detail')}
            headerSubtitle={`${t('Claim for invoice')} ${claim.invoice?.invoice_number ?? '—'}`}
        >
            <Head title={t('Claim Detail')} />

            <div className="mx-auto flex max-w-4xl flex-col gap-6">
                <PageHeader
                    title={t('Claim Detail')}
                    description={`${t('Claim for invoice')} ${claim.invoice?.invoice_number ?? '—'}`}
                    breadcrumbs={[
                        { label: t('Finance') },
                        {
                            label: t('Claims'),
                            href: route('finance.claims.index'),
                        },
                        { label: `#${claim.id}` },
                    ]}
                    meta={
                        <ClaimStatusBadge
                            status={claim.status}
                            label={t(claim.status)}
                            dot
                        />
                    }
                    actions={
                        <Button
                            variant="secondary"
                            icon={ArrowLeftIcon}
                            href={route('finance.claims.index')}
                        >
                            {t('Back to Claims')}
                        </Button>
                    }
                />

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <SectionCard
                        title={t('Claim Information')}
                        icon={DocumentTextIcon}
                    >
                        <KeyValueGrid columns={1}>
                            <KeyValue
                                label={t('Claim Amount')}
                                value={`${Number(claim.claim_amount).toLocaleString()} XAF`}
                            />
                            <KeyValue
                                label={t('Status')}
                                value={
                                    <ClaimStatusBadge
                                        status={claim.status}
                                        label={t(claim.status)}
                                    />
                                }
                            />
                            <KeyValue
                                label={t('Submitted At')}
                                value={
                                    claim.submitted_at
                                        ? new Date(
                                              claim.submitted_at,
                                          ).toLocaleDateString()
                                        : t('Not yet submitted')
                                }
                            />
                            <KeyValue
                                label={t('Paid At')}
                                value={
                                    claim.paid_at
                                        ? new Date(
                                              claim.paid_at,
                                          ).toLocaleDateString()
                                        : '—'
                                }
                            />
                            <KeyValue
                                label={t('Remittance Ref')}
                                value={claim.remittance_reference || '—'}
                            />
                            {claim.rejection_reason && (
                                <KeyValue
                                    label={t('Rejection Reason')}
                                    value={claim.rejection_reason}
                                />
                            )}
                        </KeyValueGrid>
                    </SectionCard>

                    <SectionCard
                        title={t('Invoice Details')}
                        icon={DocumentTextIcon}
                    >
                        <KeyValueGrid columns={1}>
                            <KeyValue
                                label={t('Invoice #')}
                                value={claim.invoice?.invoice_number ?? '—'}
                            />
                            <KeyValue
                                label={t('Total Amount')}
                                value={
                                    claim.invoice
                                        ? `${Number(claim.invoice.total_amount).toLocaleString()} XAF`
                                        : '—'
                                }
                            />
                            <KeyValue
                                label={t('Patient')}
                                value={
                                    claim.invoice?.patient
                                        ? `${claim.invoice.patient.first_name} ${claim.invoice.patient.last_name}`
                                        : '—'
                                }
                            />
                        </KeyValueGrid>
                    </SectionCard>

                    <SectionCard
                        title={t('Payer Information')}
                        icon={BuildingOffice2Icon}
                    >
                        <KeyValueGrid columns={1}>
                            <KeyValue
                                label={t('Payer')}
                                value={claim.payer?.name ?? '—'}
                            />
                            <KeyValue
                                label={t('Type')}
                                value={claim.payer?.type ?? '—'}
                            />
                            <KeyValue
                                label={t('Coverage')}
                                value={
                                    claim.payer
                                        ? `${claim.payer.coverage_percentage}%`
                                        : '—'
                                }
                            />
                        </KeyValueGrid>
                    </SectionCard>

                    {claim.patient_insurance && (
                        <SectionCard
                            title={t('Insurance Policy')}
                            icon={ShieldCheckIcon}
                        >
                            <KeyValueGrid columns={1}>
                                <KeyValue
                                    label={t('Policy #')}
                                    value={
                                        claim.patient_insurance.policy_number
                                    }
                                />
                                <KeyValue
                                    label={t('Card #')}
                                    value={
                                        claim.patient_insurance.card_number ||
                                        '—'
                                    }
                                />
                            </KeyValueGrid>
                        </SectionCard>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
