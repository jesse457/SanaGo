import {
    BeakerIcon,
    CalendarDaysIcon,
    ChevronRightIcon,
    UsersIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import React from 'react';
import {
    Button,
    EmptyState,
    PageHeader,
    SectionCard,
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

const Dashboard = ({
    upcomingAppointments = [],
    incomingLabResults = [],
}: any) => {
    const { t } = useTranslation();

    const kpi = [
        {
            label: t('page.doctor_dashboard.total_patients'),
            count: 42,
            icon: UsersIcon,
            tone: 'accent' as const,
        },
        {
            label: t('page.doctor_dashboard.appointments'),
            count: 8,
            icon: CalendarDaysIcon,
            tone: 'positive' as const,
        },
        {
            label: t('page.doctor_dashboard.lab_results'),
            count: 3,
            icon: BeakerIcon,
            tone: 'warning' as const,
        },
    ];

    return (
        <div className="space-y-6">
            <Head title={t('page.doctor_dashboard.title')} />

            <PageHeader
                title={t('page.doctor_dashboard.title')}
                description={t('page.doctor_dashboard.subtitle')}
                icon={CalendarDaysIcon}
            />

            <StatGrid columns={3}>
                {kpi.map((item) => (
                    <StatCard
                        key={item.label}
                        label={item.label}
                        value={item.count.toLocaleString()}
                        icon={item.icon}
                        tone={item.tone}
                    />
                ))}
            </StatGrid>

            <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <SectionCard
                    title={t('page.doctor_dashboard.upcoming_schedule')}
                    icon={CalendarDaysIcon}
                    actions={
                        <Button variant="ghost" size="sm">
                            {t('page.doctor_dashboard.view_all')}
                        </Button>
                    }
                    contentClassName="px-0 py-0 sm:px-0"
                >
                    {upcomingAppointments.length > 0 ? (
                        <Table bare>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>
                                        {t('page.doctor_dashboard.time')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        {t('page.doctor_dashboard.patient')}
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        <span className="sr-only">Open</span>
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {upcomingAppointments.map(
                                    (appt: any, i: number) => (
                                        <TableRow key={i}>
                                            <TableCell className="font-medium text-accent-ink tabular-nums">
                                                {appt.appointment_time}
                                            </TableCell>
                                            <TableCell>
                                                {appt.patient_name}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <ChevronRightIcon className="ml-auto h-4 w-4 text-ink-subtle" />
                                            </TableCell>
                                        </TableRow>
                                    ),
                                )}
                            </TableBody>
                        </Table>
                    ) : (
                        <EmptyState
                            panel={false}
                            icon={CalendarDaysIcon}
                            title={t(
                                'page.doctor_dashboard.no_appointments_today',
                            )}
                            className="py-12"
                        />
                    )}
                </SectionCard>

                <SectionCard
                    title={t('page.doctor_dashboard.latest_lab_results')}
                    icon={BeakerIcon}
                    contentClassName="px-0 py-0 sm:px-0"
                >
                    {incomingLabResults.length > 0 ? (
                        <ul className="divide-y divide-line">
                            {incomingLabResults.map(
                                (result: any, i: number) => (
                                    <li
                                        key={i}
                                        className="flex items-center justify-between gap-3 px-5 py-3.5 transition-colors duration-150 ease-premium hover:bg-surface-hover sm:px-6"
                                    >
                                        <div className="flex min-w-0 items-center gap-3">
                                            <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-warning-soft text-warning-ink">
                                                <BeakerIcon className="h-4 w-4" />
                                            </span>
                                            <div className="min-w-0">
                                                <p className="truncate text-[13px] font-medium text-ink">
                                                    {result.test_name}
                                                </p>
                                                <p className="truncate text-[12px] text-ink-muted">
                                                    {result.patient_name}
                                                </p>
                                            </div>
                                        </div>
                                        <Button variant="secondary" size="sm">
                                            {t('page.doctor_dashboard.review')}
                                        </Button>
                                    </li>
                                ),
                            )}
                        </ul>
                    ) : (
                        <EmptyState
                            panel={false}
                            icon={BeakerIcon}
                            title={t(
                                'page.doctor_dashboard.no_pending_results',
                            )}
                            className="py-12"
                        />
                    )}
                </SectionCard>
            </div>
        </div>
    );
};

/**
 * Defining the layout as a property keeps it mounted across navigations.
 */
function DashboardLayout({ children }: { children: React.ReactNode }) {
    const { t } = useTranslation();

    return (
        <AppLayout
            headerTitle={t('page.doctor_dashboard.title')}
            headerSubtitle={t('page.doctor_dashboard.subtitle')}
        >
            {children}
        </AppLayout>
    );
}
Dashboard.layout = DashboardLayout;

export default Dashboard;
