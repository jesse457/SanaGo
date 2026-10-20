import {
    ArchiveBoxIcon,
    ArrowPathRoundedSquareIcon,
    ArrowTopRightOnSquareIcon,
    BuildingOfficeIcon,
    GlobeAltIcon,
    MagnifyingGlassIcon,
    PauseCircleIcon,
    PlusIcon,
    UserCircleIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Alert,
    Badge,
    Button,
    Dialog,
    EmptyState,
    Field,
    PageHeader,
    Select,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Textarea,
    Toolbar,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import { cn } from '@/lib/utils';
import { formatDate } from '@/pages/Platform/components/format';
import type {
    Paginated,
    TenantCounts,
    TenantFilters,
    TenantListRow,
} from '@/pages/Platform/components/types';
import { SubscriptionStatusBadge } from '@/pages/Platform/components/UsageMeter';

interface TenantsIndexProps {
    tenants: Paginated<TenantListRow>;
    filters: TenantFilters;
    counts: TenantCounts;
}

type StatusFilter = 'all' | 'active' | 'suspended' | 'archived';

const STATUS_OPTIONS: { value: StatusFilter; label: string }[] = [
    { value: 'all', label: 'All hospitals' },
    { value: 'active', label: 'Active' },
    { value: 'suspended', label: 'Suspended' },
    { value: 'archived', label: 'Archived' },
];

/** Lifecycle state for one tenant, derived from the three timestamps. */
function lifecycleOf(tenant: TenantListRow): {
    label: string;
    tone: 'positive' | 'warning' | 'muted';
} {
    if (tenant.archived_at) {
        return { label: 'Archived', tone: 'muted' };
    }

    if (tenant.suspended_at || !tenant.is_active) {
        return { label: 'Suspended', tone: 'warning' };
    }

    return { label: 'Active', tone: 'positive' };
}

function csrfToken(): string {
    if (typeof document === 'undefined') {
        return '';
    }

    return (
        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.content ?? ''
    );
}

export default function TenantsIndex({
    tenants,
    filters,
    counts,
}: TenantsIndexProps) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState<StatusFilter>(
        (filters.status as StatusFilter) ?? 'all',
    );
    const [loading, setLoading] = useState(false);

    const [suspendTarget, setSuspendTarget] = useState<TenantListRow | null>(
        null,
    );
    const [suspendReason, setSuspendReason] = useState('');
    const [suspendProcessing, setSuspendProcessing] = useState(false);
    const [suspendError, setSuspendError] = useState<string | null>(null);

    const [impersonateTarget, setImpersonateTarget] =
        useState<TenantListRow | null>(null);
    const impersonateFormRef = useRef<HTMLFormElement>(null);

    const [copiedDomain, setCopiedDomain] = useState<string | null>(null);

    const applyFilters = useCallback(
        (nextSearch: string, nextStatus: StatusFilter) => {
            const query: Record<string, string> = {};

            if (nextSearch.trim() !== '') {
                query.search = nextSearch.trim();
            }

            if (nextStatus !== 'all') {
                query.status = nextStatus;
            }

            router.get('/tenants', query, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onStart: () => setLoading(true),
                onFinish: () => setLoading(false),
            });
        },
        [],
    );

    // Debounce the search field so typing does not fire a request per key.
    useEffect(() => {
        const current = (filters.search ?? '').trim();

        if (search.trim() === current) {
            return;
        }

        const handle = setTimeout(() => applyFilters(search, status), 350);

        return () => clearTimeout(handle);
        // `applyFilters` is stable; `status` is read at submit time on purpose.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const changeStatus = (next: StatusFilter) => {
        setStatus(next);
        applyFilters(search, next);
    };

    const openSuspend = (tenant: TenantListRow) => {
        setSuspendReason('');
        setSuspendError(null);
        setSuspendTarget(tenant);
    };

    const submitSuspend = () => {
        if (!suspendTarget) {
            return;
        }

        setSuspendProcessing(true);
        setSuspendError(null);

        router.post(
            `/tenants/${suspendTarget.id}/suspend`,
            { reason: suspendReason },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setSuspendTarget(null);
                    setSuspendReason('');
                },
                onError: (errors) =>
                    setSuspendError(
                        errors.reason ?? errors.tenant ?? 'Could not suspend.',
                    ),
                onFinish: () => setSuspendProcessing(false),
            },
        );
    };

    const reactivate = (tenant: TenantListRow) => {
        router.post(
            `/tenants/${tenant.id}/reactivate`,
            {},
            { preserveScroll: true },
        );
    };

    const copyDomain = async (domain: string) => {
        try {
            await navigator.clipboard.writeText(domain);
            setCopiedDomain(domain);
            setTimeout(() => setCopiedDomain(null), 1800);
        } catch {
            setCopiedDomain(null);
        }
    };

    const rows = tenants.data ?? [];
    const hasFilters =
        (filters.search ?? '') !== '' || (filters.status ?? 'all') !== 'all';

    const countChips = useMemo(
        () => [
            { key: 'all' as StatusFilter, label: 'All', value: counts.all },
            {
                key: 'active' as StatusFilter,
                label: 'Active',
                value: counts.active,
            },
            {
                key: 'suspended' as StatusFilter,
                label: 'Suspended',
                value: counts.suspended,
            },
            {
                key: 'archived' as StatusFilter,
                label: 'Archived',
                value: counts.archived,
            },
        ],
        [counts],
    );

    return (
        <SuperAdminLayout
            headerTitle="Hospitals"
            headerSubtitle="Every tenant on the platform"
        >
            <Head title="Hospitals" />

            <div className="space-y-5 pb-12">
                <PageHeader
                    title="Hospitals"
                    description="Every tenant running on this platform: lifecycle state, plan and billing health. Suspending a hospital blocks all of its users immediately."
                    icon={BuildingOfficeIcon}
                    breadcrumbs={[
                        { label: 'Platform' },
                        { label: 'Hospitals' },
                    ]}
                    actions={
                        <Button href="/tenants/create" icon={PlusIcon}>
                            New hospital
                        </Button>
                    }
                    meta={
                        <>
                            <span className="tabular-nums">
                                {counts.active} active
                            </span>
                            <span className="tabular-nums">
                                {counts.suspended} suspended
                            </span>
                            <span className="tabular-nums">
                                {counts.archived} archived
                            </span>
                        </>
                    }
                />

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder="Search by name, slug or domain…"
                        busy={loading}
                    />
                    <Select
                        aria-label="Filter by lifecycle state"
                        value={status}
                        onChange={(event) =>
                            changeStatus(event.target.value as StatusFilter)
                        }
                        className="h-9 w-auto min-w-40 text-[13px]"
                    >
                        {STATUS_OPTIONS.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </Select>
                    <ToolbarSpacer />
                    <div
                        role="group"
                        aria-label="Hospital counts"
                        className="flex flex-wrap items-center gap-1.5"
                    >
                        {countChips.map((chip) => (
                            <button
                                key={chip.key}
                                type="button"
                                onClick={() => changeStatus(chip.key)}
                                aria-pressed={status === chip.key}
                                className={cn(
                                    'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11.5px] font-medium transition-colors duration-150 ease-premium focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none',
                                    status === chip.key
                                        ? 'border-accent/40 bg-accent-soft text-accent-ink'
                                        : 'border-line bg-surface text-ink-muted hover:border-line-strong hover:text-ink',
                                )}
                            >
                                {chip.label}
                                <span className="tabular-nums opacity-70">
                                    {chip.value}
                                </span>
                            </button>
                        ))}
                    </div>
                </Toolbar>

                {rows.length === 0 ? (
                    hasFilters ? (
                        <EmptyState
                            icon={MagnifyingGlassIcon}
                            title="No hospital matches these filters"
                            description="Try a different search term or clear the lifecycle filter."
                            secondaryAction={
                                <Button
                                    variant="secondary"
                                    onClick={() => {
                                        setSearch('');
                                        setStatus('all');
                                        router.get(
                                            '/tenants',
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
                            icon={BuildingOfficeIcon}
                            title="No hospitals yet"
                            description="Provision the first hospital and it will appear here with its plan, usage and lifecycle controls."
                            action={{
                                label: 'Provision a hospital',
                                href: '/tenants/create',
                            }}
                        />
                    )
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>Hospital</TableHeaderCell>
                                <TableHeaderCell>Domain</TableHeaderCell>
                                <TableHeaderCell>State</TableHeaderCell>
                                <TableHeaderCell>Subscription</TableHeaderCell>
                                <TableHeaderCell>Created</TableHeaderCell>
                                <TableHeaderCell align="right">
                                    Actions
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {rows.map((tenant) => {
                                const lifecycle = lifecycleOf(tenant);
                                const suspended = !tenant.is_active;
                                const archived = Boolean(tenant.archived_at);

                                return (
                                    <TableRow key={tenant.id}>
                                        <TableCell>
                                            <Link
                                                href={`/tenants/${tenant.id}`}
                                                className="group flex min-w-0 items-center gap-2.5"
                                            >
                                                <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-line bg-surface-muted text-[11px] font-semibold text-ink-muted tabular-nums">
                                                    {tenant.name
                                                        .slice(0, 2)
                                                        .toUpperCase()}
                                                </span>
                                                <span className="min-w-0">
                                                    <span className="block truncate font-medium text-ink transition-colors duration-150 group-hover:text-accent">
                                                        {tenant.name}
                                                    </span>
                                                    <span className="block truncate font-mono text-[11.5px] text-ink-subtle">
                                                        {tenant.slug}
                                                    </span>
                                                </span>
                                            </Link>
                                        </TableCell>

                                        <TableCell muted>
                                            {tenant.domains.length === 0 ? (
                                                <span className="text-ink-subtle">
                                                    No domain
                                                </span>
                                            ) : (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        copyDomain(
                                                            tenant.domains[0],
                                                        )
                                                    }
                                                    title="Copy domain"
                                                    className="group inline-flex max-w-[240px] items-center gap-1.5 rounded-md px-1.5 py-0.5 text-left transition-colors duration-150 hover:bg-surface-hover focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                                >
                                                    <GlobeAltIcon className="h-3.5 w-3.5 shrink-0 text-ink-subtle" />
                                                    <span className="truncate font-mono text-[12px]">
                                                        {tenant.domains[0]}
                                                    </span>
                                                    <span
                                                        aria-live="polite"
                                                        className={cn(
                                                            'shrink-0 text-[10.5px] font-semibold uppercase',
                                                            copiedDomain ===
                                                                tenant
                                                                    .domains[0]
                                                                ? 'text-positive-ink'
                                                                : 'text-ink-subtle opacity-0 group-hover:opacity-100',
                                                        )}
                                                    >
                                                        {copiedDomain ===
                                                        tenant.domains[0]
                                                            ? 'copied'
                                                            : 'copy'}
                                                    </span>
                                                </button>
                                            )}
                                        </TableCell>

                                        <TableCell>
                                            <Badge
                                                tone={lifecycle.tone}
                                                dot
                                                title={
                                                    tenant.suspended_at
                                                        ? `Suspended ${formatDate(tenant.suspended_at)}`
                                                        : undefined
                                                }
                                            >
                                                {lifecycle.label}
                                            </Badge>
                                        </TableCell>

                                        <TableCell>
                                            {tenant.subscription ? (
                                                <div className="flex min-w-0 flex-col gap-1">
                                                    <span className="truncate text-[13px] font-medium text-ink">
                                                        {
                                                            tenant.subscription
                                                                .plan_name
                                                        }
                                                    </span>
                                                    <SubscriptionStatusBadge
                                                        label={
                                                            tenant.subscription
                                                                .status_label
                                                        }
                                                        tone={
                                                            tenant.subscription
                                                                .status_tone
                                                        }
                                                        daysRemaining={
                                                            tenant.subscription
                                                                .days_remaining
                                                        }
                                                    />
                                                </div>
                                            ) : (
                                                <span className="text-[12.5px] text-ink-subtle">
                                                    No subscription
                                                </span>
                                            )}
                                        </TableCell>

                                        <TableCell
                                            muted
                                            className="tabular-nums"
                                        >
                                            {formatDate(tenant.created_at)}
                                        </TableCell>

                                        <TableCell align="right">
                                            <div className="flex items-center justify-end gap-1">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon={
                                                        ArrowTopRightOnSquareIcon
                                                    }
                                                    href={`/tenants/${tenant.id}`}
                                                    aria-label={`Open ${tenant.name}`}
                                                >
                                                    Open
                                                </Button>

                                                {!archived &&
                                                    (suspended ? (
                                                        <Button
                                                            variant="secondary"
                                                            size="sm"
                                                            icon={
                                                                ArrowPathRoundedSquareIcon
                                                            }
                                                            onClick={() =>
                                                                reactivate(
                                                                    tenant,
                                                                )
                                                            }
                                                        >
                                                            Reactivate
                                                        </Button>
                                                    ) : (
                                                        <Button
                                                            variant="secondary"
                                                            size="sm"
                                                            icon={
                                                                PauseCircleIcon
                                                            }
                                                            onClick={() =>
                                                                openSuspend(
                                                                    tenant,
                                                                )
                                                            }
                                                        >
                                                            Suspend
                                                        </Button>
                                                    ))}

                                                {archived && (
                                                    <Badge tone="muted">
                                                        <ArchiveBoxIcon className="h-3 w-3" />
                                                        Archived
                                                    </Badge>
                                                )}

                                                {!archived && (
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        icon={UserCircleIcon}
                                                        onClick={() =>
                                                            setImpersonateTarget(
                                                                tenant,
                                                            )
                                                        }
                                                    >
                                                        Impersonate
                                                    </Button>
                                                )}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                )}

                {rows.length > 0 && (
                    <Pagination
                        links={tenants.links ?? []}
                        meta={{
                            from: tenants.from ?? 0,
                            to: tenants.to ?? 0,
                            total: tenants.total ?? rows.length,
                        }}
                    />
                )}
            </div>

            {/* ─── Suspend confirmation (never window.confirm) ─── */}
            <Dialog
                open={suspendTarget !== null}
                onClose={() => {
                    if (!suspendProcessing) {
                        setSuspendTarget(null);
                    }
                }}
                title={`Suspend ${suspendTarget?.name ?? ''}?`}
                description="Every user of this hospital is signed out and blocked until you reactivate it."
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setSuspendTarget(null)}
                            disabled={suspendProcessing}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="danger"
                            icon={PauseCircleIcon}
                            loading={suspendProcessing}
                            onClick={submitSuspend}
                        >
                            Suspend hospital
                        </Button>
                    </>
                }
            >
                <div className="space-y-4">
                    <Alert tone="warning" title="Access is cut immediately">
                        Suspension is reversible from this page at any time.
                        Billing continues to accrue unless you cancel the
                        subscription separately.
                    </Alert>

                    <Field
                        label="Reason (optional)"
                        htmlFor="suspend-reason"
                        hint="Recorded on the tenant and in the platform audit log."
                        error={suspendError}
                    >
                        <Textarea
                            id="suspend-reason"
                            value={suspendReason}
                            onChange={(event) =>
                                setSuspendReason(event.target.value)
                            }
                            placeholder="Unpaid invoice, abuse report, customer request…"
                            rows={3}
                            maxLength={500}
                            invalid={Boolean(suspendError)}
                        />
                    </Field>
                </div>
            </Dialog>

            {/* ─── Impersonation: a real cross-domain POST ───
                The backend answers with a redirect to the tenant domain, so
                this must be a native form submit — an Inertia visit would
                refuse the cross-origin response. */}
            <Dialog
                open={impersonateTarget !== null}
                onClose={() => setImpersonateTarget(null)}
                title={`Open ${impersonateTarget?.name ?? ''} as its administrator?`}
                description="A one-time support token is issued and you land on the hospital's own domain."
                size="md"
                footer={
                    <>
                        <Button
                            variant="secondary"
                            onClick={() => setImpersonateTarget(null)}
                        >
                            Cancel
                        </Button>
                        <Button
                            variant="primary"
                            icon={UserCircleIcon}
                            onClick={() =>
                                impersonateFormRef.current?.requestSubmit()
                            }
                        >
                            Start support session
                        </Button>
                    </>
                }
            >
                <div className="space-y-4">
                    <Alert tone="warning" title="This is an audited action">
                        The session is recorded in the platform audit log with
                        your staff account as the actor. Close the tab when you
                        are done — do not change data you were not asked to
                        change.
                    </Alert>

                    <form
                        ref={impersonateFormRef}
                        method="post"
                        action={
                            impersonateTarget
                                ? `/tenants/${impersonateTarget.id}/impersonate`
                                : '/tenants'
                        }
                        className="hidden"
                    >
                        <input
                            type="hidden"
                            name="_token"
                            value={csrfToken()}
                        />
                    </form>
                </div>
            </Dialog>
        </SuperAdminLayout>
    );
}
