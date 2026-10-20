import {
    CheckBadgeIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    ExclamationTriangleIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useEffect, useMemo, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    EmptyState,
    PageHeader,
    SegmentedControl,
    Select,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import {
    formatLabDate,
    LabRequestCard,
    LabRequestTableRow,
} from '@/pages/LabTests/components/LabRequestCard';
import type { LabRequestRow } from '@/pages/LabTests/components/LabRequestCard';

interface LabRequestRecord extends LabRequestRow {
    request_date: string;
}

interface Props {
    requests: {
        data: LabRequestRecord[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
        from: number;
        to: number;
    };
    filters: {
        search?: string;
        status?: string;
    };
    stats: {
        total: number;
        completed: number;
        inProgress: number;
        pending: number;
    };
}

export default function LabRequests({ requests, filters, stats }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');

    // Debounced router visit: `useMemo` keeps one debounced instance alive and
    // the cleanup cancels a pending call when the screen unmounts.
    const handleFilter = useMemo(
        () =>
            debounce((searchVal: string, statusVal: string) => {
                router.get(
                    route('lab.test-requests'),
                    { search: searchVal, status: statusVal },
                    { preserveState: true, replace: true },
                );
            }, 300),
        [],
    );

    useEffect(() => {
        handleFilter(search, statusFilter);
    }, [handleFilter, search, statusFilter]);

    useEffect(() => () => handleFilter.cancel(), [handleFilter]);

    const statusLabel = (status: string) => status.replace(/_/g, ' ');

    return (
        <AppLayout
            headerTitle={t('lab_tests.requests.title')}
            headerSubtitle={t('lab_tests.requests.subtitle')}
        >
            <Head title={t('lab_tests.requests.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={ClipboardDocumentListIcon}
                    eyebrow={t('lab_tests.requests.lab_requests')}
                    title={t('lab_tests.requests.title')}
                    description={t('lab_tests.requests.subtitle')}
                    breadcrumbs={[
                        { label: t('lab_tests.requests.lab_requests') },
                    ]}
                />

                <StatGrid>
                    <StatCard
                        label={t('lab_tests.requests.total_requests')}
                        value={stats.total}
                        icon={ClipboardDocumentListIcon}
                        tone="accent"
                    />
                    <StatCard
                        label={t('lab_tests.requests.completed')}
                        value={stats.completed}
                        icon={CheckBadgeIcon}
                        tone="positive"
                    />
                    <StatCard
                        label={t('lab_tests.requests.in_progress')}
                        value={stats.inProgress}
                        icon={ClockIcon}
                        tone="neutral"
                    />
                    <StatCard
                        label={t('lab_tests.requests.pending')}
                        value={stats.pending}
                        icon={ExclamationTriangleIcon}
                        tone="warning"
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder={t('lab_tests.requests.search_placeholder')}
                    />
                    <Select
                        aria-label={t('lab_tests.requests.status')}
                        className="h-9 w-auto text-[13px]"
                        value={statusFilter}
                        onChange={(event) =>
                            setStatusFilter(event.target.value)
                        }
                    >
                        <option value="">
                            {t('lab_tests.requests.all_statuses')}
                        </option>
                        <option value="Pending">
                            {t('lab_tests.requests.pending')}
                        </option>
                        <option value="In_Progress">
                            {t('lab_tests.requests.in_progress')}
                        </option>
                        <option value="Completed">
                            {t('lab_tests.requests.completed')}
                        </option>
                    </Select>
                    <ToolbarSpacer />
                    <SegmentedControl
                        value={viewMode}
                        onChange={setViewMode}
                        options={[
                            {
                                value: 'grid',
                                label: t('common.grid', 'Grid'),
                                ariaLabel: 'Grid view',
                            },
                            {
                                value: 'table',
                                label: t('common.table', 'Table'),
                                ariaLabel: 'Table view',
                            },
                        ]}
                        size="sm"
                    />
                </Toolbar>

                {requests.data.length === 0 ? (
                    <EmptyState
                        icon={ClipboardDocumentListIcon}
                        title={t('lab_tests.requests.no_requests')}
                        description={t(
                            'lab_tests.requests.no_requests_description',
                            'Requests raised on the wards appear here as soon as they are ordered.',
                        )}
                    />
                ) : viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {requests.data.map((request) => (
                            <LabRequestCard
                                key={request.id}
                                request={request}
                                dateLabel={formatLabDate(request.request_date)}
                                statusLabel={statusLabel(request.status)}
                                actionLabel={t(
                                    'lab_tests.requests.process_result',
                                )}
                                actionHref={route('results.create', {
                                    request_id: request.id,
                                })}
                            />
                        ))}
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    {t('lab_tests.requests.patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('lab_tests.requests.test_definition')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('lab_tests.requests.status')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">
                                        {t('common.actions', 'Actions')}
                                    </span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {requests.data.map((request) => (
                                <LabRequestTableRow
                                    key={request.id}
                                    request={request}
                                    statusLabel={statusLabel(request.status)}
                                    actionLabel={t(
                                        'lab_tests.requests.process_result',
                                    )}
                                    actionHref={route('results.create', {
                                        request_id: request.id,
                                    })}
                                />
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
