import {
    PlusIcon,
    EyeIcon,
    CameraIcon,
    ClockIcon,
    CheckCircleIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
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
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { RadiologyStatusBadge } from '@/pages/Radiology/components';
import type { RadiologyRequest } from '@/types';

interface Props {
    requests: {
        data: RadiologyRequest[];
        links: any[];
        from: number;
        to: number;
        total: number;
    };
}

export default function RadiologyRequestsIndex({ requests }: Props) {
    const { t } = useTranslation();
    const [searchQuery, setSearchQuery] = useState('');

    const stats = {
        total: requests.total,
        pending: requests.data.filter((r) => r.status === 'pending').length,
        completed: requests.data.filter(
            (r) => r.status === 'completed' || r.status === 'validated',
        ).length,
    };

    const filtered = useMemo(() => {
        if (!searchQuery.trim()) {
            return requests.data;
        }

        const q = searchQuery.toLowerCase();

        return requests.data.filter(
            (r) =>
                r.patient?.first_name?.toLowerCase().includes(q) ||
                r.patient?.last_name?.toLowerCase().includes(q) ||
                r.imaging_type?.toLowerCase().includes(q) ||
                r.body_part?.toLowerCase().includes(q),
        );
    }, [requests.data, searchQuery]);

    return (
        <AppLayout
            headerTitle={t('Radiology Requests')}
            headerSubtitle={t(
                'Manage X-ray, ultrasound, CT scan and other imaging requests',
            )}
        >
            <Head title={t('Radiology')} />

            <div className="flex flex-col gap-6">
                <PageHeader
                    title={t('Radiology Requests')}
                    description={t(
                        'Manage X-ray, ultrasound, CT scan and other imaging requests',
                    )}
                    breadcrumbs={[
                        { label: t('Lab & Diagnostics') },
                        { label: t('Radiology') },
                    ]}
                    actions={
                        <Button
                            href={route('radiology.requests.create')}
                            icon={PlusIcon}
                        >
                            {t('New Request')}
                        </Button>
                    }
                />

                <StatGrid columns={3}>
                    <StatCard
                        icon={CameraIcon}
                        label={t('Total Requests')}
                        value={stats.total}
                        tone="accent"
                    />
                    <StatCard
                        icon={ClockIcon}
                        label={t('Pending')}
                        value={stats.pending}
                        tone="warning"
                    />
                    <StatCard
                        icon={CheckCircleIcon}
                        label={t('Completed')}
                        value={stats.completed}
                        tone="positive"
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={searchQuery}
                        onChange={setSearchQuery}
                        placeholder={t('Search requests...')}
                    />
                    <ToolbarSpacer />
                </Toolbar>

                {filtered.length === 0 ? (
                    <EmptyState
                        icon={CameraIcon}
                        title={t('No Radiology Requests')}
                        description={t('Create your first radiology request.')}
                        action={{
                            label: t('New Request'),
                            href: route('radiology.requests.create'),
                        }}
                    />
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t('Patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('Imaging')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('Body Part')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('Requested By')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('Status')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('Actions')}
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.map((req) => (
                                <TableRow key={req.id}>
                                    <TableCell>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            href={route(
                                                'radiology.requests.show',
                                                req.id,
                                            )}
                                            className="-ml-3 font-semibold text-accent hover:text-accent-ink"
                                        >
                                            {req.patient
                                                ? `${req.patient.first_name} ${req.patient.last_name}`
                                                : '—'}
                                        </Button>
                                    </TableCell>
                                    <TableCell muted className="capitalize">
                                        {req.imaging_type.replace('_', ' ')}
                                    </TableCell>
                                    <TableCell muted className="capitalize">
                                        {req.body_part}
                                    </TableCell>
                                    <TableCell muted>
                                        {req.requested_by_user?.name ?? '—'}
                                    </TableCell>
                                    <TableCell numeric>
                                        <RadiologyStatusBadge
                                            status={req.status}
                                            label={t(req.status)}
                                        />
                                    </TableCell>
                                    <TableCell numeric>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            aria-label={t('Radiology')}
                                            icon={EyeIcon}
                                            href={route(
                                                'radiology.requests.show',
                                                req.id,
                                            )}
                                        />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}

                <Pagination
                    links={requests.links}
                    meta={{
                        from: requests.from,
                        to: requests.to,
                        total: requests.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
