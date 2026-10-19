import {
    BuildingOffice2Icon,
    DocumentTextIcon,
    ClockIcon,
    BanknotesIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import {
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { Claim } from '@/types';

interface Props {
    claims: {
        data: Claim[];
        links: any[];
        from: number;
        to: number;
        total: number;
    };
}

export default function ClaimsIndex({ claims }: Props) {
    const { t } = useTranslation();

    const stats = {
        total: claims.total,
        submitted: claims.data.filter(
            (c) => c.status === 'submitted' || c.status === 'approved',
        ).length,
        paid: claims.data.filter((c) => c.status === 'paid').length,
    };

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
        <AppLayout
            headerTitle={t('Insurance Claims')}
            headerSubtitle={t('Track claims submitted to insurance payers')}
        >
            <Head title={t('Claims')} />
            <PageBreadcrumb
                items={[{ label: t('Finance') }, { label: t('Claims') }]}
            />

            <div className="mt-6 mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <StatCard
                    icon={DocumentTextIcon}
                    label={t('Total Claims')}
                    value={stats.total}
                    color="blue"
                />
                <StatCard
                    icon={ClockIcon}
                    label={t('In Process')}
                    value={stats.submitted}
                    color="amber"
                />
                <StatCard
                    icon={BanknotesIcon}
                    label={t('Paid')}
                    value={stats.paid}
                    color="emerald"
                />
            </div>

            {claims.data.length === 0 ? (
                <EmptyState
                    icon={DocumentTextIcon}
                    title={t('No Claims Yet')}
                    description={t(
                        'Submit invoices to insurance payers to create claims.',
                    )}
                />
            ) : (
                <Table>
                    <TableHeader>
                        <TableRow className="hover:bg-transparent">
                            <TableHeaderCell>{t('Invoice')}</TableHeaderCell>
                            <TableHeaderCell>{t('Patient')}</TableHeaderCell>
                            <TableHeaderCell>{t('Payer')}</TableHeaderCell>
                            <TableHeaderCell numeric>
                                {t('Amount')}
                            </TableHeaderCell>
                            <TableHeaderCell>{t('Status')}</TableHeaderCell>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {claims.data.map((claim) => (
                            <TableRow key={claim.id}>
                                <TableCell>
                                    <Link
                                        href={route(
                                            'finance.claims.show',
                                            claim.id,
                                        )}
                                        className="font-medium text-accent hover:underline"
                                    >
                                        {claim.invoice?.invoice_number ?? '—'}
                                    </Link>
                                </TableCell>
                                <TableCell muted>
                                    {claim.invoice?.patient
                                        ? `${claim.invoice.patient.first_name} ${claim.invoice.patient.last_name}`
                                        : '—'}
                                </TableCell>
                                <TableCell muted>
                                    <div className="flex items-center gap-1.5">
                                        <BuildingOffice2Icon className="h-3.5 w-3.5 text-ink-subtle" />
                                        {claim.payer?.name ?? '—'}
                                    </div>
                                </TableCell>
                                <TableCell numeric className="font-semibold">
                                    {Number(
                                        claim.claim_amount,
                                    ).toLocaleString()}{' '}
                                    XAF
                                </TableCell>
                                <TableCell>
                                    <StatusBadge
                                        label={t(claim.status)}
                                        color={statusColor(claim.status)}
                                    />
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            )}

            <Pagination
                links={claims.links}
                meta={{ from: claims.from, to: claims.to, total: claims.total }}
            />
        </AppLayout>
    );
}
