import {
    BuildingOfficeIcon,
    ClockIcon,
    CurrencyDollarIcon,
    PlusIcon,
    TicketIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import {
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
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';

interface Metrics {
    total_tenants: number;
    active_tenants: number;
    active_subscriptions: number;
    monthly_revenue: number;
    yearly_revenue: number;
    pending_demo_requests: number;
    open_tickets: number;
}

interface RecentTenant {
    id: string;
    name: string;
    domain: string | null;
    is_active: boolean;
    plan_name: string | null;
    created_at: string;
}

interface Props {
    metrics: Metrics;
    recentTenants: RecentTenant[];
    recentActivity: {
        type: string;
        label: string;
        detail: string;
        time: string;
    }[];
}

export default function SuperAdminDashboard({
    metrics,
    recentTenants,
    recentActivity,
}: Props) {
    const { t } = useTranslation();

    const stats = [
        {
            label: 'Total tenants',
            value: metrics.total_tenants,
            sub: `${metrics.active_tenants} active`,
            icon: BuildingOfficeIcon,
            tone: 'accent' as const,
        },
        {
            label: 'Active subscriptions',
            value: metrics.active_subscriptions,
            sub: `$${metrics.monthly_revenue}/mo MRR`,
            icon: CurrencyDollarIcon,
            tone: 'positive' as const,
        },
        {
            label: 'Demo requests',
            value: metrics.pending_demo_requests,
            sub: 'Pending review',
            icon: UserGroupIcon,
            tone: 'warning' as const,
        },
        {
            label: 'Open tickets',
            value: metrics.open_tickets,
            sub: 'Needs attention',
            icon: TicketIcon,
            tone: 'critical' as const,
        },
    ];

    return (
        <SuperAdminLayout
            headerTitle={t('page.super_admin_dashboard.title')}
            headerSubtitle={t('page.super_admin_dashboard.subtitle')}
        >
            <Head title={t('page.super_admin_dashboard.page_title')} />

            <div className="space-y-5 pb-12">
                <PageHeader
                    eyebrow={t('nav.category.tenant_management')}
                    title={t('page.super_admin_dashboard.title')}
                    description={t('page.super_admin_dashboard.subtitle')}
                    actions={
                        <Button href="/tenants/create" icon={PlusIcon}>
                            New tenant
                        </Button>
                    }
                />

                <StatGrid>
                    {stats.map((stat) => (
                        <StatCard
                            key={stat.label}
                            label={stat.label}
                            value={stat.value}
                            icon={stat.icon}
                            tone={stat.tone}
                            hint={stat.sub}
                        />
                    ))}
                </StatGrid>

                <div className="grid gap-5 lg:grid-cols-3">
                    <Card className="lg:col-span-2">
                        <div className="flex items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
                            <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                                Recent tenants
                            </h2>
                            <Button
                                href="/tenants/create"
                                variant="ghost"
                                size="sm"
                                icon={PlusIcon}
                            >
                                New tenant
                            </Button>
                        </div>

                        {recentTenants.length > 0 ? (
                            <Table bare>
                                <TableHeader>
                                    <TableRow>
                                        <TableHeaderCell>
                                            Hospital
                                        </TableHeaderCell>
                                        <TableHeaderCell>
                                            Domain
                                        </TableHeaderCell>
                                        <TableHeaderCell>Plan</TableHeaderCell>
                                        <TableHeaderCell>
                                            Status
                                        </TableHeaderCell>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {recentTenants.map((tenant) => (
                                        <TableRow key={tenant.id}>
                                            <TableCell>
                                                <Link
                                                    href={`/tenants/${tenant.id}`}
                                                    className="font-medium text-ink transition-colors duration-150 hover:text-accent"
                                                >
                                                    {tenant.name}
                                                </Link>
                                            </TableCell>
                                            <TableCell muted>
                                                {tenant.domain || '—'}
                                            </TableCell>
                                            <TableCell muted>
                                                {tenant.plan_name || 'No plan'}
                                            </TableCell>
                                            <TableCell>
                                                <Badge
                                                    tone={
                                                        tenant.is_active
                                                            ? 'positive'
                                                            : 'critical'
                                                    }
                                                    dot
                                                >
                                                    {tenant.is_active
                                                        ? 'Active'
                                                        : 'Suspended'}
                                                </Badge>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        ) : (
                            <div className="p-5 sm:p-6">
                                <EmptyState
                                    panel={false}
                                    icon={BuildingOfficeIcon}
                                    title="No tenants yet"
                                    description="Tenants you create will appear here."
                                    action={{
                                        label: 'New tenant',
                                        href: '/tenants/create',
                                    }}
                                />
                            </div>
                        )}
                    </Card>

                    <Card>
                        <div className="flex items-center justify-between gap-3 border-b border-line px-5 py-4">
                            <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                                Recent activity
                            </h2>
                            <Badge tone="muted">{recentActivity.length}</Badge>
                        </div>

                        <div className="px-5 py-1">
                            {recentActivity.length === 0 ? (
                                <EmptyState
                                    panel={false}
                                    icon={ClockIcon}
                                    title="No recent activity"
                                />
                            ) : (
                                <ul className="divide-y divide-line">
                                    {recentActivity.map((activity, index) => (
                                        <li
                                            key={`${activity.time}-${index}`}
                                            className="flex items-start gap-3 py-3"
                                        >
                                            <span className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-accent" />
                                            <div className="min-w-0 flex-1">
                                                <p className="text-[13px] font-medium text-ink">
                                                    {activity.label}
                                                </p>
                                                <p className="truncate text-[11.5px] text-ink-subtle">
                                                    {activity.detail}
                                                </p>
                                                <p className="mt-0.5 text-[11px] text-ink-subtle tabular-nums">
                                                    {activity.time}
                                                </p>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>
                    </Card>
                </div>
            </div>
        </SuperAdminLayout>
    );
}
