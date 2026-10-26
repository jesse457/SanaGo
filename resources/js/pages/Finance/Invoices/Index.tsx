import { DocumentTextIcon, EyeIcon } from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import debounce from 'lodash/debounce';
import { useEffect, useMemo, useState } from 'react';
import { Pagination } from '@/Components/Pagination';
import {
    Button,
    EmptyState,
    PageHeader,
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
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { InvoiceStatusBadge } from '@/pages/Finance/components/FinanceStatusBadges';
import { formatDate, formatMoney } from '@/pages/Finance/components/format';

interface InvoiceRow {
    id: number;
    invoice_number: string;
    patient_name: string;
    patient_uid: string | null;
    invoice_date: string | null;
    status: string;
    total_amount: number;
    remaining_balance: number;
    items_count: number;
}

interface Props {
    invoices: {
        data: InvoiceRow[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
    };
    filters: { search: string; status: string };
}

const STATUS_TABS = [
    { value: 'all', label: 'All' },
    { value: 'draft', label: 'Draft' },
    { value: 'pending', label: 'Pending' },
    { value: 'paid', label: 'Paid' },
    { value: 'overdue', label: 'Overdue' },
    { value: 'cancelled', label: 'Cancelled' },
];

/**
 * The invoices ledger: every bill in the system with its status, what was
 * paid and what remains. Bills are opened and settled at the desk — this
 * screen reads, so an open bill links straight back into the counter.
 */
export default function InvoicesIndex({ invoices, filters }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status || 'all');

    const applyFilters = useMemo(
        () =>
            debounce((nextStatus: string, nextSearch: string) => {
                router.get(
                    route('finance.invoices.index'),
                    {
                        status: nextStatus !== 'all' ? nextStatus : undefined,
                        search: nextSearch || undefined,
                    },
                    {
                        preserveState: true,
                        preserveScroll: true,
                        replace: true,
                    },
                );
            }, 300),
        [],
    );

    useEffect(() => () => applyFilters.cancel(), [applyFilters]);

    const handleSearch = (value: string) => {
        setSearch(value);
        applyFilters(status, value);
    };

    const handleStatus = (value: string) => {
        setStatus(value);
        applyFilters(value, search);
    };

    const hasRows = invoices.data.length > 0;

    return (
        <AppLayout
            headerTitle={t('finance.invoices.index.title')}
            headerSubtitle={t('finance.invoices.index.subtitle')}
        >
            <Head title={t('finance.invoices.index.head_title')} />

            <div className="space-y-5">
                <PageHeader
                    title={t('finance.invoices.index.title')}
                    description={t('finance.invoices.index.subtitle')}
                    breadcrumbs={[
                        { label: t('finance.invoices.index.finance') },
                        { label: t('finance.invoices.index.invoices') },
                    ]}
                />

                <Toolbar>
                    <div className="min-w-0 flex-1 overflow-x-auto sm:flex-none">
                        <div className="flex items-center gap-1">
                            {STATUS_TABS.map((tab) => (
                                <Button
                                    key={tab.value}
                                    size="sm"
                                    variant={
                                        status === tab.value
                                            ? 'secondary'
                                            : 'ghost'
                                    }
                                    aria-pressed={status === tab.value}
                                    onClick={() => handleStatus(tab.value)}
                                    className="whitespace-nowrap"
                                >
                                    {t(tab.label)}
                                </Button>
                            ))}
                        </div>
                    </div>
                    <ToolbarSpacer />
                    <ToolbarSearch
                        value={search}
                        onChange={handleSearch}
                        placeholder={t(
                            'finance.invoices.index.search_placeholder',
                        )}
                    />
                </Toolbar>

                {hasRows ? (
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHeaderCell>
                                    {t('Invoice')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('Patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>{t('Date')}</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('Lines')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('Total')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('Outstanding')}
                                </TableHeaderCell>
                                <TableHeaderCell>{t('Status')}</TableHeaderCell>
                                <TableHeaderCell numeric>
                                    <span className="sr-only">
                                        {t('Actions')}
                                    </span>
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {invoices.data.map((invoice) => (
                                <TableRow key={invoice.id}>
                                    <TableCell>
                                        <span className="font-mono text-[12px] font-medium tracking-tight text-ink">
                                            {invoice.invoice_number}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <p className="max-w-[16rem] truncate text-[13px] font-medium text-ink">
                                            {invoice.patient_name}
                                        </p>
                                        {invoice.patient_uid && (
                                            <p className="font-mono text-[11px] text-ink-subtle">
                                                {invoice.patient_uid}
                                            </p>
                                        )}
                                    </TableCell>
                                    <TableCell muted>
                                        <span className="text-[12.5px] tabular-nums">
                                            {invoice.invoice_date
                                                ? formatDate(
                                                      invoice.invoice_date,
                                                  )
                                                : '—'}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric muted>
                                        <span className="text-[12.5px] tabular-nums">
                                            {invoice.items_count}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <span className="text-[12.5px] font-semibold text-ink tabular-nums">
                                            {formatMoney(invoice.total_amount)}
                                        </span>
                                    </TableCell>
                                    <TableCell numeric>
                                        <span
                                            className={
                                                invoice.remaining_balance > 0
                                                    ? 'text-[12.5px] font-semibold text-critical-ink tabular-nums'
                                                    : 'text-[12.5px] text-ink-subtle tabular-nums'
                                            }
                                        >
                                            {formatMoney(
                                                invoice.remaining_balance,
                                            )}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <InvoiceStatusBadge
                                            status={invoice.status}
                                        />
                                    </TableCell>
                                    <TableCell numeric>
                                        <Link
                                            href={route(
                                                'finance.invoices.show',
                                                invoice.id,
                                            )}
                                            aria-label={t(
                                                'Open invoice document',
                                            )}
                                            title={t('Open invoice document')}
                                            className="inline-flex h-8 w-8 items-center justify-center rounded-md text-ink-subtle transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink"
                                        >
                                            <EyeIcon className="h-4 w-4" />
                                        </Link>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                ) : (
                    <EmptyState
                        icon={DocumentTextIcon}
                        title={t('finance.invoices.index.no_invoices')}
                        description="Bills are opened at the cash desk when a patient pays."
                        action={{
                            label: 'Go to the desk',
                            href: route('caisse.index'),
                        }}
                    />
                )}

                <Pagination
                    links={invoices.links ?? []}
                    meta={{
                        from: invoices.from,
                        to: invoices.to,
                        total: invoices.total,
                    }}
                />
            </div>
        </AppLayout>
    );
}
