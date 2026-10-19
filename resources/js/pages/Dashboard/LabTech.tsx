import {
    CheckCircleIcon,
    ClockIcon,
    DocumentMagnifyingGlassIcon,
    StopIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { route } from 'ziggy-js';
import {
    Alert,
    Badge,
    Button,
    Card,
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
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { LabUrgencyBadge } from '@/pages/Dashboard/components/LabUrgencyBadge';
import type { DashboardProps } from '@/types';

export default function Dashboard({
    completedTestsToday,
    inProgressTest,
    pendingLabRequests,
}: DashboardProps) {
    const { t } = useTranslation();

    const [isOffline, setIsOffline] = useState(!navigator.onLine);

    useEffect(() => {
        const handleStatus = () => setIsOffline(!navigator.onLine);
        window.addEventListener('online', handleStatus);
        window.addEventListener('offline', handleStatus);

        return () => {
            window.removeEventListener('online', handleStatus);
            window.removeEventListener('offline', handleStatus);
        };
    }, []);

    return (
        <AppLayout
            headerTitle={t('page.lab_dashboard.title')}
            headerSubtitle={t('page.lab_dashboard.subtitle')}
        >
            <Head title={t('page.lab_dashboard.title')} />

            <div className="space-y-5 pb-12">
                <PageHeader
                    eyebrow={t('nav.category.lab_diagnostics')}
                    title={t('page.lab_dashboard.title')}
                    description={t('page.lab_dashboard.subtitle')}
                    breadcrumbs={[
                        { label: t('common.home'), href: '/dashboard' },
                        { label: t('page.lab_dashboard.title') },
                    ]}
                    actions={
                        <Button
                            href={route('requests.index')}
                            variant="secondary"
                            icon={DocumentMagnifyingGlassIcon}
                        >
                            {t('page.lab_dashboard.view_all')}
                        </Button>
                    }
                    meta={
                        <Badge tone={isOffline ? 'warning' : 'positive'} dot>
                            {isOffline
                                ? t('page.lab_dashboard.offline')
                                : t('page.lab_dashboard.online')}
                        </Badge>
                    }
                />

                {isOffline && (
                    <Alert
                        tone="warning"
                        title={t('page.lab_dashboard.offline')}
                    >
                        {t('page.lab_dashboard.offline_message')}
                    </Alert>
                )}

                <StatGrid columns={3}>
                    <StatCard
                        icon={CheckCircleIcon}
                        label={t('page.lab_dashboard.tests_completed')}
                        value={completedTestsToday.length}
                        tone="positive"
                        hint={t('page.lab_dashboard.today')}
                    />
                    <StatCard
                        icon={StopIcon}
                        label={t('page.lab_dashboard.processing')}
                        value={inProgressTest.length}
                        tone="accent"
                        hint={t('page.lab_dashboard.currently_active')}
                    />
                    <StatCard
                        icon={ClockIcon}
                        label={t('page.lab_dashboard.pending_requests')}
                        value={pendingLabRequests.length}
                        tone="warning"
                        hint={t('page.lab_dashboard.needs_attention')}
                    />
                </StatGrid>

                <Card>
                    <div className="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
                        <div>
                            <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                                {t('page.lab_dashboard.pending_lab_requests')}
                            </h2>
                            <p className="mt-0.5 text-[12.5px] text-ink-subtle">
                                {t('page.lab_dashboard.queue_management')}
                            </p>
                        </div>
                        <Button
                            href={route('requests.index')}
                            variant="ghost"
                            size="sm"
                        >
                            {t('page.lab_dashboard.view_all')}
                        </Button>
                    </div>

                    {pendingLabRequests.length === 0 ? (
                        <div className="p-5 sm:p-6">
                            <EmptyState
                                panel={false}
                                icon={ClockIcon}
                                title={t(
                                    'page.lab_dashboard.no_pending_requests',
                                )}
                            />
                        </div>
                    ) : (
                        <Table bare>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>
                                        {t('page.lab_dashboard.patient')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.lab_dashboard.test')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.lab_dashboard.doctor')}
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        {t('page.lab_dashboard.urgency')}
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pendingLabRequests.map((request) => (
                                    <TableRow key={request.id}>
                                        <TableCell>
                                            <div className="flex items-center gap-3">
                                                <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-[11px] font-semibold text-ink-muted">
                                                    {request.patient?.first_name?.charAt(
                                                        0,
                                                    ) || '?'}
                                                </span>
                                                <div className="min-w-0">
                                                    <p className="truncate text-[13px] font-medium text-ink">
                                                        {
                                                            request.patient
                                                                ?.first_name
                                                        }{' '}
                                                        {
                                                            request.patient
                                                                ?.last_name
                                                        }
                                                    </p>
                                                    <p className="truncate font-mono text-[11px] text-ink-subtle">
                                                        ID:{' '}
                                                        {
                                                            request.patient
                                                                ?.patient_uid
                                                        }
                                                    </p>
                                                </div>
                                            </div>
                                        </TableCell>
                                        <TableCell muted>
                                            {request.test_definition?.test_name}
                                        </TableCell>
                                        <TableCell muted>
                                            Dr. {request.doctor?.name}
                                        </TableCell>
                                        <TableCell numeric>
                                            <LabUrgencyBadge
                                                level={request.urgency_level}
                                            />
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </Card>
            </div>
        </AppLayout>
    );
}
