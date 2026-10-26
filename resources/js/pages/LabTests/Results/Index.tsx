import {
    ArrowDownTrayIcon,
    BeakerIcon,
    CalendarIcon,
    CheckBadgeIcon,
    ClockIcon,
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
    TableCell,
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
    LabSourceBadge,
    labTestName,
    LabPatientIdentity,
    LabRequestTableRow,
} from '@/pages/LabTests/components/LabRequestCard';
import type { LabRequestRow } from '@/pages/LabTests/components/LabRequestCard';
import { ResultStatusBadge } from '@/pages/LabTests/components/ResultStatusBadge';

interface LabResult extends LabRequestRow {
    result_date: string;
}

interface Props {
    results: {
        data: LabResult[];
        links: { url: string | null; label: string; active: boolean }[];
        total: number;
        from: number;
        to: number;
    };
    filters: {
        search?: string;
        status?: string;
    };
}

/**
 * ResultsArchiveCard — a completed/in-progress result tile for the archive
 * grid view. Patient identity, test profile and the verification date.
 */
function ResultsArchiveCard({
    result,
    editHref,
}: {
    result: LabResult;
    editHref: string;
}) {
    return (
        <article className="flex h-full flex-col rounded-xl border border-line bg-surface p-5 transition-[border-color,transform] duration-200 ease-premium hover:-translate-y-0.5 hover:border-line-strong">
            <div className="mb-3.5 flex items-start justify-between gap-3">
                <LabPatientIdentity request={result} avatarSize="md" />
                <ResultStatusBadge status={result.status} />
            </div>

            <div className="mt-1 flex items-center gap-2 rounded-lg bg-surface-muted px-3.5 py-2.5">
                <BeakerIcon className="h-4 w-4 shrink-0 text-accent" />
                <div className="min-w-0">
                    <div className="truncate text-[13px] font-medium text-ink">
                        {labTestName(result)}
                    </div>
                    {result.test_definition?.code && (
                        <div className="mt-0.5 text-[10px] tracking-[0.1em] text-ink-subtle uppercase">
                            {result.test_definition.code}
                        </div>
                    )}
                </div>
            </div>

            <div className="mt-auto flex items-center justify-between gap-3 border-t border-line pt-3.5">
                <span className="inline-flex items-center gap-1.5 text-[11px] whitespace-nowrap text-ink-subtle tabular-nums">
                    <CalendarIcon className="h-3.5 w-3.5" />
                    {formatLabDate(result.result_date)}
                </span>
                <div className="flex items-center gap-1">
                    <Button
                        href={editHref}
                        variant="ghost"
                        size="icon"
                        icon={PencilSquareIcon}
                        aria-label="Edit result"
                    />
                    <Button
                        variant="ghost"
                        size="icon"
                        icon={ArrowDownTrayIcon}
                        aria-label="Download result"
                    />
                </div>
            </div>
        </article>
    );
}

