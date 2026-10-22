import {
    ArrowDownTrayIcon,
    CameraIcon,
    CalendarIcon,
    CheckBadgeIcon,
    ClockIcon,
    DocumentCheckIcon,
    InboxIcon,
    PencilSquareIcon,
    Squares2X2Icon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useEffect, useMemo, useState } from 'react';
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
    formatRadiologyDate,
    humanise,
    imagingTypeLabel,
    RADIOLOGY_STATUS_LABEL_KEYS,
    RadiologyPatientIdentity,
    RadiologyRequestTableRow,
    RadiologyStatusBadge,
} from '@/pages/Radiology/components';
import type { RadiologyRequestRow } from '@/pages/Radiology/components';

/** A report row: the request it belongs to plus the report's own metadata. */
interface RadiologyResultRow extends RadiologyRequestRow {
    request_id: number;
    report_date?: string | null;
    findings?: string | null;
    conclusion?: string | null;
    performed_by?: { id: number; name: string } | null;
    validated_by?: { id: number; name: string } | null;
}

interface Props {
    results: {
        data: RadiologyResultRow[];
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
        signed: number;
        validated: number;
        inProgress: number;
    };
}

/**
 * ResultArchiveCard — one signed (or in-progress) report tile: patient, the
 * exam it belongs to and the clinician who read it.
 */
function ResultArchiveCard({
    result,
    statusLabel,
    editHref,
    openRequestHref,
    labels,
}: {
    result: RadiologyResultRow;
    statusLabel: string;
    editHref: string;
    openRequestHref: string | null;
    labels: { edit: string; request: string; noFindings: string };
}) {
    return (
        <article className="flex h-full flex-col rounded-xl border border-line bg-surface p-5 transition-[border-color,transform] duration-200 ease-premium hover:-translate-y-0.5 hover:border-line-strong">
            <div className="mb-3.5 flex items-start justify-between gap-3">
                <RadiologyPatientIdentity request={result} avatarSize="md" />
                <RadiologyStatusBadge
                    status={result.status}
                    label={statusLabel}
                    dot
                />
            </div>

            <div className="flex items-center gap-2 rounded-lg bg-surface-muted px-3.5 py-2.5">
                <CameraIcon className="h-4 w-4 shrink-0 text-accent" />
                <div className="min-w-0">
                    <div className="truncate text-[13px] font-medium text-ink">
                        {imagingTypeLabel(result.imaging_type)}
                    </div>
                    <div className="mt-0.5 text-[10px] tracking-[0.1em] text-ink-subtle uppercase">
                        {humanise(result.body_part)}
                    </div>
                </div>
            </div>

            <p className="mt-3 line-clamp-2 min-h-[2.25rem] text-[12.5px] leading-relaxed text-ink-muted">
                {result.findings?.trim() || labels.noFindings}
            </p>

            <div className="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                <span className="inline-flex items-center gap-1.5 text-[11px] whitespace-nowrap text-ink-subtle tabular-nums">
                    <CalendarIcon className="h-3.5 w-3.5" />
                    {formatRadiologyDate(result.report_date)}
                </span>
                <div className="flex items-center gap-1">
                    <Button
                        href={editHref}
                        variant="ghost"
                        size="icon"
                        icon={PencilSquareIcon}
                        aria-label={labels.edit}
                    />
                    {openRequestHref && (
                        <Button
                            href={openRequestHref}
                            variant="ghost"
                            size="icon"
                            icon={DocumentCheckIcon}
                            aria-label={labels.request}
                        />
                    )}
                </div>
            </div>
        </article>
    );
}

