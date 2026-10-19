import {
    PlusIcon,
    MagnifyingGlassIcon,
    ShieldCheckIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import { useState, useMemo } from 'react';
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
import type { PatientInsurance } from '@/types';

interface Props {
    insurances: {
        data: PatientInsurance[];
        links: any[];
        from: number;
        to: number;
        total: number;
    };
}

export default function PatientInsurancesIndex({ insurances }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');

    const now = new Date();
    const valid = insurances.data.filter(
        (i) => new Date(i.valid_until) >= now,
    ).length;
    const expired = insurances.data.filter(
        (i) => new Date(i.valid_until) < now,
    ).length;

    const filtered = useMemo(() => {
        if (!searchQuery.trim()) {
            return insurances.data;
        }

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
        <AppLayout
            headerTitle={t('Patient Insurances')}
            headerSubtitle={t('Manage patient insurance coverage and policies')}
        >
            <Head title={t('Patient Insurances')} />
            <PageBreadcrumb
                items={[
                    { label: t('Finance') },
                    { label: t('Patient Insurances') },
                ]}
            />

            <div className="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <StatCard
                    icon={ShieldCheckIcon}
                    label={t('Total Insured')}
                    value={insurances.total}
                    color="blue"
                />
                <StatCard
                    icon={ShieldCheckIcon}
                    label={t('Active')}
                    value={valid}
                    color="emerald"
                />
                <StatCard
                    icon={ExclamationTriangleIcon}
                    label={t('Expired')}
                    value={expired}
                    color="red"
                />
            </div>

            <div className="mb-6 flex items-center justify-between">
                <div className="relative w-72">
                    <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-ink-subtle" />
                    <input
                        type="text"
                        placeholder={t(
                            'Search by patient, payer, or policy...',
                        )}
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        className="w-full rounded-lg border border-line bg-surface py-2 pr-4 pl-9 text-sm text-ink outline-none focus:border-accent focus:ring-2 focus:ring-blue-500/20"
                    />
                </div>
                <Link
                    href={route('patient-insurances.create')}
                    className="inline-flex items-center gap-1.5 rounded-lg bg-accent px-4 py-2 text-sm font-medium text-ink-inverse transition-colors hover:bg-accent-hover"
                >
                    <PlusIcon className="h-4 w-4" />
                    {t('Link Insurance')}
                </Link>
            </div>

            {filtered.length === 0 ? (
                <EmptyState
                    icon={ShieldCheckIcon}
                    title={t('No Patient Insurances')}
                    description={t(
                        'Link patients to their insurance policies.',
                    )}
                    actionLabel={t('Link Insurance')}
                    actionHref={route('patient-insurances.create')}
                />
            ) : (
                <Table>
                    <TableHeader>
                        <TableRow className="hover:bg-transparent">
                            <TableHeaderCell>{t('Patient')}</TableHeaderCell>
                            <TableHeaderCell>{t('Payer')}</TableHeaderCell>
                            <TableHeaderCell>{t('Policy #')}</TableHeaderCell>
                            <TableHeaderCell>
                                {t('Beneficiary')}
                            </TableHeaderCell>
                            <TableHeaderCell>
                                {t('Valid Until')}
                            </TableHeaderCell>
                            <TableHeaderCell>{t('Status')}</TableHeaderCell>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {filtered.map((ins) => (
                            <TableRow key={ins.id}>
                                <TableCell>
                                    <Link
                                        href={route(
                                            'patient-insurances.show',
                                            ins.id,
                                        )}
                                        className="font-medium text-accent hover:underline"
                                    >
                                        {ins.patient
                                            ? `${ins.patient.first_name} ${ins.patient.last_name}`
                                            : '—'}
                                    </Link>
                                </TableCell>
                                <TableCell muted>
                                    {ins.payer?.name ?? '—'}
                                </TableCell>
                                <TableCell className="font-mono text-[12px] text-ink-muted">
                                    {ins.policy_number}
                                </TableCell>
                                <TableCell muted className="capitalize">
                                    {ins.beneficiary_type}
                                </TableCell>
                                <TableCell muted className="tabular-nums">
                                    {ins.valid_until}
                                </TableCell>
                                <TableCell>
                                    {isExpired(ins.valid_until) ? (
                                        <StatusBadge
                                            label={t('Expired')}
                                            color="red"
                                        />
                                    ) : (
                                        <StatusBadge
                                            label={t('Active')}
                                            color="emerald"
                                        />
                                    )}
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            )}

            <Pagination
                links={insurances.links}
                meta={{
                    from: insurances.from,
                    to: insurances.to,
                    total: insurances.total,
                }}
            />
        </AppLayout>
    );
}
