import {
    PlusIcon,
    MagnifyingGlassIcon,
    TrashIcon,
    EyeIcon,
    CheckCircleIcon,
    DocumentTextIcon,
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import React, { useState } from 'react';
import EmptyState from '@/Components/EmptyState';
import PageBreadcrumb from '@/Components/PageBreadcrumb';
import { Pagination } from '@/Components/Pagination';
import StatusBadge from '@/Components/StatusBadge';
import { useTranslation } from '@/hooks/useTranslation';
import AppLayout from '@/Layouts/AppLayout';

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

const STATUS_BADGE_MAP: Record<string, 'gray' | 'amber' | 'emerald' | 'red'> = {
    draft: 'gray',
    pending: 'amber',
    paid: 'emerald',
    overdue: 'red',
    cancelled: 'gray',
};

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

    const handleSearch = (val: string) => {
        setSearch(val);
        applyFilters(statusFilter, val);
    };

    const handleStatus = (val: string) => {
        setStatusFilter(val);
        applyFilters(val, search);
    };

    const statuses = [
        'all',
        'draft',
        'pending',
        'paid',
        'overdue',
        'cancelled',
    ];

    const paginationLinks = invoices.links?.map((link) => ({
        url: link.url,
        label: link.label,
        active: link.active,
    }));

    return (
        <AppLayout
            headerTitle={t('finance.invoices.index.title')}
            headerSubtitle={t('finance.invoices.index.subtitle')}
        >
            <Head title={t('finance.invoices.index.head_title')} />

            <div className="mx-auto max-w-7xl space-y-6 px-4 pb-12 sm:px-6 lg:px-8">
                <PageBreadcrumb
                    items={[
                        { label: t('common.home'), href: route('dashboard') },
                        { label: t('finance.invoices.index.finance') },
                        { label: t('finance.invoices.index.invoices') },
                    ]}
                />

                <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div className="flex overflow-x-auto rounded-lg bg-gray-100 p-1 dark:bg-neutral-800">
                        {statuses.map((s) => (
                            <button
                                key={s}
                                onClick={() => handleStatus(s)}
                                className={`rounded-lg px-4 py-2 text-xs font-bold tracking-wide whitespace-nowrap uppercase transition-all ${statusFilter === s ? 'bg-white text-blue-600 shadow-sm dark:bg-neutral-700' : 'text-gray-500 hover:text-gray-900 dark:text-neutral-400 dark:hover:text-neutral-100'}`}
                            >
                                {t(`finance.invoices.index.status_${s}`)}
                            </button>
                        ))}
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-neutral-500" />
                            <input
                                type="text"
                                placeholder={t(
                                    'finance.invoices.index.search_placeholder',
                                )}
                                value={search}
                                onChange={(e) => handleSearch(e.target.value)}
                                className="rounded-lg border border-gray-200 bg-white py-2 pr-4 pl-9 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>
                        <Link
                            href={route('finance.invoices.create')}
                            className="flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold whitespace-nowrap text-white shadow-sm transition-colors hover:bg-blue-700"
                        >
                            <PlusIcon className="h-4 w-4" />{' '}
                            {t('finance.invoices.index.new_invoice')}
                        </Link>
                    </div>
                </div>

                <div className="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
                    <table className="w-full text-left">
                        <thead className="border-b border-gray-200 bg-gray-50 dark:border-neutral-700 dark:bg-neutral-800">
                            <tr>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.index.invoice_number')}
                                </th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.index.patient')}
                                </th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.index.date')}
                                </th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.index.items')}
                                </th>
                                <th className="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.index.amount')}
                                </th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 uppercase dark:text-neutral-400">
                                    {t('finance.invoices.index.status')}
                                </th>
                                <th className="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {invoices.data.map((inv) => (
                                <tr
                                    key={inv.id}
                                    className="group transition-colors hover:bg-gray-50 dark:hover:bg-neutral-700/30"
                                >
                                    <td className="px-6 py-4">
                                        <code className="rounded bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-900 dark:bg-neutral-700 dark:text-neutral-100">
                                            {inv.invoice_number}
                                        </code>
                                    </td>
                                    <td className="px-6 py-4">
                                        {inv.patient ? (
                                            <div>
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                                    {inv.patient.first_name}{' '}
                                                    {inv.patient.last_name}
                                                </p>
                                                <p className="text-xs text-gray-400 dark:text-neutral-500">
                                                    {inv.patient.patient_uid}
                                                </p>
                                            </div>
                                        ) : (
                                            <span className="text-sm text-gray-400 italic">
                                                —
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">
                                        {new Date(
                                            inv.invoice_date,
                                        ).toLocaleDateString()}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">
                                        {inv.items_count}
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <span className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                            {Number(
                                                inv.total_amount,
                                            ).toLocaleString()}{' '}
                                            FCFA
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <StatusBadge
                                            label={inv.status}
                                            color={
                                                STATUS_BADGE_MAP[inv.status] ||
                                                'gray'
                                            }
                                        />
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                            <Link
                                                href={route(
                                                    'finance.invoices.show',
                                                    inv.id,
                                                )}
                                                className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20"
                                            >
                                                <EyeIcon className="h-4 w-4" />
                                            </Link>
                                            {inv.status !== 'paid' &&
                                                inv.status !== 'cancelled' && (
                                                    <button
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
                                                                        inv.id,
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
                                                        className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-900/20"
                                                    >
                                                        <CheckCircleIcon className="h-4 w-4" />
                                                    </button>
                                                )}
                                            <button
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
                                                                inv.id,
                                                            ),
                                                            {
                                                                preserveScroll: true,
                                                            },
                                                        );
                                                    }
                                                }}
                                                className="rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                                            >
                                                <TrashIcon className="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                    {invoices.data.length === 0 && (
                        <div className="p-6">
                            <EmptyState
                                icon={DocumentTextIcon}
                                title={t('finance.invoices.index.no_invoices')}
                                action={{
                                    label: t(
                                        'finance.invoices.index.create_first_invoice',
                                    ),
                                    href: route('finance.invoices.create'),
                                }}
                            />
                        </div>
                    )}
                </div>

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
