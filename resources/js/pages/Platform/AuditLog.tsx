import {
    ChevronDownIcon,
    MagnifyingGlassIcon,
    ShieldCheckIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { Fragment, useCallback, useEffect, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Badge,
    Button,
    EmptyState,
    PageHeader,
    Select,
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
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { cn } from '@/lib/utils';
import { formatDateTime } from '@/pages/Platform/components/format';
import type { AuditLogRow, Paginated } from '@/pages/Platform/components/types';

interface PlatformAuditProps {
    logs: Paginated<AuditLogRow>;
    filters: { search: string; action: string; tenant?: string | null };
    actions: string[];
}

/** Tone for an audit action: green for creation, red for destruction. */
function actionTone(
    action: string,
): 'positive' | 'critical' | 'warning' | 'accent' | 'muted' {
    if (
        /delete|purge|destroy|expire|cancel|suspend|archive|remove/.test(action)
    ) {
        return 'critical';
    }

    if (/create|store|provision|assign|paid|renew|restore/.test(action)) {
        return 'positive';
    }

    if (/update|change|extend|override|settings/.test(action)) {
        return 'accent';
    }

    if (/impersonat|login|access/.test(action)) {
        return 'warning';
    }

    return 'muted';
}

/** `tenant.settings_updated` → `Tenant · Settings updated` */
function humaniseAction(action: string): string {
    return action
        .split('.')
        .map((part) => part.replace(/_/g, ' '))
        .join(' · ');
}

export default function PlatformAudit({
    logs,
    filters,
    actions,
}: PlatformAuditProps) {
    const [search, setSearch] = useState(filters?.search ?? '');
    const [action, setAction] = useState(filters?.action ?? 'all');
    const [loading, setLoading] = useState(false);
    const [expanded, setExpanded] = useState<Set<number>>(new Set());

    const tenantFilter = filters?.tenant ?? null;

    const applyFilters = useCallback(
        (nextSearch: string, nextAction: string) => {
            const query: Record<string, string> = {};

            if (nextSearch.trim() !== '') {
                query.search = nextSearch.trim();
            }

            if (nextAction !== 'all') {
                query.action = nextAction;
            }

            if (filters?.tenant) {
                query.tenant = filters.tenant;
            }

            router.get('/platform/audit', query, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onStart: () => setLoading(true),
                onFinish: () => setLoading(false),
            });
        },
        // `tenantFilter` is the extracted form of `filters.tenant`.
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [tenantFilter],
    );

    useEffect(() => {
        if (search.trim() === (filters?.search ?? '').trim()) {
            return;
        }

        const handle = setTimeout(() => applyFilters(search, action), 350);

        return () => clearTimeout(handle);
        // `action` is read at submit time on purpose.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const changeAction = (next: string) => {
        setAction(next);
        applyFilters(search, next);
    };

    const toggleExpanded = (id: number) => {
        setExpanded((previous) => {
            const next = new Set(previous);

            if (next.has(id)) {
                next.delete(id);
            } else {
                next.add(id);
            }

            return next;
        });
    };

    const rows = logs?.data ?? [];
    const hasFilters =
        (filters?.search ?? '') !== '' ||
        (filters?.action ?? 'all') !== 'all' ||
        Boolean(filters?.tenant);

    return (
        <SuperAdminLayout
            headerTitle="Audit log"
            headerSubtitle="Everything the platform staff did"
        >
            <Head title="Audit log" />
            <div className="space-y-5 pb-12">
                <PageHeader
                    title="Audit log"
                    description="Every privileged action on the platform: who did it, to which hospital, from which address, and with what payload."
                    breadcrumbs={[
                        { label: 'Platform' },
                        { label: 'Audit log' },
                    ]}
                    meta={
                        <>
                            <span className="tabular-nums">
                                {logs?.total ?? rows.length} entries
                            </span>
                            {filters?.tenant && (
                                <Badge tone="accent" variant="outline">
                                    Filtered to {filters.tenant}
                                </Badge>
                            )}
                        </>
                    }
                />

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder="Search description, actor, email or hospital…"
                        busy={loading}
                    />
                    <Select
                        aria-label="Filter by action"
                        value={action}
                        onChange={(event) => changeAction(event.target.value)}
                        className="h-9 w-auto min-w-48 text-[13px]"
                    >
                        <option value="all">All actions</option>
                        {(actions ?? []).map((value) => (
                            <option key={value} value={value}>
                                {humaniseAction(value)}
                            </option>
                        ))}
                    </Select>
                    <ToolbarSpacer />
                    {filters?.tenant && (
                        <Button
                            variant="secondary"
                            size="sm"
                            onClick={() =>
                                router.get(
                                    '/platform/audit',
                                    {},
                                    { preserveScroll: true },
                                )
                            }
                        >
                            Clear hospital filter
                        </Button>
                    )}
                </Toolbar>

                {rows.length === 0 ? (
                    hasFilters ? (
                        <EmptyState
                            icon={MagnifyingGlassIcon}
                            title="No audit entry matches these filters"
                            description="Try another search term or action."
                            secondaryAction={
                                <Button
                                    variant="secondary"
                                    onClick={() => {
                                        setSearch('');
                                        setAction('all');
                                        router.get(
                                            '/platform/audit',
                                            {},
                                            { preserveScroll: true },
                                        );
                                    }}
                                >
                                    Clear filters
                                </Button>
                            }
                        />
                    ) : (
                        <EmptyState
                            icon={ShieldCheckIcon}
                            title="Nothing has been logged yet"
                            description="Platform actions are recorded here as soon as staff start operating."
                        />
                    )
                ) : (
                    <>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>Action</TableHeaderCell>
                                    <TableHeaderCell>Actor</TableHeaderCell>
                                    <TableHeaderCell>Hospital</TableHeaderCell>
                                    <TableHeaderCell>IP</TableHeaderCell>
                                    <TableHeaderCell>When</TableHeaderCell>
                                    <TableHeaderCell>
                                        <span className="sr-only">Details</span>
                                    </TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {rows.map((log) => {
                                    const isExpanded = expanded.has(log.id);
                                    const hasProperties =
                                        log.properties !== null &&
                                        typeof log.properties === 'object' &&
                                        Object.keys(log.properties).length > 0;

                                    return (
                                        <Fragment key={log.id}>
                                            <TableRow>
                                                <TableCell>
                                                    <Badge
                                                        tone={actionTone(
                                                            log.action,
                                                        )}
                                                        variant="outline"
                                                    >
                                                        {humaniseAction(
                                                            log.action,
                                                        )}
                                                    </Badge>
                                                    <p className="mt-1 max-w-[46ch] text-[12.5px] text-ink-muted">
                                                        {log.description ?? '—'}
                                                    </p>
                                                </TableCell>
                                                <TableCell muted>
                                                    <span className="block text-ink">
                                                        {log.actor_name ??
                                                            'System'}
                                                    </span>
                                                    {log.actor_email && (
                                                        <span className="block font-mono text-[11.5px] text-ink-subtle">
                                                            {log.actor_email}
                                                        </span>
                                                    )}
                                                </TableCell>
                                                <TableCell>
                                                    {log.tenant_id ? (
                                                        <a
                                                            href={`/tenants/${log.tenant_id}`}
                                                            className="font-mono text-[12px] text-accent-ink transition-colors duration-150 hover:underline"
                                                        >
                                                            {log.tenant_id}
                                                        </a>
                                                    ) : (
                                                        <span className="text-[12px] text-ink-subtle">
                                                            Platform
                                                        </span>
                                                    )}
                                                </TableCell>
                                                <TableCell muted>
                                                    <span className="font-mono text-[11.5px]">
                                                        {log.ip_address ?? '—'}
                                                    </span>
                                                </TableCell>
                                                <TableCell
                                                    muted
                                                    className="whitespace-nowrap tabular-nums"
                                                >
                                                    {formatDateTime(
                                                        log.created_at,
                                                    )}
                                                </TableCell>
                                                <TableCell align="right">
                                                    {hasProperties ? (
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                toggleExpanded(
                                                                    log.id,
                                                                )
                                                            }
                                                            aria-expanded={
                                                                isExpanded
                                                            }
                                                            className="inline-flex items-center gap-1 rounded-md px-2 py-1 text-[12px] font-medium text-ink-muted transition-colors duration-150 hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                                        >
                                                            {isExpanded
                                                                ? 'Hide'
                                                                : 'Payload'}
                                                            <ChevronDownIcon
                                                                className={cn(
                                                                    'h-3.5 w-3.5 transition-transform duration-200 ease-premium',
                                                                    isExpanded &&
                                                                        'rotate-180',
                                                                )}
                                                            />
                                                        </button>
                                                    ) : (
                                                        <span className="text-[12px] text-ink-subtle">
                                                            —
                                                        </span>
                                                    )}
                                                </TableCell>
                                            </TableRow>

                                            {isExpanded && hasProperties && (
                                                <TableRow>
                                                    <TableCell
                                                        colSpan={6}
                                                        className="bg-surface-muted"
                                                    >
                                                        <pre className="max-h-64 overflow-auto rounded-lg border border-line bg-surface p-3 font-mono text-[11.5px] leading-relaxed text-ink-muted">
                                                            {JSON.stringify(
                                                                log.properties,
                                                                null,
                                                                2,
                                                            )}
                                                        </pre>
                                                    </TableCell>
                                                </TableRow>
                                            )}
                                        </Fragment>
                                    );
                                })}
                            </TableBody>
                        </Table>

                        <Pagination
                            links={logs?.links ?? []}
                            meta={{
                                from: logs?.from ?? 0,
                                to: logs?.to ?? 0,
                                total: logs?.total ?? rows.length,
                            }}
                        />
                    </>
                )}
            </div>
        </SuperAdminLayout>
    );
}
