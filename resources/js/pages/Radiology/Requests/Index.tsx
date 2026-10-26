import {
    CameraIcon,
    CheckBadgeIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    PlusIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useEffect, useMemo, useRef, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Button,
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
    RADIOLOGY_STATUS_LABEL_KEYS,
    RadiologyRequestCard,
    RadiologyRequestTableRow,
} from '@/pages/Radiology/components';
import type { RadiologyRequestRow } from '@/pages/Radiology/components';

interface Props {
    requests: {
        data: RadiologyRequestRow[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
        from: number | null;
        to: number | null;
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

export default function RadiologyRequestsIndex({
    requests,
    filters,
    stats,
}: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('grid');

    // One debounced visit keeps typing cheap; the cleanup cancels a pending
    // call when the screen unmounts.
    const handleFilter = useMemo(
        () =>
            debounce((searchValue: string, statusValue: string) => {
                router.get(
                    route('radiology.requests.index'),
                    { search: searchValue, status: statusValue },
                    { preserveState: true, replace: true },
                );
            }, 300),
        [],
    );

    // The first render already has the server-filtered rows; only fire a
    // visit when the user actually changes a filter.
    const hasMounted = useRef(false);

    useEffect(() => {
        if (!hasMounted.current) {
            hasMounted.current = true;

            return;
        }

        handleFilter(search, statusFilter);
    }, [handleFilter, search, statusFilter]);

    useEffect(() => () => handleFilter.cancel(), [handleFilter]);

    const statusLabel = (status: string) => {
        const fallback = status.replace(/_/g, ' ');
        const key = RADIOLOGY_STATUS_LABEL_KEYS[status];

        return key ? t(key, fallback) : fallback;
    };

    /** A request with a report opens it; a fresh one starts the report. */
    const actionFor = (request: RadiologyRequestRow) => {
        if (request.result) {
            return {
                href: route('radiology.results.edit', request.result.id),
                label: t('radiology.requests.open_report'),
            };
        }

        return {
            href: route('radiology.results.create', {
                request_id: request.id,
            }),
            label: t('radiology.requests.process_result'),
        };
    };

    return (
        <AppLayout
            headerTitle={t('radiology.requests.title')}
            headerSubtitle={t('radiology.requests.subtitle')}
        >
            <Head title={t('radiology.requests.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={CameraIcon}
                    eyebrow={t('radiology.requests.imaging_requests')}
                    title={t('radiology.requests.title')}
                    description={t('radiology.requests.subtitle')}
                    breadcrumbs={[
                        { label: t('radiology.requests.imaging_requests') },
                    ]}
                    actions={
                        <Button
                            href={route('radiology.requests.create')}
                            icon={PlusIcon}
                            variant="accent"
                        >
                            {t('radiology.requests.new_request')}
                        </Button>
                    }
                />

                <StatGrid>
                    <StatCard
                        label={t('radiology.requests.total_requests')}
                        value={stats.total}
                        icon={ClipboardDocumentListIcon}
                        tone="accent"
                    />
                    <StatCard
                        label={t('radiology.requests.completed')}
                        value={stats.completed}
                        icon={CheckBadgeIcon}
                        tone="positive"
                    />
                    <StatCard
                        label={t('radiology.requests.in_progress')}
                        value={stats.inProgress}
                        icon={ClockIcon}
                        tone="neutral"
                    />
                    <StatCard
                        label={t('radiology.requests.pending')}
                        value={stats.pending}
                        icon={ExclamationTriangleIcon}
                        tone="warning"
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder={t('radiology.requests.search_placeholder')}
                    />
                    <Select
                        aria-label={t('radiology.requests.status')}
                        className="h-9 w-auto text-[13px]"
                        value={statusFilter}
                        onChange={(event) =>
                            setStatusFilter(event.target.value)
                        }
                    >
                        <option value="">
                            {t('radiology.requests.all_statuses')}
                        </option>
                        <option value="pending">
                            {t('radiology.status.pending')}
                        </option>
                        <option value="in_progress">
                            {t('radiology.status.in_progress')}
                        </option>
                        <option value="completed">
                            {t('radiology.status.completed')}
                        </option>
                        <option value="validated">
                            {t('radiology.status.validated')}
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
                        icon={CameraIcon}
                        title={t('radiology.requests.no_requests')}
                        description={t(
                            'radiology.requests.no_requests_description',
                        )}
                        action={{
                            label: t('radiology.requests.new_request'),
                            href: route('radiology.requests.create'),
                        }}
                    />
                ) : viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {requests.data.map((request) => (
                            <RadiologyRequestCard
                                key={request.id}
                                request={request}
                                statusLabel={statusLabel(request.status)}
                                actionHref={actionFor(request).href}
                                actionLabel={actionFor(request).label}
                                meta={
                                    request.requested_by
                                        ? t('radiology.requests.referred_by', {
                                              name: request.requested_by.name,
                                          })
                                        : undefined
                                }
                            />
                        ))}
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    {t('radiology.requests.patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('radiology.requests.exam')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('radiology.requests.status')}
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
                                <RadiologyRequestTableRow
                                    key={request.id}
                                    request={request}
                                    statusLabel={statusLabel(request.status)}
                                    actionHref={actionFor(request).href}
                                    actionLabel={actionFor(request).label}
                                />
                            ))}
                        </TableBody>
                    </Table>
                )}

                <Pagination
                    links={requests.links}
                    meta={{
                        from: requests.from || 0,
                        to: requests.to || 0,
                        total: requests.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