export default function RadiologyResultsIndex({
    results,
    filters,
    stats,
}: Props) {
    const { t } = useTranslation();

    // The chosen layout survives navigation, like the lab result archive.
    const [viewMode, setViewMode] = useState<'grid' | 'table'>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('radiology_results_view_mode');

            if (saved === 'grid' || saved === 'table') {
                return saved;
            }
        }

        return 'grid';
    });

    const [params, setParams] = useState({
        search: filters.search || '',
        status: filters.status || '',
    });

    useEffect(() => {
        // Mirror server-driven filters back into the inputs on navigation.
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setParams({
            search: filters.search || '',
            status: filters.status || '',
        });
    }, [filters]);

    const handleViewModeChange = (mode: 'grid' | 'table') => {
        setViewMode(mode);

        if (typeof window !== 'undefined') {
            localStorage.setItem('radiology_results_view_mode', mode);
        }
    };

    const requestUpdate = useMemo(
        () =>
            debounce((query: Record<string, string>) => {
                router.get(route('radiology.results.index'), query, {
                    preserveState: true,
                    replace: true,
                });
            }, 300),
        [],
    );

    const handleFilterChange = (key: 'search' | 'status', value: string) => {
        const next = { ...params, [key]: value, page: '1' };

        setParams({ search: next.search, status: next.status });
        requestUpdate(next);
    };

    const statusLabel = (status: string) => {
        const fallback = status.replace(/_/g, ' ');
        const key = RADIOLOGY_STATUS_LABEL_KEYS[status];

        return key ? t(key, fallback) : fallback;
    };

    return (
        <AppLayout
            headerTitle={t('radiology.results.index.title')}
            headerSubtitle={t('radiology.results.index.subtitle')}
        >
            <Head title={t('radiology.results.index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={CameraIcon}
                    eyebrow={t('radiology.results.index.archive')}
                    title={t('radiology.results.index.title')}
                    description={t('radiology.results.index.subtitle')}
                    breadcrumbs={[
                        { label: t('radiology.results.index.archive') },
                    ]}
                    meta={
                        <span className="tabular-nums">
                            {results.total}{' '}
                            {t('radiology.results.index.archive')}
                        </span>
                    }
                    actions={
                        <Button
                            variant="secondary"
                            icon={ArrowDownTrayIcon}
                            href={route('radiology.requests.index')}
                        >
                            {t('radiology.results.index.back_to_requests')}
                        </Button>
                    }
                />

                <StatGrid>
                    <StatCard
                        icon={CameraIcon}
                        label={t('radiology.results.index.archive')}
                        value={stats.total}
                        tone="accent"
                    />
                    <StatCard
                        icon={CheckBadgeIcon}
                        label={t('radiology.results.index.validated')}
                        value={stats.validated}
                        tone="positive"
                    />
                    <StatCard
                        icon={DocumentCheckIcon}
                        label={t('radiology.results.index.signed')}
                        value={stats.signed}
                        tone="neutral"
                    />
                    <StatCard
                        icon={ClockIcon}
                        label={t('radiology.results.index.in_progress')}
                        value={stats.inProgress}
                        tone="warning"
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={params.search}
                        onChange={(value) =>
                            handleFilterChange('search', value)
                        }
                        placeholder={t(
                            'radiology.results.index.search_placeholder',
                        )}
                    />
                    <Select
                        aria-label={t('radiology.results.index.status')}
                        className="h-9 w-auto text-[13px]"
                        value={params.status}
                        onChange={(event) =>
                            handleFilterChange('status', event.target.value)
                        }
                    >
                        <option value="">
                            {t('radiology.results.index.all_statuses')}
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
                        onChange={(value) =>
                            handleViewModeChange(value as 'grid' | 'table')
                        }
                        size="sm"
                        options={[
                            {
                                value: 'grid',
                                icon: Squares2X2Icon,
                                ariaLabel: 'Grid view',
                            },
                            {
                                value: 'table',
                                label: t('common.table', 'Table'),
                                ariaLabel: 'Table view',
                            },
                        ]}
                    />
                </Toolbar>

                {results.data.length === 0 ? (
                    <EmptyState
                        icon={InboxIcon}
                        title={t('radiology.results.index.no_results')}
                        description={t(
                            'radiology.results.index.no_results_description',
                        )}
                        action={{
                            label: t(
                                'radiology.results.index.back_to_requests',
                            ),
                            href: route('radiology.requests.index'),
                        }}
                    />
                ) : viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {results.data.map((result) => (
                            <ResultArchiveCard
                                key={result.id}
                                result={result}
                                statusLabel={statusLabel(result.status)}
                                editHref={route(
                                    'radiology.results.edit',
                                    result.id,
                                )}
                                openRequestHref={
                                    result.request_id
                                        ? route(
                                              'radiology.requests.show',
                                              result.request_id,
                                          )
                                        : null
                                }
                                labels={{
                                    edit: t('radiology.results.index.edit'),
                                    request: t(
                                        'radiology.results.index.open_request',
                                    ),
                                    noFindings: t(
                                        'radiology.results.index.no_findings',
                                    ),
                                }}
                            />
                        ))}
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    {t('radiology.results.index.patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('radiology.results.index.exam')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('radiology.results.index.report_date')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('radiology.results.index.status')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">
                                        {t('common.actions', 'Actions')}
                                    </span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {results.data.map((result) => (
                                <RadiologyRequestTableRow
                                    key={result.id}
                                    request={result}
                                    statusLabel={statusLabel(result.status)}
                                    date={result.report_date}
                                    dateIcon={
                                        <CalendarIcon className="h-3.5 w-3.5 text-ink-subtle" />
                                    }
                                    actionLabel={t('common.edit')}
                                    actionHref={route(
                                        'radiology.results.edit',
                                        result.id,
                                    )}
                                />
                            ))}
                        </TableBody>
                    </Table>
                )}

                <Pagination
                    links={results.links}
                    meta={{
                        from: results.from || 0,
                        to: results.to || 0,
                        total: results.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