export default function ResultsIndex({ results, filters }: Props) {
    const { t } = useTranslation();

    // Persist selected view format (grid vs table) locally
    const [viewMode, setViewMode] = useState<'grid' | 'table'>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('results_view_mode');

            if (saved === 'grid' || saved === 'table') {
                return saved;
            }
        }

        return 'table';
    });

    const [params, setParams] = useState({
        search: filters.search || '',
        status: filters.status || '',
    });

    // Sync input parameters with query filters on navigation
    useEffect(() => {
        // Mirror server-driven filters into local params on navigation.
        // eslint-disable-next-line react-hooks/set-state-in-effect
        setParams({
            search: filters.search || '',
            status: filters.status || '',
        });
    }, [filters]);

    const handleViewModeChange = (mode: 'grid' | 'table') => {
        setViewMode(mode);

        if (typeof window !== 'undefined') {
            localStorage.setItem('results_view_mode', mode);
        }
    };

    const stats = useMemo(
        () => ({
            total: results.total,
            completed: results.data.filter((r) => r.status === 'Completed')
                .length,
            inProgress: results.data.filter((r) => r.status === 'In Progress')
                .length,
        }),
        [results],
    );

    const requestUpdate = useMemo(
        () =>
            debounce((query) => {
                router.get(route('lab-results.index'), query, {
                    preserveState: true,
                    replace: true,
                });
            }, 300),
        [],
    );

    const handleFilterChange = (key: string, value: string) => {
        const newParams = {
            ...params,
            [key]: value,
            page: '1',
        };
        setParams(newParams);
        requestUpdate(newParams);
    };

    return (
        <AppLayout
            headerTitle={t('lab_tests.results.index.title')}
            headerSubtitle={t('lab_tests.results.index.subtitle')}
        >
            <Head title={t('lab_tests.results.index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={BeakerIcon}
                    eyebrow={t('lab_tests.results.index.results_archive')}
                    title={t('lab_tests.results.index.title')}
                    description={t('lab_tests.results.index.subtitle')}
                    breadcrumbs={[
                        { label: t('lab_tests.results.index.results_archive') },
                    ]}
                    meta={
                        <>
                            <span className="tabular-nums">
                                {results.total}{' '}
                                {t('lab_tests.results.index.results_archive')}
                            </span>
                        </>
                    }
                    actions={
                        <Button variant="secondary" icon={ArrowDownTrayIcon}>
                            {t('lab_tests.results.index.export')}
                        </Button>
                    }
                />

                <StatGrid columns={3}>
                    <StatCard
                        icon={BeakerIcon}
                        label={t('lab_tests.results.index.results_archive')}
                        value={stats.total}
                        tone="accent"
                    />
                    <StatCard
                        icon={CheckBadgeIcon}
                        label={t('lab_tests.results.index.completed')}
                        value={stats.completed}
                        tone="positive"
                    />
                    <StatCard
                        icon={ClockIcon}
                        label={t('lab_tests.results.index.in_progress')}
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
                            'lab_tests.results.index.search_placeholder',
                        )}
                    />
                    <Select
                        aria-label={t('lab_tests.results.index.status')}
                        className="h-9 w-auto text-[13px]"
                        value={params.status}
                        onChange={(event) =>
                            handleFilterChange('status', event.target.value)
                        }
                    >
                        <option value="">
                            {t('lab_tests.results.index.all_statuses')}
                        </option>
                        <option value="Completed">
                            {t('lab_tests.results.index.completed')}
                        </option>
                        <option value="In Progress">
                            {t('lab_tests.results.index.in_progress')}
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
                                label: 'Table',
                                ariaLabel: 'Table view',
                            },
                        ]}
                    />
                </Toolbar>

                {results.data.length === 0 ? (
                    <EmptyState
                        icon={InboxIcon}
                        title={t(
                            'lab_tests.results.index.no_results',
                            'No Results Found',
                        )}
                        description={t(
                            'lab_tests.results.index.no_results_description',
                            'Please adjust filters or try a different search query.',
                        )}
                    />
                ) : viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {results.data.map((result) => (
                            <ResultsArchiveCard
                                key={result.id}
                                result={result}
                                editHref={route('results.edit', result.id)}
                            />
                        ))}
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    {t(
                                        'lab_tests.results.index.patient_details',
                                    )}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('lab_tests.results.index.test_profile')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    <span className="sr-only">
                                        {t(
                                            'lab_tests.results.index.source',
                                            'Source',
                                        )}
                                    </span>
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('lab_tests.results.index.result_date')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('lab_tests.results.index.status')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">
                                        {t('common.actions')}
                                    </span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {results.data.map((result) => (
                                <LabRequestTableRow
                                    key={result.id}
                                    request={result}
                                    extraCells={
                                        <TableCell muted>
                                            <LabSourceBadge request={result} />
                                        </TableCell>
                                    }
                                    date={result.result_date}
                                    dateIcon={
                                        <CalendarIcon className="h-3.5 w-3.5 text-ink-subtle" />
                                    }
                                    actionLabel={t('common.edit')}
                                    actionHref={route(
                                        'results.edit',
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
                        from: results.from,
                        to: results.to,
                        total: results.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
