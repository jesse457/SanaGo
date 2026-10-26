import {
    ArrowPathRoundedSquareIcon,
    BanknotesIcon,
    ClockIcon,
} from '@heroicons/react/24/outline';
import { Head, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Badge,
    Button,
    EmptyState,
    PageHeader,
    SegmentedControl,
    StatCard,
    StatGrid,
    Table,
    TableBody,
    TableCell,
    TableHeader,
    TableHeaderCell,
    TableRow,
    Toolbar,
} from '@/Components/ui';
import type { BadgeTone } from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

interface Shift {
    id: number;
    payment_point: string;
    cashier: string;
    cash_point: string;
    status: string;
    opened_at: string;
    closed_at: string | null;
    opening_balance: number;
    closing_balance: number | null;
    expected_total: number | null;
    variance: number | null;
    payments_count: number;
    collected: number;
}

interface Props {
    shifts: {
        data: Shift[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
    };
    filters: { status: string };
}

const STATUS_TONES: Record<string, BadgeTone> = {
    open: 'accent',
    closed: 'neutral',
};

const TABS = [
    { value: 'all', label: 'All' },
    { value: 'open', label: 'Open' },
    { value: 'closed', label: 'Closed' },
];

function money(value: number): string {
    return `${Math.round(value).toLocaleString('fr-FR')} FCFA`;
}

/**
 * The till ledger. The desk (Caisse) opens and closes shifts; this screen is
 * the reconciliation view over every shift ever opened, open or closed, with
 * the counted balance and the variance next to it.
 */
export default function ShiftsIndex({ shifts, filters }: Props) {
    const { t } = useTranslation();
    const [status, setStatus] = useState(filters.status || 'all');

    const applyFilter = (next: string) => {
        setStatus(next);
        router.get(
            route('caisse.shifts'),
            { status: next !== 'all' ? next : undefined },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    };

    const stats = useMemo(() => {
        const open = shifts.data.filter((s) => s.status === 'open').length;
        const collected = shifts.data.reduce((sum, s) => sum + s.collected, 0);
        const variance = shifts.data
            .filter((s) => s.variance !== null)
            .reduce((sum, s) => sum + (s.variance ?? 0), 0);

        return { open, collected, variance };
    }, [shifts.data]);

    return (
        <AppLayout
            headerTitle="Cash shifts"
            headerSubtitle="Every till shift with its collected money and variance."
        >
            <Head title="Cash shifts" />

            <div className="space-y-5">
                <PageHeader
                    title="Cash shifts"
                    description="Every till shift with its collected money and variance."
                    breadcrumbs={[
                        { label: 'Cash register' },
                        { label: 'Cash shifts' },
                    ]}
                    actions={
                        <Button
                            variant="secondary"
                            icon={BanknotesIcon}
                            href={route('caisse.index')}
                        >
                            Go to the desk
                        </Button>
                    }
                />

                <StatGrid>
                    <StatCard
                        icon={ClockIcon}
                        label="Open shifts"
                        value={stats.open}
                        tone="accent"
                        hint="On this page"
                    />
                    <StatCard
                        icon={BanknotesIcon}
                        label="Collected"
                        value={money(stats.collected)}
                        tone="positive"
                        hint="On this page"
                    />
                    <StatCard
                        icon={ArrowPathRoundedSquareIcon}
                        label="Variance"
                        value={money(stats.variance)}
                        tone={
                            stats.variance < 0
                                ? 'critical'
                                : stats.variance > 0
                                  ? 'warning'
                                  : 'positive'
                        }
                        hint="Closed shifts on this page"
                    />
                </StatGrid>

                <Toolbar>
                    <SegmentedControl
                        value={status}
                        onChange={applyFilter}
                        size="sm"
                        options={TABS.map((tab) => ({
                            value: tab.value,
                            label: t(tab.label),
                        }))}
                    />
                </Toolbar>

                {shifts.data.length > 0 ? (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>Till</TableHeaderCell>
                                <TableHeaderCell>Cashier</TableHeaderCell>
                                <TableHeaderCell>Opened</TableHeaderCell>
                                <TableHeaderCell>Closed</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Opening
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Collected
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Expected
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    Variance
                                </TableHeaderCell>
                                <TableHeaderCell>Status</TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {shifts.data.map((shift) => (
                                <TableRow key={shift.id}>
                                    <TableCell>
                                        <p className="text-[13px] font-medium text-ink">
                                            {shift.cash_point}
                                        </p>
                                        <p className="text-[11.5px] text-ink-subtle">
                                            {shift.payments_count}{' '}
                                            {shift.payments_count === 1
                                                ? t('payment')
                                                : t('payments')}
                                        </p>
                                    </TableCell>
                                    <TableCell>
                                        <span className="text-[12.5px] text-ink-muted">
                                            {shift.cashier}
                                        </span>
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="text-[12.5px] tabular-nums">
                                            {shift.opened_at}
                                        </span>
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="text-[12.5px] tabular-nums">
                                            {shift.closed_at ?? '—'}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <span className="text-[12.5px] text-ink tabular-nums">
                                            {money(shift.opening_balance)}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <span className="text-[12.5px] font-semibold text-ink tabular-nums">
                                            {money(shift.collected)}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <span className="text-[12.5px] text-ink-muted tabular-nums">
                                            {shift.expected_total === null
                                                ? '—'
                                                : money(shift.expected_total)}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        {shift.variance === null ? (
                                            <span className="text-[12.5px] text-ink-subtle">
                                                —
                                            </span>
                                        ) : (
                                            <Badge
                                                tone={
                                                    shift.variance === 0
                                                        ? 'positive'
                                                        : shift.variance > 0
                                                          ? 'warning'
                                                          : 'critical'
                                                }
                                                size="sm"
                                            >
                                                {money(shift.variance)}
                                            </Badge>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            tone={
                                                STATUS_TONES[shift.status] ??
                                                'neutral'
                                            }
                                            size="sm"
                                        >
                                            {shift.status === 'open'
                                                ? t('Open')
                                                : t('Closed')}
                                        </Badge>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                ) : (
                    <EmptyState
                        icon={ClockIcon}
                        title="No shifts for this filter"
                        description="Open a shift at the desk to start collecting payments."
                        action={{
                            label: 'Go to the desk',
                            href: route('caisse.index'),
                        }}
                    />
                )}

                <Pagination
                    links={shifts.links ?? []}
                    meta={{
                        from: shifts.from,
                        to: shifts.to,
                        total: shifts.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
