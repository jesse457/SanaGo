import {
    CheckCircleIcon,
    DocumentTextIcon,
    EyeIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
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
    ToolbarGroup,
    ToolbarSearch,
    ToolbarSpacer,
} from '@/Components/ui';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';
import { InvoiceStatusBadge } from '@/pages/Finance/components/FinanceStatusBadges';
import { formatDate, formatMoney } from '@/pages/Finance/components/format';

interface InvoiceItem {
    id: number;
    invoice_number: string;
    patient: {
        id: number;
        first_name: string;
        last_name: string;
        patient_uid: string;
    } | null;
    invoice_date: string;
    due_date: string | null;
    total_amount: number;
    status: 'draft' | 'pending' | 'paid' | 'overdue' | 'cancelled';
    paid_at: string | null;
    items_count: number;
}

interface Props {
    invoices: {
        data: InvoiceItem[];
        links: { url: string | null; label: string; active: boolean }[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
    };
    filters: { search?: string; status?: string };
}

const STATUSES = ['all', 'draft', 'pending', 'paid', 'overdue', 'cancelled'];

export default function InvoiceIndex({ invoices, filters }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || 'all');

    const applyFilters = (newStatus: string, newSearch: string) => {
        router.get(
            route('finance.invoices.index'),
            {
                status: newStatus !== 'all' ? newStatus : undefined,
                search: newSearch || undefined,
            },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    };

    const handleSearch = (value: string) => {
        setSearch(value);
        applyFilters(statusFilter, value);
    };

    const handleStatus = (value: string) => {
        setStatusFilter(value);
        applyFilters(value, search);
    };

    const paginationLinks = invoices.links?.map((link) => ({
        url: link.url,
        label: link.label,
        active: link.active,
    }));

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
                    actions={
                        <Button
                            icon={PlusIcon}
                            href={route('finance.invoices.create')}
                        >
                            {t('finance.invoices.index.new_invoice')}
                        </Button>
                    }
                />

                <Toolbar>
                    <ToolbarGroup>
                        {STATUSES.map((status) => {
                            const active = statusFilter === status;

                            return (
                                <Button
                                    key={status}
                                    size="sm"
                                    variant={active ? 'secondary' : 'ghost'}
                                    aria-pressed={active}
                                    onClick={() => handleStatus(status)}
                                >
                                    {t(
                                        `finance.invoices.index.status_${status}`,
                                    )}
                                </Button>
                            );
                        })}
                    </ToolbarGroup>
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
                                    {t('finance.invoices.index.invoice_number')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('finance.invoices.index.patient')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('finance.invoices.index.date')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('finance.invoices.index.items')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('finance.invoices.index.amount')}
                                </TableHeaderCell>
                                <TableHeaderCell>
                                    {t('finance.invoices.index.status')}
                                </TableHeaderCell>
                                <TableHeaderCell numeric>
                                    {t('common.actions')}
                                </TableHeaderCell>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {invoices.data.map((invoice) => {
                                const canMarkPaid =
                                    invoice.status !== 'paid' &&
                                    invoice.status !== 'cancelled';

                                return (
                                    <TableRow key={invoice.id}>
                                        <TableCell>
                                            <span className="font-mono text-[12.5px] font-medium tracking-tight text-ink">
                                                {invoice.invoice_number}
                                            </span>
                                        </TableCell>
                                        <TableCell muted>
                                            {invoice.patient ? (
                                                <div className="min-w-0">
                                                    <p className="truncate text-[13px] font-medium text-ink">
                                                        {
                                                            invoice.patient
                                                                .first_name
                                                        }{' '}
                                                        {
                                                            invoice.patient
                                                                .last_name
                                                        }
                                                    </p>
                                                    <p className="mt-0.5 text-[11.5px] text-ink-subtle tabular-nums">
                                                        {
                                                            invoice.patient
                                                                .patient_uid
                                                        }
                                                    </p>
                                                </div>
                                            ) : (
                                                '—'
                                            )}
                                        </TableCell>
                                        <TableCell muted>
                                            {formatDate(invoice.invoice_date)}
                                        </TableCell>
                                        <TableCell numeric muted>
                                            {invoice.items_count}
                                        </TableCell>
                                        <TableCell numeric>
                                            <span className="font-semibold text-ink">
                                                {formatMoney(
                                                    invoice.total_amount,
                                                )}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <InvoiceStatusBadge
                                                status={invoice.status}
                                                label={t(
                                                    `finance.invoices.index.status_${invoice.status}`,
                                                )}
                                            />
                                        </TableCell>
                                        <TableCell numeric>
                                            <div className="flex items-center justify-end gap-1">
                                                <Link
                                                    href={route(
                                                        'finance.invoices.show',
                                                        invoice.id,
                                                    )}
                                                    aria-label={t(
                                                        'common.view_details',
                                                    )}
                                                    className="inline-flex h-8 w-8 items-center justify-center rounded-md text-ink-subtle transition-colors duration-150 ease-premium hover:bg-surface-hover hover:text-ink focus-visible:ring-2 focus-visible:ring-accent/40 focus-visible:outline-none"
                                                >
                                                    <EyeIcon className="h-4 w-4" />
                                                </Link>
                                                {canMarkPaid && (
                                                    <Button
                                                        variant="ghost"
                                                        size="sm"
                                                        icon={CheckCircleIcon}
                                                        aria-label={t(
                                                            'finance.invoices.index.mark_paid_confirm',
                                                        )}
                                                        onClick={() => {
                                                            if (
                                                                confirm(
                                                                    t(
                                                                        'finance.invoices.index.mark_paid_confirm',
                                                                    ),
                                                                )
                                                            ) {
                                                                router.patch(
                                                                    route(
                                                                        'finance.invoices.status',
                                                                        invoice.id,
                                                                    ),
                                                                    {
                                                                        status: 'paid',
                                                                    },
                                                                    {
                                                                        preserveScroll: true,
                                                                    },
                                                                );
                                                            }
                                                        }}
                                                    />
                                                )}
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    icon={TrashIcon}
                                                    aria-label={t(
                                                        'common.delete',
                                                    )}
                                                    onClick={() => {
                                                        if (
                                                            confirm(
                                                                t(
                                                                    'finance.invoices.index.delete_confirm',
                                                                ),
                                                            )
                                                        ) {
                                                            router.delete(
                                                                route(
                                                                    'finance.invoices.destroy',
                                                                    invoice.id,
                                                                ),
                                                                {
                                                                    preserveScroll: true,
                                                                },
                                                            );
                                                        }
                                                    }}
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                ) : (
                    <EmptyState
                        icon={DocumentTextIcon}
                        title={t('finance.invoices.index.no_invoices')}
                        description={t('finance.invoices.index.subtitle')}
                        action={{
                            label: t(
                                'finance.invoices.index.create_first_invoice',
                            ),
                            href: route('finance.invoices.create'),
                        }}
                    />
                )}

                <Pagination
                    links={paginationLinks}
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
