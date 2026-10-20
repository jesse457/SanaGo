import {
    BanknotesIcon,
    BuildingOfficeIcon,
    CalendarDateRangeIcon,
    PlusIcon,
    ShieldCheckIcon,
    TicketIcon,
    ArrowTrendingUpIcon,
    UserGroupIcon,
} from '@heroicons/react/24/outline';
import { Head, Link } from '@inertiajs/react';
import {
    Alert,
    Badge,
    Button,
    Card,
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
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import {
    formatCurrency,
    formatCurrencyCompact,
    formatDate,
    formatDateTime,
    formatNumber,
} from '@/pages/Platform/components/format';
import type {
    DashboardMetrics,
    ExpiringSubscription,
    RecentActivityRow,
    RecentTenantRow,
    RevenueSummary,
} from '@/pages/Platform/components/types';

interface SuperAdminDashboardProps {
    metrics: DashboardMetrics;
    recentTenants: RecentTenantRow[];
    recentActivity: RecentActivityRow[];
    revenue: RevenueSummary;
    expiringSoon: ExpiringSubscription[];
}

/**
 * Landing screen of the landlord console.
 *
 * Every number comes from `SuperAdminDashboardService` / `SubscriptionService`
 * — there are no illustrative figures here.
 */
export default function SuperAdminDashboard({
    metrics,
    recentTenants,
    recentActivity,
    revenue,
    expiringSoon,
}: SuperAdminDashboardProps) {
    const currency = metrics?.currency ?? revenue?.currency ?? 'XAF';
    const needsAttention =
        (metrics?.past_due_subscriptions ?? 0) > 0 ||
        (metrics?.suspended_tenants ?? 0) > 0;

    return (
        <SuperAdminLayout
            headerTitle="Platform overview"
            headerSubtitle="Hospitals, revenue and activity"
        >
            <Head title="Platform overview" />
            <div className="space-y-5 pb-12">
                <PageHeader
                    eyebrow="Platform"
                    title="Overview"
                    description="The state of the platform: how many hospitals are live, what they are worth and what needs an operator today."
                    icon={ShieldCheckIcon}
                    breadcrumbs={[{ label: 'Platform' }, { label: 'Overview' }]}
                    actions={
                        <>
                            <Button
                                variant="secondary"
                                href="/platform/audit"
                                icon={ShieldCheckIcon}
                            >
                                Audit log
                            </Button>
                            <Button href="/tenants/create" icon={PlusIcon}>
                                New hospital
                            </Button>
                        </>
                    }
                    meta={
                        <>
                            <span className="tabular-nums">
                                {metrics?.total_tenants ?? 0} hospitals
                            </span>
                            <span className="tabular-nums">
                                {metrics?.active_subscriptions ?? 0} active
                                subscriptions
                            </span>
                            <span className="tabular-nums">
                                Currency {currency}
                            </span>
                        </>
                    }
                />

                {needsAttention && (
                    <Alert tone="warning" title="Some hospitals need attention">
                        {metrics?.past_due_subscriptions ?? 0} subscription
                        {(metrics?.past_due_subscriptions ?? 0) === 1
                            ? ' is'
                            : 's are'}{' '}
                        past due and {metrics?.suspended_tenants ?? 0} hospital
                        {(metrics?.suspended_tenants ?? 0) === 1
                            ? ' is'
                            : 's are'}{' '}
                        suspended.{' '}
                        <a
                            href="/platform/health"
                            className="font-medium text-warning-ink underline underline-offset-2"
                        >
                            Open health & monitoring
                        </a>
                    </Alert>
                )}

                {/* ── Revenue ── */}
                <StatGrid columns={4}>
                    <StatCard
                        label="Monthly recurring revenue"
                        value={formatCurrencyCompact(metrics?.mrr, currency)}
                        icon={BanknotesIcon}
                        tone="positive"
                        hint={`${formatCurrency(metrics?.arr, currency)} annualised`}
                    />
                    <StatCard
                        label="Active hospitals"
                        value={metrics?.active_tenants ?? 0}
                        icon={BuildingOfficeIcon}
                        tone="accent"
                        hint={`${metrics?.total_tenants ?? 0} total · ${metrics?.archived_tenants ?? 0} archived`}
                    />
                    <StatCard
                        label="Active subscriptions"
                        value={metrics?.active_subscriptions ?? 0}
                        icon={ArrowTrendingUpIcon}
                        tone="positive"
                        hint={`${metrics?.trialing_subscriptions ?? 0} on trial`}
                    />
                    <StatCard
                        label="Past due"
                        value={metrics?.past_due_subscriptions ?? 0}
                        icon={CalendarDateRangeIcon}
                        tone={
                            (metrics?.past_due_subscriptions ?? 0) > 0
                                ? 'critical'
                                : 'neutral'
                        }
                        hint={
                            (metrics?.past_due_subscriptions ?? 0) > 0
                                ? 'Collection needed'
                                : 'Nothing overdue'
                        }
                    />
                </StatGrid>

                {/* ── Growth + lifecycle ── */}
                <StatGrid columns={4}>
                    <StatCard
                        label="Demo requests"
                        value={metrics?.pending_demo_requests ?? 0}
                        icon={UserGroupIcon}
                        tone="warning"
                        hint="Awaiting first contact"
                    />
                    <StatCard
                        label="Open tickets"
                        value={metrics?.open_tickets ?? 0}
                        icon={TicketIcon}
                        tone={
                            (metrics?.open_tickets ?? 0) > 0
                                ? 'critical'
                                : 'neutral'
                        }
                        hint="Support queue"
                    />
                    <StatCard
                        label="Suspended"
                        value={metrics?.suspended_tenants ?? 0}
                        icon={BuildingOfficeIcon}
                        tone={
                            (metrics?.suspended_tenants ?? 0) > 0
                                ? 'warning'
                                : 'neutral'
                        }
                        hint="Access blocked"
                    />
                    <StatCard
                        label="Expiring soon"
                        value={expiringSoon?.length ?? 0}
                        icon={CalendarDateRangeIcon}
                        tone={
                            (expiringSoon?.length ?? 0) > 0
                                ? 'warning'
                                : 'positive'
                        }
                        hint="Inside the warning window"
                    />
                </StatGrid>

                <div className="grid gap-5 xl:grid-cols-3">
                    {/* ── Recent hospitals ── */}
                    <Card className="xl:col-span-2">
                        <div className="flex items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-6">
                            <div>
                                <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                                    Recent hospitals
                                </h2>
                                <p className="mt-0.5 text-[12.5px] text-ink-muted">
                                    The most recently provisioned tenants.
                                </p>
                            </div>
                            <Button href="/tenants" variant="ghost" size="sm">
                                View all
                            </Button>
                        </div>

                        {(recentTenants?.length ?? 0) === 0 ? (
                            <div className="p-5 sm:p-6">
                                <EmptyState
                                    panel={false}
                                    icon={BuildingOfficeIcon}
                                    title="No hospitals yet"
                                    description="Provision the first hospital to start filling this table."
                                    action={{
                                        label: 'Provision a hospital',
                                        href: '/tenants/create',
                                    }}
                                />
                            </div>
                        ) : (
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
                                        <TableHeaderCell>State</TableHeaderCell>
                                        <TableHeaderCell>
                                            Created
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
                                                <span className="font-mono text-[12px]">
                                                    {tenant.domain ?? '—'}
                                                </span>
                                            </TableCell>
                                            <TableCell muted>
                                                {tenant.plan_name ?? (
                                                    <span className="text-ink-subtle">
                                                        No plan
                                                    </span>
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex flex-col gap-1">
                                                    <Badge
                                                        tone={
                                                            tenant.is_active
                                                                ? 'positive'
                                                                : 'warning'
                                                        }
                                                        dot
                                                    >
                                                        {tenant.is_active
                                                            ? 'Active'
                                                            : 'Suspended'}
                                                    </Badge>
                                                    {tenant.status && (
                                                        <span className="text-[11px] text-ink-subtle">
                                                            {tenant.status}
                                                        </span>
                                                    )}
                                                </div>
                                            </TableCell>
                                            <TableCell
                                                muted
                                                className="whitespace-nowrap tabular-nums"
                                            >
                                                {formatDateTime(
                                                    tenant.created_at,
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </Card>

                    {/* ── Activity ── */}
                    <SectionCard
                        title="Recent activity"
                        description="Platform audit trail."
                        icon={ShieldCheckIcon}
                        actions={
                            <Button
                                href="/platform/audit"
                                variant="ghost"
                                size="sm"
                            >
                                All
                            </Button>
                        }
                    >
                        {(recentActivity?.length ?? 0) === 0 ? (
                            <EmptyState
                                panel={false}
                                icon={ShieldCheckIcon}
                                title="No activity recorded"
                                description="Platform actions appear here as soon as staff operate."
                            />
                        ) : (
                            <ol className="space-y-3">
                                {recentActivity.map((entry, index) => (
                                    <li
                                        key={`${entry.type}-${index}`}
                                        className="flex gap-2.5"
                                    >
                                        <span
                                            aria-hidden="true"
                                            className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-accent"
                                        />
                                        <div className="min-w-0">
                                            <p className="text-[12.5px] leading-snug font-medium text-ink">
                                                {entry.label}
                                            </p>
                                            <p className="mt-0.5 truncate text-[11.5px] text-ink-subtle">
                                                {entry.detail ?? entry.type}
                                                {entry.time
                                                    ? ` · ${entry.time}`
                                                    : ''}
                                            </p>
                                        </div>
                                    </li>
                                ))}
                            </ol>
                        )}
                    </SectionCard>
                </div>

                {/* ── Expiring ── */}
                <SectionCard
                    title="Expiring soon"
                    description="Subscriptions reaching their end date inside the warning window."
                    icon={CalendarDateRangeIcon}
                    actions={
                        <Badge
                            tone={
                                (expiringSoon?.length ?? 0) > 0
                                    ? 'warning'
                                    : 'muted'
                            }
                        >
                            {expiringSoon?.length ?? 0}
                        </Badge>
                    }
                >
                    {(expiringSoon?.length ?? 0) === 0 ? (
                        <EmptyState
                            panel={false}
                            icon={CalendarDateRangeIcon}
                            title="Nothing expiring"
                            description="No subscription is close to its end date."
                        />
                    ) : (
                        <Table bare>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>Hospital</TableHeaderCell>
                                    <TableHeaderCell>Plan</TableHeaderCell>
                                    <TableHeaderCell>Ends</TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        Days left
                                    </TableHeaderCell>
                                    <TableHeaderCell align="right">
                                        <span className="sr-only">Open</span>
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {expiringSoon.map((item) => (
                                    <TableRow key={item.tenant_id}>
                                        <TableCell>
                                            <Link
                                                href={`/tenants/${item.tenant_id}`}
                                                className="font-medium text-ink transition-colors duration-150 hover:text-accent"
                                            >
                                                {item.tenant_name}
                                            </Link>
                                        </TableCell>
                                        <TableCell muted>
                                            {item.plan_name ?? '—'}
                                        </TableCell>
                                        <TableCell
                                            muted
                                            className="tabular-nums"
                                        >
                                            {formatDate(item.end_date)}
                                        </TableCell>
                                        <TableCell numeric>
                                            <span
                                                className={
                                                    (item.days_remaining ??
                                                        0) <= 3
                                                        ? 'font-semibold text-critical-ink'
                                                        : 'text-ink'
                                                }
                                            >
                                                {formatNumber(
                                                    item.days_remaining,
                                                )}
                                            </span>
                                        </TableCell>
                                        <TableCell align="right">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                href={`/tenants/${item.tenant_id}`}
                                            >
                                                Open
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </SectionCard>

                {/* ── Revenue detail ── */}
                <SectionCard
                    title="Revenue detail"
                    description="Recurring revenue computed from the subscription table."
                    icon={BanknotesIcon}
                >
                    <dl className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        {[
                            { label: 'MRR', value: revenue?.mrr },
                            { label: 'ARR', value: revenue?.arr },
                        ].map((item) => (
                            <div
                                key={item.label}
                                className="rounded-lg border border-line bg-surface-muted px-3.5 py-3"
                            >
                                <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                    {item.label}
                                </dt>
                                <dd className="mt-0.5 text-[15px] font-semibold text-ink tabular-nums">
                                    {formatCurrency(item.value, currency)}
                                </dd>
                            </div>
                        ))}
                        <div className="rounded-lg border border-line bg-surface-muted px-3.5 py-3">
                            <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                Active / trialing
                            </dt>
                            <dd className="mt-0.5 text-[15px] font-semibold text-ink tabular-nums">
                                {revenue?.active ?? 0} /{' '}
                                {revenue?.trialing ?? 0}
                            </dd>
                        </div>
                        <div className="rounded-lg border border-line bg-surface-muted px-3.5 py-3">
                            <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                Past due / cancelled
                            </dt>
                            <dd className="mt-0.5 text-[15px] font-semibold text-ink tabular-nums">
                                {revenue?.past_due ?? 0} /{' '}
                                {revenue?.cancelled ?? 0}
                            </dd>
                        </div>
                    </dl>
                </SectionCard>
            </div>
        </SuperAdminLayout>
    );
}
