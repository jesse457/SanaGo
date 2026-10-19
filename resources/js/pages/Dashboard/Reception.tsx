import {
    BuildingOfficeIcon,
    CalendarDaysIcon,
    PlusCircleIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head } from '@inertiajs/react';
import { route } from 'ziggy-js';
import {
    Badge,
    Button,
    Card,
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

const Dashboard = () => {
    const { t } = useTranslation();
    const stats = [
        {
            label: t('page.reception_dashboard.patients_today'),
            count: 24,
            icon: UserGroupIcon,
            tone: 'accent' as const,
        },
        {
            label: t('page.reception_dashboard.appointments'),
            count: 18,
            icon: CalendarDaysIcon,
            tone: 'positive' as const,
        },
        {
            label: t('page.reception_dashboard.admissions_active'),
            count: 42,
            icon: BuildingOfficeIcon,
            tone: 'warning' as const,
        },
    ];

    const quickActions = [
        {
            label: t('page.reception_dashboard.register_patient'),
            href: route('patients.create'),
            icon: PlusCircleIcon,
        },
        {
            label: t('page.reception_dashboard.book_appointment'),
            href: route('appointments.create'),
            icon: CalendarDaysIcon,
        },
        {
            label: t('page.reception_dashboard.patient_checkin'),
            href: route('admissions.index'),
            icon: UserGroupIcon,
        },
    ];

    return (
        <AppLayout
            headerTitle={t('page.reception_dashboard.title')}
            headerSubtitle={t('page.reception_dashboard.subtitle')}
        >
            <Head title={t('page.reception_dashboard.title')} />

            <div className="space-y-5 pb-12">
                <PageHeader
                    eyebrow={t('nav.category.patient_care')}
                    title={t('page.reception_dashboard.title')}
                    description={t('page.reception_dashboard.subtitle')}
                    actions={
                        <Button
                            href={route('patients.create')}
                            icon={PlusCircleIcon}
                        >
                            {t('page.reception_dashboard.register_patient')}
                        </Button>
                    }
                />

                <StatGrid columns={3}>
                    {stats.map((stat) => (
                        <StatCard
                            key={stat.label}
                            label={stat.label}
                            value={stat.count}
                            icon={stat.icon}
                            tone={stat.tone}
                        />
                    ))}
                </StatGrid>

                <Card>
                    <div className="border-b border-line px-5 py-4 sm:px-6">
                        <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                            {t('page.reception_dashboard.quick_actions')}
                        </h2>
                    </div>
                    <div className="grid grid-cols-1 gap-2 p-4 sm:grid-cols-3 sm:p-5">
                        {quickActions.map((action) => (
                            <Button
                                key={action.href}
                                href={action.href}
                                variant="secondary"
                                icon={action.icon}
                                fullWidth
                                className="justify-start"
                            >
                                {action.label}
                            </Button>
                        ))}
                    </div>
                </Card>

                <Card>
                    <div className="flex items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
                        <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                            {t('page.reception_dashboard.waiting_queue')}
                        </h2>
                        <Badge tone="muted">0</Badge>
                    </div>

                    <Table bare>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t('page.reception_dashboard.patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('page.reception_dashboard.doctor')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('page.reception_dashboard.time')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('page.reception_dashboard.position')}
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow>
                                <TableCell
                                    colSpan={4}
                                    className="px-4 py-10 text-center text-ink-subtle"
                                >
                                    {t(
                                        'page.reception_dashboard.waiting_queue_empty',
                                    )}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </Card>
            </div>
        </AppLayout>
    );
};

export default Dashboard;
