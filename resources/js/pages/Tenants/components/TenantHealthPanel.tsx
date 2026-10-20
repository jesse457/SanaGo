import {
    CheckCircleIcon,
    CircleStackIcon,
    ClockIcon,
    ServerStackIcon,
    UserGroupIcon,
    XCircleIcon,
} from '@heroicons/react/24/outline';
import type { ComponentType, SVGProps } from 'react';
import { Badge, SectionCard } from '@/Components/ui';
import {
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    EmptyState,
} from '@/Components/ui';
import { cn } from '@/lib/utils';
import {
    formatDateTime,
    formatNumber,
} from '@/pages/Platform/components/format';
import type {
    TenantHealth,
    TenantUsage,
    TenantUser,
} from '@/pages/Platform/components/types';

function StatusChip({
    ok,
    label,
    detail,
    icon: Icon,
}: {
    ok: boolean;
    label: string;
    detail?: string;
    icon: ComponentType<SVGProps<SVGSVGElement>>;
}) {
    return (
        <div className="flex min-w-0 items-start gap-2.5 rounded-lg border border-line bg-surface-muted px-3 py-2.5">
            <span
                className={cn(
                    'mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-md',
                    ok
                        ? 'bg-positive-soft text-positive-ink'
                        : 'bg-critical-soft text-critical-ink',
                )}
            >
                {ok ? (
                    <CheckCircleIcon className="h-4 w-4" />
                ) : (
                    <XCircleIcon className="h-4 w-4" />
                )}
            </span>
            <div className="min-w-0">
                <p className="flex items-center gap-1.5 text-[12px] font-semibold text-ink">
                    <Icon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                    {label}
                </p>
                <p className="mt-0.5 truncate text-[11.5px] text-ink-subtle">
                    {detail ?? (ok ? 'Operational' : 'Needs attention')}
                </p>
            </div>
        </div>
    );
}

/**
 * Tenant health + users.
 *
 * `health` comes from `TenantService::health()` (reachability, migration
 * state, last activity) and `users` from the tenant database. Both are
 * read-only: the users table is informational for support work.
 */
export function TenantHealthPanel({
    health,
    usage,
    users,
    lastSeenAt,
}: {
    health: TenantHealth;
    usage: TenantUsage;
    users: TenantUser[];
    lastSeenAt: string | null;
}) {
    return (
        <div className="grid gap-5 xl:grid-cols-2">
            <SectionCard
                title="Health & activity"
                description="Checked live against the hospital's own database."
                icon={ServerStackIcon}
            >
                <div className="space-y-3">
                    <div className="grid gap-2 sm:grid-cols-2">
                        <StatusChip
                            ok={health.reachable}
                            label="Database"
                            detail={
                                health.reachable
                                    ? 'Reachable'
                                    : 'Unreachable — check the tenant database'
                            }
                            icon={CircleStackIcon}
                        />
                        <StatusChip
                            ok={health.migrations_ok}
                            label="Migrations"
                            detail={
                                health.migrations_ok
                                    ? 'Schema up to date'
                                    : 'Tenant schema incomplete'
                            }
                            icon={ServerStackIcon}
                        />
                        <StatusChip
                            ok={Boolean(health.last_activity ?? lastSeenAt)}
                            label="Last activity"
                            detail={formatDateTime(
                                health.last_activity ?? lastSeenAt,
                            )}
                            icon={ClockIcon}
                        />
                        <StatusChip
                            ok={health.users > 0}
                            label="Staff accounts"
                            detail={`${formatNumber(health.users)} user${health.users === 1 ? '' : 's'}`}
                            icon={UserGroupIcon}
                        />
                    </div>

                    <dl className="grid grid-cols-2 gap-3 border-t border-line pt-3.5">
                        <div>
                            <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                Patients
                            </dt>
                            <dd className="text-[15px] font-semibold text-ink tabular-nums">
                                {formatNumber(usage.patients)}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-[10px] font-semibold tracking-[0.1em] text-ink-subtle uppercase">
                                Appointments
                            </dt>
                            <dd className="text-[15px] font-semibold text-ink tabular-nums">
                                {formatNumber(usage.appointments)}
                            </dd>
                        </div>
                    </dl>
                </div>
            </SectionCard>

            <SectionCard
                title="Users"
                description={`${users.length} account${users.length === 1 ? '' : 's'} in this hospital (read-only).`}
                icon={UserGroupIcon}
            >
                {users.length === 0 ? (
                    <EmptyState
                        panel={false}
                        icon={UserGroupIcon}
                        title="No users yet"
                        description="The hospital database is reachable but has no staff accounts."
                    />
                ) : (
                    <Table bare scroll containerClassName="max-h-96">
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>Name</TableHeaderCell>
                                <TableHeaderCell>Email</TableHeaderCell>
                                <TableHeaderCell>Roles</TableHeaderCell>
                                <TableHeaderCell>State</TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {users.map((user) => (
                                <TableRow key={user.id}>
                                    <TableCell>
                                        <span className="font-medium text-ink">
                                            {user.name}
                                        </span>
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="font-mono text-[12px]">
                                            {user.email}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        {user.roles.length === 0 ? (
                                            <span className="text-[12px] text-ink-subtle">
                                                No role
                                            </span>
                                        ) : (
                                            <span className="flex flex-wrap gap-1">
                                                {user.roles.map((role) => (
                                                    <Badge
                                                        key={role}
                                                        tone="muted"
                                                        variant="outline"
                                                    >
                                                        {role}
                                                    </Badge>
                                                ))}
                                            </span>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={
                                                user.is_active
                                                    ? 'positive'
                                                    : 'muted'
                                            }
                                            dot
                                        >
                                            {user.is_active
                                                ? 'Active'
                                                : 'Disabled'}
                                        </Badge>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}
            </SectionCard>
        </div>
    );
}
