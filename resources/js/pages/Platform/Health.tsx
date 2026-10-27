import {
    ArrowPathIcon,
    CheckCircleIcon,
    CircleStackIcon,
    ClockIcon,
    CpuChipIcon,
    ExclamationTriangleIcon,
    ServerStackIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import {
    Alert,
    Badge,
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
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { cn } from '@/lib/utils';
import {
    formatDate,
    formatDateTime,
    formatNumber,
    toneFromBackend,
} from '@/pages/Platform/components/format';
import type {
    PlatformAttention,
    PlatformSystemStatus,
    HealthTenantRow,
} from '@/pages/Platform/components/types';

interface PlatformHealthProps {
    tenants: HealthTenantRow[];
    system: PlatformSystemStatus;
    attention: PlatformAttention;
}

/** One system service as a chip + detail line. */
function SystemTile({
    label,
    ok,
    detail,
    icon: Icon,
}: {
    label: string;
    ok: boolean;
    detail: string;
    icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
}) {
    return (
        <div className="flex min-w-0 items-start gap-2.5 rounded-lg border border-line bg-surface px-3.5 py-3">
            <span
                className={cn(
                    'mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
                    ok
                        ? 'bg-positive-soft text-positive-ink'
                        : 'bg-critical-soft text-critical-ink',
                )}
            >
                <Icon className="h-4 w-4" />
            </span>
            <div className="min-w-0">
                <p className="flex items-center gap-1.5 text-[10.5px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                    {label}
                    <Badge tone={ok ? 'positive' : 'critical'} dot>
                        {ok ? 'OK' : 'Down'}
                    </Badge>
                </p>
                <p
                    className="mt-0.5 truncate text-[12.5px] font-medium text-ink"
                    title={detail}
                >
                    {detail}
                </p>
            </div>
        </div>
    );
}

export default function PlatformHealth({
    tenants,
    system,
    attention,
}: PlatformHealthProps) {
    const [refreshing, setRefreshing] = useState(false);

    const reachable = tenants.filter((tenant) => tenant.reachable).length;
    const migrationsOk = tenants.filter(
        (tenant) => tenant.migrations_ok,
    ).length;
    const unreachable = tenants.length - reachable;

    const refresh = () => {
        router.reload({
            only: ['tenants', 'system', 'attention'],
            onStart: () => setRefreshing(true),
            onFinish: () => setRefreshing(false),
        });
    };

    const systemHealthy =
        system?.database?.ok &&
        system?.queue?.ok &&
        unreachable === 0 &&
        migrationsOk === tenants.length;

    return (
        <SuperAdminLayout
            headerTitle="Health & monitoring"
            headerSubtitle="Platform services and tenant databases"
        >
            <Head title="Health & monitoring" />
            <div className="space-y-5 pb-12">
                <PageHeader
                    title="Health & monitoring"
                    description="Live reachability of every hospital database, the platform's own services, and the subscriptions that need an operator's attention."
                    breadcrumbs={[{ label: 'Platform' }, { label: 'Health' }]}
                    actions={
                        <Button
                            variant="secondary"
                            icon={ArrowPathIcon}
                            loading={refreshing}
                            onClick={refresh}
                        >
                            Re-check
                        </Button>
                    }
                    meta={
                        <>
                            <Badge
                                tone={systemHealthy ? 'positive' : 'warning'}
                                dot
                            >
                                {systemHealthy
                                    ? 'All systems operational'
                                    : 'Needs attention'}
                            </Badge>
                            <span className="tabular-nums">
                                {reachable}/{tenants.length} databases reachable
                            </span>
                        </>
                    }
                />

                <StatGrid columns={4}>
                    <StatCard
                        label="Hospitals"
                        value={tenants.length}
                        icon={ServerStackIcon}
                        tone="accent"
                        hint={`${reachable} reachable`}
                    />
                    <StatCard
                        label="Unreachable"
                        value={unreachable}
                        icon={ExclamationTriangleIcon}
                        tone={unreachable > 0 ? 'critical' : 'positive'}
                        hint={
                            unreachable > 0
                                ? 'Check the tenant connection'
                                : 'All databases respond'
                        }
                    />
                    <StatCard
                        label="Schema current"
                        value={migrationsOk}
                        icon={CircleStackIcon}
                        tone={
                            migrationsOk === tenants.length
                                ? 'positive'
                                : 'warning'
                        }
                        hint={`of ${tenants.length} hospitals`}
                    />
                    <StatCard
                        label="Pending jobs"
                        value={
                            system?.pending_jobs === null ||
                            system?.pending_jobs === undefined
                                ? '—'
                                : formatNumber(system.pending_jobs)
                        }
                        icon={CpuChipIcon}
                        tone={
                            (system?.pending_jobs ?? 0) > 100
                                ? 'warning'
                                : 'neutral'
                        }
                        hint={`Queue: ${system?.queue?.driver ?? 'unknown'}`}
                    />
                </StatGrid>

                <SectionCard
                    title="Platform services"
                    description="The central application's own dependencies."
                    icon={CircleStackIcon}
                >
                    <div className="grid gap-2.5 sm:grid-cols-2 xl:grid-cols-4">
                        <SystemTile
                            label="Database"
                            ok={Boolean(system?.database?.ok)}
                            detail={
                                system?.database?.message ??
                                'Central connection unknown'
                            }
                            icon={CircleStackIcon}
                        />
                        <SystemTile
                            label="Queue"
                            ok={Boolean(system?.queue?.ok)}
                            detail={system?.queue?.driver ?? 'unknown'}
                            icon={CpuChipIcon}
                        />
                        <SystemTile
                            label="Broadcast"
                            ok={Boolean(system?.broadcast_driver)}
                            detail={system?.broadcast_driver ?? 'unknown'}
                            icon={ServerStackIcon}
                        />
                        <SystemTile
                            label="Mail"
                            ok={Boolean(system?.mail_driver)}
                            detail={system?.mail_driver ?? 'unknown'}
                            icon={ServerStackIcon}
                        />
                    </div>

                    {!system?.database?.ok && (
                        <Alert
                            tone="critical"
                            title="Central database unreachable"
                            className="mt-4"
                        >
                            {system?.database?.message ??
                                'The platform cannot reach its own database.'}
                        </Alert>
                    )}
                </SectionCard>

                {/* ── Attention queue ── */}
                <div className="grid gap-5 xl:grid-cols-2">
                    <SectionCard
                        title="Expiring soon"
                        description="Subscriptions ending inside the warning window."
                        icon={ClockIcon}
                        actions={
                            <Badge
                                tone={
                                    (attention?.expiring?.length ?? 0) > 0
                                        ? 'warning'
                                        : 'muted'
                                }
                            >
                                {attention?.expiring?.length ?? 0}
                            </Badge>
                        }
                    >
                        {(attention?.expiring?.length ?? 0) === 0 ? (
                            <EmptyState
                                panel={false}
                                icon={CheckCircleIcon}
                                title="Nothing expiring"
                                description="No subscription is close to its end date."
                            />
                        ) : (
                            <Table bare>
                                <TableHeader>
                                    <TableRow>
                                        <TableHeaderCell>
                                            Hospital
                                        </TableHeaderCell>
                                        <TableHeaderCell>Plan</TableHeaderCell>
                                        <TableHeaderCell numeric>
                                            Days left
                                        </TableHeaderCell>
                                        <TableHeaderCell>Ends</TableHeaderCell>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {attention.expiring.map((item) => (
                                        <TableRow key={item.tenant_id}>
                                            <TableCell>
                                                <a
                                                    href={`/tenants/${item.tenant_id}`}
                                                    className="font-medium text-ink transition-colors duration-150 hover:text-accent"
                                                >
                                                    {item.tenant_name}
                                                </a>
                                            </TableCell>
                                            <TableCell muted>
                                                {item.plan_name ?? '—'}
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
                                                    {item.days_remaining ?? '—'}
                                                </span>
                                            </TableCell>
                                            <TableCell
                                                muted
                                                className="tabular-nums"
                                            >
                                                {formatDate(item.end_date)}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </SectionCard>

                    <SectionCard
                        title="Past due"
                        description="Subscriptions whose payment has lapsed."
                        icon={XCircleIcon}
                        actions={
                            <Badge
                                tone={
                                    (attention?.past_due?.length ?? 0) > 0
                                        ? 'critical'
                                        : 'muted'
                                }
                            >
                                {attention?.past_due?.length ?? 0}
                            </Badge>
                        }
                    >
                        {(attention?.past_due?.length ?? 0) === 0 ? (
                            <EmptyState
                                panel={false}
                                icon={CheckCircleIcon}
                                title="Nothing past due"
                                description="Every active subscription is paid up."
                            />
                        ) : (
                            <ul className="space-y-1.5">
                                {attention.past_due.map((tenantId) => (
                                    <li key={tenantId}>
                                        <a
                                            href={`/tenants/${tenantId}`}
                                            className="flex items-center gap-1.5 rounded-md px-2 py-1.5 font-mono text-[12.5px] text-critical-ink transition-colors duration-150 hover:bg-critical-soft focus-visible:ring-2 focus-visible:ring-critical/40 focus-visible:outline-none"
                                        >
                                            <XCircleIcon className="h-3.5 w-3.5 shrink-0" />
                                            {tenantId}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </SectionCard>
                </div>

                {/* ── Per-tenant health ── */}
                <SectionCard
                    title="Hospital health"
                    description="Database reachability and schema state for every tenant."
                    icon={ServerStackIcon}
                >
                    {tenants.length === 0 ? (
                        <EmptyState
                            panel={false}
                            icon={ServerStackIcon}
                            title="No hospitals provisioned"
                            description="Provision a hospital and its health appears here."
                        />
                    ) : (
                        <Table bare scroll>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>Hospital</TableHeaderCell>
                                    <TableHeaderCell>Database</TableHeaderCell>
                                    <TableHeaderCell>Schema</TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        Users
                                    </TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        Patients
                                    </TableHeaderCell>
                                    <TableHeaderCell>
                                        Subscription
                                    </TableHeaderCell>
                                    <TableHeaderCell>Last seen</TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {tenants.map((tenant) => (
                                    <TableRow key={tenant.id}>
                                        <TableCell>
                                            <a
                                                href={`/tenants/${tenant.id}`}
                                                className="font-medium text-ink transition-colors duration-150 hover:text-accent"
                                            >
                                                {tenant.name}
                                            </a>
                                            <span className="block font-mono text-[11.5px] text-ink-subtle">
                                                {tenant.domain ?? 'No domain'}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                tone={
                                                    tenant.reachable
                                                        ? 'positive'
                                                        : 'critical'
                                                }
                                                dot
                                            >
                                                {tenant.reachable
                                                    ? 'Reachable'
                                                    : 'Down'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                tone={
                                                    tenant.migrations_ok
                                                        ? 'positive'
                                                        : 'warning'
                                                }
                                                dot
                                            >
                                                {tenant.migrations_ok
                                                    ? 'Current'
                                                    : 'Incomplete'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell numeric muted>
                                            {formatNumber(tenant.users)}
                                        </TableCell>
                                        <TableCell numeric muted>
                                            {formatNumber(tenant.patients)}
                                        </TableCell>
                                        <TableCell>
                                            {tenant.subscription_status ? (
                                                <span className="flex flex-col gap-0.5">
                                                    <Badge
                                                        tone={toneFromBackend(
                                                            tenant.subscription_tone,
                                                        )}
                                                        dot
                                                    >
                                                        {
                                                            tenant.subscription_status
                                                        }
                                                    </Badge>
                                                    <span className="text-[11.5px] text-ink-subtle">
                                                        {tenant.plan_name ??
                                                            'No plan'}
                                                        {tenant.days_remaining !==
                                                        null
                                                            ? ` · ${tenant.days_remaining}d`
                                                            : ''}
                                                    </span>
                                                </span>
                                            ) : (
                                                <span className="text-[12px] text-ink-subtle">
                                                    No subscription
                                                </span>
                                            )}
                                        </TableCell>
                                        <TableCell
                                            muted
                                            className="whitespace-nowrap tabular-nums"
                                        >
                                            {formatDateTime(
                                                tenant.last_seen_at,
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </SectionCard>
            </div>
        </SuperAdminLayout>
    );
}
