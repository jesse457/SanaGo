import { Head, Link } from '@inertiajs/react';
import { PlusIcon, MagnifyingGlassIcon, EyeIcon, CameraIcon, TrashIcon } from '@heroicons/react/24/outline';
import { useState, useMemo } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatCard from '@/Components/StatCard';
import StatusBadge from '@/Components/StatusBadge';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';
import type { RadiologyRequest } from '@/types';

interface Props {
    requests: { data: RadiologyRequest[]; links: any[]; from: number; to: number; total: number };
}

export default function RadiologyRequestsIndex({ requests }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');

    const stats = {
        total: requests.total,
        pending: requests.data.filter((r) => r.status === 'pending').length,
        completed: requests.data.filter((r) => r.status === 'completed' || r.status === 'validated').length,
    };

    const filtered = useMemo(() => {
        if (!searchQuery.trim()) return requests.data;
        const q = searchQuery.toLowerCase();
        return requests.data.filter(
            (r) =>
                r.patient?.first_name?.toLowerCase().includes(q) ||
                r.patient?.last_name?.toLowerCase().includes(q) ||
                r.imaging_type?.toLowerCase().includes(q) ||
                r.body_part?.toLowerCase().includes(q),
        );
    }, [requests.data, searchQuery]);

    const statusColor = (status: string) => {
        switch (status) {
            case 'pending':
                return 'amber';
            case 'in_progress':
                return 'blue';
            case 'completed':
                return 'emerald';
            case 'validated':
                return 'purple';
            default:
                return 'gray';
        }
    };

    return (
        <AppLayout headerTitle={t('Radiology Requests')} headerSubtitle={t('Manage X-ray, ultrasound, CT scan and other imaging requests')}>
            <Head title={t('Radiology')} />
            <PageBreadcrumb items={[{ label: t('Lab & Diagnostics') }, { label: t('Radiology') }]} />

            <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <StatCard icon={<CameraIcon className="h-5 w-5" />} label={t('Total Requests')} value={stats.total} color="blue" />
                <StatCard icon={<ClockIcon className="h-5 w-5" />} label={t('Pending')} value={stats.pending} color="amber" />
                <StatCard icon={<CheckCircleIcon className="h-5 w-5" />} label={t('Completed')} value={stats.completed} color="emerald" />
            </div>

            <div className="flex items-center justify-between mb-6">
                <div className="relative w-72">
                    <MagnifyingGlassIcon className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400 dark:text-neutral-500" />
                    <input
                        type="text"
                        placeholder={t('Search requests...')}
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none"
                    />
                </div>
                <Link href={route('radiology.requests.create')} className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <PlusIcon className="h-4 w-4" />
                    {t('New Request')}
                </Link>
            </div>

            {filtered.length === 0 ? (
                <EmptyState icon={<CameraIcon className="h-12 w-12" />} title={t('No Radiology Requests')} description={t('Create your first radiology request.')} actionLabel={t('New Request')} actionHref={route('radiology.requests.create')} />
            ) : (
                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 overflow-hidden">
                    <table className="w-full text-sm">
                        <thead className="bg-gray-50 dark:bg-neutral-900">
                            <tr>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Patient')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Imaging')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Body Part')}</th>
                                <th className="px-4 py-3 text-left font-medium text-gray-600 dark:text-neutral-400">{t('Requested By')}</th>
                                <th className="px-4 py-3 text-center font-medium text-gray-600 dark:text-neutral-400">{t('Status')}</th>
                                <th className="px-4 py-3 text-right font-medium text-gray-600 dark:text-neutral-400">{t('Actions')}</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {filtered.map((req) => (
                                <tr key={req.id} className="hover:bg-gray-50 dark:hover:bg-neutral-750">
                                    <td className="px-4 py-3">
                                        <Link href={route('radiology.requests.show', req.id)} className="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            {req.patient ? `${req.patient.first_name} ${req.patient.last_name}` : '—'}
                                        </Link>
                                    </td>
                                    <td className="px-4 py-3 text-gray-700 dark:text-neutral-300 capitalize">{req.imaging_type.replace('_', ' ')}</td>
                                    <td className="px-4 py-3 text-gray-700 dark:text-neutral-300 capitalize">{req.body_part}</td>
                                    <td className="px-4 py-3 text-gray-500 dark:text-neutral-400">{req.requested_by_user?.name ?? '—'}</td>
                                    <td className="px-4 py-3 text-center">
                                        <StatusBadge label={t(req.status)} color={statusColor(req.status)} />
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <Link href={route('radiology.requests.show', req.id)} className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg inline-flex cursor-pointer">
                                            <EyeIcon className="h-4 w-4" />
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}

            <Pagination links={requests.links} meta={{ from: requests.from, to: requests.to, total: requests.total }} />
        </AppLayout>
    );
}

function ClockIcon(props: any) {
    return (
        <svg {...props} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    );
}

function CheckCircleIcon(props: any) {
    return (
        <svg {...props} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    );
}
