import {
    HomeIcon, ChevronRightIcon, DocumentTextIcon,
    CheckCircleIcon, XCircleIcon, ArrowLeftIcon
} from '@heroicons/react/24/outline';
import { Head, Link, router } from '@inertiajs/react';
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { useTranslation } from '@/hooks/useTranslation';

interface InvoiceItem {
    id: number;
    item_type: string;
    description: string;
    quantity: number;
    unit_price: number;
    total_price: number;
}

interface InvoiceDetail {
    id: number;
    invoice_number: string;
    invoice_date: string;
    due_date: string | null;
    subtotal: number;
    tax_amount: number;
    total_amount: number;
    status: string;
    paid_at: string | null;
    notes: string | null;
    patient: { id: number; first_name: string; last_name: string; patient_uid: string } | null;
    items: InvoiceItem[];
}

interface Props {
    invoice: InvoiceDetail;
}

const STATUS_COLORS: Record<string, string> = {
    draft: 'bg-gray-50 dark:bg-neutral-700 text-gray-600 dark:text-neutral-300 border-gray-200 dark:border-neutral-600',
    pending: 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-800',
    paid: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800',
    overdue: 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-100 dark:border-red-800',
    cancelled: 'bg-gray-100 dark:bg-neutral-700 text-gray-500 dark:text-neutral-400 border-gray-200 dark:border-neutral-600',
};

