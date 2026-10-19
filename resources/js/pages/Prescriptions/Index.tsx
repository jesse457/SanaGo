import {
    CheckCircleIcon,
    ClockIcon,
    DocumentTextIcon,
    PlusIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Badge,
    Button,
    EmptyState,
    PageHeader,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import type { Prescription } from '@/types';
import { PrescriptionStatus } from '@/types/enums';

interface Props {
    prescriptions: {
        data: Prescription[];
        links: any[];
        from: number;
        to: number;
        total: number;
    };
}

export default function PrescriptionsIndex({ prescriptions }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');

    const stats = {
        total: prescriptions.total,
        active: prescriptions.data.filter(
            (p) => p.status === PrescriptionStatus.NEW,
        ).length,
        dispensed: prescriptions.data.filter(
            (p) => p.status === PrescriptionStatus.COMPLETED,
        ).length,
    };

    const filtered = useMemo(() => {
        if (!searchQuery.trim()) {
            return prescriptions.data;
        }

        const q = searchQuery.toLowerCase();

        return prescriptions.data.filter(
            (p) =>
                p.patient?.first_name?.toLowerCase().includes(q) ||
                p.patient?.last_name?.toLowerCase().includes(q) ||
                p.doctor?.name?.toLowerCase().includes(q),
        );
    }, [prescriptions.data, searchQuery]);

    const statusTone = (status: string): BadgeTone => {
        switch (status) {
            case 'active':
            case 'new':
                return 'accent';
            case 'dispensed':
            case 'completed':
                return 'positive';
            case 'cancelled':
                return 'critical';
            default:
                return 'neutral';
        }
    };

    return (
        <AppLayout
            headerTitle={t('Prescriptions')}
            headerSubtitle={t('Manage medical prescriptions and dispensing')}
        >
            <Head title={t('Prescriptions')} />
            <div className="space-y-5 pb-12">
                <PageHeader
                    title={t('Prescriptions')}
                    description={t(
                        'Manage medical prescriptions and dispensing',
                    )}
                    breadcrumbs={[
                        { label: t('Clinical') },
                        { label: t('Prescriptions') },
                    ]}
                    actions={
                        <Button
                            href={route('prescriptions.create')}
                            icon={PlusIcon}
                        >
                            {t('New Prescription')}
                        </Button>
                    }
                />

                <StatGrid columns={3}>
                    <StatCard
                        icon={DocumentTextIcon}
                        label={t('Total')}
                        value={stats.total}
                        tone="accent"
                        hint={t('All prescriptions on record')}
                    />
                    <StatCard
                        icon={ClockIcon}
                        label={t('Active')}
                        value={stats.active}
                        tone="warning"
                        hint={t('Awaiting dispensation')}
                    />
                    <StatCard
                        icon={CheckCircleIcon}
                        label={t('Dispensed')}
                        value={stats.dispensed}
                        tone="positive"
                        hint={t('Completed on this page')}
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder={t('Search by patient or doctor...')}
                    />
                    <ToolbarSpacer />
                    <Button
                        href={route('prescriptions.create')}
                        variant="secondary"
                        icon={PlusIcon}
                    >
                        {t('New Prescription')}
                    </Button>
                </Toolbar>

                {filtered.length === 0 ? (
                    <EmptyState
                        icon={DocumentTextIcon}
                        title={t('No Prescriptions')}
                        description={t(
                            'Create your first prescription to get started.',
                        )}
                        action={{
                            label: t('New Prescription'),
                            href: route('prescriptions.create'),
                        }}
                    />
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t('Patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>{t('Doctor')}</TableHeaderCell>
                                <TableHeaderCell>{t('Date')}</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('Items')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('Amount')}
                                </TableHeaderCell>
                                <TableHeaderCell>{t('Status')}</TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.map((rx) => (
                                <TableRow key={rx.id}>
                                    <TableCell>
                                        <Link
                                            href={route(
                                                'prescriptions.show',
                                                rx.id,
                                            )}
                                            className="font-medium text-accent transition-colors duration-150 hover:underline"
                                        >
                                            {rx.patient
                                                ? `${rx.patient.first_name} ${rx.patient.last_name}`
                                                : '—'}
                                        </Link>
                                    </TableCell>
                                    <TableCell muted>
                                        {rx.doctor?.name ?? '—'}
                                    </TableCell>
                                    <TableCell muted>
                                        {new Date(
                                            rx.prescription_date,
                                        ).toLocaleDateString()}
                                    </TableCell>
                                    <TableCell numeric muted>
                                        {rx.items_count ?? 0}
                                    </TableCell>
                                    <TableCell numeric>
                                        {Number(
                                            rx.total_amount,
                                        ).toLocaleString()}{' '}
                                        XAF
                                    </TableCell>
                                    <TableCell>
                                        <Badge tone={statusTone(rx.status)}>
                                            {t(rx.status)}
                                        </Badge>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}

                <Pagination
                    links={prescriptions.links}
                    meta={{
                        from: prescriptions.from,
                        to: prescriptions.to,
                        total: prescriptions.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
