import {
    CalendarDaysIcon,
    ClockIcon,
    ExclamationCircleIcon,
    EyeIcon,
    InboxIcon,
    Squares2X2Icon,
    ListBulletIcon,
    BanknotesIcon,
    PlusIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Badge,
    Button,
    Card,
    EmptyState,
    KeyValue,
    KeyValueGrid,
    PageHeader,
    SegmentedControl,
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
    Avatar,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Surgery {
    id: number;
    patient?: { id: number; first_name: string; last_name: string };
    surgeon?: { id: number; name: string };
    surgery_name: string;
    scheduled_at: string;
    urgency: string;
    status: string;
    surgery_fee: number;
}

interface Props {
    surgeries: {
        data: Surgery[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        total?: number;
        from?: number;
        to?: number;
    };
}

const URGENCY_TONE: Record<string, BadgeTone> = {
    emergency: 'critical',
    urgent: 'warning',
    routine: 'accent',
};

const STATUS_TONE: Record<string, BadgeTone> = {
    completed: 'positive',
    in_progress: 'warning',
    cancelled: 'critical',
    scheduled: 'accent',
};

const urgencyTone = (urgency: string): BadgeTone =>
    URGENCY_TONE[urgency.toLowerCase()] ?? 'accent';

const statusTone = (status: string): BadgeTone =>
    STATUS_TONE[status.toLowerCase()] ?? 'accent';

/**
 * Surgeries/Index — the theatre schedule. Page header, KPI strip, a toolbar
 * with the status filter and view switch, then either a table or a card grid.
 */
export default function SurgeriesIndex({ surgeries }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState('all');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('table');

    // Schedule stats derived from the current page of surgeries.
    const stats = useMemo(() => {
        const total = surgeries.data.length;
        const emergencyCount = surgeries.data.filter(
            (surgery) => surgery.urgency.toLowerCase() === 'emergency',
        ).length;
        const activeOrScheduled = surgeries.data.filter(
            (surgery) =>
                surgery.status === 'scheduled' ||
                surgery.status === 'in_progress',
        ).length;
        const totalFees = surgeries.data.reduce(
            (sum, surgery) => sum + Number(surgery.surgery_fee || 0),
            0,
        );

        return { total, emergencyCount, activeOrScheduled, totalFees };
    }, [surgeries.data]);

    // Search and status filtering stay client-side over the paginated page.
    const filtered = useMemo(() => {
        let rows = surgeries.data;

        if (filter !== 'all') {
            rows = rows.filter((surgery) => surgery.status === filter);
        }

        if (search) {
            const query = search.toLowerCase();
            rows = rows.filter(
                (surgery) =>
                    surgery.surgery_name.toLowerCase().includes(query) ||
                    surgery.patient?.first_name
                        ?.toLowerCase()
                        .includes(query) ||
                    surgery.patient?.last_name?.toLowerCase().includes(query) ||
                    surgery.surgeon?.name?.toLowerCase().includes(query),
            );
        }

        return rows;
    }, [surgeries.data, filter, search]);

    const statusFilters = [
        'all',
        'scheduled',
        'in_progress',
        'completed',
        'cancelled',
    ];

    return (
        <AppLayout
            headerTitle={t('surgeries.index.title')}
            headerSubtitle={t('surgeries.index.subtitle')}
        >
            <Head title={t('surgeries.index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-5">
                <PageHeader
                    title={t('surgeries.index.title')}
                    description={t('surgeries.index.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: route('home') },
                        { label: t('surgeries.index.surgeries') },
                    ]}
                    actions={
                        <Button
                            href={route('surgeries.create')}
                            icon={PlusIcon}
                        >
                            {t('surgeries.index.schedule_surgery')}
                        </Button>
                    }
                />

                <StatGrid>
                    <StatCard
                        label={t('surgeries.index.total_surgeries')}
                        value={stats.total}
                        icon={ClockIcon}
                        tone="accent"
                    />
                    <StatCard
                        label={t('surgeries.index.scheduled_active')}
                        value={stats.activeOrScheduled}
                        icon={CalendarDaysIcon}
                        tone="warning"
                    />
                    <StatCard
                        label={t('surgeries.index.emergencies')}
                        value={stats.emergencyCount}
                        icon={ExclamationCircleIcon}
                        tone="critical"
                    />
                    <StatCard
                        label={t('surgeries.index.total_fee_value')}
                        value={`${stats.totalFees.toLocaleString()} FCFA`}
                        icon={BanknotesIcon}
                        tone="positive"
                    />
                </StatGrid>

                <Toolbar>
                    <div className="flex flex-wrap items-center gap-1">
                        {statusFilters.map((status) => (
                            <Button
                                key={status}
                                variant={
                                    filter === status ? 'secondary' : 'ghost'
                                }
                                size="sm"
                                onClick={() => setFilter(status)}
                            >
                                {t(`surgeries.index.filter_${status}`)}
                            </Button>
                        ))}
                    </div>

                    <ToolbarSpacer />

                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder={t('surgeries.index.search_placeholder')}
                    />

                    <SegmentedControl
                        value={viewMode}
                        onChange={setViewMode}
                        options={[
                            {
                                value: 'grid',
                                icon: Squares2X2Icon,
                                ariaLabel: 'grid',
                            },
                            {
                                value: 'table',
                                icon: ListBulletIcon,
                                ariaLabel: 'table',
                            },
                        ]}
                    />
                </Toolbar>

                {filtered.length === 0 ? (
                    <EmptyState
                        icon={InboxIcon}
                        title={t('surgeries.index.no_surgeries')}
                        description={t(
                            'surgeries.index.no_surgeries_description',
                        )}
                        action={{
                            label: t('surgeries.index.schedule_first_surgery'),
                            href: route('surgeries.create'),
                            icon: PlusIcon,
                        }}
                    />
                ) : viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {filtered.map((surgery) => (
                            <Card
                                key={surgery.id}
                                className="flex h-full flex-col p-5"
                            >
                                <div className="mb-4 flex items-start justify-between gap-3">
                                    <Avatar
                                        size="md"
                                        name={`${surgery.patient?.first_name ?? ''} ${surgery.patient?.last_name ?? ''}`}
                                    />
                                    <div className="flex flex-col items-end gap-1.5">
                                        <Badge
                                            tone={urgencyTone(surgery.urgency)}
                                            variant="outline"
                                        >
                                            {surgery.urgency}
                                        </Badge>
                                        <Badge
                                            tone={statusTone(surgery.status)}
                                        >
                                            {surgery.status.replace('_', ' ')}
                                        </Badge>
                                    </div>
                                </div>

                                <div className="mb-4 flex-1">
                                    <h3 className="truncate text-[15px] font-semibold tracking-tight text-ink">
                                        {surgery.surgery_name}
                                    </h3>
                                    <p className="mt-0.5 truncate text-[12px] text-ink-subtle">
                                        {t('surgeries.index.patient_label')}{' '}
                                        {surgery.patient?.first_name}{' '}
                                        {surgery.patient?.last_name}
                                    </p>

                                    <KeyValueGrid
                                        columns={2}
                                        className="mt-4"
                                        tile={false}
                                    >
                                        <KeyValue
                                            label={t(
                                                'surgeries.index.scheduled',
                                            )}
                                            icon={ClockIcon}
                                            value={new Date(
                                                surgery.scheduled_at,
                                            ).toLocaleDateString()}
                                        />
                                        <KeyValue
                                            label={t('surgeries.index.surgeon')}
                                            value={surgery.surgeon?.name}
                                        />
                                    </KeyValueGrid>
                                </div>

                                <div className="flex items-center justify-between border-t border-line pt-4">
                                    <span className="text-[15px] font-semibold text-ink tabular-nums">
                                        {surgery.surgery_fee.toLocaleString()}{' '}
                                        FCFA
                                    </span>
                                    <Button
                                        href={route(
                                            'surgeries.show',
                                            surgery.id,
                                        )}
                                        variant="ghost"
                                        size="icon"
                                        icon={EyeIcon}
                                        aria-label={t(
                                            'surgeries.index.patient_label',
                                        )}
                                    />
                                </div>
                            </Card>
                        ))}
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t('surgeries.index.patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('surgeries.index.procedure')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('surgeries.index.surgeon')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('surgeries.index.scheduled')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('surgeries.index.status')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('surgeries.index.fee')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    <span className="sr-only">
                                        {t('surgeries.index.status')}
                                    </span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.map((surgery) => (
                                <TableRow key={surgery.id}>
                                    <TableCell>
                                        <span className="flex items-center gap-2.5">
                                            <Avatar
                                                size="xs"
                                                name={`${surgery.patient?.first_name ?? ''} ${surgery.patient?.last_name ?? ''}`}
                                            />
                                            <span className="truncate font-medium">
                                                {surgery.patient?.first_name}{' '}
                                                {surgery.patient?.last_name}
                                            </span>
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="block font-medium text-ink">
                                            {surgery.surgery_name}
                                        </span>
                                        <Badge
                                            tone={urgencyTone(surgery.urgency)}
                                            variant="outline"
                                            className="mt-1"
                                        >
                                            {surgery.urgency}
                                        </Badge>
                                    </TableCell>
                                    <TableCell muted>
                                        {surgery.surgeon?.name ?? '—'}
                                    </TableCell>
                                    <TableCell muted>
                                        {new Date(
                                            surgery.scheduled_at,
                                        ).toLocaleDateString()}
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={statusTone(surgery.status)}
                                        >
                                            {surgery.status.replace('_', ' ')}
                                        </Badge>
                                    </TableCell>
                                    <TableCell numeric>
                                        {surgery.surgery_fee.toLocaleString()}{' '}
                                        FCFA
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <Button
                                            href={route(
                                                'surgeries.show',
                                                surgery.id,
                                            )}
                                            variant="ghost"
                                            size="icon"
                                            icon={EyeIcon}
                                            aria-label={t(
                                                'surgeries.index.patient_label',
                                            )}
                                        />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}

                {surgeries.links.length > 0 && filtered.length > 0 && (
                    <div className="space-y-3">
                        <p className="text-[12px] text-ink-subtle tabular-nums">
                            {t('surgeries.index.showing_entries', {
                                from: surgeries.from || 0,
                                to: surgeries.to || 0,
                                total: surgeries.total || surgeries.data.length,
                            })}
                        </p>
                        <Pagination links={surgeries.links} />
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