export default function InvoiceShow({ invoice }: Props) {
    const { t } = useTranslation();
    const canPay = invoice.status !== 'paid' && invoice.status !== 'cancelled';

    return (
        <AppLayout headerTitle={t('finance.invoices.show.title', { number: invoice.invoice_number })} headerSubtitle={t('finance.invoices.show.subtitle')}>
            <Head title={t('finance.invoices.show.head_title', { number: invoice.invoice_number })} />

            <div className="max-w-5xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                <nav className="flex text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-neutral-400">
                    <ol className="inline-flex items-center space-x-2">
                        <li className="flex items-center hover:text-blue-600 cursor-pointer transition-colors">
                            <HomeIcon className="w-4 h-4 mr-1" /> {t('common.home')}
                        </li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="hover:text-blue-600 cursor-pointer">{t('finance.invoices.show.finance')}</li>
                        <li><ChevronRightIcon className="w-3 h-3" /></li>
                        <li className="text-blue-600 font-medium">{invoice.invoice_number}</li>
                    </ol>
                </nav>

                <div className="flex items-center justify-between">
                    <Link
                        href={route('finance.invoices.index')}
                        className="flex items-center gap-1.5 text-sm font-medium text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 transition-colors"
                    >
                        <ArrowLeftIcon className="w-4 h-4" /> {t('finance.invoices.show.back_to_invoices')}
                    </Link>
                    {canPay && (
                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => {
                                    if (confirm(t('finance.invoices.show.mark_paid_confirm'))) {
                                        router.patch(route('finance.invoices.status', invoice.id), { status: 'paid' });
                                    }
                                }}
                                className="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors"
                            >
                                <CheckCircleIcon className="w-4 h-4" /> {t('finance.invoices.show.mark_as_paid')}
                            </button>
                            <button
                                onClick={() => {
                                    if (confirm(t('finance.invoices.show.cancel_confirm'))) {
                                        router.patch(route('finance.invoices.status', invoice.id), { status: 'cancelled' });
                                    }
                                }}
                                className="flex items-center gap-1.5 px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 text-gray-600 dark:text-neutral-300 rounded-lg text-sm font-bold hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            >
                                <XCircleIcon className="w-4 h-4" /> {t('finance.invoices.show.cancel_invoice')}
                            </button>
                        </div>
                    )}
                </div>

                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6 space-y-4">
                    <div className="flex items-start justify-between">
                        <div>
                            <div className="flex items-center gap-3 mb-2">
                                <code className="text-lg font-bold text-gray-900 dark:text-neutral-100 bg-gray-100 dark:bg-neutral-700 px-3 py-1 rounded-lg">
                                    {invoice.invoice_number}
                                </code>
                                <span className={`px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border ${STATUS_COLORS[invoice.status] || STATUS_COLORS.draft}`}>
                                    {invoice.status}
                                </span>
                            </div>
                            {invoice.patient && (
                                <div>
                                    <p className="text-sm font-bold text-gray-900 dark:text-neutral-100">
                                        {invoice.patient.first_name} {invoice.patient.last_name}
                                    </p>
                                    <p className="text-xs text-gray-400 dark:text-neutral-500">{invoice.patient.patient_uid}</p>
                                </div>
                            )}
                        </div>
                        <div className="text-right space-y-1">
                            <div className="text-xs text-gray-500 dark:text-neutral-400">
                                <span className="font-semibold">{t('finance.invoices.show.date')}:</span> {new Date(invoice.invoice_date).toLocaleDateString()}
                            </div>
                            {invoice.due_date && (
                                <div className="text-xs text-gray-500 dark:text-neutral-400">
                                    <span className="font-semibold">{t('finance.invoices.show.due')}:</span> {new Date(invoice.due_date).toLocaleDateString()}
                                </div>
                            )}
                            {invoice.paid_at && (
                                <div className="text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                                    {t('finance.invoices.show.paid')}: {new Date(invoice.paid_at).toLocaleDateString()}
                                </div>
                            )}
                        </div>
                    </div>
                    {invoice.notes && (
                        <div className="p-3 bg-gray-50 dark:bg-neutral-700/50 rounded-lg">
                            <p className="text-xs text-gray-500 dark:text-neutral-400">{invoice.notes}</p>
                        </div>
                    )}
                </div>

                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm overflow-hidden">
                    <div className="px-6 py-4 border-b border-gray-100 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800">
                        <h3 className="text-sm font-bold text-gray-900 dark:text-neutral-100 uppercase">{t('finance.invoices.show.line_items')}</h3>
                    </div>
                    <table className="w-full text-left">
                        <thead className="bg-gray-50 dark:bg-neutral-800 border-b border-gray-200 dark:border-neutral-700">
                            <tr>
                                <th className="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">{t('finance.invoices.show.item')}</th>
                                <th className="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase">{t('finance.invoices.show.type')}</th>
                                <th className="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase text-center">{t('finance.invoices.show.qty')}</th>
                                <th className="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase text-right">{t('finance.invoices.show.unit_price')}</th>
                                <th className="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-neutral-400 uppercase text-right">{t('finance.invoices.show.total')}</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100 dark:divide-neutral-700">
                            {invoice.items.map(item => (
                                <tr key={item.id}>
                                    <td className="px-6 py-4 text-sm font-bold text-gray-900 dark:text-neutral-100">{item.description}</td>
                                    <td className="px-6 py-4">
                                        <span className="text-xs font-medium text-gray-500 dark:text-neutral-400 bg-gray-100 dark:bg-neutral-700 px-2 py-0.5 rounded capitalize">
                                            {item.item_type.replace('_', ' ')}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-sm text-center text-gray-500 dark:text-neutral-400">{item.quantity}</td>
                                    <td className="px-6 py-4 text-sm text-right text-gray-500 dark:text-neutral-400">{Number(item.unit_price).toLocaleString()} FCFA</td>
                                    <td className="px-6 py-4 text-sm font-bold text-right text-gray-900 dark:text-neutral-100">{Number(item.total_price).toLocaleString()} FCFA</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 p-6">
                    <div className="space-y-2 max-w-sm ml-auto">
                        <div className="flex justify-between text-sm">
                            <span className="text-gray-500 dark:text-neutral-400">{t('finance.invoices.show.subtotal')}</span>
                            <span className="font-bold text-gray-900 dark:text-neutral-100">{Number(invoice.subtotal).toLocaleString()} FCFA</span>
                        </div>
                        <div className="flex justify-between text-sm">
                            <span className="text-gray-500 dark:text-neutral-400">{t('finance.invoices.show.tax')}</span>
                            <span className="font-bold text-gray-900 dark:text-neutral-100">{Number(invoice.tax_amount).toLocaleString()} FCFA</span>
                        </div>
                        <div className="flex justify-between text-lg pt-2 border-t border-gray-100 dark:border-neutral-700">
                            <span className="font-bold text-gray-900 dark:text-neutral-100">{t('finance.invoices.show.total_label')}</span>
                            <span className="font-black text-gray-900 dark:text-neutral-100">{Number(invoice.total_amount).toLocaleString()} FCFA</span>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
