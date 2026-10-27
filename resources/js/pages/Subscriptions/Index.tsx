import {
    ArrowTopRightOnSquareIcon,
    BanknotesIcon,
    CalendarDaysIcon,
    MagnifyingGlassIcon,
    QueueListIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
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
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import SuperAdminLayout from '@/Layouts/SuperAdminLayout';
import {
    formatCurrency,
    formatCurrencyCompact,
    formatDate,
} from '@/pages/Platform/components/format';
import type {
    Paginated,
    RevenueSummary,
    SubscriptionListRow,
} from '@/pages/Platform/components/types';
import { SubscriptionStatusBadge } from '@/pages/Platform/components/UsageMeter';

interface SubscriptionsIndexProps {
    subscriptions: Paginated<SubscriptionListRow>;
    filters: { search: string; status: string };
    summary: RevenueSummary;
    plans: { id: number; name: string }[];
    statuses: string[];
}

/** Readable label for a status enum value (`past_due` → `Past due`). */
function statusLabel(value: string): string {
    return value
        .replace(/_/g, ' ')
        .replace(/^\w/, (character) => character.toUpperCase());
}

export default function SubscriptionsIndex({
    subscriptions,
    filters,
    summary,
    statuses,
}: SubscriptionsIndexProps) {
    const { props } = usePage();
    const platformCurrency =
        (props as any)?.platform?.currency ?? summary?.currency ?? 'XAF';

    const [search, setSearch] = useState(filters?.search ?? '');
    const [status, setStatus] = useState(filters?.status ?? 'all');
    const [loading, setLoading] = useState(false);

    const applyFilters = useCallback(
        (nextSearch: string, nextStatus: string) => {
            const query: Record<string, string> = {};

            if (nextSearch.trim() !== '') {
                query.search = nextSearch.trim();
            }

            if (nextStatus !== 'all') {
                query.status = nextStatus;
            }

            router.get('/subscriptions', query, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onStart: () => setLoading(true),
                onFinish: () => setLoading(false),
            });
        },
        [],
    );

    useEffect(() => {
        if (search.trim() === (filters?.search ?? '').trim()) {
            return;
        }

        const handle = setTimeout(() => applyFilters(search, status), 350);

        return () => clearTimeout(handle);
        // `status` is read at submit time on purpose.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const changeStatus = (next: string) => {
        setStatus(next);
        applyFilters(search, next);
    };

    const rows = subscriptions?.data ?? [];
    const currency = summary?.currency ?? platformCurrency;
    const hasFilters =
        (filters?.search ?? '') !== '' || (filters?.status ?? 'all') !== 'all';

    return (
        <SuperAdminLayout
            headerTitle="Subscriptions"
            headerSubtitle="Platform-wide contracts"
        >
            <Head title="Subscriptions" />
            <div className="space-y-5 pb-12">
                <PageHeader
                    title="Subscriptions"
                    description="Every plan contract across the platform, with recurring revenue and expiry pressure in one view."
                    breadcrumbs={[
                        { label: 'Billing' },
                        { label: 'Subscriptions' },
                    ]}
                />

                <StatGrid columns={4}>
                    <StatCard
                        label="MRR"
                        value={formatCurrencyCompact(summary?.mrr, currency)}
                        icon={BanknotesIcon}
                        tone="positive"
                        hint={`ARR ${formatCurrencyCompact(summary?.arr, currency)}`}
                    />
                    <StatCard
                        label="Active"
                        value={summary?.active ?? 0}
                        icon={QueueListIcon}
                        tone="accent"
                        hint="Paying subscriptions"
                    />
                    <StatCard
                        label="Trialing"
                        value={summary?.trialing ?? 0}
                        icon={CalendarDaysIcon}
                        tone="neutral"
                        hint="Not yet invoiced"
                    />
                    <StatCard
                        label="Past due"
                        value={summary?.past_due ?? 0}
                        icon={BanknotesIcon}
                        tone={
                            (summary?.past_due ?? 0) > 0
                                ? 'critical'
                                : 'neutral'
                        }
                        hint={
                            (summary?.past_due ?? 0) > 0
                                ? 'Needs collection'
                                : 'Nothing to collect'
                        }
                    />
                </StatGrid>

                <Toolbar>
                    <ToolbarSearch
                        value={search}
                        onChange={setSearch}
                        placeholder="Search by tenant or plan…"
                        busy={loading}
                    />
                    <Select
                        aria-label="Filter by subscription status"
                        value={status}
                        onChange={(event) => changeStatus(event.target.value)}
                        className="h-9 w-auto min-w-40 text-[13px]"
                    >
                        <option value="all">All statuses</option>
                        {(statuses ?? []).map((value) => (
                            <option key={value} value={value}>
                                {statusLabel(value)}
                            </option>
                        ))}
                    </Select>
                    <ToolbarSpacer />
                    <span className="text-[12px] text-ink-subtle tabular-nums">
                        {subscriptions?.total ?? rows.length} subscription
                        {(subscriptions?.total ?? rows.length) === 1 ? '' : 's'}
                    </span>
                </Toolbar>

                {rows.length === 0 ? (
                    hasFilters ? (
                        <EmptyState
                            icon={MagnifyingGlassIcon}
                            title="No subscription matches these filters"
                            description="Try another tenant, plan or status."
                            secondaryAction={
                                <Button
                                    variant="secondary"
                                    onClick={() => {
                                        setSearch('');
                                        setStatus('all');
                                        router.get(
                                            '/subscriptions',
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
                            icon={QueueListIcon}
                            title="No subscriptions yet"
                            description="Subscriptions are assigned from a hospital page once a plan exists."
                            action={{
                                label: 'Browse hospitals',
                                href: '/tenants',
                            }}
                        />
                    )
                ) : (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>Hospital</TableHeaderCell>
                                <TableHeaderCell>Plan</TableHeaderCell>
                                <TableHeaderCell numeric>Price</TableHeaderCell>
                                <TableHeaderCell>Period</TableHeaderCell>
                                <TableHeaderCell>Status</TableHeaderCell>
                                <TableHeaderCell align="right">
                                    Actions
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {rows.map((subscription) => (
                                <TableRow key={subscription.id}>
                                    <TableCell>
                                        <Link
                                            href={`/tenants/${subscription.tenant_id}`}
                                            className="font-medium text-ink transition-colors duration-150 hover:text-accent"
                                        >
                                            {subscription.tenant_name}
                                        </Link>
                                        <span className="block font-mono text-[11.5px] text-ink-subtle">
                                            {subscription.tenant_id}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium text-ink">
                                            {subscription.plan_name}
                                        </span>
                                        <span className="block text-[11.5px] text-ink-subtle">
                                            {subscription.billing_cycle}
                                            {subscription.auto_renew
                                                ? ' · auto-renews'
                                                : ''}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        {formatCurrency(
                                            subscription.price,
                                            subscription.currency,
                                        )}
                                    </TableCell>
                                    <TableCell muted className="tabular-nums">
                                        {formatDate(subscription.start_date)} →{' '}
                                        {formatDate(subscription.end_date)}
                                    </TableCell>
                                    <TableCell>
                                        <SubscriptionStatusBadge
                                            label={subscription.status_label}
                                            tone={subscription.status_tone}
                                            daysRemaining={
                                                subscription.days_remaining
                                            }
                                        />
                                    </TableCell>
                                    <TableCell align="right">
                                        <div className="flex items-center justify-end gap-1">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                icon={ArrowTopRightOnSquareIcon}
                                                href={`/subscriptions/${subscription.id}`}
                                            >
                                                Details
                                            </Button>
                                            <Button
                                                variant="secondary"
                                                size="sm"
                                                href={`/tenants/${subscription.tenant_id}`}
                                            >
                                                Hospital
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                )}

                {rows.length > 0 && (
                    <Pagination
                        links={subscriptions?.links ?? []}
                        meta={{
                            from: subscriptions?.from ?? 0,
                            to: subscriptions?.to ?? 0,
                            total: subscriptions?.total ?? rows.length,
                        }}
                    />
                )}
            </div>
        </SuperAdminLayout>
    );
}
