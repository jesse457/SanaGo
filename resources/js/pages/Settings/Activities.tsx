import {
    CalendarIcon,
    FunnelIcon,
    MagnifyingGlassIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useEffect, useMemo, useRef, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Alert,
    Avatar,
    Badge,
    Button,
    Card,
    EmptyState,
    PageHeader,
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
import { ActivityDetailsDialog } from '@/pages/Settings/components/ActivityDetailsDialog';
import {
    formatEventName,
    formatFieldName,
    getActivityTone,
} from '@/pages/Settings/components/activityFormatters';

// --- Types ---
interface User {
    name: string;
    email: string;
}

export interface Activity {
    id: number;
    activity_type: string;
    description: string;
    subject_type?: string | null;
    subject_id?: number | null;
    created_at: string;
    user: User | null;
    metadata?: Record<string, any>;
    attribute_changes?: {
        attributes?: Record<string, any> | null;
        old?: Record<string, any> | null;
    } | null;
    ip_address?: string;
    user_agent?: string;
}

interface PaginationProps {
    data: Activity[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number;
    to: number;
    total: number;
    current_page: number;
}

interface Props {
    activities: PaginationProps;
    filters: {
        search?: string;
        date?: string;
        type?: string;
    };
}

const TONE_TO_BADGE: Record<string, BadgeTone> = {
    positive: 'positive',
    accent: 'accent',
    warning: 'warning',
    critical: 'critical',
    neutral: 'neutral',
};

export default function UserActivities({ activities, filters }: Props) {
    const { t } = useTranslation();

    // --- State ---
    const [search, setSearch] = useState(filters.search || '');
    const [dateFilter, setDateFilter] = useState(filters.date || '');
    const [filterType, setFilterType] = useState(filters.type || '');
    const [isLoading, setIsLoading] = useState(false);
    const [selectedActivity, setSelectedActivity] = useState<Activity | null>(
        null,
    );

    // --- Server-Side Filtering Logic ---
    const updateFilters = useMemo(
        () =>
            debounce((query: any) => {
                router.get(route('activities.index'), query, {
                    preserveState: true,
                    preserveScroll: true,
                    onBefore: () => setIsLoading(true),
                    onFinish: () => setIsLoading(false),
                });
            }, 300),
        [],
    );

    // The server already returned a filtered first page; don't refetch it on
    // mount just to land on the same URL.
    const hasMounted = useRef(false);

    useEffect(() => {
        if (!hasMounted.current) {
            hasMounted.current = true;

            return;
        }

        updateFilters({ search, date: dateFilter, type: filterType });
    }, [updateFilters, search, dateFilter, filterType]);

    useEffect(() => () => updateFilters.cancel(), [updateFilters]);

    const clearFilters = () => {
        setSearch('');
        setDateFilter('');
        setFilterType('');
    };

    const hasActiveFilters = Boolean(search || dateFilter || filterType);

    return (
        <AppLayout
            headerTitle={t('page.activities.title')}
            headerSubtitle={t('page.activities.subtitle')}
        >
            <Head title={t('page.activities.head_title')} />

            <div className="space-y-5 pb-12">
                <PageHeader
                    eyebrow={t('nav.category.administration')}
                    title={t('page.activities.title')}
                    description={t('page.activities.subtitle')}
                    breadcrumbs={[{ label: t('page.activities.head_title') }]}
                    meta={
                        <span className="tabular-nums">
                            {t('page.activities.showing_results', {
                                from: activities.from || 0,
                                to: activities.to || 0,
                                total: activities.total,
                            })}
                        </span>
                    }
                />

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder={t('page.activities.search_placeholder')}
                        busy={isLoading}
                    />

                    <label className="flex items-center gap-2 text-[12px] text-ink-subtle">
                        <CalendarIcon className="h-4 w-4 shrink-0" />
                        <input
                            type="date"
                            name="date"
                            value={dateFilter}
                            onChange={(event) =>
                                setDateFilter(event.target.value)
                            }
                            aria-label={t('page.activities.col_date')}
                            className="h-9 rounded-md border border-line bg-surface px-2.5 text-[13px] text-ink tabular-nums transition-[border-color,box-shadow] duration-200 ease-premium hover:border-line-strong focus:border-accent focus:ring-2 focus:ring-accent/25 focus:outline-none"
                        />
                    </label>

                    <label className="flex items-center gap-2 text-[12px] text-ink-subtle">
                        <FunnelIcon className="h-4 w-4 shrink-0" />
                        <select
                            name="type"
                            value={filterType}
                            onChange={(event) =>
                                setFilterType(event.target.value)
                            }
                            aria-label={t('page.activities.col_event')}
                            className="h-9 cursor-pointer rounded-md border border-line bg-surface px-2.5 text-[13px] text-ink transition-[border-color,box-shadow] duration-200 ease-premium hover:border-line-strong focus:border-accent focus:ring-2 focus:ring-accent/25 focus:outline-none"
                        >
                            <option value="">
                                {t('page.activities.all_events')}
                            </option>
                            <option value="created">
                                {t('page.activities.created')}
                            </option>
                            <option value="updated">
                                {t('page.activities.updated')}
                            </option>
                            <option value="deleted">
                                {t('page.activities.deleted')}
                            </option>
                            <option value="login">
                                {t('page.activities.logins')}
                            </option>
                        </select>
                    </label>

                    <ToolbarSpacer />

                    {hasActiveFilters && (
                        <Button
                            variant="ghost"
                            size="sm"
                            icon={TrashIcon}
                            onClick={clearFilters}
                        >
                            {t('page.activities.clear_filters')}
                        </Button>
                    )}
                </Toolbar>

                {isLoading && (
                    <Alert tone="info" title={t('page.activities.syncing')} />
                )}

                {activities.data.length > 0 ? (
                    <Card>
                        <Table bare>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>
                                        {t('page.activities.col_user')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.activities.col_event')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.activities.col_activity')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.activities.col_date')}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t('page.activities.col_action')}
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {activities.data.map((activity) => {
                                    const changedKeys = Object.keys(
                                        activity.attribute_changes
                                            ?.attributes ||
                                            activity.metadata?.attributes ||
                                            activity.metadata?.old ||
                                            {},
                                    );

                                    return (
                                        <TableRow key={activity.id}>
                                            <TableCell>
                                                <div className="flex items-center gap-3">
                                                    <Avatar
                                                        name={
                                                            activity.user
                                                                ?.name ??
                                                            t(
                                                                'page.activities.system',
                                                            )
                                                        }
                                                        size="sm"
                                                    />
                                                    <div className="min-w-0">
                                                        <p className="truncate text-[13px] font-medium text-ink">
                                                            {activity.user
                                                                ?.name ||
                                                                t(
                                                                    'page.activities.system',
                                                                )}
                                                        </p>
                                                        <p className="truncate text-[11.5px] text-ink-subtle">
                                                            {activity.user
                                                                ?.email ||
                                                                'automated@system.com'}
                                                        </p>
                                                    </div>
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <Badge
                                                    tone={
                                                        TONE_TO_BADGE[
                                                            getActivityTone(
                                                                activity.activity_type,
                                                            )
                                                        ]
                                                    }
                                                    dot
                                                >
                                                    {formatEventName(
                                                        activity.activity_type,
                                                    )}
                                                </Badge>
                                            </TableCell>
                                            <TableCell muted>
                                                <p className="line-clamp-1">
                                                    {activity.description}
                                                </p>
                                                {changedKeys.length > 0 && (
                                                    <div className="mt-1.5 flex flex-wrap gap-1">
                                                        {changedKeys.map(
                                                            (key) => (
                                                                <span
                                                                    key={key}
                                                                    className="rounded-full bg-surface-inset px-1.5 py-0.5 text-[10px] font-medium text-ink-subtle"
                                                                >
                                                                    {formatFieldName(
                                                                        key,
                                                                        t,
                                                                    )}
                                                                </span>
                                                            ),
                                                        )}
                                                    </div>
                                                )}
                                            </TableCell>
                                            <TableCell muted>
                                                <span className="block text-[13px] tabular-nums">
                                                    {new Date(
                                                        activity.created_at,
                                                    ).toLocaleDateString(
                                                        undefined,
                                                        {
                                                            month: 'short',
                                                            day: 'numeric',
                                                            year: 'numeric',
                                                        },
                                                    )}
                                                </span>
                                                <span className="block text-[11.5px] text-ink-subtle tabular-nums">
                                                    {new Date(
                                                        activity.created_at,
                                                    ).toLocaleTimeString([], {
                                                        hour: '2-digit',
                                                        minute: '2-digit',
                                                    })}
                                                </span>
                                            </TableCell>
                                            <TableCell numeric>
                                                <Button
                                                    variant="ghost"
                                                    size="icon"
                                                    icon={MagnifyingGlassIcon}
                                                    aria-label={t(
                                                        'page.activities.log_details',
                                                    )}
                                                    onClick={() =>
                                                        setSelectedActivity(
                                                            activity,
                                                        )
                                                    }
                                                />
                                            </TableCell>
                                        </TableRow>
                                    );
                                })}
                            </TableBody>
                        </Table>

                        <div className="border-t border-line px-4 py-3">
                            <Pagination
                                links={activities.links}
                                meta={{
                                    from: activities.from,
                                    to: activities.to,
                                    total: activities.total,
                                }}
                            />
                        </div>
                    </Card>
                ) : (
                    <EmptyState
                        icon={MagnifyingGlassIcon}
                        title={t('page.activities.no_results')}
                        secondaryAction={
                            hasActiveFilters ? (
                                <Button
                                    variant="secondary"
                                    onClick={clearFilters}
                                >
                                    {t('page.activities.clear_filters')}
                                </Button>
                            ) : undefined
                        }
                    />
                )}
            </div>

            <ActivityDetailsDialog
                activity={selectedActivity}
                onClose={() => setSelectedActivity(null)}
            />
        </AppLayout>
    );
}
