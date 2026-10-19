import {
    ClockIcon,
    ExclamationTriangleIcon,
    HeartIcon,
    InboxIcon,
    PlusIcon,
    Squares2X2Icon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Button,
    EmptyState,
    PageHeader,
    SegmentedControl,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Tabs,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import {
    EmergencyCaseCard,
    EmergencyCaseRow,
} from '@/pages/Emergencies/components/EmergencyCaseCard';
import type {
    CaseLabels,
    EmergencyCase,
} from '@/pages/Emergencies/components/EmergencyCaseCard';

interface Props {
    emergencies: {
        data: EmergencyCase[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        total?: number;
        from?: number;
        to?: number;
    };
}

type StatusFilter = 'all' | 'triage' | 'active' | 'stabilized' | 'discharged';

export default function EmergenciesIndex({ emergencies }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState<StatusFilter>('all');
    const [viewMode, setViewMode] = useState<'grid' | 'table'>('table');

    const stats = useMemo(() => {
        const total = emergencies.data.length;
        const active = emergencies.data.filter(
            (e) => e.status === 'active',
        ).length;
        const critical = emergencies.data.filter(
            (e) => e.triage_level.toLowerCase() === 'emergency',
        ).length;
        const stabilized = emergencies.data.filter(
            (e) => e.status === 'stabilized',
        ).length;

        return { total, active, critical, stabilized };
    }, [emergencies.data]);

    // Handle Search and Status filtering locally
    const filtered = useMemo(() => {
        let rows = emergencies.data;

        if (filter !== 'all') {
            rows = rows.filter((e) => e.status === filter);
        }

        if (search) {
            const q = search.toLowerCase();
            rows = rows.filter(
                (e) =>
                    e.chief_complaint.toLowerCase().includes(q) ||
                    e.patient?.first_name?.toLowerCase().includes(q) ||
                    e.patient?.last_name?.toLowerCase().includes(q) ||
                    e.attending_doctor?.name?.toLowerCase().includes(q),
            );
        }

        return rows;
    }, [emergencies.data, filter, search]);

    const labels: CaseLabels = {
        complaining: t('complaining'),
        arrived: t('arrived'),
        physician: t('physician'),
        unassigned: t('unassigned'),
        fee: t('fee'),
        open: t('viewProfile'),
        unassignedDoctor: t('unassignedDoctor'),
    };

    const statusTabs: { value: StatusFilter; label: string }[] = [
        { value: 'all', label: t('all') },
        { value: 'triage', label: t('triage') },
        { value: 'active', label: t('active') },
        { value: 'stabilized', label: t('stabilized') },
        { value: 'discharged', label: t('discharged') },
    ];

    return (
        <AppLayout
            headerTitle={t('emergencyCases')}
            headerSubtitle={t('urgentClinicalCareRegistry')}
        >
            <Head title={t('emergencies')} />
            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageHeader
                    icon={HeartIcon}
                    eyebrow={t('emergencies')}
                    title={t('emergencyCases')}
                    description={t('urgentClinicalCareRegistry')}
                    breadcrumbs={[
                        { label: t('common.home'), href: route('dashboard') },
                        { label: t('emergencies') },
                    ]}
                    meta={
                        <>
                            <span className="tabular-nums">
                                {filtered.length} / {stats.total}
                            </span>
                            <span
                                aria-hidden="true"
                                className="text-line-strong"
                            >
                                /
                            </span>
                            <span className="tabular-nums">
                                {stats.critical} {t('criticalTriage')}
                            </span>
                        </>
                    }
                    actions={
                        <Button
                            icon={PlusIcon}
                            href={route('emergencies.create')}
                        >
                            {t('newEmergency')}
                        </Button>
                    }
                />

                <StatGrid>
                    <StatCard
                        icon={ClockIcon}
                        label={t('totalRegistry')}
                        value={stats.total}
                        tone="neutral"
                    />
                    <StatCard
                        icon={HeartIcon}
                        label={t('activeEmergencies')}
                        value={stats.active}
                        tone="critical"
                    />
                    <StatCard
                        icon={ExclamationTriangleIcon}
                        label={t('criticalTriage')}
                        value={stats.critical}
                        tone="warning"
                    />
                    <StatCard
                        icon={Squares2X2Icon}
                        label={t('stabilizedCases')}
                        value={stats.stabilized}
                        tone="positive"
                    />
                </StatGrid>

                <div className="space-y-3">
                    <Tabs
                        variant="pill"
                        value={filter}
                        onChange={(value) => setFilter(value as StatusFilter)}
                        items={statusTabs.map((tab) => ({
                            value: tab.value,
                            label: tab.label,
                        }))}
                    />

                    <Toolbar>
                        <ToolbarSearch
                            value={search}
                            onChange={setSearch}
                            placeholder={t('searchEmergencies')}
                        />
                        <ToolbarSpacer />
                        <SegmentedControl
                            value={viewMode}
                            onChange={setViewMode}
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
                </div>

                {filtered.length === 0 ? (
                    <EmptyState
                        icon={InboxIcon}
                        title={t('noEmergencyCases')}
                        description={t('noEmergencyCasesDescription')}
                        action={{
                            label: t('registerEmergencyCase'),
                            href: route('emergencies.create'),
                        }}
                    />
                ) : viewMode === 'grid' ? (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {filtered.map((emergency) => (
                            <EmergencyCaseCard
                                key={emergency.id}
                                emergency={emergency}
                                labels={labels}
                                viewHref={route(
                                    'emergencies.show',
                                    emergency.id,
                                )}
                            />
                        ))}
                    </div>
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow className="hover:bg-transparent">
                                <TableHeaderCell>
                                    {t('patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('complaint')}
                                </TableHeaderCell>
                                <TableHeaderCell>{t('triage')}</TableHeaderCell>
                                <TableHeaderCell>{t('status')}</TableHeaderCell>
                                <TableHeaderCell>
                                    {t('arrived')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">
                                        {t('common.actions')}
                                    </span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.map((emergency) => (
                                <EmergencyCaseRow
                                    key={emergency.id}
                                    emergency={emergency}
                                    labels={labels}
                                    viewHref={route(
                                        'emergencies.show',
                                        emergency.id,
                                    )}
                                />
                            ))}
                        </TableBody>
                    </Table>
                )}

                {emergencies.links &&
                    emergencies.links.length > 0 &&
                    filtered.length > 0 && (
                        <Pagination
                            links={emergencies.links}
                            meta={{
                                from: emergencies.from || 0,
                                to: emergencies.to || 0,
                                total:
                                    emergencies.total ||
                                    emergencies.data.length,
                            }}
                        />
                    )}
            </div>
        </AppLayout>
    );
}
