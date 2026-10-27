import {
    BanknotesIcon,
    DocumentTextIcon,
    MagnifyingGlassIcon,
    UsersIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { Pagination } from '@/Components/Pagination';
import {
    Alert,
    Badge,
    Button,
    EmptyState,
    PageHeader,
    Select,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Toolbar,
    ToolbarSpacer,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import {
    formatCurrency,
    formatCurrencyCompact,
    formatDate,
    formatDateTime,
} from '@/pages/Platform/components/format';
import type {
    BillingInvoice,
    BillingRow,
    BillingTotals,
    Paginated,
    RevenueSummary,
} from '@/pages/Platform/components/types';
import {
    SubscriptionStatusBadge,
    UsageMeter,
} from '@/pages/Platform/components/UsageMeter';

interface PlatformBillingProps {
    rows: BillingRow[];
    invoices: Paginated<BillingInvoice>;
    revenue: RevenueSummary;
    totals: BillingTotals;
    filters: { status: string; invoice_status?: string };
    invoiceStatuses?: string[];
}

const STATUS_OPTIONS = [
    { value: 'all', label: 'All statuses' },
    { value: 'active', label: 'Active' },
    { value: 'trialing', label: 'Trialing' },
    { value: 'past_due', label: 'Past due' },
    { value: 'expired', label: 'Expired' },
    { value: 'cancelled', label: 'Cancelled' },
    { value: 'inactive', label: 'Inactive' },
];

const INVOICE_STATUS_OPTIONS = [
    { value: 'all', label: 'All invoices' },
    { value: 'pending', label: 'Pending' },
    { value: 'paid', label: 'Paid' },
    { value: 'overdue', label: 'Overdue' },
    { value: 'void', label: 'Void' },
    { value: 'draft', label: 'Draft' },
];

/** Invoice statuses that represent money actually received. */
function isCollected(status: string): boolean {
    return status === 'paid';
}

export default function PlatformBilling({
    rows,
    invoices,
    revenue,
    totals,
    filters,
}: PlatformBillingProps) {
    const currency = revenue?.currency ?? rows[0]?.currency ?? 'XAF';
    const status = filters?.status ?? 'all';
    const invoiceStatus = filters?.invoice_status ?? 'all';

    /**
     * Subscription filters and invoice filters are independent: one narrows
     * the tenant usage table, the other the invoice ledger. Both are carried
     * on every request so switching one never resets the other.
     */
    const changeFilters = (next: {
        status?: string;
        invoiceStatus?: string;
    }) => {
        const nextStatus = next.status ?? status;
        const nextInvoiceStatus = next.invoiceStatus ?? invoiceStatus;
        const params: Record<string, string> = {};

        if (nextStatus !== 'all') {
            params.status = nextStatus;
        }

        if (nextInvoiceStatus !== 'all') {
            params.invoice_status = nextInvoiceStatus;
        }

        router.get('/platform/billing', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only: ['rows', 'invoices', 'filters', 'totals'],
        });
    };

    const invoiceRows = invoices?.data ?? [];

    return (
        <SuperAdminLayout
            headerTitle="Billing & usage"
            headerSubtitle="Revenue, quotas and invoices"
        >
            <Head title="Billing & usage" />
            <div className="space-y-5 pb-12">
                <PageHeader
                    title="Billing & usage"
                    description="Recurring revenue, what each hospital consumes against its plan, and every invoice the platform has raised."
                    breadcrumbs={[{ label: 'Billing' }, { label: 'Usage' }]}
                    meta={
                        <>
                            <span className="tabular-nums">
                                {totals?.tenants ?? 0} active hospitals
                            </span>
                            <span className="tabular-nums">
                                MRR {formatCurrency(revenue?.mrr, currency)}
                            </span>
                        </>
                    }
                />

                <StatGrid columns={4}>
                    <StatCard
                        label="MRR"
                        value={formatCurrencyCompact(revenue?.mrr, currency)}
                        icon={BanknotesIcon}
                        tone="positive"
                        hint={`ARR ${formatCurrencyCompact(revenue?.arr, currency)}`}
                    />
                    <StatCard
                        label="Outstanding"
                        value={formatCurrencyCompact(
                            totals?.outstanding,
                            currency,
                        )}
                        icon={DocumentTextIcon}
                        tone={
                            (totals?.outstanding ?? 0) > 0
                                ? 'warning'
                                : 'neutral'
                        }
                        hint="Unpaid invoices across all hospitals"
                    />
                    <StatCard
                        label="Collected"
                        value={formatCurrencyCompact(
                            totals?.collected,
                            currency,
                        )}
                        icon={BanknotesIcon}
                        tone="positive"
                        hint="Lifetime paid invoices"
                    />
                    <StatCard
                        label="Active hospitals"
                        value={totals?.tenants ?? 0}
                        icon={UsersIcon}
                        tone="accent"
                        hint={`${revenue?.past_due ?? 0} past due`}
                    />
                </StatGrid>

                {(revenue?.past_due ?? 0) > 0 && (
                    <Alert tone="warning" title="Collection needed">
                        {revenue?.past_due} subscription
                        {revenue?.past_due === 1 ? ' is' : 's are'} past due.
                        Filter the table by “Past due” to work through them.
                    </Alert>
                )}

                {/* ── Per-tenant usage ── */}
                <section aria-label="Usage by hospital" className="space-y-2.5">
                    <div className="flex flex-wrap items-center justify-between gap-2">
                        <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                            Usage by hospital
                        </h2>
                        <Toolbar className="border-0 bg-transparent px-0 py-0">
                            <Select
                                aria-label="Filter subscriptions by status"
                                value={status}
                                onChange={(event) =>
                                    changeFilters({
                                        status: event.target.value,
                                    })
                                }
                                className="h-9 w-auto min-w-40 text-[13px]"
                            >
                                {STATUS_OPTIONS.map((option) => (
                                    <option
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </option>
                                ))}
                            </Select>
                            <ToolbarSpacer />
                            <span className="text-[12px] text-ink-subtle tabular-nums">
                                {rows.length} subscription
                                {rows.length === 1 ? '' : 's'}
                            </span>
                        </Toolbar>
                    </div>

                    {rows.length === 0 ? (
                        <EmptyState
                            icon={MagnifyingGlassIcon}
                            title="No subscriptions for this filter"
                            description="Try another status, or assign a plan to a hospital first."
                            secondaryAction={
                                status !== 'all' ? (
                                    <Button
                                        variant="secondary"
                                        onClick={() =>
                                            changeFilters({ status: 'all' })
                                        }
                                    >
                                        Clear filter
                                    </Button>
                                ) : undefined
                            }
                        />
                    ) : (
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHeaderCell>Hospital</TableHeaderCell>
                                    <TableHeaderCell>Plan</TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        Price
                                    </TableHeaderCell>
                                    <TableHeaderCell>Status</TableHeaderCell>
                                    <TableHeaderCell numeric>
                                        Outstanding
                                    </TableHeaderCell>
                                    <TableHeaderCell>Usage</TableHeaderCell>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {rows.map((row) => (
                                    <TableRow key={row.tenant_id}>
                                        <TableCell>
                                            <Link
                                                href={`/tenants/${row.tenant_id}`}
                                                className="font-medium text-ink transition-colors duration-150 hover:text-accent"
                                            >
                                                {row.tenant_name}
                                            </Link>
                                            <span className="block text-[11.5px] text-ink-subtle">
                                                ends {formatDate(row.end_date)}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <span className="font-medium text-ink">
                                                {row.plan_name}
                                            </span>
                                            <span className="block text-[11.5px] text-ink-subtle">
                                                {row.billing_cycle}
                                            </span>
                                        </TableCell>
                                        <TableCell numeric>
                                            {formatCurrency(
                                                row.price,
                                                row.currency,
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            <SubscriptionStatusBadge
                                                label={row.status_label}
                                                tone={row.status_tone}
                                            />
                                        </TableCell>
                                        <TableCell numeric>
                                            <span
                                                className={
                                                    row.outstanding > 0
                                                        ? 'font-semibold text-warning-ink'
                                                        : 'text-ink-subtle'
                                                }
                                            >
                                                {row.outstanding > 0
                                                    ? formatCurrency(
                                                          row.outstanding,
                                                          row.currency,
                                                      )
                                                    : '—'}
                                            </span>
                                        </TableCell>
                                        <TableCell className="min-w-[260px]">
                                            <div className="space-y-1.5">
                                                <UsageMeter
                                                    label="Users"
                                                    used={row.users.used}
                                                    limit={row.users.limit}
                                                    compact
                                                />
                                                <UsageMeter
                                                    label="Patients"
                                                    used={row.patients.used}
                                                    limit={row.patients.limit}
                                                    compact
                                                />
                                                <UsageMeter
                                                    label="Storage"
                                                    used={row.storage_gb.used}
                                                    limit={row.storage_gb.limit}
                                                    unit="GB"
                                                    compact
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    )}
                </section>

                {/* ── Invoices ── */}
                <section aria-label="Invoices" className="space-y-2.5">
                    <div className="flex flex-wrap items-center justify-between gap-2">
                        <h2 className="text-[15px] font-semibold tracking-tight text-ink">
                            Invoices
                        </h2>
                        <Toolbar className="border-0 bg-transparent px-0 py-0">
                            <Select
                                aria-label="Filter invoices by status"
                                value={invoiceStatus}
                                onChange={(event) =>
                                    changeFilters({
                                        invoiceStatus: event.target.value,
                                    })
                                }
                                className="h-9 w-auto min-w-40 text-[13px]"
                            >
                                {INVOICE_STATUS_OPTIONS.map((option) => (
                                    <option
                                        key={option.value}
                                        value={option.value}
                                    >
                                        {option.label}
                                    </option>
                                ))}
                            </Select>
                            <ToolbarSpacer />
                            <span className="text-[12px] text-ink-subtle tabular-nums">
                                {invoices?.total ?? invoiceRows.length} invoice
                                {(invoices?.total ?? invoiceRows.length) === 1
                                    ? ''
                                    : 's'}
                            </span>
                        </Toolbar>
                    </div>

                    {invoiceRows.length === 0 ? (
                        <EmptyState
                            icon={DocumentTextIcon}
                            title="No invoices yet"
                            description="Invoices are raised when a subscription period is billed."
                        />
                    ) : (
                        <>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHeaderCell>
                                            Number
                                        </TableHeaderCell>
                                        <TableHeaderCell>
                                            Hospital
                                        </TableHeaderCell>
                                        <TableHeaderCell>
                                            Period
                                        </TableHeaderCell>
                                        <TableHeaderCell numeric>
                                            Total
                                        </TableHeaderCell>
                                        <TableHeaderCell>Due</TableHeaderCell>
                                        <TableHeaderCell>
                                            Status
                                        </TableHeaderCell>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {invoiceRows.map((invoice) => (
                                        <TableRow key={invoice.id}>
                                            <TableCell>
                                                <span className="font-mono text-[12.5px]">
                                                    {invoice.number}
                                                </span>
                                            </TableCell>
                                            <TableCell>
                                                <Link
                                                    href={`/tenants/${invoice.tenant_id}`}
                                                    className="text-ink transition-colors duration-150 hover:text-accent"
                                                >
                                                    {invoice.tenant_name}
                                                </Link>
                                            </TableCell>
                                            <TableCell
                                                muted
                                                className="tabular-nums"
                                            >
                                                {formatDate(
                                                    invoice.period_start,
                                                )}{' '}
                                                →{' '}
                                                {formatDate(invoice.period_end)}
                                            </TableCell>
                                            <TableCell numeric>
                                                {formatCurrency(
                                                    invoice.total,
                                                    invoice.currency,
                                                )}
                                            </TableCell>
                                            <TableCell
                                                muted
                                                className="tabular-nums"
                                            >
                                                {invoice.paid_at
                                                    ? formatDateTime(
                                                          invoice.paid_at,
                                                      )
                                                    : formatDate(
                                                          invoice.due_at,
                                                      )}
                                            </TableCell>
                                            <TableCell>
                                                <Badge
                                                    tone={
                                                        isCollected(
                                                            invoice.status,
                                                        )
                                                            ? 'positive'
                                                            : invoice.status ===
                                                                'past_due'
                                                              ? 'warning'
                                                              : 'muted'
                                                    }
                                                    dot
                                                >
                                                    {invoice.status.replace(
                                                        /_/g,
                                                        ' ',
                                                    )}
                                                </Badge>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>

                            <Pagination
                                links={invoices?.links ?? []}
                                meta={{
                                    from: invoices?.from ?? 0,
                                    to: invoices?.to ?? 0,
                                    total:
                                        invoices?.total ?? invoiceRows.length,
                                }}
                            />
                        </>
                    )}
                </section>
            </div>
        </SuperAdminLayout>
    );
}
