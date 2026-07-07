import { Head, Link } from '@inertiajs/react';
import { PlusIcon, MagnifyingGlassIcon, DocumentTextIcon, ClockIcon, CheckCircleIcon } from '@heroicons/react/24/outline';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import type { Prescription } from '@/types';

interface Props {
    prescriptions: { data: Prescription[]; links: any[]; from: number; to: number; total: number };
}

export default function PrescriptionsIndex({ prescriptions }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');

    const stats = {
        total: prescriptions.total,
        active: prescriptions.data.filter((p) => p.status === 'active' || p.status === 'new').length,
        dispensed: prescriptions.data.filter((p) => p.status === 'dispensed' || p.status === 'completed').length,
    };

    const filtered = useMemo(() => {
        if (!searchQuery.trim()) return prescriptions.data;
        const q = searchQuery.toLowerCase();
        return prescriptions.data.filter(
            (p) =>
                p.patient?.first_name?.toLowerCase().includes(q) ||
                p.patient?.last_name?.toLowerCase().includes(q) ||
                p.doctor?.name?.toLowerCase().includes(q),
        );
    }, [prescriptions.data, searchQuery]);

    const statusColor = (status: string) => {
        switch (status) {
            case 'active':
            case 'new':
                return 'blue';
            case 'dispensed':
            case 'completed':
                return 'emerald';
            case 'cancelled':
                return 'red';
            default:
                return 'gray';
        }
    };

    return (
        <AppLayout headerTitle={t('Prescriptions')} headerSubtitle={t('Manage medical prescriptions and dispensing')}>
            <Head title={t('Prescriptions')} />
            <PageBreadcrumb items={[{ label: t('Clinical') }, { label: t('Prescriptions') }]} />

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <StatCard icon={<DocumentTextIcon className="h-5 w-5" />} label={t('Total')} value={stats.total} color="blue" />
                <StatCard icon={<ClockIcon className="h-5 w-5" />} label={t('Active')} value={stats.active} color="amber" />
                <StatCard icon={<CheckCircleIcon className="h-5 w-5" />} label={t('Dispensed')} value={stats.dispensed} color="emerald" />
            </div>

            <div className="flex items-center justify-between mb-6">
                <div className="relative w-72">
                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 dark:text-neutral-500" />
                    <input
                        type="text"
                        placeholder={t('Search by patient or doctor...')}
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none"
                    />
                </div>
                <Link
                    href={route('prescriptions.create')}
                    className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <PlusIcon className="h-4 w-4" />
                    {t('New Prescription')}
                </Link>
            </div>

            {filtered.length === 0 ? (
                <EmptyState icon={<DocumentTextIcon className="h-12 w-12" />} title={t('No Prescriptions')} description={t('Create your first prescription to get started.')} actionLabel={t('New Prescription')} actionHref={route('prescriptions.create')} />
            ) : (
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <table className="w-full text-sm">
                        <thead className="bg-gray-50 dark:bg-neutral-900">
                            <tr>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Patient')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Doctor')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Date')}</th>
                                <th className="px-4 py-3 text-center font-medium text-gray-600 dark:text-neutral-400">{t('Items')}</th>
                                <th className="px-4 py-3 text-right font-medium text-gray-600 dark:text-neutral-400">{t('Amount')}</th>
                                <th className="px-4 py-3 text-center font-medium text-gray-600 dark:text-neutral-400">{t('Status')}</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {filtered.map((rx) => (
                                <tr key={rx.id} className="hover:bg-gray-50 dark:hover:bg-neutral-750">
                                    <td className="px-4 py-3">
                                        <Link href={route('prescriptions.show', rx.id)} className="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            {rx.patient ? `${rx.patient.first_name} ${rx.patient.last_name}` : '—'}
                                        </Link>
                                    </td>
                                    <td className="px-4 py-3 text-gray-700 dark:text-neutral-300">{rx.doctor?.name ?? '—'}</td>
                                    <td className="px-4 py-3 text-gray-500 dark:text-neutral-400">{new Date(rx.prescription_date).toLocaleDateString()}</td>
                                    <td className="px-4 py-3 text-center text-gray-700 dark:text-neutral-300">{rx.items_count ?? 0}</td>
                                    <td className="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{Number(rx.total_amount).toLocaleString()} XAF</td>
                                    <td className="px-4 py-3 text-center">
                                        <StatusBadge label={t(rx.status)} color={statusColor(rx.status)} />
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <Pagination links={prescriptions.links} meta={{ from: prescriptions.from, to: prescriptions.to, total: prescriptions.total }} />
        </AppLayout>
    );
}
