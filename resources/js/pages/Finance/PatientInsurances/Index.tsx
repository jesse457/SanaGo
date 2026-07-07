import { Head, Link } from '@inertiajs/react';
import { PlusIcon, MagnifyingGlassIcon, ShieldCheckIcon, ExclamationTriangleIcon } from '@heroicons/react/24/outline';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import type { PatientInsurance } from '@/types';

interface Props {
    insurances: { data: PatientInsurance[]; links: any[]; from: number; to: number; total: number };
}

export default function PatientInsurancesIndex({ insurances }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');

    const now = new Date();
    const valid = insurances.data.filter((i) => new Date(i.valid_until) >= now).length;
    const expired = insurances.data.filter((i) => new Date(i.valid_until) < now).length;

    const filtered = useMemo(() => {
        if (!searchQuery.trim()) return insurances.data;
        const q = searchQuery.toLowerCase();
        return insurances.data.filter(
            (i) =>
                i.patient?.first_name?.toLowerCase().includes(q) ||
                i.patient?.last_name?.toLowerCase().includes(q) ||
                i.payer?.name?.toLowerCase().includes(q) ||
                i.policy_number?.toLowerCase().includes(q),
        );
    }, [insurances.data, searchQuery]);

    const isExpired = (date: string) => new Date(date) < now;

    return (
        <AppLayout headerTitle={t('Patient Insurances')} headerSubtitle={t('Manage patient insurance coverage and policies')}>
            <Head title={t('Patient Insurances')} />
            <PageBreadcrumb items={[{ label: t('Finance') }, { label: t('Patient Insurances') }]} />

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <StatCard icon={ShieldCheckIcon} label={t('Total Insured')} value={insurances.total} color="blue" />
                <StatCard icon={ShieldCheckIcon } label={t('Active')} value={valid} color="emerald" />
                <StatCard icon={ExclamationTriangleIcon } label={t('Expired')} value={expired} color="red" />
            </div>

            <div className="flex items-center justify-between mb-6">
                <div className="relative w-72">
                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 dark:text-neutral-500" />
                    <input
                        type="text"
                        placeholder={t('Search by patient, payer, or policy...')}
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none"
                    />
                </div>
                <Link href={route('patient-insurances.create')} className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <PlusIcon className="h-4 w-4" />
                    {t('Link Insurance')}
                </Link>
            </div>

            {filtered.length === 0 ? (
                <EmptyState icon={ShieldCheckIcon} title={t('No Patient Insurances')} description={t('Link patients to their insurance policies.')} actionLabel={t('Link Insurance')} actionHref={route('patient-insurances.create')} />
            ) : (
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <table className="w-full text-sm">
                        <thead className="bg-gray-50 dark:bg-neutral-900">
                            <tr>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Patient')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Payer')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Policy #')}</th>
                                <th className="px-4 py-3 text-center font-medium text-gray-600 dark:text-neutral-400">{t('Beneficiary')}</th>
                                <th className="px-4 py-3 text-center font-medium text-gray-600 dark:text-neutral-400">{t('Valid Until')}</th>
                                <th className="px-4 py-3 text-center font-medium text-gray-600 dark:text-neutral-400">{t('Status')}</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {filtered.map((ins) => (
                                <tr key={ins.id} className="hover:bg-gray-50 dark:hover:bg-neutral-750">
                                    <td className="px-4 py-3">
                                        <Link href={route('patient-insurances.show', ins.id)} className="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            {ins.patient ? `${ins.patient.first_name} ${ins.patient.last_name}` : '—'}
                                        </Link>
                                    </td>
                                    <td className="px-4 py-3 text-gray-700 dark:text-neutral-300">{ins.payer?.name ?? '—'}</td>
                                    <td className="px-4 py-3 font-mono text-gray-600 dark:text-neutral-400">{ins.policy_number}</td>
                                    <td className="px-4 py-3 text-center capitalize text-gray-700 dark:text-neutral-300">{ins.beneficiary_type}</td>
                                    <td className="px-4 py-3 text-center text-gray-700 dark:text-neutral-300">{ins.valid_until}</td>
                                    <td className="px-4 py-3 text-center">
                                        {isExpired(ins.valid_until) ? (
                                            <StatusBadge label={t('Expired')} color="red" />
                                        ) : (
                                            <StatusBadge label={t('Active')} color="emerald" />
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <Pagination links={insurances.links} meta={{ from: insurances.from, to: insurances.to, total: insurances.total }} />
        </AppLayout>
    );
}
