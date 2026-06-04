import {
    PlusIcon, MagnifyingGlassIcon, TrashIcon,
    EyeIcon, CheckCircleIcon, ChevronRightIcon,
    HomeIcon, DocumentTextIcon
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import React, { useState, useMemo } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface InvoiceItem {
    id: number;
    invoice_number: string;
    patient: { id: number; first_name: string; last_name: string; patient_uid: string } | null;
    invoice_date: string;
    due_date: string | null;
    total_amount: number;
    status: 'draft' | 'pending' | 'paid' | 'overdue' | 'cancelled';
    paid_at: string | null;
    items_count: number;
}

interface Props {
    invoices: { data: InvoiceItem[]; links: any[]; current_page: number; last_page: number; from: number; to: number; total: number };
    filters: { search?: string; status?: string };
}

const STATUS_COLORS: Record<string, string> = {
    draft: 'bg-gray-50 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300 border-gray-200 dark:border-neutral-600',
    pending: 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
    paid: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
    overdue: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-100 dark:border-red-800',
    cancelled: 'bg-gray-100 dark:bg-neutral-700 text-gray-500 dark:text-neutral-400 border-gray-200 dark:border-neutral-600',
};

export default function InvoiceIndex({ invoices, filters }: Props) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || 'all');

    const applyFilters = (newStatus: string, newSearch: string) => {
        router.get(route('finance.invoices.index'), {
            status: newStatus !== 'all' ? newStatus : undefined,
            search: newSearch || undefined,
        }, { preserveState: true, preserveScroll: true, replace: true });
    };

    const handleSearch = (val: string) => {
        setSearch(val);
        applyFilters(statusFilter, val);
    };

    const handleStatus = (val: string) => {
        setStatusFilter(val);
        applyFilters(val, search);
    };

    const statuses = ['all', 'draft', 'pending', 'paid', 'overdue', 'cancelled'];

    return (
        <AppLayout headerTitle={t('finance.invoices.index.title')} headerSubtitle={t('finance.invoices.index.subtitle')}>
            <Head title={t('finance.invoices.index.head_title')} />

            <div className="max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer transition-colors">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}
                        </li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li>{t('finance.invoices.index.finance')}</li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="text-blue-600 font-medium">{t('finance.invoices.index.invoices')}</li>
                    </ol>
                </nav>

                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex p-1 bg-gray-100 dark:bg-neutral-800 rounded-lg overflow-x-auto">
                        {statuses.map(s => (
                            <button
                                key={s}
                                onClick={() => handleStatus(s)}
                                className={`px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wide transition-all whitespace-nowrap ${statusFilter === s ? 'bg-white dark:bg-neutral-700 text-blue-600 shadow-sm' : 'text-gray-500 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-neutral-100'}`}
                            >
                                {t(`finance.invoices.index.status_${s}`)}
                            </button>
                        ))}
                    </div>

                    <div className="flex items-center gap-3">
                        <div className="relative">
                            <MagnifyingGlassIcon className="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                            <input
                                type="text"
                                placeholder={t('finance.invoices.index.search_placeholder')}
                                value={search}
                                onChange={e => handleSearch(e.target.value)}
                                className="pl-9 pr-4 py-2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:text-neutral-100"
                            />
                        </div>
                        <Link
                            href={route('finance.invoices.create')}
                            className="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm whitespace-nowrap"
                        >
                            <PlusIcon className="w-4 h-4" /> {t('finance.invoices.index.new_invoice')}
                        </Link>
                    </div>
                </div>

                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                            <tr>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">{t('finance.invoices.index.invoice_number')}</th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">{t('finance.invoices.index.patient')}</th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">{t('finance.invoices.index.date')}</th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">{t('finance.invoices.index.items')}</th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase text-right">{t('finance.invoices.index.amount')}</th>
                                <th className="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">{t('finance.invoices.index.status')}</th>
                                <th className="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {invoices.data.map(inv => (
                                <tr key={inv.id} className="hover:bg-gray-50 dark:hover:bg-neutral-700/30 transition-colors group">
                                    <td className="px-6 py-4">
                                        <code className="text-xs font-bold text-gray-900 dark:text-neutral-100 bg-gray-100 dark:bg-neutral-700 px-2 py-0.5 rounded">
                                            {inv.invoice_number}
                                        </code>
                                    </td>
                                    <td className="px-6 py-4">
                                        {inv.patient ? (
                                            <div>
                                                <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">{inv.patient.first_name} {inv.patient.last_name}</p>
                                                <p className="text-xs text-gray-400 dark:text-neutral-500">{inv.patient.patient_uid}</p>
                                            </div>
                                        ) : (
                                            <span className="text-sm text-gray-400 italic">—</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">
                                        {new Date(inv.invoice_date).toLocaleDateString()}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-500 dark:text-neutral-400">
                                        {inv.items_count}
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <span className="text-sm font-bold text-gray-900 dark:text-neutral-100">{Number(inv.total_amount).toLocaleString()} FCFA</span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border ${STATUS_COLORS[inv.status] || STATUS_COLORS.draft}`}>
                                            {inv.status}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <Link
                                                href={route('finance.invoices.show', inv.id)}
                                                className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                            >
                                                <EyeIcon className="w-4 h-4" />
                                            </Link>
                                            {inv.status !== 'paid' && inv.status !== 'cancelled' && (
                                                <button
                                                    onClick={() => {
                                                        if (confirm(t('finance.invoices.index.mark_paid_confirm'))) {
                                                            router.patch(route('finance.invoices.status', inv.id), { status: 'paid' }, { preserveScroll: true });
                                                        }
                                                    }}
                                                    className="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 rounded-lg transition-colors"
                                                >
                                                    <CheckCircleIcon className="w-4 h-4" />
                                                </button>
                                            )}
                                            <button
                                                onClick={() => {
                                                    if (confirm(t('finance.invoices.index.delete_confirm'))) {
                                                        router.delete(route('finance.invoices.destroy', inv.id), { preserveScroll: true });
                                                    }
                                                }}
                                                className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                            >
                                                <TrashIcon className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                    {invoices.data.length === 0 && (
                        <div className="px-6 py-16 text-center">
                            <div className="w-16 h-16 bg-gray-100 dark:bg-neutral-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <DocumentTextIcon className="w-8 h-8 text-gray-300 dark:text-neutral-500" />
                            </div>
                            <p className="text-gray-400 dark:text-neutral-500 font-medium">{t('finance.invoices.index.no_invoices')}</p>
                            <Link href={route('finance.invoices.create')} className="mt-3 inline-block text-sm font-bold text-blue-600 hover:text-blue-700">
                                {t('finance.invoices.index.create_first_invoice')}
                            </Link>
                        </div>
                    )}
                </div>

                {invoices.last_page > 1 && (
                    <div className="flex items-center justify-between bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 px-6 py-4">
                        <p className="text-sm text-gray-500 dark:text-neutral-400">
                            {t('finance.invoices.index.showing', { from: invoices.from, to: invoices.to, total: invoices.total })}
                        </p>
                        <div className="flex gap-2">
                            {invoices.links.map((link: any, i: number) => (
                                <button
                                    key={i}
                                    onClick={() => link.url && router.get(link.url, { status: statusFilter !== 'all' ? statusFilter : undefined, search: search || undefined }, { preserveScroll: true })}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    disabled={!link.url}
                                    className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-colors ${link.active ? 'bg-blue-600 text-white' : link.url ? 'text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-700' : 'text-gray-300 dark:text-neutral-600 cursor-default'}`}
                                />
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
