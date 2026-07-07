import { Head } from '@inertiajs/react';
import { BuildingOffice2Icon, DocumentTextIcon, ClockIcon, BanknotesIcon } from '@heroicons/react/24/outline';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import { Link } from '@inertiajs/react';
import type { Claim } from '@/types';

interface Props {
    claims: { data: Claim[]; links: any[]; from: number; to: number; total: number };
}

export default function ClaimsIndex({ claims }: Props) {
    const { t } = useTranslation();

    const stats = {
        total: claims.total,
        submitted: claims.data.filter((c) => c.status === 'submitted' || c.status === 'approved').length,
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
        <AppLayout headerTitle={t('Insurance Claims')} headerSubtitle={t('Track claims submitted to insurance payers')}>
            <Head title={t('Claims')} />
            <PageBreadcrumb items={[{ label: t('Finance') }, { label: t('Claims') }]} />

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 mt-6">
                <StatCard icon={DocumentTextIcon} label={t('Total Claims')} value={stats.total} color="blue" />
                <StatCard icon={ClockIcon} label={t('In Process')} value={stats.submitted} color="amber" />
                <StatCard icon={BanknotesIcon} label={t('Paid')} value={stats.paid} color="emerald" />
            </div>

            {claims.data.length === 0 ? (
                <EmptyState icon={DocumentTextIcon} title={t('No Claims Yet')} description={t('Submit invoices to insurance payers to create claims.')} />
            ) : (
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <table className="w-full text-sm">
                        <thead className="bg-gray-50 dark:bg-neutral-900">
                            <tr>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Invoice')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Patient')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Payer')}</th>
                                <th className="px-4 py-3 text-right font-medium text-gray-600 dark:text-neutral-400">{t('Amount')}</th>
                                <th className="px-4 py-3 text-center font-medium text-gray-600 dark:text-neutral-400">{t('Status')}</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {claims.data.map((claim) => (
                                <tr key={claim.id} className="hover:bg-gray-50 dark:hover:bg-neutral-750">
                                    <td className="px-4 py-3">
                                        <Link href={route('finance.claims.show', claim.id)} className="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            {claim.invoice?.invoice_number ?? '—'}
                                        </Link>
                                    </td>
                                    <td className="px-4 py-3 text-gray-700 dark:text-neutral-300">
                                        {claim.invoice?.patient ? `${claim.invoice.patient.first_name} ${claim.invoice.patient.last_name}` : '—'}
                                    </td>
                                    <td className="px-4 py-3 text-gray-700 dark:text-neutral-300">
                                        <div className="flex items-center gap-1.5">
                                            <BuildingOffice2Icon className="h-3.5 w-3.5 text-gray-400" />
                                            {claim.payer?.name ?? '—'}
                                        </div>
                                    </td>
                                    <td className="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">
                                        {Number(claim.claim_amount).toLocaleString()} XAF
                                    </td>
                                    <td className="px-4 py-3 text-center">
                                        <StatusBadge label={t(claim.status)} color={statusColor(claim.status)} />
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <Pagination links={claims.links} meta={{ from: claims.from, to: claims.to, total: claims.total }} />
        </AppLayout>
    );
}
